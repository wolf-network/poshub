<?php

namespace Modules\Finance\Controllers;

use App\Libraries\Php_spreadsheets;
use TCPDF;

class Credit_note_controller extends \CodeIgniter\Controller {
	public function __construct(){
		$this->session = \Config\Services::session();
        $this->userdata = $this->session->get('user_data');
        if(empty($this->userdata['ID'])){
            header('location:'.base_url().'login');
        }

        $this->credit_note_model = new \Modules\Finance\Models\Credit_note_model();
        $this->client_model = new \Modules\Clients\Models\Client_model();

        $this->form_validation = \Config\Services::validation();
	}

	public function saveCreditNote(){
		$comp_id = $this->userdata['CompID'];
		$subscription_time_left = subscription_time_left();

        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start creating Credit Notes.']);
            return $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];

        if(!empty($_POST)){
        	$this->form_validation->setRule('ClientID', 'Client', 'required');
	        $this->form_validation->setRule('CreditNoteDate', 'Credit Note Date', 'required');
	        $this->form_validation->setRule('InvoiceID', 'Invoice No.', 'required|checkInvoiceID');
	        $this->form_validation->setRule('PaymentStatus', 'Payment Status', 'required|in_list[Paid,Unpaid]');
	        $this->form_validation->setRule('Particular.*', 'Particular', 'required');

	        $particular_post_count = (!empty($_POST['Particular']))?count($_POST['Particular']):1;

        	$invoice_id = $this->request->getPost('InvoiceID');
			for($i=0;$i<$particular_post_count;$i++){
				
				$particular = $_POST['Particular'][$i];

				if(empty($_POST['ParticularType'][$i]) || $_POST['ParticularType'][$i] == 'Good'){
					$valid_particular = $_POST['Particular'][$i];
					$extra_data = $i.'|'.$invoice_id.'|'.$valid_particular;
					$this->form_validation->setRule('Quantity.'.$i, 'Quantity', 'required|regex_match[/^\d*[.]?\d+$/m]|checkSoldQty['.$extra_data.']|checkReturnableQty['.$extra_data.']',['regex_match' => 'Quantity should either be decimal or number.']);

					$this->form_validation->setRule('PricePerUnit.'.$i, 'Price Per Unit', 'required|regex_match[/^\d*[.]?\d+$/m]|greater_than[0]|checkPricePerUnit['.$extra_data.']',['regex_match' => 'Price Per Unit should either be decimal or number.']);
				}
			}

			$duplicate_particulars = array_diff_key( array_filter($_POST['Particular']) , array_filter(array_unique( $_POST['Particular'] )) );

    		if(!empty($duplicate_particulars)){
    			$duplicate_particular_keys = array_keys($duplicate_particulars);

    			for($i=0;$i<count($duplicate_particular_keys);$i++){
    				$this->form_validation->setRule('Particular.'.$duplicate_particular_keys[$i], 'Particular['.$i.']', 'valid_email',['valid_email' => 'Particular['.$i.'] item has already been selected. Please update the qty if you need to.']);	
    			}
    		}

    		if ($this->form_validation->withRequest($this->request)->run())
	        {
	        	$credit_note_data = [
	        		'CompID' => $comp_id,
	        		'CreditNoteNo' => ($this->request->getPost('CreditNoteNo'))?$this->request->getPost('CreditNoteNo'):'CN-'.date('Ymd').'-'.mt_rand(111,999),
	        		'CreditNoteDate' => $this->request->getPost('CreditNoteDate'),
	        		'InvoiceID' => $invoice_id,
	        		'Reason' => ($this->request->getPost('Reason'))?$this->request->getPost('Reason'):null,
	        		'PaymentStatus' => ($this->request->getPost('PaymentStatus'))?$this->request->getPost('PaymentStatus'):'Unpaid',
	        		'AddedBy' => $this->userdata['ID'],
	        		'AddedDate' => date('Y-m-d H:i:s'),
	        	];

	        	$credit_note_id = $this->credit_note_model->saveCreditNote($credit_note_data);

	        	$total_payable_amount = 0;

	        	for($i=0;$i<count($_POST['Particular']);$i++){
	        		$qty = (!empty($_POST['Quantity'][$i]))?$_POST['Quantity'][$i]:null;

	        		$fetched_invoice_particular_amt = $this->credit_note_model->fetchInvoiceParticularAmount($invoice_id,$_POST['Particular'][$i]);

	        		$particular_pre_tax_amount = $_POST['PricePerUnit'][$i];

	        		$particular_taxable_amount = $particular_pre_tax_amount * $fetched_invoice_particular_amt['TotalTaxPercentage'] / 100;


	        		$particular_discount = $particular_pre_tax_amount * $fetched_invoice_particular_amt['Discount'] / 100;

	        		$particular_amount = ($particular_pre_tax_amount - $particular_discount) + $particular_taxable_amount;

	        		if(!empty($qty)){
	        			$total_payable_amount += $particular_amount * $qty;
	        		}else{
	        			$total_payable_amount += $particular_amount;
	        		}

	        		$credit_note_details = [
	        			'CreditNoteID' => $credit_note_id,
	        			'Particular' => $_POST['Particular'][$i],
	        			'HSN' => $fetched_invoice_particular_amt['HSN'],
	        			'Qty' => $qty,
	        			'PricePerUnit' => $particular_pre_tax_amount
	        		];

	        		$credit_note_detail_id = $this->credit_note_model->saveCreditNoteDetails($credit_note_details);

	        		if(!empty($fetched_invoice_particular_amt['taxes'])){
	        			$particular_taxes_arr = explode(',', $fetched_invoice_particular_amt['taxes']);

	        			for($j=0;$j<count($particular_taxes_arr);$j++){
	        				$particular_tax_split = explode('|', $particular_taxes_arr[$j]);
	        				$credit_note_details_tax_data[] = [
			        			'CreditNoteDetailID' => $credit_note_detail_id,
			        			'Tax' => (!empty($particular_tax_split[0]))?$particular_tax_split[0]:'',
			        			'TaxPercentage' => (!empty($particular_tax_split[1]))?$particular_tax_split[1]:''
			        		];
	        			}
	        		}

	        		if($fetched_invoice_particular_amt['ParticularType'] == 'Good'){
	        			$item_model = new \Modules\Inventory\Models\Item_model();

	        			$fetched_item_category_details = $item_model->fetchItemCategoryIDViaItemCategory($fetched_invoice_particular_amt['ItemCategory'],$comp_id);
	        			if(!empty($fetched_item_category_details['ItemCategoryMasterID'])){
	        				$item_category_master_id = $fetched_item_category_details['ItemCategoryMasterID'];
	        			}else{
	        				if(!empty($fetched_invoice_particular_amt['ItemCategory'])){
	        					$item_category_data = [
		        					'ItemCategory' => $fetched_invoice_particular_amt['ItemCategory'],
		        					'CompID' => $comp_id,
		        					'AddedBy' => $this->userdata['ID'],
		        					'AddedDate' => date('Y-m-d H:i:s')
		        				];

		        				$item_category_master_id = $item_model->saveItemCategory($item_category_data, $comp_id);
	        				}
	        			}

		        		$item_data = [
		        			'CompID' => $comp_id,
		        			'Item' => $_POST['Particular'][$i],
		        			'ItemCategoryMasterID' => $item_category_master_id,
		        			'ItemType' => $fetched_invoice_particular_amt['ParticularType'],
		        			'BarcodeNo' => $fetched_invoice_particular_amt['BarcodeNo'],
		        			'Price' => $particular_pre_tax_amount,
		        			'HSN' => $fetched_invoice_particular_amt['HSN'],
		        			'Qty' => $qty,
		        			'AddedBy' => $this->userdata['ID'],
		        			'AddedDate' => date('Y-m-d H:i:s'),
		        		];

		        		$this->credit_note_model->updateInventory($item_data);
	        		}
	        	}

	        	$credit_note_payable_data = [
	        		'PayableAmount' => round($total_payable_amount,2,PHP_ROUND_HALF_DOWN)
	        	];

	        	$this->credit_note_model->saveCreditNote($credit_note_payable_data,$credit_note_id);

	        	$this->credit_note_model->saveCreditNoteDetailsTaxDataBatch($credit_note_details_tax_data);

	        	$this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Credit Note Saved Succesfully!']);
	        	$this->response->redirect(base_url('manage-credit-notes'));
	        }
        }

