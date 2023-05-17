<?php

namespace Modules\Company\Controllers;

class Company_controller extends \CodeIgniter\Controller {
	public function __construct(){
	        
        $this->session = \Config\Services::session();  
        $this->userdata = $this->session->get('user_data');
        if(empty($this->userdata['ID'])){
            header('location:'.base_url().'login');
        }

        $this->company_model = new \Modules\Company\Models\Company_model();
        $this->region_model = new \Modules\Management\Models\Region_model();
        $this->user_model = new \Modules\Layouts\Models\User_model();
        $this->finance_model = new \Modules\Finance\Models\Finance_model();

        $this->form_validation = \Config\Services::validation();
	}

	public function editCompany(){

        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $subscription_time_left = subscription_time_left();
        if(!empty($_POST) && $subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start adding/editing company.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $comp_id = $this->userdata['CompID'];
		$comp_details = $this->company_model->fetchCompDetails($comp_id);

        if(empty($comp_details)){
            echo "Either the company does not exist or does not belong to you.";
            exit;
        }



		$this->form_validation->setRule('CompName', 'Company Name', 'required');
		$this->form_validation->setRule('EmailID', 'EmailID', 'required|valid_email');
		$this->form_validation->setRule('ContactNo', 'Contact No', 'required');
        $this->form_validation->setRule('FirmTypeID', 'Firm Type', 'required');

		if(!empty($_FILES['CompLogoPath']['name'])){
            $required_extensions = ['png','jpg','jpeg','jfif'];
            $ext = pathinfo($_FILES['CompLogoPath']['name'], PATHINFO_EXTENSION);
            if(!in_array(strtolower($ext),$required_extensions)){
                $this->form_validation->setRule('CompLogoPath', 'Company Logo', 'required',['required' => 'Please upload a valid image.']);
            }
            
            if($_FILES['CompLogoPath']['size'] == 0){
                $this->form_validation->setRule('CompLogoPath', 'Company Logo', 'required',['required' => 'Invalid image with 0 bytes.']);
            }
            
            if($_FILES['CompLogoPath']['size'] > 2097152){
                $this->form_validation->setRule('CompLogoPath', 'Company Logo', 'required',['required' => 'File size should be maximum of 2MB.']);
            }
        }

        if(!empty($_FILES['SignatureImgPath']['name'])){
            $required_extensions = ['png','jpg','jpeg','jfif'];
            $ext = pathinfo($_FILES['SignatureImgPath']['name'], PATHINFO_EXTENSION);
            if(!in_array(strtolower($ext),$required_extensions)){
                $this->form_validation->setRule('SignatureImgPath', 'Signature Logo', 'required',['required' => 'Invalid file extension.']);
            }
            
            if($_FILES['SignatureImgPath']['size'] == 0){
                $this->form_validation->setRule('SignatureImgPath', 'Signature', 'required',['required' => 'Invalid image with 0 bytes.']);
            }
            
            if($_FILES['SignatureImgPath']['size'] > 2097152){
                $this->form_validation->setRule('SignatureImgPath', 'Signature', 'required',['required' => 'File size should be maximum of 2MB.']);
            }
        }

	  	if ($this->form_validation->withRequest($this->request)->run())
        {
        	$app_key = env('media_server_app_key');
        	$app_secret = env('media_server_app_secret');

        	$comp_data = array(
        		'CompName' => $this->request->getPost('CompName'),
        		'ContactNo' => $this->request->getPost('ContactNo'),
        		'EmailID' => $this->request->getPost('EmailID'),
                'FirmTypeID' => $this->request->getPost('FirmTypeID'),
                'TaxIdentificationTypeID' => ($this->request->getPost('TaxIdentificationTypeID'))?$this->request->getPost('TaxIdentificationTypeID'):null,
                'TaxIdentificationNumber' => ($this->request->getPost('TaxIdentificationNumber'))?$this->request->getPost('TaxIdentificationNumber'):null,
                'CIN' => ($this->request->getPost('CIN'))?$this->request->getPost('CIN'):null,
        	);

        	if(!empty($_FILES['CompLogoPath']['name'])){

                if(!empty($comp_details['CompLogoPath'])){
                    $delete_media = curl_request(media_server('delete-media'),$media_delete_data = json_encode(
                        [
                            'app_key' => $app_key,
                            'app_secret' => $app_secret,
                            'path' => $comp_details['CompLogoPath']
                        ]
                    ));
                }
                
                
                $CompLogo_data = upload_file('cms-documents',$_FILES['CompLogoPath']);

                $CompLogoPath = $CompLogo_data['data']['bucket_path'][0];
                $comp_data['CompLogoPath'] = $CompLogoPath;
            }

            if(!empty($_FILES['SignatureImgPath']['name'])){

                if(!empty($comp_details['SignatureImgPath'])){
                    $delete_media = curl_request(media_server('delete-media'),$media_delete_data = json_encode(
                        [
                            'app_key' => $app_key,
                            'app_secret' => $app_secret,
                            'bucket' => 'company-documents',
                            'path' => $comp_details['SignatureImgPath']
                        ]
                    ));
                }
                
                
                $signature_img_data = upload_file('company-documents',$_FILES['SignatureImgPath']);

                $signature_img_path = $signature_img_data['data']['bucket_path'][0];
                $comp_data['SignatureImgPath'] = $signature_img_path;
            }

        	$update_status = $this->company_model->saveCompDetails($comp_data, $comp_id);

        	
    		$this->session->setFlashdata('flashmsg', array('status' => true,'msg' => 'Company Details Updated Successfully'));
            
        	$this->response->redirect(base_url('edit-comp-details'));
        }

		if (empty($_POST)) {
			foreach ($comp_details as $comp_details_key => $comp_details_value) {
				$_POST[$comp_details_key] = $comp_details_value;
			}
		}

		$data = [
			'comp_id' => $comp_details['CompID'],
            'firm_types' => $this->user_model->fetchFirmTypes(),
            'tax_identification_types' => $this->finance_model->fetchTaxIdentificationTypes(),
            'add_bel_global_js' => base_url('assets/js/company.js')
		];

		return default_view('\Modules\Company\Views\edit_comp_details',$data);
	}

	public function manageAddresses(){
        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $data = [
            'add_bel_global_js' => base_url('assets/js/company.js')
        ];

		return default_view('\Modules\Company\Views\manage_addresses',$data);
	}

	public function saveAddress($address_id = 0){
        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start adding/editing company.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $comp_id = $this->userdata['CompID'];

         if(!empty($address_id)){
            $company_address_details = $this->company_model->fetchCompanyAddressDetails($address_id,$comp_id);
            if(empty($company_address_details)){
                echo "Either the address does not exist or does not belong to you.";
                return false;
            }
        }

        if(!empty($_POST)){
    		$this->form_validation->setRule('CountryID', 'Country', 'required');
    		$this->form_validation->setRule('StateID', 'State', 'required');
    		$this->form_validation->setRule('CityID', 'City', 'required');
    		$this->form_validation->setRule('Address', 'Address', 'required');
    		
            if ($this->form_validation->withRequest($this->request)->run())
            {
            	
            	$address_data = [
            		'CompID' => $comp_id,
            		'CountryID' => $this->request->getPost('CountryID'),
            		'StateID' => $this->request->getPost('StateID'),
            		'CityID' => $this->request->getPost('CityID'),
            		'Address' => $this->request->getPost('Address'),
            		'GoogleMap' => $this->request->getPost('GoogleMap'),
            		'OfficeType' => $this->request->getPost('OfficeType')
            	];

            	$this->company_model->saveAddress($address_data, $address_id);
            	$this->session->setFlashdata('flashmsg',['status' => true, 'msg' => 'Address saved successfully!']);
            	$this->response->redirect(base_url('manage-addresses'));
            }
        }



        if(!empty($company_address_details)){
    		foreach ($company_address_details as $form_key => $form_value) {
    			if(empty($_POST[$form_key])){
    				$_POST[$form_key] = $form_value;
    			}
    		}
        }


		$data = [
            'countries' => $this->region_model->fetchCountries(),
            'office_types' => $this->company_model->fetchOfficeTypes(),
            'add_bel_global_js' => base_url('assets/js/company.js')
        ];
		return default_view('\Modules\Company\Views\save_address',$data);
	}

	public function deleteAddress($company_address_id){
        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start deleting company address.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $comp_id = $this->userdata['CompID'];

        $company_address_details = $this->company_model->fetchCompanyAddressDetails($company_address_id,$comp_id);
        if(empty($company_address_details)){
            echo "Either the address does not exist or does not belong to you.";
            exit;
        }else{
            $this->company_model->deleteAddress($company_address_id);
            $this->session->setFlashdata('flashmsg',['status' => true, 'msg' => 'Address deleted successfully!']);
            $this->response->redirect(base_url('manage-addresses'));
        }
	}

	public function manageCompanyDocuments(){
        $comp_id = $this->userdata['CompID'];
        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }
        
		$data = [
			'add_bel_global_js' => base_url('assets/js/company.js')
		];
		return default_view('\Modules\Company\Views\manage_company_documents', $data);
	}

