<?php

namespace Modules\Vendors\Controllers;

class Vendor_controller extends \CodeIgniter\Controller {
    function __construct()
    {  
        $this->session = \Config\Services::session(); 
        $this->userdata = $this->session->get('user_data');
        if(empty($this->userdata['ID'])){
            header('location:'.base_url().'login');
            exit();
        }

        $this->vendor_model = new \Modules\Vendors\Models\Vendor_model();
        $this->region_model = new \Modules\Management\Models\Region_model();
        $this->user_model = new \Modules\Layouts\Models\User_model();
        $this->finance_model = new \Modules\Finance\Models\Finance_model();

        $this->form_validation = \Config\Services::validation();
    }
    
    public function saveVendor($vendor_id = 0){

        if(!empty($vendor_id) && $this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $comp_id = $this->userdata['CompID'];

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start managing Vendors.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        if(!empty($vendor_id)){
            $vendor_details = $this->vendor_model->fetchVendorDetails($vendor_id,$this->userdata['CompID']);
            if(empty($vendor_details)){
                show_404();
                exit;
            }
            $vendor_services = $this->vendor_model->fetchVendorServices($vendor_id);
            
        }

        if(!empty($_POST)){
            $this->form_validation->setRule('VendorName', 'Vendor Name', 'required|duplicateVendor['.$vendor_id.']');
            $this->form_validation->setRule('ServiceID.*', 'Vendor Services', 'required|numeric');
            $this->form_validation->setRule('FirmTypeID', 'Firm Type', 'required|numeric');
            $this->form_validation->setRule('CIN', 'CIN', 'validateVendorCIN['.$vendor_id.']');
            $this->form_validation->setRule('StateID', 'State', 'required|numeric');
            $this->form_validation->setRule('CityID', 'City', 'required|numeric');
            $this->form_validation->setRule('VendorUserFirstName', 'VendorUserFirstName', 'required');
            $this->form_validation->setRule('VendorUserLastName', 'VendorUserLastName', 'required');
            $this->form_validation->setRule('RoleID.*', 'Role', 'required');
            $this->form_validation->setRule('VendorUserContactNo', 'VendorUserContactNo', 'required|min_length[8]|max_length[15]');
            $this->form_validation->setRule('VendorUserEmailID', 'VendorUserEmailID', 'required|valid_email');
            $this->form_validation->setRule('Address', 'Address', 'required');
            $this->form_validation->setRule('BankID', 'Bank Name', 'required');
            $this->form_validation->setRule('BankDetailsID', 'IFSC', 'required');
            $this->form_validation->setRule('AccountHolderName', 'Account Holder Name', 'required');
            $this->form_validation->setRule('AccountNo', 'Account No', 'required');
            $this->form_validation->setRule('ConfirmAccountNo', 'Confirm Account No', 'required|matches[AccountNo]');

            if(!empty($_POST['TaxIdentificationTypeID']) && empty($_POST['TaxIdentificationNumber'])){
                $this->form_validation->setRule('TaxIdentificationNumber', 'TaxIdentificationNumber', 'required|xss_clean',['required' => 'Tax Identification Number is required along with Tax Identification Type']);            
            }

            if(!empty($_POST['TaxIdentificationNumber']) && empty($_POST['TaxIdentificationTypeID'])){
                $this->form_validation->setRule('TaxIdentificationTypeID', 'TaxIdentificationTypeID', 'required|xss_clean',['required' => 'Tax Identification Type is required along with Tax Identification Number']);            
            }
            
            $required_extensions = ['png','jpg','jpeg','jfif','pdf'];
          
            
            if(!empty($_FILES['ChequeImgPath']['name'])){
                $ext = pathinfo($_FILES['ChequeImgPath']['name'], PATHINFO_EXTENSION);
                if(!in_array(strtolower($ext),['jpg','png','jpeg','jfif'])){
                    $this->form_validation->setRule('ChequeImgPath', 'Bank Cheque', 'required',['required' => 'Invalid file extension.']);
                }
                
                if($_FILES['ChequeImgPath']['size'] == 0){
                    $this->form_validation->setRule('ChequeImgPath', 'Bank Cheque', 'required',['required' => 'Invalid image with 0 bytes.']);
                }
                
                if($_FILES['ChequeImgPath']['size'] > 2097152){
                    $this->form_validation->setRule('ChequeImgPath', 'Bank Cheque', 'required',['required' => 'File size should be maximum of 2MB.']);
                }
            }
        
            if ($this->form_validation->withRequest($this->request)->run())
            {
                
                $app_key = env('media_server_app_key');
                $app_secret = env('media_server_app_secret');
                
                $vendor_data = [
                    'VendorName' => $this->request->getPost('VendorName'),
                    'FirmTypeID' => $this->request->getPost('FirmTypeID'),
                    'TaxIdentificationTypeID' => ($this->request->getPost('TaxIdentificationTypeID'))?$this->request->getPost('TaxIdentificationTypeID'):null,
                    'TaxIdentificationNumber' => ($this->request->getPost('TaxIdentificationNumber'))?$this->request->getPost('TaxIdentificationNumber'):null,
                    'CIN' => ($this->request->getPost('CIN'))?$this->request->getPost('CIN'):null,
                    'CompID' => $comp_id
                ];

                if(empty($vendor_id)){
                    $vendor_data['RegisteredBy'] = $this->userdata['ID'];
                    $vendor_data['RegisteredDate'] = date('Y-m-d H:i:s');
                }else{
                    $vendor_data['UpdatedBy'] = $this->userdata['ID'];
                    $vendor_data['UpdatedDate'] = date('Y-m-d H:i:s');
                }
                
                $vendor_id = $this->vendor_model->saveVendor($vendor_data, $vendor_id);
                
                for($i=0;$i<count($_POST['ServiceID']);$i++){
                    $vendor_service_mapper_data[] = [
                        'VendorID' => $vendor_id,
                        'ServiceID' => $_POST['ServiceID'][$i]
                    ];   
                }
                
                if(!empty($vendor_details)){
                    $this->vendor_model->deleteVendorServices($vendor_id);
                }
                
                $this->vendor_model->saveVendorServices($vendor_service_mapper_data);
                
                $vendor_users_data = [
                    'VendorID' => $vendor_id,
                    'VendorUserFirstName' => $this->request->getPost('VendorUserFirstName'),
                    'VendorUserLastName' => $this->request->getPost('VendorUserLastName'),
                    'VendorUserEmailID' => $this->request->getPost('VendorUserEmailID'),
                    'VendorUserContactNo' => $this->request->getPost('VendorUserContactNo'),
                    'IsPrimaryUser' => 1,
                    'AddedByRegisteredUser' => $this->userdata['ID']
                ];
                
                $vendor_user_id = (!empty($vendor_details['VendorUserID']))?$vendor_details['VendorUserID']:0;
                
                $vendor_user_id = $this->vendor_model->saveVendorUser($vendor_users_data,$vendor_user_id);
                
                for($i=0;$i<count($_POST['RoleID']);$i++){
                    $vendor_user_roles_mapper_data[] = [
                        'VendorUserID' => $vendor_user_id,
                        'RoleID' => $_POST['RoleID'][$i]
                    ];
                }
                
                $this->vendor_model->saveVendorUserRolesBatch($vendor_user_roles_mapper_data,$vendor_user_id);
                
                $vendor_geography_data = [
                    'VendorID' => $vendor_id,
                    'CountryID' => $this->request->getPost('CountryID'),
                    'StateID' => $this->request->getPost('StateID'),
                    'CityID' => $this->request->getPost('CityID'),
                    'Address' => $this->request->getPost('Address'),
                    'IsHeadOffice' => 1
                ];

                $vendor_geography_id = (!empty($vendor_details['VendorGeographyID']))?$vendor_details['VendorGeographyID']:0;
                
                $vendor_geography_id = $this->vendor_model->saveVendorGeography($vendor_geography_data,$vendor_geography_id);
                
                $vendor_banking_documents = [
                    'VendorID' => $vendor_id,
                    'BankDetailsID' => $this->request->getPost('BankDetailsID'),
                    'AccountHolderName' => $this->request->getPost('AccountHolderName'),
                    'AccountNo' => $this->request->getPost('AccountNo')
                ];
                
                
                if(!empty($_FILES['ChequeImgPath']['name'])){
                    
                    if(!empty($vendor_details['ChequeImgPath'])){
                        $delete_media = curl_request(media_server('delete-media'),$media_delete_data = json_encode(
                            [
                                'app_key' => $app_key,
                                'app_secret' => $app_secret,
                                'bucket' => 'vendor-documents',
                                'path' => $vendor_details['ChequeImgPath']
                            ]
                        ));   
                    }

                    $bank_cheque_file_uploaded_data = upload_file('vendor-documents',$_FILES['ChequeImgPath']);
                    
                    $vendor_banking_documents['ChequeImgPath'] = $bank_cheque_file_uploaded_data['data']['bucket_path'][0];

                }

                $vendor_banking_document_id = (!empty($vendor_details['VendorBankingDocumentID']))?$vendor_details['VendorBankingDocumentID']:0;
                $this->vendor_model->saveVendorBankingDocuments($vendor_banking_documents,$vendor_banking_document_id);
                
                $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Vendor Details Saved!']);
                $this->response->redirect(base_url('manage-vendors'));
            }
        }

        if(!empty($vendor_details)){
            foreach($vendor_details as $vendor_key => $vendor_value){
                $_POST[$vendor_key] = $vendor_value;
            }
            
            $_POST['RoleID'] = explode(',',$vendor_details['RoleID']);
            
            
            
            if(empty($_POST['ServiceID'])){
                for($i=0;$i<count($vendor_services);$i++){
                    foreach($vendor_services[$i] as $vendor_service_key => $vendor_service_value){
                        $_POST[$vendor_service_key][] = $vendor_service_value;
                    }   
                }
            }
        }

        $data = [
            'vendor_id' => $vendor_id,
            'services' => $this->user_model->fetchServices(),
            'firm_types' => $this->user_model->fetchFirmTypes(),
            'tax_identification_types' => $this->finance_model->fetchTaxIdentificationTypes(),
            'roles' => $this->user_model->fetchRoles($comp_id),
            'bank_details' => $this->user_model->fetchBanks(),
            'countries' => $this->region_model->fetchCountries(),
            'add_bel_global_js' => base_url('assets/js/vendor.js')
        ];
        return default_view('\Modules\Vendors\Views\save_vendor',$data);
    }
    
    public function saveVendorDocument($vendor_id,$vendor_document_id = 0){

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start managing vendor documents.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        if(!empty($_POST)){
            $this->form_validation->setRule('CountryID', 'CountryID', 'required');
            $this->form_validation->setRule('StateID', 'StateID', 'required');
            $this->form_validation->setRule('CityID', 'CityID', 'required');
            $this->form_validation->setRule('DocumentName.*', 'Document Name', 'required');
            $this->form_validation->setRule('DocumentName.*', 'Document Name', 'required');
            
            $document_name = ($this->request->getPost('DocumentName'))?count($this->request->getPost('DocumentName')):1;
            for($i=0;$i<$document_name;$i++){
                
                if($vendor_document_id == 0){
                    if(empty($_FILES['DocumentFile']['name'][$i])){
                        $this->form_validation->setRule('DocumentFile.'.$i, 'Document File', 'required',['required' => 'DocumentFile.'.$i.' field is required.']);
                    }else{
                        $this->validate([
                            'DocumentFile.'.$i => 'uploaded[DocumentFile.'.$i.']|max_size[DocumentFile.'.$i.',5048]|ext_in[DocumentFile.'.$i.',png,jpg,jfif]',
                        ]);
                    } 
    
                }
            }
            
            
            if ($this->form_validation->withRequest($this->request)->run())
            {
    
                $vendor_geography_data = [
                    'VendorID' => $vendor_id,
                    'CountryID' => $this->request->getPost('CountryID'),
                    'StateID' => $this->request->getPost('StateID'),
                    'CityID' => $this->request->getPost('CityID')
                ];
    
                $vendor_geography_id = $this->vendor_model->saveVendorGeographyIfNotExist($vendor_geography_data);
                
                for($i=0;$i<$document_name;$i++){
                    $vendor_document_data = [
                        'VendorID' => $vendor_id,
                        'VendorGeographyID' => $vendor_geography_id,
                        'DocumentName' => $_POST['DocumentName'][$i],
                        'DocumentDescription' => $_POST['DocumentDescription'][$i],
                        'DocumentApproved' => 1,
                        'DocumentApprovedBy' => $this->userdata['ID'],
                        'DocumentApprovedDate' => date('Y-m-d H:i:s')
                    ];
                    
                    $vendor_document_id = $this->vendor_model->saveVendorDocuments($vendor_document_data);
    
                    if(!empty($_FILES['DocumentFile']['name'][$i])){
                        $_FILES['DocumentFiles']['name']     = $_FILES['DocumentFile']['name'][$i]; 
                        $_FILES['DocumentFiles']['type']     = $_FILES['DocumentFile']['type'][$i]; 
                        $_FILES['DocumentFiles']['tmp_name'] = $_FILES['DocumentFile']['tmp_name'][$i];
                        $_FILES['DocumentFiles']['error']     = $_FILES['DocumentFile']['error'][$i]; 
                        $_FILES['DocumentFiles']['size']     = $_FILES['DocumentFile']['size'][$i];
    
                        $vendor_documents_uploaded_data = upload_file('vendor-documents',$_FILES['DocumentFiles']);
    
                        $vendor_document_media_data[] = [
                            'VendorDocumentID' => $vendor_document_id,
                            'VendorDocumentMediaPath' => $vendor_documents_uploaded_data['data']['bucket_path'][0]
                        ];
                    }
                } 
                    
                $this->vendor_model->saveVendorDocumentMediaData($vendor_document_media_data);
                
                $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Vendor Document Saved Successfully!']);
                if($this->request->getPost('save') == 'save'){
                    $this->response->redirect(base_url('manage-vendor-documents/'.$vendor_id));   
                }else{
                    $this->response->redirect(base_url('add-vendor-document/'.$vendor_id));
                }
            }
        }

        $data = [
            'countries' => $this->region_model->fetchCountries(),
            'add_bel_global_js' => base_url('assets/js/vendor.js')
        ];
        return default_view('\Modules\Vendors\Views\save_vendor_documents',$data);
    }
    
    public function manageVendors(){
        $data = [
            'add_bel_global_js' => base_url('assets/js/vendor.js')
        ];
        return default_view('\Modules\Vendors\Views\manage_vendors',$data);
    }
    
    public function manageVendorDocuments($vendor_id){
        $data = [
            'vendor_id' => $vendor_id,
            'add_bel_global_js' => base_url('assets/js/vendor.js')
        ];
     
        return default_view('\Modules\Vendors\Views\manage_vendor_documents',$data);
    }

    public function deleteVendor($vendor_id){

        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $vendor_details = $this->vendor_model->fetchBasicVendorDetails($vendor_id,$this->userdata['CompID']);

        if(!empty($vendor_details)){
            $vendor_data = [
                'VendorStatus' => 'Deleted',
                'VendorName' => (!empty($vendor_details['VendorName']))?$vendor_details['VendorName'].'-d':null,
                'CIN' => (!empty($vendor_details['CIN']))?$vendor_details['CIN'].'-d':null
            ];
            
            $this->vendor_model->saveVendor($vendor_data,$vendor_id);
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Vendor deleted successfully!']);
        }else{
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Either the Vendor Does not Exist or Does not Belong to You.']);
        }

        $this->response->redirect(base_url('manage-vendors'));
    }

    public function manageVendorServiceTax($vendor_id){
        $comp_id = $this->userdata['CompID'];
        $data = [
            'vendor_id' => $vendor_id,
            'vendor_details' => $this->vendor_model->fetchBasicVendorDetails($vendor_id,$comp_id),
            'vendor_service_taxes' => $this->vendor_model->fetchVendorServiceTaxes($vendor_id, $comp_id),
            'add_bel_global_js' => base_url('assets/js/vendor.js')
        ];
        return default_view('\Modules\Vendors\Views\manage_vendor_service_taxes',$data);
    }

    public function saveVendorServiceTax($vendor_id, $vendor_service_tax_id = 0){
        
        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start adding service tax for vendors.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $comp_id = $this->userdata['CompID'];
        
        if(!empty($vendor_service_tax_id)){
            $vendor_service_tax_details = $this->vendor_model->fetchVendorServiceTaxData($vendor_service_tax_id,$comp_id);
            if(empty($vendor_service_tax_details)){
                echo "Either the service tax does not exist or does not belong to you.";
                exit;
            }
        }

        if(!empty($_POST)){
            $this->form_validation->setRule('Label', 'Label', 'required|validateVendorServiceTaxLabel['.$vendor_id.'-'.$vendor_service_tax_id.']');
            $this->form_validation->setRule('ServiceTaxTypeID', 'Service Tax Type', 'required');
            $this->form_validation->setRule('ServiceTaxNumber', 'Service Tax Number', 'required|validateVendorServiceTaxNumber['.$vendor_id.'-'.$vendor_service_tax_id.']');
            $this->form_validation->setRule('BillingCountryID', 'Country', 'required');
            $this->form_validation->setRule('BillingStateID', 'State', 'required');
            $this->form_validation->setRule('BillingAddress', 'Billing Address', 'required');

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $vendor_service_tax_data = [
                    'VendorID' => $vendor_id,
                    'Label' => $this->request->getPost('Label'),
                    'ServiceTaxTypeID' => $this->request->getPost('ServiceTaxTypeID'),
                    'ServiceTaxNumber' => $this->request->getPost('ServiceTaxNumber'),
                    'BillingCountryID' => $this->request->getPost('BillingCountryID'),
                    'BillingStateID' => $this->request->getPost('BillingStateID'),
                    'BillingAddress' => $this->request->getPost('BillingAddress')
                ];

                if(empty($vendor_service_tax_id)){
                    $vendor_service_tax_data['AddedBy'] = $this->userdata['ID'];
                    $vendor_service_tax_data['AddedDate'] = date('Y-m-d H:i:s');
                }else{
                    $vendor_service_tax_data['UpdatedBy'] = $this->userdata['ID'];
                    $vendor_service_tax_data['UpdatedDate'] = date('Y-m-d H:i:s');
                }

                $this->vendor_model->saveVendorServiceTax($vendor_service_tax_data, $vendor_service_tax_id);

                $this->session->setFlashdata('flashmsg',['status' => true, 'msg' => 'Service Tax saved successfully!']);

                $this->response->redirect(base_url('manage-vendor-service-taxes/'.$vendor_id));
            }
        }


        if(!empty($vendor_service_tax_details)){
            foreach($vendor_service_tax_details as $form_key => $form_value) {
                if(empty($_POST[$form_key])){
                    $_POST[$form_key] = $form_value;
                }
            }
        }

        $data = [
            'vendor_service_tax_id' => $vendor_service_tax_id,
            'vendor_id' => $vendor_id,
            'service_tax_types' => $this->finance_model->fetchServiceTaxTypes(),
            'countries' => $this->region_model->fetchCountries(),
            'add_bel_global_js' => base_url('assets/js/vendor.js')
        ];

        return default_view('\Modules\Vendors\Views\save_vendor_service_tax',$data);
    }

    public function deleteVendorServiceTax($vendor_service_tax_id){

        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start deleting vendors.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $comp_id = $this->userdata['CompID'];

        $vendor_service_tax_details = $this->vendor_model->fetchVendorServiceTaxData($vendor_service_tax_id,$comp_id);

        if(!empty($vendor_service_tax_details)){
            $this->vendor_model->deleteVendorServiceTax($vendor_service_tax_id);
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Vendor service tax deleted successfully!']);
        }else{
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Either the Vendor Does not Exist or Does not Belong to You.']);
        }

        $this->response->redirect(base_url('manage-vendor-service-taxes/'.$vendor_service_tax_details['VendorID']));
    }
}