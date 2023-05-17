<?php

namespace Modules\Finance\Controllers;

use TCPDF;

class Purchase_order_controller extends \CodeIgniter\Controller {
	public function __construct(){
	    $this->session = \Config\Services::session();    
        $this->userdata = $this->session->get('user_data');
        if(empty($this->userdata['ID'])){
            header('location:'.base_url().'login');
        }

        $this->finance_model = new \Modules\Finance\Models\Finance_model();
        $this->vendor_model = new \Modules\Vendors\Models\Vendor_model();
        $this->company_model = new \Modules\Company\Models\Company_model();
        $this->purchase_order_model = new \Modules\Finance\Models\Purchase_order_model();

        $this->form_validation = \Config\Services::validation();
	}

	public function createPurchaseOrder(){
		$comp_id = $this->userdata['CompID'];
		$subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start creating purchase orders.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

		if(!empty($_POST)){
			$this->form_validation->setRule('CompanyAddress', 'Company Address', 'required');
			$this->form_validation->setRule('VendorID', 'Vendor', 'required');
			$this->form_validation->setRule('VendorContactNo', 'Vendor Contact No', 'required|numeric|min_length[8]|max_length[15]');
			$this->form_validation->setRule('DeliveryDate', 'Delivery Date', 'required');
			$this->form_validation->setRule('PurchaseOrderStatusID', 'Purchase Order Status', 'required');
			$this->form_validation->setRule('PaymentTerms', 'Payment Terms', 'required');

			if(!empty($_POST['PurchaseOrderNo'])){
				$this->form_validation->setRule('PurchaseOrderNo', 'PO No', 'duplicatePurchaseOrder');		
			}

			$this->form_validation->setRule('Particular.*', 'Particular', 'required');
			$this->form_validation->setRule('PricePerUnit.*', 'Price Per Unit', 'required|regex_match[/^\d*[.]?\d+$/m]',['regex_match' => 'Price Per Unit should either be decimal or number.']);
			$this->form_validation->setRule('HSN.*', 'HSN/SAC', 'required');

			
			$particular_post_count = (!empty($_POST['Particular']))?count($_POST['Particular']):1;
			for($i=0;$i<$particular_post_count;$i++){
				if(empty($_POST['ItemType'][$i])){
					echo "Item type is missing";
					exit;
				}

				if($_POST['ItemType'][$i] == 'Good'){
					$this->form_validation->setRule('Quantity.'.$i, 'Quantity', 'required|regex_match[/^\d*[.]?\d+$/m]',['regex_match' => 'Quantity should either be decimal or number.']);
				}
			}
			

			if ($this->form_validation->withRequest($this->request)->run())
	        {
	        	$company_details = $this->company_model->fetchCompDetails($comp_id);

	        	$purchase_order_data = [
	        		'PurchaseOrderNo' => ($this->request->getPost('PurchaseOrderNo'))?$this->request->getPost('PurchaseOrderNo'):date('Ymd').'-'.mt_rand(111,999),
	        		'CompID' => $comp_id,
	        		'CompanyName' => $company_details['CompName'],
	        		'FirmTypeID' => $company_details['FirmTypeID'],
	        		'CompanyContactNumber' => $company_details['ContactNo'],
	        		'CompanyAddress' => $this->request->getPost('CompanyAddress'),
	        		'CompanyServiceTaxTypeID' => ($this->request->getPost('CompanyServiceTaxTypeID'))?$this->request->getPost('CompanyServiceTaxTypeID'):null,
	        		'CompanyServiceTaxIdentificationNumber' => ($this->request->getPost('CompanyServiceTaxIdentificationNumber'))?$this->request->getPost('CompanyServiceTaxIdentificationNumber'):null,
	        		'VendorID' => $this->request->getPost('VendorID'),
	        		'ServiceTaxTypeID' => ($this->request->getPost('ServiceTaxTypeID'))?$this->request->getPost('ServiceTaxTypeID'):null,
	        		'VendorServiceTaxIdentificationNumber' => ($this->request->getPost('VendorServiceTaxIdentificationNumber'))?$this->request->getPost('VendorServiceTaxIdentificationNumber'):null,
	        		'VendorContactNo' => $this->request->getPost('VendorContactNo'),
	        		'VendorBillingAddress' => ($this->request->getPost('VendorBillingAddress'))?$this->request->getPost('VendorBillingAddress'):null,
	        		'DeliveryDate' => $this->request->getPost('DeliveryDate'),
	        		'ShippingAddress' => $this->request->getPost('ShippingAddress'),
	        		'ShippingTermsAndConditions' => $this->request->getPost('ShippingTermsAndConditions'),
	        		'PaymentTerms' => $this->request->getPost('PaymentTerms'),
	        		'PurchaseOrderStatusID' => $this->request->getPost('PurchaseOrderStatusID'),
	        		'AddedBy' => $this->userdata['ID'],
	        		'AddedDate' => date('Y-m-d H:i:s')
	        	];

	        	$total_amount = 0;
	        	$purchase_order_id = $this->purchase_order_model->savePurchaseOrder($purchase_order_data);

	        	for ($i=0; $i <count($_POST['Particular']) ; $i++) { 
	        		$purchase_order_details_data[] = [
	        			'PurchaseOrderID' => $purchase_order_id,
	        			'Particular' => $_POST['Particular'][$i],
	        			'HSN' => $_POST['HSN'][$i],
	        			'Quantity' => $_POST['Quantity'][$i],
	        			'PricePerUnit' => $_POST['PricePerUnit'][$i],
	        		];

	        		$total_amount += $_POST['PricePerUnit'][$i] * $_POST['Quantity'][$i];
	        	}

	        	$purchase_order_data['TotalAmount'] = $total_amount;
	        	$this->purchase_order_model->savePurchaseOrder($purchase_order_data,$purchase_order_id);
	        	$this->purchase_order_model->savePurchaseOrderDetailsBatch($purchase_order_details_data);

	        	$this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'PO Saved Succesfully!']);
	        	$this->response->redirect(base_url('manage-purchase-orders'));
	        }
		}

        $app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];
        $posted_vendor_id = $this->request->getPost('VendorID');

        $item_model = new \Modules\Inventory\Models\Item_model();

        $data = [
        	'vendors' => $this->vendor_model->fetchVendors($comp_id,$subscription_end_date,30,0,[],false,$posted_vendor_id),
        	'company_service_tax_master' => $this->finance_model->fetchCompanyServiceTaxMasterData($comp_id),
        	'purchase_order_status' => $this->purchase_order_model->fetchPurchaseOrderStatuses(),
        	'items' => $item_model->fetchAllItems($comp_id),
        	'company_addresses' => $this->company_model->fetchAllAddresses($comp_id),
        	'purchase_order_settings_details' => $this->purchase_order_model->fetchPOSettings($comp_id),
        	'add_bel_global_js' => base_url('assets/js/finance.js')
        ];

        return default_view('\Modules\Finance\Views\create_purchase_order',$data);
	}

    public function managePurchaseOrders(){
    	$data = [
    		'purchase_order_status' => $this->purchase_order_model->fetchPurchaseOrderStatuses()
    	];
    	return default_view('\Modules\Finance\Views\manage_purchase_orders',$data);
    }

    public function managePurchaseOrderDetails($purchase_order_id){
    	$comp_id = $this->userdata['CompID'];
    	$purchase_order_data = $this->purchase_order_model->fetchPurchaseOrderData($purchase_order_id,$comp_id);

    	if(empty($purchase_order_data)){
    		echo "Either the purchase order does not exist or does not belong to you.";
    		exit;
    	}

    	$data = [
    		'purchase_order_id' => $purchase_order_id,
    		'purchase_order_data' => $purchase_order_data,
    		'purchase_order_status' => $this->purchase_order_model->fetchPurchaseOrderStatuses(),
    		'purchase_order_details' => $this->purchase_order_model->fetchPurchaseOrderDetails($purchase_order_id),
    		'add_bel_global_js' => base_url('assets/js/finance.js')
    	];

    	return default_view('\Modules\Finance\Views\manage_purchase_order_details',$data);
    }

    public function editPurchaseOrderSettings(){
    	$comp_id = $this->userdata['CompID'];
    	$po_settings_details = $this->purchase_order_model->fetchPOSettings($comp_id);
    	
    	if(!empty($_POST)){
	    	$this->form_validation->setRule('PaymentTerms', 'Payment Terms', 'required');

	    	if ($this->form_validation->withRequest($this->request)->run())
	        {
	        	$purchase_order_setting_data = [
	        		'CompID' => $comp_id,
	        		'ShippingTermsAndConditions' => ($this->request->getPost('ShippingTermsAndConditions'))?$this->request->getPost('ShippingTermsAndConditions'):null,
	        		'PaymentTerms' => $this->request->getPost('PaymentTerms'),
	        		'UpdatedBy' => $this->userdata['ID'],
	        		'UpdatedDate' => date('Y-m-d H:i:s'),
	        	];

	        	$purchase_order_setting_id = (!empty($po_settings_details))?$po_settings_details['PurchaseOrderSettingID']:0;

	        	$this->purchase_order_model->savePurchaseOrderSettings($purchase_order_setting_data,$purchase_order_setting_id);

	        	$this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'PO Settings saved successfully!']);
	        	$this->response->redirect(base_url('edit-purchase-order-settings'));
	        }
    	}


        if(!empty($po_settings_details)){
	        foreach ($po_settings_details as $form_key => $form_value) {
	        	if(empty($_POST[$form_key])){
	        		$_POST[$form_key] = $form_value;
	        	}
	        }
        }

        $data = [
        	'add_bel_global_js' => base_url('assets/js/finance.js')
        ];
    	return default_view('\Modules\Finance\Views\edit_purchase_order_settings',$data);
    }

    public function downloadPurchaseOrder($purchase_order_id){
		$comp_id = $this->userdata['CompID'];
		$purchase_order_data = $this->purchase_order_model->fetchPurchaseOrderData($purchase_order_id,$comp_id);
		

		if(empty($purchase_order_data)){
			echo "Either the PO does not exist or does not belong to you.";
			exit;
		}

		$data = [
			'purchase_order_data' => $purchase_order_data,
			'purchase_order_details' => $this->purchase_order_model->fetchPurchaseOrderDetails($purchase_order_id)
		];

		$html = view('\Modules\Finance\Views\download_purchase_order_details',$data);

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
		$pdf->SetFont('helvetica', '', 10);

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
		$pdf->Output($purchase_order_data['PurchaseOrderNo'].'.pdf', 'D');
	}
}