<?php

namespace Modules\Finance\Controllers;

use App\Libraries\Php_spreadsheets;

class Expense_controller extends \CodeIgniter\Controller {
	public function __construct(){
	        $this->session = \Config\Services::session();
	        $this->userdata = $this->session->get('user_data');
	        if(empty($this->userdata['ID'])){
                header('location:'.base_url().'login');
            }

            $this->expense_model = new \Modules\Finance\Models\Expense_model();
            $this->form_validation = \Config\Services::validation();
	}

	public function saveExpense(){
        $comp_id = $this->userdata['CompID'];
        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start adding expenses.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        if(!empty($_POST)){
            $this->form_validation->setRule('ExpenseHeadMasterID', 'Expense Head', 'required');
            $this->form_validation->setRule('ExpenseDate', 'Expense Date', 'required');
            $this->form_validation->setRule('ExpenseAmount', 'Amount', 'required|regex_match[/^\d*[.]?\d+$/m]',['regex_match' => 'Amount should either be decimal or number.']);
            $this->form_validation->setRule('InvoiceNo', 'Invoice No', 'permit_empty|validateVendorInvoice['.$this->request->getPost('VendorID').']');
            

            if(!empty($_FILES['AttachedDocumentPath']['name'])){
                if($_FILES['AttachedDocumentPath']['size'] == 0){
                    $this->form_validation->setRule('AttachedDocumentPath', 'Attached Document Path', 'required',['required' => 'Invalid image with 0 bytes.']);
                }
                
                if($_FILES['AttachedDocumentPath']['size'] > 2097152){
                    $this->form_validation->setRule('AttachedDocumentPath', 'Attached Document Path', 'required',['required' => 'File size should be maximum of 2MB.']);
                }
            }

            $this->form_validation->setRule('Tax.*', 'Tax', 'required');
            $this->form_validation->setRule('TaxPercentage.*', 'Tax Percentage', 'required|numeric');

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $expense_data = [
                    'ExpenseHeadMasterID' => $this->request->getPost('ExpenseHeadMasterID'),
                    'CompID' => $comp_id,
                    'VendorID' => ($this->request->getPost('VendorID'))?$this->request->getPost('VendorID'):null,
                    'ExpenseDate' => $this->request->getPost('ExpenseDate'),
                    'ExpenseAmount' => $this->request->getPost('ExpenseAmount'),
                    'InvoiceNo' => ($this->request->getPost('InvoiceNo'))?$this->request->getPost('InvoiceNo'):null,
                    'Remarks' => ($this->request->getPost('Remarks'))?$this->request->getPost('Remarks'):null,
                    'AddedBy' => $this->userdata['ID'],
                    'AddedDate' => date('Y-m-d H:i:s')
                ];

                if(!empty($_FILES['AttachedDocumentPath']['name'])){
                    $expense_document_uploaded_data = upload_file('client-documents',$_FILES['AttachedDocumentPath']);
                    
                    $expense_data['AttachedDocumentPath'] = $expense_document_uploaded_data['data']['bucket_path'][0];
                }

                $expense_id = $this->expense_model->saveExpense($expense_data);

                for($i=0;$i<count($_POST['Tax']);$i++){
                    if(!empty($_POST['Tax'][$i]) && isset($_POST['TaxPercentage'][$i])){
                        $expenses_taxes_data = [
                            'ExpenseID' => $expense_id,
                            'Tax' => $_POST['Tax'][$i],
                            'TaxPercentage' => (!empty($_POST['TaxPercentage'][$i]))?$_POST['TaxPercentage'][$i]:'0',
                        ];

                        $expenses_taxes_data_arr[] = $expenses_taxes_data;
                    }
                }

                $this->expense_model->saveExpenseTaxesData($expenses_taxes_data_arr);

                $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Expense saved successfully!']);
                $this->response->redirect(base_url('view-expenses'));
            }
        }

        $vendor_model = new \Modules\Vendors\Models\Vendor_model();

        $app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];

        $posted_vendor_id = $this->request->getPost('VendorID');

        $data = [
            'expense_heads' => $this->expense_model->fetchExpenseHeads($this->userdata['CompID']),
            'vendors' => $vendor_model->fetchVendors($comp_id,$subscription_end_date,30,0,[],false,$posted_vendor_id),
            'add_bel_global_js' => base_url('assets/js/finance.js')
        ];

        return default_view('\Modules\Finance\Views\save_expense', $data);
    }

	public function viewExpenses(){
        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }
        
		$data = [
			'add_bel_global_js' => base_url('assets/js/finance.js')
		];

		return default_view('\Modules\Finance\Views\view_expenses',$data);
	}

	public function deleteExpense($expense_id){
		if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start deleting expenses.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $expense_details = $this->expense_model->fetchExpenseData($expense_id, $this->userdata['CompID']);
        if(!empty($expense_details)){
            $this->expense_model->deleteExpense($expense_id);

            if(!empty($expense_details['AttachedDocumentPath'])){
            	$app_key = env('media_server_app_key');
                $app_secret = env('media_server_app_secret');

                $delete_media = curl_request(media_server('delete-media'),$media_delete_data = json_encode(
                    [
                        'app_key' => $app_key,
                        'app_secret' => $app_secret,
                        'bucket' => 'client-documents',
                        'path' => $expense_details['AttachedDocumentPath']
                    ]
                ));
            }
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Expense deleted successfully!']);
        }else{
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Either the Expense detail Does not Exist or Does not Belong to You.']);
        }

        $this->response->redirect(base_url('view-expenses'));
	}

    public function exportExcelExpenses(){
        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <= 0 && $subscription_time_left['months'] <= 0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start exporting expenses excel.']);
            $this->response->redirect(base_url('plan-renewal'));
        }

        $comp_id = $this->userdata['CompID'];
        $client_id = $this->request->getGet('ClientID');

        $filter = [
            'ClientID' => $client_id,
            'ExpenseDateFrom' => $this->request->getGet('ExpenseDateFrom'),
            'ExpenseDateTo' => $this->request->getGet('ExpenseDateTo'),
            'TaxAmount' => $this->request->getGet('TaxAmount'),
        ];

        $app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];

        $full_expense_data = $this->expense_model->fetchFullExpenseData($comp_id,$subscription_end_date,$filter);

        $headers = ['Expense Head','Vendor','Expense Date','Expense Amount','Taxes','Tax Amount','Total Expense Amount','Remarks'];

        $php_spreadsheets = new Php_spreadsheets();
        $php_spreadsheets->export_excel($headers, $full_expense_data);
    }
}