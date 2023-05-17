<?php

namespace Modules\Finance\Controllers;

class Pos_controller extends \CodeIgniter\Controller {
	public function __construct(){
	    $this->session = \Config\Services::session();    
        $this->userdata = $this->session->get('user_data');
        if(empty($this->userdata['ID'])){
            header('location:'.base_url().'login');
        }

        $this->finance_model = new \Modules\Finance\Models\Finance_model();
        $this->registered_user_model = new \Modules\Registered_users\Models\Registered_user_model();
        $this->company_model = new \Modules\Company\Models\Company_model();

        $this->form_validation = \Config\Services::validation();
	}

	public function pos(){
		$comp_id = $this->userdata['CompID'];

		$subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start creating POS.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $item_model = new \Modules\Inventory\Models\Item_model();

        $company_details = $this->company_model->fetchCompDetails($comp_id);


        if(!empty($_POST)){
        	if(empty($_POST['Particular'])){
        		$this->session->setFlashdata('flashmsg',['status' => true, 'msg' => 'Please select at least one particular']);
        	}else{

        		$this->form_validation->setRule('CompanyAddress', 'Company Address', 'required');
		        $this->form_validation->setRule('PaymentModeID', 'Payment Mode', 'required');
		        $this->form_validation->setRule('Particular.*', 'Particular', 'required');
		        $this->form_validation->setRule('PricePerUnit.*', 'Price Per Unit', 'required|regex_match[/^\d*[.]?\d+$/m]',['regex_match' => 'Price Per Unit should either be decimal or number.']);
		        $this->form_validation->setRule('Discount.*', 'Discount', 'permit_empty|regex_match[/^\d*[.]?\d+$/m]',['regex_match' => 'Discount should either be decimal or number.']);
		        $this->form_validation->setRule('HSN.*', 'HSN/SAC', 'required');
		        $this->form_validation->setRule('Tax.*', 'Tax', 'required');
		        $this->form_validation->setRule('TaxPercentage.*', 'Tax Rate', 'required');

				$particular_post_count = (!empty($_POST['Particular']))?count($_POST['Particular']):1;
				for($i=0;$i<$particular_post_count;$i++){
					$particular_tax_key = $_POST['ParticularTaxKey'][$i];
					$particular = $_POST['Particular'][$i];
					
					$this->form_validation->setRule('Quantity.'.$i, 'Quantity', 'required|regex_match[/^\d*[.]?\d+$/m]|checkQty['.$particular.']',['regex_match' => 'Paid Amount should either be decimal or number.']);
				}

				$duplicate_particulars = array_diff_key( $_POST['Particular'] , array_unique( $_POST['Particular'] ) );
        		if(!empty($duplicate_particulars)){
        			$duplicate_particular_keys = array_keys($duplicate_particulars);

        			for($i=0;$i<count($duplicate_particular_keys);$i++){
        				$this->form_validation->setRule('Particular.'.$duplicate_particular_keys[$i], 'Particular', 'permit_empty|valid_email',['valid_email' => 'This item has already been selected. Please update the qty if you need to.']);	
        			}
        		}
        	}

        	if ($this->form_validation->withRequest($this->request)->run())
	        {
	        	$stock_model = new \Modules\Inventory\Models\Stock_model();
	        	
	        	$invoice_data = [
	        		'InvoiceNo' => 'POS-'.date('Ymd').'-'.mt_rand(111,999),
	        		'CompID' => $comp_id,
	        		'CompanyName' => $company_details['CompName'],
	        		'FirmTypeID' => $company_details['FirmTypeID'],
	        		'CompanyContactNumber' => $company_details['ContactNo'],
	        		'CompanyAddress' => $this->request->getPost('CompanyAddress'),
	        		'CompanyServiceTaxTypeID' => ($this->request->getPost('CompanyServiceTaxTypeID'))?$this->request->getPost('CompanyServiceTaxTypeID'):null,
	        		'CompanyServiceTaxIdentificationNumber' => ($this->request->getPost('CompanyServiceTaxIdentificationNumber'))?$this->request->getPost('CompanyServiceTaxIdentificationNumber'):null,
	        		'ClientInvoiceDate' => date('Y-m-d H:i:s'),
	        		'ClientInvoiceDueDate' => date('Y-m-d H:i:s'),
	        		'CreatedBy' => $this->userdata['ID'],
	        		'CreatedDate' => date('Y-m-d H:i:s')
	        	];

	        	$invoice_id = $this->finance_model->saveInvoice($invoice_data);
	        	
	        	$total_payable_amount = 0;
	        	$paid_amount = 0;
	        	$particular_total_amount = 0;

	        	for($i=0;$i<$particular_post_count;$i++){
	        		$particular_tax_key = $_POST['ParticularTaxKey'][$i];

	        		$total_amount = $_POST['Quantity'][$i] * $_POST['PricePerUnit'][$i];

	        		if(!empty($_POST['Discount'][$i])){
	        			$discount_amt = ($total_amount * $_POST['Discount'][$i]) / 100;
	        			$total_amount = $total_amount - $discount_amt;
	        		}

	        		$particular_total_amount += $total_amount;

	        		$total_tax_percentage = array_sum($_POST['TaxPercentage'][$particular_tax_key]);
	        		
	        		$invoice_details_data = [
	        			'InvoiceID' => $invoice_id,
	        			'Particular' => $_POST['Particular'][$i],
	        			'ParticularType' => $_POST['ItemType'][$i],
	        			'ItemCategory' => ($_POST['ItemCategory'][$i])?$_POST['ItemCategory'][$i]:null,
	        			'SerialNo' => (!empty($_POST['SerialNo'][$i]))?$_POST['SerialNo'][$i]:null,
	        			'Quantity' => $_POST['Quantity'][$i],
	        			'PricePerUnit' => $_POST['PricePerUnit'][$i],
	        			'TotalTaxPercentage' => $total_tax_percentage,
	        			'TotalAmount' => round($total_amount + ($total_amount) * $total_tax_percentage / 100,2, PHP_ROUND_HALF_DOWN),
	        			'HSN' => $_POST['HSN'][$i],
	        			'BarcodeNo' => (!empty($_POST['BarcodeNo'][$i]))?$_POST['BarcodeNo'][$i]:null,
	        			'Discount' => $_POST['Discount'][$i],
	        		];

	        		$invoice_detail_id = $this->finance_model->saveInvoiceDetails($invoice_details_data);

	        		$total_payable_amount += $invoice_details_data['TotalAmount'];

	        		$tax_count = $_POST['Tax'][$particular_tax_key];
					for($j=0;$j<count($tax_count);$j++){
						$invoice_details_tax_data[] = [
			    			'InvoiceDetailID' => $invoice_detail_id,
			    			'Tax' => $_POST['Tax'][$particular_tax_key][$j],
			    			'TaxPercentage' => $_POST['TaxPercentage'][$particular_tax_key][$j],
			    		];
					}
		    		
		    		$this->finance_model->reduceItemQty($invoice_details_data['Particular'],$invoice_details_data['Quantity'],$comp_id);

		    		$stock_outward_report_data = [
		                'CompID' => $comp_id,
		                'Item' => $invoice_details_data['Particular'],
		                'HSN' => $invoice_details_data['HSN'],
		                'ReportDate' => date('Y-m-d')
		            ];

		            $stock_model->saveOutwardReports($stock_outward_report_data, $invoice_details_data['Quantity']);

		            $stock_model->updateStockInwardLogQty($comp_id,$stock_outward_report_data['Item'], $invoice_details_data['Quantity']);
	        	}
	    		
	    		$this->finance_model->saveInvoiceDetailsTaxData($invoice_details_tax_data);

	    		$total_payable_amount = round($total_payable_amount,PHP_ROUND_HALF_DOWN);

				if(!empty(array_filter($_POST['AdditionalChargeType']))){
	        		$additional_charge_count = count(array_filter($_POST['AdditionalChargeType']));

					$total_additional_charge = 0;

					for($j=0;$j<$additional_charge_count;$j++){
						$invoice_additional_charges_data[] = [
			    			'InvoiceID' => $invoice_id,
			    			'AdditionalChargeType' => $_POST['AdditionalChargeType'][$j],
			    			'AdditionalCharge' => $_POST['AdditionalCharge'][$j],
			    		];

			    		$total_additional_charge += (!empty($_POST['AdditionalCharge'][$j]))?$_POST['AdditionalCharge'][$j]:0;
					}

					$this->finance_model->saveInvoiceAdditionalChargesData($invoice_additional_charges_data);

					$total_payable_amount += $total_additional_charge;
	        	}

	        	$invoice_payable_data = [
	        		'TotalPayableAmount' => $total_payable_amount
	        	];

	        	$this->finance_model->saveInvoice($invoice_payable_data,$invoice_id);

	    		$receipt_data = [
	        		'ReceiptNo' => mt_rand(1111111111111111,9999999999999999),
	        		'InvoiceID' => $invoice_id,
	        		'PaidAmount' => $total_payable_amount,
	        		'ReceiptDate' => date('Y-m-d H:i:s'),
	        		'PaymentModeID' => $this->request->getPost('PaymentModeID'),
	        		'AddedBy' => $this->userdata['ID'],
	        		'AddedDate' => date('Y-m-d H:i:s'),
	        	];

	        	$receipt_id = $this->finance_model->saveReceipt($receipt_data);

	        	$this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'POS Saved Succesfully!']);
	        	$this->response->redirect(base_url('manage-invoice-details/'.$invoice_id));
	        }
		}

		$data = [
			'company_service_tax_master' => $this->finance_model->fetchCompanyServiceTaxMasterData($comp_id),
			'items' => $item_model->fetchAllGoods($comp_id),
			'payment_modes' => $this->finance_model->fetchPaymentModes(),
			'add_bel_global_js' => base_url('assets/js/finance.js')
		];
		return default_view('\Modules\Finance\Views\pos',$data);
	}
}