<?php

namespace Modules\Finance\Controllers;

use \App\Libraries\Php_spreadsheets;

use TCPDF;

class Finance_controller extends \CodeIgniter\Controller {
	public function __construct(){
		$this->session = \Config\Services::session();	
        $this->userdata = $this->session->get('user_data');
        if(empty($this->userdata['ID'])){
            header('location:'.base_url().'login');
        }

        $this->finance_model = new \Modules\Finance\Models\Finance_model();
        $this->registered_user_model = new \Modules\Registered_users\Models\Registered_user_model();
        $this->company_model = new \Modules\Company\Models\Company_model();
        $this->client_model = new \Modules\Clients\Models\Client_model();
        $this->stock_model = new \Modules\Inventory\Models\Stock_model();

        $this->form_validation = \Config\Services::validation();
	}

	public function saveInvoice(){
		$comp_id = $this->userdata['CompID'];
		$subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start creating Invoices.']);
            return $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        if(!empty($_POST)){
        	$this->form_validation->setRule('ClientID', 'Client', 'required');
			$this->form_validation->setRule('CompanyAddress', 'Company Address', 'required');
			$this->form_validation->setRule('ClientContactNo', 'Contact No', 'numeric|min_length[8]|max_length[15]');
			$this->form_validation->setRule('ClientInvoiceDate', 'Invoice Date', 'required');
			$this->form_validation->setRule('ClientInvoiceDueDate', 'Due Date', 'required');
			$this->form_validation->setRule('ClientBillingAddress', 'Billing Address', 'required');

			if(!empty($_POST['InvoiceNo'])){
				$this->form_validation->setRule('InvoiceNo', 'Invoice No', 'permit_empty|duplicateInvoice');		
			}

			$this->form_validation->setRule('Particular.*', 'Particular', 'required');
			$this->form_validation->setRule('PricePerUnit.*', 'Price Per Unit', 'required|regex_match[/^\d*[.]?\d+$/m]',['regex_match' => 'Paid Amount should either be decimal or number.']);
			$this->form_validation->setRule('HSN.*', 'HSN/SAC', 'required');
			$this->form_validation->setRule('Discount.*', 'Discount', 'permit_empty|regex_match[/^\d*[.]?\d+$/m]',['regex_match' => 'Discount should either be decimal or number.']);
			$this->form_validation->setRule('Tax.*', 'Tax', 'required');
			$this->form_validation->setRule('TaxPercentage.*', 'Tax Rate', 'required');

			if(!empty($_POST)){
				$particular_post_count = (!empty($_POST['Particular']))?count($_POST['Particular']):1;
				for($i=0;$i<$particular_post_count;$i++){
					$particular = $_POST['Particular'][$i];
					$particular_tax_key = $_POST['ParticularTaxKey'][$i];

					

					if($_POST['ItemType'][$i] == 'Good'){
						$this->form_validation->setRule('Quantity.'.$i, 'Quantity', 'required|regex_match[/^\d*[.]?\d+$/m]|checkQty['.$particular.']',['regex_match' => 'Quantity should either be decimal or number.']);
					}
				}

				if(!empty(array_filter($_POST['Particular']))){
					$duplicate_particulars = array_diff_key( $_POST['Particular'] , array_unique( $_POST['Particular'] ) );
		    		if(!empty($duplicate_particulars)){
		    			$duplicate_particular_keys = array_keys($duplicate_particulars);

		    			for($i=0;$i<count($duplicate_particular_keys);$i++){
		    				$this->form_validation->setRule('Particular.'.$duplicate_particular_keys[$i], 'Particular', 'valid_email',['valid_email' => 'This item has already been selected. Please update the qty if you need to.']);	
		    			}
		    		}
				}

			}

			if(!empty($_POST['DeductibleType']) && !empty(array_filter($_POST['DeductibleType']))){
				for($i=0;$i<count(array_filter($_POST['DeductibleType']));$i++){
					$this->form_validation->setRule('DeductiblePercentage.'.$i, 'Deductible Percentage', 'required');
				}
			}

			if(!empty($_POST['AdditionalChargeType']) && !empty(array_filter($_POST['AdditionalChargeType']))){
				$additional_charge_count = count(array_filter($_POST['AdditionalChargeType']));

				for($j=0;$j<$additional_charge_count;$j++){
					$this->form_validation->setRule('AdditionalChargeType.'.$j, 'Charge Type', 'required');
					$this->form_validation->setRule('AdditionalCharge.'.$j, 'Rate', 'required|regex_match[/^\d*[.]?\d+$/m]',['regex_match' => 'Rate should either be decimal or number.']);
				}
			}



			$company_details = $this->company_model->fetchCompDetails($comp_id);
			
			if ($this->form_validation->withRequest($this->request)->run())
	        {
	        	$invoice_settings_details = $this->finance_model->fetchInvoiceSettings($comp_id);

	        	$invoice_data = [
	        		'InvoiceNo' => ($this->request->getPost('InvoiceNo'))?$this->request->getPost('InvoiceNo'):date('Ymd').'-'.mt_rand(111,999),
	        		'CompID' => $comp_id,
	        		'CompanyName' => $company_details['CompName'],
	        		'FirmTypeID' => $company_details['FirmTypeID'],
	        		'CompanyContactNumber' => $company_details['ContactNo'],
	        		'CompanyAddress' => $this->request->getPost('CompanyAddress'),
	        		'CompanyServiceTaxTypeID' => ($this->request->getPost('CompanyServiceTaxTypeID'))?$this->request->getPost('CompanyServiceTaxTypeID'):null,
	        		'CompanyServiceTaxIdentificationNumber' => ($this->request->getPost('CompanyServiceTaxIdentificationNumber'))?$this->request->getPost('CompanyServiceTaxIdentificationNumber'):null,
	        		'ClientID' => ($this->request->getPost('ClientID'))?$this->request->getPost('ClientID'):null,
	        		'ServiceTaxTypeID' => ($this->request->getPost('ServiceTaxTypeID'))?$this->request->getPost('ServiceTaxTypeID'):null,
	        		'ClientServiceTaxIdentificationNumber' => ($this->request->getPost('ClientServiceTaxIdentificationNumber'))?$this->request->getPost('ClientServiceTaxIdentificationNumber'):null,
	        		'ClientContactNo' => $this->request->getPost('ClientContactNo'),
	        		'ClientInvoiceDate' => $this->request->getPost('ClientInvoiceDate'),
	        		'ClientInvoiceDueDate' => $this->request->getPost('ClientInvoiceDueDate'),
	        		'ClientBillingAddress' => $this->request->getPost('ClientBillingAddress'),
	        		'ClientShippingAddress' => $this->request->getPost('ClientShippingAddress'),
	        		'CustomerNotes' => $this->request->getPost('CustomerNotes'),
	        		'TermsAndConditions' => ($this->request->getPost('TermsAndConditions'))?$this->request->getPost('TermsAndConditions'):null,
	        		'CreatedBy' => $this->userdata['ID'],
	        		'CreatedDate' => date('Y-m-d H:i:s')
	        	];

	        	$invoice_id = $this->finance_model->saveInvoice($invoice_data);
	        	
	        	$total_payable_amount = 0;
	        	$paid_amount = 0;
	        	$particular_total_amount = 0;
	        	
	        	for($i=0;$i<$particular_post_count;$i++){
	        		$particular_tax_key = $_POST['ParticularTaxKey'][$i];

	        		$total_amount = (!empty($_POST['Quantity'][$i])  && $_POST['ItemType'][$i] == 'Good')?$_POST['Quantity'][$i] * $_POST['PricePerUnit'][$i]:$_POST['PricePerUnit'][$i];

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
	        			'Quantity' => (!empty($_POST['Quantity'][$i]) && $_POST['ItemType'][$i] == 'Good')?$_POST['Quantity'][$i]:null,
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

					if($invoice_details_data['ParticularType'] == 'Good'){
						$this->finance_model->reduceItemQty($invoice_details_data['Particular'],$invoice_details_data['Quantity'],$comp_id);

						$stock_outward_report_data = [
			                'CompID' => $comp_id,
			                'Item' => $invoice_details_data['Particular'],
			                'HSN' => $invoice_details_data['HSN'],
			                'ReportDate' => date('Y-m-d')
			            ];

			            $this->stock_model->saveOutwardReports($stock_outward_report_data, $invoice_details_data['Quantity']);
					}

	        	}

	    		$this->finance_model->saveInvoiceDetailsTaxData($invoice_details_tax_data);

	        	$total_deductible_percentage = 0;

	        	if(!empty(array_filter($_POST['DeductibleType']))){
					for($i=0;$i<count(array_filter($_POST['DeductibleType']));$i++){
						$total_deductible_percentage += $_POST['DeductiblePercentage'][$i];

						$invoice_deductibles_data[] = [
							'InvoiceID' => $invoice_id,
							'DeductibleType' => $_POST['DeductibleType'][$i],
							'DeductiblePercentage' => $_POST['DeductiblePercentage'][$i],
						];
					}

					$this->finance_model->saveInvoiceDeductiblesData($invoice_deductibles_data);
				}

				$total_payable_amount = round($total_payable_amount - ($particular_total_amount*$total_deductible_percentage/100),PHP_ROUND_HALF_DOWN);

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

	        	$this->session->setFlashdata('flashmsg',['status' => true, 'msg' => 'Invoice Created Successfully!']);
	        	// return $this->response->redirect(base_url('create-receipt/'.$invoice_id));
	        	return $this->response->redirect(base_url('manage-invoice-details/'.$invoice_id));
	        }
        }

        $app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];

