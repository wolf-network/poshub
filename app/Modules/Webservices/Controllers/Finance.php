<?php

namespace Modules\Webservices\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\respondTrait;

use TCPDF;
use App\Libraries\Php_mail;

class Finance extends ResourceController {
    function __construct()
	{
		$this->session = \Config\Services::session();
        $this->user_data = $this->session->get('user_data');
        if(empty($this->user_data)){
            return $this->respond([
                'status' => false,
                'msg' => 'Invalid User!',
                'data' => [],
             ],401);
        }

        $this->finance_model = new \Modules\Finance\Models\Finance_model();        
        $this->expense_model = new \Modules\Finance\Models\Expense_model();
        $this->purchase_order_model = new \Modules\Finance\Models\Purchase_order_model();

        $this->form_validation = \Config\Services::validation();
        $this->php_mail = new Php_mail();
	}
    
    public function get_ifsc(){
        $bank_id = $this->request->getGet('BankID');
        $offset = $this->request->getGet('offset');
        if(empty($bank_id)){
            return $this->respond([
                'status' => false,
                'msg' => 'Bank ID is required',
                'data' => []
            ], 501);
            return false;
        }else{
            $filters = [
                'search_txt' => $this->request->getGet('search_txt'),
                'BankDetailsID' => $this->request->getGet('BankDetailsID')
            ];
            $bank_ifsc_details = $this->finance_model->fetchIFSC($bank_id,50,$offset,$filters);
            return $this->respond([
                'status' => true,
                'msg' => 'Following are the IFSC Codes',
                'data' => $bank_ifsc_details
            ], 200);
        }
    }
    
    public function get_micr(){
        $bank_details_id = $this->request->getGet('BankDetailsID');
        if(empty($bank_details_id)){
            return $this->respond([
                'status' => false,
                'msg' => 'Bank Details ID is required',
                'data' => []
            ], 501);
            return false;
        }else{
            $bank_micr_details = $this->finance_model->fetchMICR($bank_details_id);
            return $this->respond([
                'status' => true,
                'msg' => 'Following is the MICR Code',
                'data' => $bank_micr_details
            ], 200);
        }
    }

    public function get_invoices(){

        $offset = $this->request->getGet('iDisplayStart');
        $client_id = 0;

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

            $client_id = $this->request->getGet('ClientID');

            $limit = $this->request->getGet('iDisplayLength');
            $filter = [
                'ClientID' => $client_id,
                'search_txt' => $this->request->getGet('sSearch'),
                'InvoiceDateFrom' => $this->request->getGet('InvoiceDateFrom'),
                'InvoiceDateTo' => $this->request->getGet('InvoiceDateTo'),
                'DueDateFrom' => $this->request->getGet('DueDateFrom'),
                'amountFilter' => $this->request->getGet('amountFilter'),
            ];
        }else{
            $limit = 10;
            $filter = [];
        }

        $sort_by = '';
        $sort_order = $this->request->getGet('sSortDir_0');

        switch ($this->request->getGet('iSortCol_0')) {
            case '1':
                $sort_by = 'I.InvoiceNo';
                break;
            case '2':
                $sort_by = 'C.ClientName';
                break;
            case '3':
                $sort_by = 'I.ClientContactNo';
                break;
            case '4':
                $sort_by = 'I.ClientInvoiceDate';
                break;
            case '5':
                $sort_by = 'I.ClientInvoiceDueDate';
                break;
            case '6':
                $sort_by = 'I.TotalPayableAmount';
                break;
            case '7':
                $sort_by = 'R.TotalPaidAmount';
                break;
            case '8':
                $sort_by = '(I.TotalPayableAmount) - SUM(CASE WHEN R.TotalPaidAmount is not null THEN R.TotalPaidAmount ELSE 0 END)';
                break;
            
            default:
                // code...
                break;
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];
        $comp_id = $this->user_data['CompID'];
        $total_records = $this->finance_model->fetchInvoiceList($comp_id,$subscription_end_date,0,0,$filter,true);