	public function saveCompanyDocuments($company_document_id = 0){

        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start managing Clients.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $comp_id = $this->userdata['CompID'];

        if(!empty($_POST)){
            $this->form_validation->setRule('CountryID', 'CountryID', 'required');
            $this->form_validation->setRule('StateID', 'StateID', 'required');
            $this->form_validation->setRule('CityID', 'CityID', 'required');
            $this->form_validation->setRule('DocumentName.*', 'Document Name', 'required');
            $this->form_validation->setRule('DocumentDescription.*', 'Document Description', 'required');
            
            $document_name = ($this->request->getPost('DocumentName'))?count($this->request->getPost('DocumentName')):1;

            for($i=0;$i<$document_name;$i++){
                
                if($company_document_id == 0){
                    if(empty($_FILES['DocumentFilePath']['name'][$i])){
                        $this->form_validation->setRule('DocumentFilePath.'.$i, 'Document File', 'required');
                    }else{
                        $allowed_extensions = ['jpg','png','pdf','jfif'];
                        $ext = pathinfo($_FILES['DocumentFilePath']['name'][$i], PATHINFO_EXTENSION);
                        if(!in_array(strtolower($ext),$allowed_extensions)){
                            $this->form_validation->setRule('DocumentFilePath.'.$i, 'Document File', 'required',['required' => 'Document file extension should be on of the following: '.implode(',',$allowed_extensions)]); 
                        }

                        if($_FILES['DocumentFilePath']['size'][$i] > 2097152){
                            $this->form_validation->setRule('DocumentFilePath.'.$i, 'Document File', 'required',['required' => 'Document max file size can be only 2MB']);
                        }
                    }   
                }
            }
            
            
            if ($this->form_validation->withRequest($this->request)->run())
            {
            	for($i=0;$i<$document_name;$i++){
                    $company_document_data = [
                        'CompID' => $comp_id,
                        'CountryID' => $this->request->getPost('CountryID'),
    	                'StateID' => $this->request->getPost('StateID'),
    	                'CityID' => $this->request->getPost('CityID'),
                        'DocumentName' => $_POST['DocumentName'][$i],
                        'DocumentDescription' => $_POST['DocumentDescription'][$i],
                        'DocumentApproved' => 1,
                        'DocumentApprovedBy' => $this->userdata['ID'],
                        'DocumentApprovedDate' => date('Y-m-d H:i:s')
                    ];

                    if(!empty($_FILES['DocumentFilePath']['name'][$i])){
                    	$_FILES['DocumentFiles']['name']     = $_FILES['DocumentFilePath']['name'][$i]; 
                        $_FILES['DocumentFiles']['type']     = $_FILES['DocumentFilePath']['type'][$i]; 
                        $_FILES['DocumentFiles']['tmp_name'] = $_FILES['DocumentFilePath']['tmp_name'][$i];
                        $_FILES['DocumentFiles']['error']     = $_FILES['DocumentFilePath']['error'][$i]; 
                        $_FILES['DocumentFiles']['size']     = $_FILES['DocumentFilePath']['size'][$i];

                    	$company_documents_uploaded_data = upload_file('company-documents',$_FILES['DocumentFiles']);

                    	$company_document_data['DocumentFilePath'] = $company_documents_uploaded_data['data']['bucket_path'][0];
                    }
                    
                    $company_document_data_arr[] = $company_document_data;
                }

                $this->company_model->saveCompanyDocuments($company_document_data_arr);

                $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Company Document Saved Successfully!']);
                if($this->request->getPost('save') == 'save'){
                    $this->response->redirect(base_url('manage-company-documents'));   
                }else{
                    $this->response->redirect(base_url('add-company-document'));
                } 
            }
        }
		

		$data = [
			'countries' => $this->region_model->fetchCountries(),
			'add_bel_global_js' => base_url('assets/js/company.js')
		];
		return default_view('\Modules\Company\Views\save_company_document', $data);
	}