		$data = [
			'clients' => $this->client_model->fetchClients($comp_id,$subscription_end_date,30),
			'add_bel_global_js' => base_url('assets/js/credit_note.js')
		];
		return default_view('\Modules\Finance\Views\save_credit_note',$data);
	}

    public function manageCreditNotes(){
    	$data = [
    		'client_id' => $this->request->getGet('ClientID'),
    		'add_bel_global_js' => base_url('assets/js/credit_note.js')
    	];

    	return default_view('\Modules\Finance\Views\manage_credit_notes',$data);
    }

    public function deleteCreditNote($credit_note_id){
    	$comp_id = $this->userdata['CompID'];
    	$credit_note_basic_data = $this->credit_note_model->fetchCreditNoteBasicDetails($credit_note_id,$comp_id);

    	if(empty($credit_note_basic_data)){
    		$this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'either the credit note does not exist or does not belong to you.']);
    	}

    	if(!empty($credit_note_basic_data) && $credit_note_basic_data['PaymentStatus'] == 'Paid'){
    		$this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Sorry, This credit note cannot be delete because payment against this credit note has been made.']);
    	}

    	if(!empty($credit_note_basic_data) && $credit_note_basic_data['PaymentStatus'] == 'Unpaid'){
    		$this->credit_note_model->deleteCreditNote($credit_note_id,$comp_id);
    		$this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Credit note deleted successfully!']);
    	}

    	$this->response->redirect(base_url('manage-credit-notes'));
    }

    public function exportCreditNotes(){

    	$subscription_time_left = subscription_time_left();
		if($subscription_time_left['years'] <= 0 && $subscription_time_left['months'] <= 0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start exporting credit notes.']);
            $this->response->redirect(base_url('plan-renewal'));
        }

    	$comp_id = $this->userdata['CompID'];
    	$client_id = $this->request->getGet('ClientID');

    	$filter = [
    		'ClientID' => $client_id,
        	'CreditNoteDateFrom' => $this->request->getGet('CreditNoteDateFrom'),
        	'CreditNoteDateTo' => $this->request->getGet('CreditNoteDateTo')
    	];

    	$headers = ['Client','Client Service Tax Identifier','Credit Note No','Credit Note Date','Original Invoice No','Particular','HSN/SAC','Qty','Price Per Unit','Taxable Amount','Taxes','Tax Amount','Total Amount','Payment Status','Reason'];

    	$app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];

    	$full_credit_notes_data = $this->credit_note_model->fetchFullCreditNoteData($comp_id,$subscription_end_date,$filter);

    	$php_spreadsheets = new Php_spreadsheets();
        $php_spreadsheets->export_excel($headers, $full_credit_notes_data);
    }

    public function manageCreditNoteDetails($credit_note_id){

    	$app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];
        $comp_id = $this->userdata['CompID'];

        $credit_note_data = $this->credit_note_model->fetchCreditNoteData($credit_note_id,$comp_id,$subscription_end_date);

        if(empty($credit_note_data)){
        	echo "Either the credit note does not exist, does not belong to you or your subscription might have ended.";
        	exit;
        }

    	$data = [
    		'credit_note_id' => $credit_note_id,
    		'credit_note_data' => $credit_note_data,
    		'credit_note_details' => $this->credit_note_model->fetchCreditNoteDetails($credit_note_id,$comp_id)
    	];
    	return default_view('\Modules\Finance\Views\manage_credit_note_details',$data);
    }

    public function markCreditNotePaid($credit_note_id){
    	$credit_note_details = $this->credit_note_model->fetchCreditNoteDetails($credit_note_id);
    	if(empty($credit_note_details)){
			echo "Either this credit note does not exist or does not belong to you.";
			exit;
		}else{
			$credit_note_data = [
				'PaymentStatus' => 'Paid'
			];

			$this->credit_note_model->saveCreditNote($credit_note_data,$credit_note_id);
			$this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Marked credit note as paid']);
			$this->response->redirect(base_url('manage-credit-note-details/'.$credit_note_id));
		}
    }

    public function downloadCreditNote($credit_note_id){
    	$app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];
        $comp_id = $this->userdata['CompID'];

        $credit_note_data = $this->credit_note_model->fetchCreditNoteData($credit_note_id,$comp_id,$subscription_end_date);

        if(empty($credit_note_data)){
        	echo "Either the credit note does not exist, does not belong to you or your subscription might have ended.";
        	exit;
        }

        $data = [
        	'credit_note_data' => $credit_note_data,
        	'credit_note_details' => $this->credit_note_model->fetchCreditNoteDetails($credit_note_id,$comp_id)
        ];

    	$html = view('\Modules\Finance\Views\download_credit_note',$data);

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
		$pdf->Output($credit_note_data['CreditNoteNo'].'.pdf', 'D');
    }
}