        $invoice_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->finance_model->fetchInvoiceList($comp_id,$subscription_end_date,$limit,$offset,$filter,0,$sort_by,$sort_order),
            'total_sales' => $this->finance_model->fetchTotalSales($comp_id,$filter,$client_id),
        ];
        
        return $this->respond($invoice_list, 200);
    }

    public function get_expenses(){

        if($this->user_data['Privilege'] != 'Admin'){
            return $this->respond([
                'status' => true,
                'msg' => 'Sorry, But you do not have authorization to access this data',
                'data' => []
            ], 403);

            return false;
        }

        $offset = $this->request->getGet('iDisplayStart');

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

            $limit = $this->request->getGet('iDisplayLength');
            $filter = [
                'VendorID' => $this->request->getGet('VendorID'),
                'search_txt' => $this->request->getGet('sSearch'),
                'ExpenseDateFrom' => $this->request->getGet('ExpenseDateFrom'),
                'ExpenseDateTo' => $this->request->getGet('ExpenseDateTo'),
                'TaxAmount' => $this->request->getGet('TaxAmount'),
            ];
        }else{
            $limit = 10;
            $filter = [];
        }

        $sort_by = '';
        $sort_order = $this->request->getGet('sSortDir_0');

        switch ($this->request->getGet('iSortCol_0')) {
            case '1':
                $sort_by = 'EHM.ExpenseHead';
                break;
            case '2':
                $sort_by = 'E.ExpenseDate';
                break;
            case '3':
                $sort_by = 'E.ExpenseAmount';
                break;
            case '4':
                $sort_by = 'E.Remarks';
                break;
            case '5':
                $sort_by = 'RU.Name';
                break;
            default:
                // code...
                break;
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];
        $total_records = $this->expense_model->fetchExpenseList($this->user_data['CompID'],$subscription_end_date,0,0,$filter,true);

        $expense_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->expense_model->fetchExpenseList($this->user_data['CompID'],$subscription_end_date,$limit,$offset,$filter,0,$sort_by,$sort_order)
        ];
        
        return $this->respond($expense_list, 200);
    }

    public function save_expense_heading(){
        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){

            return $this->respond([
                 'status' => true,
                 'msg' => 'Kindly Re-new your subscription to start adding expense headings.',
                 'data' => []
            ], 403);
        }else{
            $comp_id = $this->user_data['CompID'];

            $this->form_validation->setRule('ExpenseHead', 'Expense Head', 'required|duplicateExpenseHead');

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $expense_heading_data = [
                    'ExpenseHead' => $this->request->getPost('ExpenseHead'),
                    'CompID' => $comp_id,
                    'AddedBy' => $this->user_data['ID'],
                    'AddedDate' => date('Y-m-d H:i:s')
                ];

                $expense_head_master_id = $this->expense_model->saveExpenseHeading($expense_heading_data);

                return $this->respond([
                    'status' => true,
                    'msg' => 'Expense Heading Added Successfully!',
                    'data' => ['ExpenseHeadMasterID' => $expense_head_master_id]
                 ], 200);
            }else{
                return $this->respond([
                    'status' => false,
                    'msg' => 'Kindly fix the following validation errors.',
                    'data' => [],
                    'err' => $this->form_validation->error_array()
                ],501);
            }
        }
    }

    public function get_reports(){
        

        if (!empty($_GET['FromDate']) || !empty($_GET['ToDate']))
        {
            $comp_id = $this->user_data['CompID'];
            $filter = [
                'FromDate' => $this->request->getGet('FromDate'),
                'ToDate' => $this->request->getGet('ToDate'),
            ];

            $reports = $this->finance_model->fetchFinanceReports($comp_id,$filter);

            return $this->respond([
                'status' => true,
                'msg' => 'Following is the financial report!',
                'data' => $reports
             ], 200);
        }else{
            return $this->respond([
                'status' => false,
                'msg' => 'Kindly fix the following validation errors.',
                'data' => [],
                'error' => 'Please provide From date or to date.'
            ],501);
        }
    }

    public function get_purchase_orders(){
        $offset = $this->request->getGet('iDisplayStart');

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

            $limit = $this->request->getGet('iDisplayLength');
            $filter = [
                'search_txt' => $this->request->getGet('sSearch'),
                'PurchaseOrderStatusID' => $this->request->getGet('PurchaseOrderStatusID'),
                'DeliveryDateFrom' => $this->request->getGet('DeliveryDateFrom'),
                'DeliveryDateTo' => $this->request->getGet('DeliveryDateTo'),
            ];
        }else{
            $limit = 10;
            $filter = [];
        }

        $comp_id = $this->user_data['CompID'];

        $sort_by = '';
        $sort_order = $this->request->getGet('sSortDir_0');

        switch ($this->request->getGet('iSortCol_0')) {
            case '1':
                $sort_by = 'PO.PurchaseOrderNo';
                break;
            case '2':
                $sort_by = 'V.VendorName';
                break;
            case '3':
                $sort_by = 'PO.VendorContactNo';
                break;
            case '4':
                $sort_by = 'PO.DeliveryDate';
                break;
            case '5':
                $sort_by = 'PO.TotalAmount';
                break;
            case '6':
                $sort_by = 'POSM.PurchaseOrderStatus';
                break;
            default:
                // code...
                break;
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        $total_records = $this->purchase_order_model->fetchPurchaseOrderList($comp_id,$subscription_end_date,0,0,$filter,true);

        $purchase_order_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->purchase_order_model->fetchPurchaseOrderList($comp_id,$subscription_end_date,$limit,$offset,$filter,0,$sort_by,$sort_order)
        ];
        
        return $this->respond($purchase_order_list, 200);
    }

    public function update_purchase_order_status(){
        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){

            return $this->respond([
                 'status' => true,
                 'msg' => 'Kindly Re-new your subscription to start updating PO status.',
                 'data' => []
            ], 403);
        }else{
            $comp_id = $this->user_data['CompID'];
            $purchase_order_id = $this->request->getPost('PurchaseOrderID');
            $purchase_order_status_id = $this->request->getPost('PurchaseOrderStatusID');

            $selected_purchase_order_status = $this->purchase_order_model->fetchPurchaseOrderStatusViaID($purchase_order_status_id);

            if(empty($selected_purchase_order_status)){
                return $this->respond([
                     'status' => true,
                     'msg' => 'Invalid PO status selected.',
                     'data' => []
                ], 404);
                return false;
            }

            $this->form_validation->setRule('PurchaseOrderID', 'Purchase Order ID', 'required');
            $this->form_validation->setRule('PurchaseOrderStatusID', 'Purchase Order Status', 'required');

            if($selected_purchase_order_status['PurchaseOrderStatus'] == 'Canceled'){

                $this->form_validation->setRule('CancelationRemark', 'Cancelation Remark', 'required');                
            }

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $purchase_order_details = $this->purchase_order_model->fetchPurchaseOrderBasicData($purchase_order_id,$comp_id);

                if(empty($purchase_order_details)){
                    return $this->respond([
                         'status' => true,
                         'msg' => 'Either the PO does not exist or does not belong to you.',
                         'data' => []
                    ], 404);
                }else{
                    $purchase_order_data = [
                        'PurchaseOrderStatusID' => $this->request->getPost('PurchaseOrderStatusID'),
                        'CancelationRemark' => ($selected_purchase_order_status['PurchaseOrderStatus'] == 'Canceled')?$this->request->getPost('CancelationRemark'):null
                    ];

                    if($purchase_order_details['PurchaseOrderStatus'] == 'Released' && in_array($selected_purchase_order_status['PurchaseOrderStatus'],['New'])){
                        return $this->respond([
                             'status' => true,
                             'msg' => 'PO Has been released, You can either Receive or Cancel the PO.',
                             'error' => '',
                             'data' => $purchase_order_details['PurchaseOrderStatusID']
                        ], 403);
                        return false;
                    }

                    if(in_array($purchase_order_details['PurchaseOrderStatus'],['Received','Canceled'])){
                        return $this->respond([
                             'status' => true,
                             'msg' => 'PO Has been marked '.$purchase_order_details['PurchaseOrderStatus'].', No further changes are allowed.',
                             'error' => '',
                             'data' => $purchase_order_details['PurchaseOrderStatusID']
                        ], 403);
                        return false;
                    }

                    $this->purchase_order_model->savePurchaseOrder($purchase_order_data,$purchase_order_id);

                    return $this->respond([
                         'status' => true,
                         'msg' => 'PO Status Updated Successfully',
                         'data' => $purchase_order_details
                    ], 200);
                    

                }
            }else{
                return $this->respond([
                    'status' => false,
                    'msg' => 'Kindly fix the following validation errors.',
                    'data' => $purchase_order_status_id,
                    'error' => validation_errors()
                ],501);
            }

            
        }
    }

    public function get_sales_data(){
        $invoice_date = $this->request->getGet('ClientInvoiceDate');

        if(empty($invoice_date)){
            return $this->respond([
                'status' => false,
                'msg' => 'Invoice Date is required!',
                'error' => '',
             ],404);
        }

        $comp_id = $this->user_data['CompID'];
        $filter = [
            'InvoiceDateFrom' => $invoice_date
        ];

        return $this->respond([
            'status' => true,
            'msg' => 'Following is the sales data!',
            'data' => $this->finance_model->fetchTotalSales($comp_id,$filter),
         ],200);
    }

    public function mail_invoice(){
        $this->form_validation->setRule('InvoiceID', 'Invoice ID', 'required');
        $this->form_validation->setRule('InvoiceNo', 'Invoice No', 'required');
        $this->form_validation->setRule('Recipents', 'Recipents', 'required|valid_email');
        $this->form_validation->setRule('CC', 'CC', 'permit_empty|valid_email');
        $this->form_validation->setRule('BCC', 'BCC', 'permit_empty|valid_email');

        if ($this->form_validation->withRequest($this->request)->run())
        {
            $comp_id = $this->user_data['CompID'];
            $invoice_no = $this->request->getPost('InvoiceNo');
            $invoice_id = $this->request->getPost('InvoiceID');

            $check_invoice = $this->finance_model->checkInvoiceNo($invoice_no,$comp_id);
            if(empty($check_invoice)){
                return $this->respond([
                    'status' => false,
                    'msg' => 'Invalid Invoice No.',
                    'data' => [],
                    'error' => 'This Invoice No does not exist or does not belong to you.'
                ],403);
            }else{
                $company_model = new \Modules\Company\Models\Company_model();

                $invoice_data = $this->finance_model->fetchInvoiceData($invoice_id,$comp_id);
                $data = [
                    'invoice_data' => $invoice_data,
                    'invoice_details_data' => $this->finance_model->fetchInvoiceDetails($invoice_id,$comp_id),
                    'invoice_details_tax_data' => $this->finance_model->fetchInvoiceDetailsTaxData($invoice_id,$comp_id),
                    'invoice_additional_charges_data' => $this->finance_model->fetchInvoiceAdditionalChargesData($invoice_id),
                    'company_banking_details' => $company_model->fetchCompanyBankingDetails($comp_id)
                ];

                $html = view('\Modules\Finance\Views\download_invoice_details',$data);

                $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

                // remove default header/footer
                $pdf->setPrintHeader(false);
                $pdf->setPrintFooter(false);

                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                // set margins
                $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                // set font
                $pdf->SetFont('helvetica', '', 9);

                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                // set image scale factor
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                // ---------------------------------------------------------

                // add a page
                $pdf->AddPage();

                

                // print a block of text using Write()
                $pdf->writeHTML($html, true, false, true, false, '');

                // ---------------------------------------------------------

                //Close and output PDF document
                $attachment = $pdf->Output($invoice_data['InvoiceNo'].'.pdf', 'S');

                $content = $this->request->getPost('Content');

                 $mailer_data = [
                    'smtp_settings' => [
                        'host' => 'mail.wolfnetwork.in',
                        'smtp_auth' => true,
                        'username' => 'noreply@wolfnetwork.in',
                        'password' => 'WolfNetwork',
                        'mail_from' => 'noreply@wolfnetwork.in',
                        'smtp_secure' => true,
                        'port' => 465
                    ],
                    'recipents' => $this->request->getPost('Recipents'),
                    'cc' => $this->request->getPost('CC'),
                    'bcc' => $this->request->getPost('BCC'),
                    'subject' => $this->request->getPost('Subject'),
                    'content' => $content,
                    'string_attachment' => [
                        'attachment' => $attachment,
                        'filename' => $invoice_data['InvoiceNo'].'.pdf',
                        'format' => 'base64',
                        'file_type' => 'application/pdf'
                    ]
                ];

                if($this->php_mail->php_send_mail($mailer_data) != false){
                    return $this->respond([
                        'status' => false,
                        'msg' => 'Invoice Mailed Successfully!.',
                        'data' => []
                    ],200);
                }else{
                    return $this->respond([
                        'status' => false,
                        'msg' => 'Mail not sent',
                        'data' => [],
                        'error' => 'An error occured while sending the mail, Please try again later.'
                    ],500);
                }
            }
        }else{
            return $this->respond([
                'status' => false,
                'msg' => 'Kindly fix the following validation errors.',
                'data' => [],
                'err' => $this->form_validation->error_array()
            ],501);
        }
    }

    public function get_client_invoices(){
        $client_id = $this->request->getGet('ClientID');

        if(empty($client_id)){
            return $this->respond([
                'status' => false,
                'msg' => 'Client ID is required',
                'data' => []
            ], 501);
            return false;
        }else{
            $client_invoices = $this->finance_model->fetchInvoices($this->user_data['CompID'],$client_id);

            return $this->respond([
                'status' => true,
                'msg' => 'Following are the invoices of client',
                'data' => $client_invoices
            ], 200);
        }
    }

    public function get_invoice_data(){
        $invoice_id = $this->request->getGet('InvoiceID');

        if(empty($invoice_id)){
            return $this->respond([
                'status' => false,
                'msg' => 'Invoice ID is required',
                'data' => []
            ], 501);
            return false;
        }else{
            $comp_id = $this->user_data['CompID'];
            $invoice_data = $this->finance_model->fetchBasicInvoiceData($invoice_id,$comp_id);

            if(!empty($invoice_data)){
                
                $credit_note_model = new \Modules\Finance\Models\Credit_note_model();

                return $this->respond([
                    'status' => true,
                    'msg' => 'Following are the invoices of client',
                    'data' => [
                        'invoice_data' => $invoice_data,
                        'invoice_details' => $credit_note_model->fetchCreditNoteInvoiceDetails($invoice_id,$comp_id)
                    ]
                ], 200);
            }else{
                return $this->respond([
                    'status' => false,
                    'msg' => 'No Data found against this invoice No.',
                    'data' => []
                ], 404);
            }
        }
    }

    public function get_credit_notes(){
        $offset = $this->request->getGet('iDisplayStart');

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

            $limit = $this->request->getGet('iDisplayLength');
            $filter = [
                'search_txt' => $this->request->getGet('sSearch'),
                'CreditNoteDateFrom' => $this->request->getGet('CreditNoteDateFrom'),
                'CreditNoteDateTo' => $this->request->getGet('CreditNoteDateTo'),
            ];
        }else{
            $limit = 10;
            $filter = [];
        }

        $comp_id = $this->user_data['CompID'];

        $sort_by = '';
        $sort_order = $this->request->getGet('sSortDir_0');

        switch ($this->request->getGet('iSortCol_0')) {
            case '1':
                $sort_by = 'CN.CreditNoteNo';
                break;
            case '2':
                $sort_by = 'I.InvoiceNo';
                break;
            case '3':
                $sort_by = 'C.ClientName';
                break;
            case '4':
                $sort_by = 'CN.CreditNoteDate';
                break;
            case '5':
                $sort_by = 'CN.Reason';
                break;
            case '6':
                $sort_by = 'CN.PayableAmount';
                break;
            case '7':
                $sort_by = 'CN.AmountPaid';
                break;
            default:
                // code...
                break;
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        $credit_note_model = new \Modules\Finance\Models\Credit_note_model();

        $total_records = $credit_note_model->fetchCreditNotesList($comp_id,$subscription_end_date,0,0,$filter,true);

        $credit_notes_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $credit_note_model->fetchCreditNotesList($comp_id,$subscription_end_date,$limit,$offset,$filter,0,$sort_by,$sort_order)
        ];
        
        return $this->respond($credit_notes_list, 200);
    }

    public function get_debit_notes(){
        $offset = $this->request->getGet('iDisplayStart');

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

            $limit = $this->request->getGet('iDisplayLength');
            $filter = [
                'search_txt' => $this->request->getGet('sSearch'),
                'DebitNoteDateFrom' => $this->request->getGet('DebitNoteDateFrom'),
                'DebitNoteDateTo' => $this->request->getGet('DebitNoteDateTo'),
            ];
        }else{
            $limit = 10;
            $filter = [];
        }

        $comp_id = $this->user_data['CompID'];

        $sort_by = '';
        $sort_order = $this->request->getGet('sSortDir_0');

        switch ($this->request->getGet('iSortCol_0')) {
            case '1':
                $sort_by = 'DN.DebitNoteNo';
                break;
            case '2':
                $sort_by = 'DN.InvoiceNo';
                break;
            case '3':
                $sort_by = 'V.VendorName';
                break;
            case '4':
                $sort_by = 'DN.DebitNoteDate';
                break;
            case '6':
                $sort_by = 'DN.ReceivableAmount';
                break;
            case '7':
                $sort_by = 'DN.PaymentStatus';
                break;
            default:
                // code...
                break;
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        $debit_note_model = new \Modules\Finance\Models\Debit_note_model();

        $total_records = $debit_note_model->fetchDebitNotesList($comp_id,$subscription_end_date,0,0,$filter,true);

        $debit_notes_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $debit_note_model->fetchDebitNotesList($comp_id,$subscription_end_date,$limit,$offset,$filter,0,$sort_by,$sort_order)
        ];
        
        return $this->respond($debit_notes_list, 200);
    }
}