	public function deleteCompanyDocument($company_document_id){
        
        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start deleting documents.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        
        $company_document_data = $this->company_model->fetchCompanyDocument($this->userdata['CompID'],$company_document_id);

        if(empty($company_document_data)){
            echo "Either the document does not exist or does not belong to you.";
            exit;
        }

		$app_key = env('media_server_app_key');
        $app_secret = env('media_server_app_secret');

		if(!empty($company_document_data['DocumentFilePath'])){
            $delete_media = curl_request(media_server('delete-media'),$company_document_delete_data = json_encode(
                [
                    'app_key' => $app_key,
                    'app_secret' => $app_secret,
                    'bucket' => 'company-documents',
                    'path' => $company_document_data['DocumentFilePath']
                ]
            ));   
        }

        $this->company_model->deleteCompanyDocument($company_document_id);
        $this->session->setFlashdata('flashmsg', array('status' => true,'msg' => 'Company Document Deleted Successfully'));
        $this->response->redirect(base_url('manage-company-documents'));
	}

    public function saveCompanyBankDetails(){

        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $subscription_time_left = subscription_time_left();
        if(!empty($_POST) && $subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start adding/editing company social media.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $comp_id = $this->userdata['CompID'];
        $company_banking_details = $this->company_model->fetchCompanyBankingDetails($comp_id);

        if(!empty($_POST)){
            $this->form_validation->setRule('BankID', 'Bank Name', 'required');
            $this->form_validation->setRule('BankDetailsID', 'IFSC Code', 'required');
            $this->form_validation->setRule('AccountHolderName', 'Account Holder Name', 'required');
            $this->form_validation->setRule('AccountNo', 'Account No', 'required');
            $this->form_validation->setRule('ConfirmAccountNo', 'Confirm Account No', 'required|matches[AccountNo]');

            $required_extensions = ['png','jpg','jpeg','jfif'];
            
            if(!empty($_FILES['QRCode']['name'])){
                $ext = pathinfo($_FILES['QRCode']['name'], PATHINFO_EXTENSION);
                if(!in_array(strtolower($ext),['jpg','png','jpeg','jfif'])){
                    $this->form_validation->setRule('QRCode', 'QR Code', 'required',['required' => 'Invalid file extension.']);
                }
                
                if($_FILES['QRCode']['size'] == 0){
                    $this->form_validation->setRule('QRCode', 'QR Code', 'required',['required' => 'Invalid image with 0 bytes.']);
                }
                
                if($_FILES['QRCode']['size'] > 2097152){
                    $this->form_validation->setRule('QRCode', 'QR Code', 'required',['required' => 'File size should be maximum of 2MB.']);
                }
            }

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $company_banking_data = [
                    'CompID' => $comp_id,
                    'BankDetailsID' => $this->request->getPost('BankDetailsID'),
                    'AccountHolderName' => $this->request->getPost('AccountHolderName'),
                    'AccountNo' => $this->request->getPost('AccountNo')
                ];

                if(!empty($_FILES['QRCode']['name'])){
                    $app_key = env('media_server_app_key');
                    $app_secret = env('media_server_app_secret');
                    
                    if(!empty($company_banking_details['QRCode'])){
                        $delete_media = curl_request(media_server('delete-media'),$media_delete_data = json_encode(
                            [
                                'app_key' => $app_key,
                                'app_secret' => $app_secret,
                                'bucket' => 'company-documents',
                                'path' => $company_banking_details['QRCode']
                            ]
                        ));   
                    }

                    $bank_qr_uploaded_data = upload_file('company-documents',$_FILES['QRCode']);
                    
                    $company_banking_data['QRCode'] = $bank_qr_uploaded_data['data']['bucket_path'][0];
                }

                $company_banking_detail_id = (!empty($company_banking_details['CompanyBankingDetailID']))?$company_banking_details['CompanyBankingDetailID']:0;

                $this->company_model->saveCompanyBankingDetails($company_banking_data,$company_banking_detail_id);
                $this->session->setFlashdata('flashmsg',['status' => true, 'msg' => 'Company banking documents saved successfully!']);
                $this->response->redirect(base_url('edit-company-bank-details'));
            }
        }

        if(!empty($company_banking_details)){
            foreach ($company_banking_details as $form_key => $form_value) {
                if(empty($_POST[$form_key])){
                    $_POST[$form_key] = $form_value;
                }
            }

            if(empty($_POST[''])){
                $_POST['ConfirmAccountNo'] = $company_banking_details['AccountNo'];
            }
        }

        $data = [
            'bank_details' => $this->user_model->fetchBanks(),
            'add_bel_global_js' => base_url('assets/js/company.js')
        ];
        return default_view('\Modules\Company\Views\save_company_bank_details',$data);
    }