        $item_model = new \Modules\Inventory\Models\Item_model();

		$data = [
			'clients' => $this->client_model->fetchClients($comp_id,$subscription_end_date,30),
			'invoice_settings_details' => $this->finance_model->fetchInvoiceSettings($comp_id),
			'company_service_tax_master' => $this->finance_model->fetchCompanyServiceTaxMasterData($comp_id),
			'items' => $item_model->fetchAllItems($comp_id),
			'add_bel_global_js' => base_url('assets/js/finance.js')
		];
		
		return default_view('\Modules\Finance\Views\save_invoice',$data);
	}

	public function manageInvoices(){
		$comp_id = $this->userdata['CompID'];
		$client_id = $this->request->getGet('client_id');
		$data = [
			'client_id' => $client_id,
			'add_bel_global_js' => base_url('assets/js/finance.js')
		];

		return default_view('\Modules\Finance\Views\manage_invoices',$data);
	}

	public function manageInvoiceDetails($invoice_id){
		$comp_id = $this->userdata['CompID'];
		$invoice_data = $this->finance_model->fetchInvoiceData($invoice_id,$comp_id);

		if(empty($invoice_data)){
			echo "Either the invoice does not exist or does not belong to you.";
			exit;
		}

		$data = [
			'invoice_id' => $invoice_id,
			'invoice_data' => $invoice_data,
			'invoice_details_data' => $this->finance_model->fetchInvoiceDetails($invoice_id,$comp_id),
			'invoice_details_tax_data' => $this->finance_model->fetchInvoiceDetailsTaxData($invoice_id,$comp_id),
			'deductibles_data' => $this->finance_model->fetchDeductiblesDetails($invoice_id),
			'invoice_additional_charges_data' => $this->finance_model->fetchInvoiceAdditionalChargesData($invoice_id),
			'company_banking_details' => $this->company_model->fetchCompanyBankingDetails($comp_id),
			'add_bel_global_js' => ['https://html2canvas.hertzen.com/dist/html2canvas.min.js',base_url('assets/js/finance.js')]
		];

		return default_view('\Modules\Finance\Views\manage_invoice_details',$data);
	}

	public function viewPricingPlans($app_id){
		$data = [
			'subscription_plans' => $this->finance_model->fetchSubscriptionPlans($app_id),
			'add_bel_global_js' => base_url('assets/js/finance.js')
		];

		return default_view('\Modules\Finance\Views\view_pricing_plans',$data);
	}

	public function userAppSubscription($registered_user_id){

		if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

		$subscription_time_left = subscription_time_left();
        if($registered_user_id != $this->userdata['ID'] && $subscription_time_left['years'] <= 0 && $subscription_time_left['months'] <= 0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start Renewing plans for other users.']);
            return $this->response->redirect(base_url('plan-renewal'));
        }

		$plan = $this->request->getGet('plan');
		$app_id = env('app_id');
		$app = env('app');
		$plan_details = $this->finance_model->fetchPlanDetailsViaPlanID($plan, $app_id);

		if(empty($plan_details)){
			echo "Please go back and select a Valid Plan.";	
			exit;
		}else{
			$employee_existing_subscription_details = $this->finance_model->fetchEmployeeExistingSubscriptionDetails($registered_user_id, $this->userdata['CompID'],$app_id);

			if(empty($employee_existing_subscription_details)){
				echo "Either this user does not exist or does not belong to you.";
				exit;
			}else{
				$payment_gateways = new \App\Libraries\Payment_gateways();
				$tax_amount = $plan_details['TotalAmount'] * $plan_details['TaxPercentage'] / 100;

                $plan_amount = $plan_details['TotalAmount'] + $tax_amount;

				$data = [
					'receipt' => rand(111,999),
					'amount' => $plan_amount,
					'notes' => [
						'plan_name' => $plan_details['PlanName']
					],
					'payee_name' => $this->userdata['Name'],
					'payee_email' => $this->userdata['EmailID'],
					'payee_contact' => $this->userdata['Mobile'],
					'callback_url' => base_url('user-subscription-response/'.$registered_user_id.'?plan='.$plan)
				];

				$payment_gateways->razorPay($data);
			}
		}
	}

	public function userAppSubscriptionResponse($registered_user_id){
		$order_id = $this->request->getGet('order_id');
		$plan = $this->request->getGet('plan');
		if(empty($order_id)){
			echo "Please provide order ID";
			exit;
		}

		if(empty($plan)){
			echo "Please provide plan ID";
			exit;
		}

		$comp_id = $this->userdata['CompID'];
		$payment_gateway = 'RazorPay';

		$order_details = $this->finance_model->fetchTransactionDataViaOrderID($order_id, $payment_gateway);

		if(!empty($order_details) && $order_details['Status'] == 'paid'){
			$order_fulfilled_date = date('d M Y - H:i',strtotime($order_details['PaymentReceivedDate']));
			$this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'This order was fulfilled on '.$order_fulfilled_date]);
			return $this->response->redirect(base_url('manage-users'));
		}

		$payment_gateways = new \App\Libraries\Payment_gateways();

		$order_details = $payment_gateways->fetchRazorpayOrderDetails($order_id);

		if(empty($order_details)){
			echo "An error occured with the payment gateway. Kindly contact our support team for clarifications.<br> Email: support@wolfnetwork.in <br> Mobile: 9137166653 <br> <a href='".base_url()."'>Go to dashboard</a>";
			exit;
		}

		$app_id = env('app_id');
		$plan_details = $this->finance_model->fetchPlanDetailsViaPlanID($plan, $app_id);
		$user_details = $this->registered_user_model->fetchUserDetails($registered_user_id, $comp_id);

		$transaction_data = [
			'RegisteredUserID' => $registered_user_id,
			'Name' => $user_details['Name'],
			'EmailID' => $user_details['EmailID'],
			'ContactNo' => (!empty($user_details['Mobile']))?$user_details['Mobile']:null,
			'CompName' => $user_details['CompName'].' '.$user_details['FirmType'],
			'ServiceTaxTypeID' => (!empty($user_details['ServiceTaxTypeID']))?$user_details['ServiceTaxTypeID']:null,
			'TaxIdentificationNumber' => (!empty($user_details['TaxIdentificationNumber']))?$user_details['TaxIdentificationNumber']:null,
			'ServiceTaxIdentificationNumber' => (!empty($user_details['ServiceTaxIdentificationNumber']))?$user_details['ServiceTaxIdentificationNumber']:null,
			'AppID' => $app_id,
			'SubscriptionPlanID' => $plan,
			'InvoiceNo' => date('Ymd').'-'.mt_rand(111,999),
			'ReceiptNo' => mt_rand(1111111111111111,9999999999999999),
			'PaymentGateway' => $payment_gateway,
			'OrderID' => $order_details->id,
			'PlanAmount' => $plan_details['TotalAmount'],
			'TaxPercentage' => $plan_details['TaxPercentage'],
			'AmountPaid' => $order_details->amount_paid,
			'Currency' => $order_details->currency,
			'Status' => $order_details->status,
			'PaymentMadeBy' => $this->userdata['ID'],
			'PaymentReceivedDate' => date('Y-m-d H:i:s')
		];

		if(!empty($user_details['ReferredBy'])){
			$referrer_details = $this->registered_user_model->fetchUserBasicDetails($user_details['ReferredBy']);

			if(!empty($referrer_details['CommissionPercentage'])){
				$transaction_data['ReferrerCommissionPercentage'] = $referrer_details['CommissionPercentage'];
			}
		}

		$user_receipt_data = $transaction_data;
		$user_receipt_data['ServiceTaxType'] = $user_details['ServiceTaxType'];

		$this->finance_model->saveTransactionData($transaction_data);

		if($order_details['status'] != 'paid'){
			$this->session->setFlashdata('flashmsg',['status' => false, 'msg' => 'Payment failed, Please try again.']);
			return $this->response->redirect(base_url('login'));
			exit;
		}

		

		
		$app = env('app');
		$plan_details = $this->finance_model->fetchPlanDetailsViaPlanID($plan, $app_id);

		$employee_existing_subscription_details = $this->finance_model->fetchEmployeeExistingSubscriptionDetails($registered_user_id, $this->userdata['CompID'],$app_id);

		$current_subscription_end_date = strtotime($employee_existing_subscription_details['SubscriptionEndDate']);
		$current_datetime = strtotime(date('Y-m-d H:i:s'));

		$current_date_subscription_date_interval = ($current_subscription_end_date - $current_datetime) / 86400;

		if($current_date_subscription_date_interval > 0){
			$subscription_start_date = $employee_existing_subscription_details['SubscriptionEndDate'];

			$subscription_end_date = date('Y-m-d H:i:s', strtotime($employee_existing_subscription_details['SubscriptionEndDate'].' +'.$plan_details['Duration'].' '.$plan_details['DurationType']));

			// echo $subscription_end_date;
			// exit;
		}else{
			$subscription_start_date = date('Y-m-d H:i:s');
			$subscription_end_date = date('Y-m-d H:i:s', strtotime('+'.$plan_details['Duration'].' '.$plan_details['DurationType']));
		}

		$registered_user_app_mapper_data = [
			'AppID' => $app_id,
			'RegisteredUserID' => $registered_user_id,
			'SubscriptionPlanID' => $plan_details['SubscriptionPlanID'],
			'SubscribedDate' => date('Y-m-d H:i:s'),
			'SubscriptionStartDate' => $subscription_start_date,
			'SubscriptionEndDate' => $subscription_end_date
		];

		$this->registered_user_model->saveUserApps($registered_user_app_mapper_data, true);

		$user_subscription_log_data = [
			'RegisteredUserID' => $registered_user_id,
			'CompID' => $comp_id,
			'App' => $plan_details['App'],
			'SubscriptionPlanID' => $plan_details['SubscriptionPlanID'],
			'SubscribedDate' => date('Y-m-d H:i:s'),
			'SubscriptionStartDate' => $subscription_start_date,
			'SubscriptionEndDate' => $subscription_end_date,
			'AmountPaid' => $plan_details['TotalAmount'],
			'AmountPaidBy' => $this->userdata['ID'],
		];

		$this->registered_user_model->saveUserSubscriptionLogs($user_subscription_log_data);
		$this->userdata['apps'][$app]['SubscriptionEndDate'] = $subscription_end_date;

		$recipents = array_unique([$user_details['EmailID'],$this->userdata['EmailID']]);
		
		$user_receipt_data['RenewedBy'] = $this->userdata['Name'];

		$mailer_data = [
            'recipents' => $recipents,
            'subject' => 'Wolf Network - Acknowledgement of your online payment',
            'content' => view('Modules\Finance\Views\acknowledgement_receipt',$user_receipt_data)
        ];
        $php_mail = new \App\Libraries\Php_mail();

        $php_mail->php_send_mail($mailer_data);

		$this->session->set_userdata('user_data',$this->userdata);
		return $this->response->redirect(base_url('thank-you'));
	}

	public function planRenewal(){
		$app_id = env('app_id');

		$data = [
			'pricing_plans' => $this->finance_model->fetchSubscriptionPlans($app_id),
			'add_bel_global_js' => base_url('assets/js/employee.js')
		];
		return default_view('Modules\Finance\Views\plan_renewal', $data);
	}
	
	public function invoiceSettings(){
	    $comp_id = $this->userdata['CompID'];
	    $invoice_settings_details = $this->finance_model->fetchInvoiceSettings($comp_id);
	    
	    if(!empty($_POST)){
		    $this->form_validation->setRule('TermsAndConditions', 'Invoice Terms And Conditions', 'required');

			if ($this->form_validation->withRequest($this->request)->run())
	        {
	        	$invoice_setting_id = (!empty($invoice_settings_details['InvoiceSettingID']))?$invoice_settings_details['InvoiceSettingID']:0;
		        $invoice_settings_data = [
		            'CompID' => $comp_id,
		            'TermsAndConditions' => ($this->request->getPost('TermsAndConditions'))?$this->request->getPost('TermsAndConditions'):null
		        ];
		        
		        if(empty($invoice_settings_details)){
		            $invoice_settings_data['AddedBy'] = $this->userdata['CompID'];
		            $invoice_settings_data['AddedDate'] = date('Y-m-d H:i:s');
		        }else{
		            $invoice_settings_data['UpdatedBy'] = $this->userdata['CompID'];
		            $invoice_settings_data['UpdatedDate'] = date('Y-m-d H:i:s');
		        }

		        $this->finance_model->saveInvoiceSettings($invoice_settings_data, $invoice_setting_id);
		        $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Invoice Saved Succesfully!']);
		        return $this->response->redirect(base_url('invoice-settings'));
	        }
	    }


	    if(!empty($invoice_settings_details)){
	    	foreach ($invoice_settings_details as $form_key => $form_value) {
	    		if(empty($_POST[$form_key])){
	    			$_POST[$form_key] = $form_value;
	    		}
	    	}
	    }

	    return default_view('\Modules\Finance\Views\invoice_settings');
	}

	public function manageReceipts($invoice_id){
		$comp_id = $this->userdata['CompID'];
		$invoice_data = $this->finance_model->fetchInvoiceData($invoice_id,$comp_id);

		if(empty($invoice_data)){
			echo "Either the invoice does not exist or does not belong to you.";
			exit;
		}

		$app = env('app');
		$subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];

		$invoice_details_data = $this->finance_model->fetchInvoiceDetailsData($invoice_id);
		$invoice_receipt_data = $this->finance_model->fetchInvoiceReceiptData($invoice_id);

		$data = [
			'invoice_id' => $invoice_id,
			'invoice_data' => $invoice_data,
			'invoice_details' => $invoice_details_data,
			'invoice_receipt_data' => $invoice_receipt_data,
			'receipts' => $this->finance_model->fetchReceipts($invoice_id,$subscription_end_date)
		];

		// echo "<pre>";
		// print_r($data['invoice_data']);
		// exit;

		return default_view('\Modules\Finance\Views\manage_receipts',$data);
	}

	public function createReceipt($invoice_id){

		$subscription_time_left = subscription_time_left();
		if($subscription_time_left['years'] <= 0 && $subscription_time_left['months'] <= 0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start creating receipts.']);
            return $this->response->redirect(base_url('plan-renewal'));
        }

		$comp_id = $this->userdata['CompID'];
		$invoice_data = $this->finance_model->fetchInvoiceData($invoice_id,$comp_id);
		if(empty($invoice_data)){
			echo "Either the invoice does not exist or does not belong to you.";
			exit;
		}

		$invoice_receipt_data = $this->finance_model->fetchInvoiceReceiptData($invoice_id);

		$total_payable_amount = $invoice_data['TotalPayableAmount'] - $invoice_receipt_data['TotalPaidAmount'];

		if(!empty($_POST)){
			$this->form_validation->setRule('PaidAmount', 'Paid Amount', 'required|regex_match[/^\d*[.]?\d+$/m]|less_than_equal_to['.$total_payable_amount.']|greater_than[0]',['regex_match' => 'Paid Amount should either be decimal or number.']);
			$this->form_validation->setRule('ReceiptDate', 'Payment Date', 'required');
			$this->form_validation->setRule('PaymentModeID', 'Payment Mode', 'required');

			if(!empty($_POST['ReceiptDate']) && $_POST['ReceiptDate'] < $invoice_data['ClientInvoiceDate']){
				$this->form_validation->setRule('ReceiptDate', 'Payment Date', 'valid_email',['valid_email' => 'Receipt date should be greater than or equal to invoice date.']);
			}

			if ($this->form_validation->withRequest($this->request)->run())
	        {
	        	$receipt_data = [
	        		'ReceiptNo' => mt_rand(1111111111111111,9999999999999999),
	        		'InvoiceID' => $invoice_id,
	        		'PaidAmount' => $this->request->getPost('PaidAmount'),
	        		'ReceiptDate' => $this->request->getPost('ReceiptDate'),
	        		'PaymentModeID' => $this->request->getPost('PaymentModeID'),
	        		'AddedBy' => $this->userdata['ID'],
	        		'AddedDate' => date('Y-m-d H:i:s'),
	        	];

	        	$receipt_id = $this->finance_model->saveReceipt($receipt_data);

	        	$this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Receipt Created Successfully!']);
	        	return $this->response->redirect(base_url('view-receipt-details/'.$receipt_id));
	        }
		}


		$data = [
			'invoice_id' => $invoice_id,
			'invoice_data' => $invoice_data,
			'invoice_receipt_data' => $invoice_receipt_data,
			'payment_modes' => $this->finance_model->fetchPaymentModes()
		];

		return default_view('\Modules\Finance\Views\create_receipt',$data);
	}

	public function viewReceiptDetails($receipt_id){
		$comp_id = $this->userdata['CompID'];
		$receipt_data = $this->finance_model->fetchReceiptData($receipt_id,$comp_id);
		if(empty($receipt_data)){
			echo "Either the invoice does not exist or does not belong to you.";
			exit;
		}

		$data = [
			'receipt_id' => $receipt_id,
			'receipt_data' => $receipt_data
		];
		return default_view('\Modules\Finance\Views\view_receipt_details',$data);
	}

	public function downloadInvoice($invoice_id){
		$comp_id = $this->userdata['CompID'];
		$invoice_data = $this->finance_model->fetchInvoiceData($invoice_id,$comp_id);

		if(empty($invoice_data)){
			echo "Either the invoice does not exist or does not belong to you.";
			exit;
		}

		$data = [
			'invoice_id' => $invoice_id,
			'invoice_data' => $invoice_data,
			'invoice_details_data' => $this->finance_model->fetchInvoiceDetails($invoice_id,$comp_id),
			'invoice_details_tax_data' => $this->finance_model->fetchInvoiceDetailsTaxData($invoice_id,$comp_id),
			'invoice_additional_charges_data' => $this->finance_model->fetchInvoiceAdditionalChargesData($invoice_id),
			'company_banking_details' => $this->company_model->fetchCompanyBankingDetails($comp_id)
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
		$pdf->Output($invoice_data['InvoiceNo'].'.pdf', 'D');
	}

	public function downloadReceipt($receipt_id){
		$comp_id = $this->userdata['CompID'];
		$receipt_data = $this->finance_model->fetchReceiptData($receipt_id,$comp_id);
		if(empty($receipt_data)){
			echo "Either the invoice does not exist or does not belong to you.";
			exit;
		}

		$data = [
			'receipt_id' => $receipt_id,
			'receipt_data' => $receipt_data,
			'invoice_data' => $this->finance_model->fetchInvoiceData($receipt_data['InvoiceID'],$comp_id)
		];

		$html = view('\Modules\Finance\Views\download_receipt_details',$data,TRUE);

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
		$pdf->SetFont('dejavusans', '', 10);

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
		$pdf->Output($receipt_data['ReceiptNo'].'.pdf', 'D');
	}

    public function deleteInvoice($invoice_id){
		$subscription_time_left = subscription_time_left();
		if($subscription_time_left['years'] <= 0 && $subscription_time_left['months'] <= 0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start deleting invoices.']);
            return $this->response->redirect(base_url('plan-renewal'));
        }

        $comp_id = $this->userdata['CompID'];
		$invoice_data = $this->finance_model->fetchInvoiceData($invoice_id,$comp_id);

		if(empty($invoice_data)){
			echo "Either the invoice does not exist or does not belong to you.";
			exit;
		}else{
			$this->finance_model->deleteInvoice($invoice_id);

			$this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Invoice deleted successfully!']);
			return $this->response->redirect(base_url('manage-invoices'));
		}
    }

    public function exportExcelInvoices(){

    	$subscription_time_left = subscription_time_left();
		if($subscription_time_left['years'] <= 0 && $subscription_time_left['months'] <= 0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start exporting invoice excel.']);
            return $this->response->redirect(base_url('plan-renewal'));
        }

        $comp_id = $this->userdata['CompID'];
        $client_id = $this->request->getGet('ClientID');

        $filter = [
        	'ClientID' => $client_id,
        	'InvoiceDateFrom' => $this->request->getGet('InvoiceDateFrom'),
        	'InvoiceDateTo' => $this->request->getGet('InvoiceDateTo'),
        	'DueDateFrom' => $this->request->getGet('DueDateFrom'),
        	'amountFilter' => $this->request->getGet('amountFilter'),
        ];

        $app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];

        $full_invoice_data = $this->finance_model->fetchFullInvoiceData($comp_id,$subscription_end_date,$filter);

        $headers = ['Invoice No','Invoice Date','Invoice Due Date','My Service Tax Identification Number','Client','Client Tax Identifier','Client Service Tax Identifier','Particular','HSN','Price Per Unit','Qty','Taxes','Total Tax Percentage','Total taxable amount','Total Amount'];

        $php_spreadsheets = new Php_spreadsheets();
        $php_spreadsheets->export_excel($headers, $full_invoice_data);
    }
}