    public function manageCompanyServiceTaxes(){
        $comp_id = $this->userdata['CompID'];
        $data = [
            'company_service_taxes' => $this->company_model->fetchCompanyServiceTaxes($comp_id),
            'add_bel_global_js' => base_url('assets/js/company.js')
        ];

        return default_view('\Modules\Company\Views\manage_company_service_taxes',$data);
    }

    public function saveCompanyServiceTax($company_service_tax_id = 0){
        
        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start adding service tax for your company.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $comp_id = $this->userdata['CompID'];
        
        if(!empty($company_service_tax_id)){
            $company_service_tax_details = $this->company_model->fetchCompanyServiceTaxData($company_service_tax_id,$comp_id);
            if(empty($company_service_tax_details)){
                echo "Either the service tax does not exist or does not belong to you.";
                exit;
            }
        }


        if(!empty($_POST)){
            $this->form_validation->setRule('ServiceTaxTypeID', 'Service Tax Type', 'required');
            $this->form_validation->setRule('ServiceTaxIdentificationNumber', 'Service Tax Number', 'required|validateCompanyServiceTaxNumber['.$company_service_tax_id.']');
            $this->form_validation->setRule('BillingCountryID', 'Country', 'required');
            $this->form_validation->setRule('BillingStateID', 'State', 'required');
            $this->form_validation->setRule('RegisteredAddress', 'Registered Address', 'required');

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $company_service_tax_data = [
                    'CompID' => $comp_id,
                    'ServiceTaxTypeID' => $this->request->getPost('ServiceTaxTypeID'),
                    'BillingCountryID' => $this->request->getPost('BillingCountryID'),
                    'BillingStateID' => $this->request->getPost('BillingStateID'),
                    'ServiceTaxIdentificationNumber' => $this->request->getPost('ServiceTaxIdentificationNumber'),
                    'RegisteredAddress' => $this->request->getPost('RegisteredAddress')
                ];

                if(empty($company_service_tax_id)){
                    $company_service_tax_data['AddedBy'] = $this->userdata['ID'];
                    $company_service_tax_data['AddedDate'] = date('Y-m-d H:i:s');
                }else{
                    $company_service_tax_data['UpdatedBy'] = $this->userdata['ID'];
                    $company_service_tax_data['UpdatedDate'] = date('Y-m-d H:i:s');
                }

                $this->company_model->saveCompanyServiceTax($company_service_tax_data, $company_service_tax_id);

                $this->session->setFlashdata('flashmsg',['status' => true, 'msg' => 'Service Tax saved successfully!']);

                $this->response->redirect(base_url('manage-company-service-taxes'));
            }
        }


        if(!empty($company_service_tax_details)){
            foreach($company_service_tax_details as $form_key => $form_value) {
                if(empty($_POST[$form_key])){
                    $_POST[$form_key] = $form_value;
                }
            }
        }

        $data = [
            'company_service_tax_id' => $company_service_tax_id,
            'service_tax_types' => $this->finance_model->fetchServiceTaxTypes(),
            'countries' => $this->region_model->fetchCountries(),
            'add_bel_global_js' => base_url('assets/js/company.js')
        ];

        return default_view('\Modules\Company\Views\save_company_service_tax',$data);
    }

    public function deleteCompanyServiceTax($company_service_tax_id){

        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start deleting your service tax.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $comp_id = $this->userdata['CompID'];

        $company_service_tax_details = $this->company_model->fetchCompanyServiceTaxData($company_service_tax_id,$comp_id);

        if(!empty($company_service_tax_details)){
            $this->company_model->deleteCompanyServiceTax($company_service_tax_id);
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Service tax deleted successfully!']);
        }else{
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Either the Service Tax Does not Exist or Does not Belong to You.']);
        }

        $this->response->redirect(base_url('manage-company-service-taxes'));
    }
}