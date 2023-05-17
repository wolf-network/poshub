<?php

namespace Modules\Clients\Controllers;

use App\Libraries\Php_spreadsheets;

class Client_controller extends \CodeIgniter\Controller {
    function __construct()
    {
        $this->session = \Config\Services::session();   
        $this->userdata = $this->session->get('user_data');
        if(empty($this->userdata['ID'])){
            header('location:'.base_url().'login');
            exit();
        }

        $this->client_model = new \Modules\Clients\Models\Client_model();
        $this->user_model = new \Modules\Layouts\Models\User_model();
        $this->region_model = new \Modules\Management\Models\Region_model();
        $this->finance_model = new \Modules\Finance\Models\Finance_model();

        $this->form_validation = \Config\Services::validation();
    }
    
    public function manageClients(){
        $data = [
            'add_bel_global_js' => base_url('assets/js/client.js')
        ];
        return default_view('\Modules\Clients\Views\manage_clients',$data);
    }
    
    public function saveClient($client_id = 0){

        if(!empty($client_id) && $this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $comp_id = $this->userdata['CompID'];

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start managing Clients.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $received_client_id = $client_id;

        if(!empty($client_id)){
            $client_details = $this->client_model->fetchClientData($client_id, $this->userdata['CompID']);

            if(empty($client_details)){
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                exit;
            }
        }
        
        if(!empty($_POST)){
            $this->form_validation->setRule('ClientName', 'Business Name', 'required|validateClient['.$client_id.']');
            $this->form_validation->setRule('BusinessIndustryID.*', 'Industry', 'permit_empty|numeric');
            $this->form_validation->setRule('FirmTypeID', 'Firm Type', 'numeric');
            $this->form_validation->setRule('CIN', 'CIN', 'validateClientCIN['.$client_id.']');
            $this->form_validation->setRule('StateID', 'State', 'required|numeric');
            $this->form_validation->setRule('CityID', 'City', 'required|numeric');
            $this->form_validation->setRule('ClientUserFirstName', 'ClientUserFirstName', 'required');
            $this->form_validation->setRule('ClientUserLastName', 'ClientUserLastName', 'required');
            $this->form_validation->setRule('ClientUserContactNo', 'ClientUserContactNo', 'required|min_length[8]|max_length[15]');
            $this->form_validation->setRule('ClientUserEmailID', 'Official Email ID', 'permit_empty|valid_email');

            if(!empty($_POST['TaxIdentificationTypeID']) && empty($_POST['TaxIdentificationNumber'])){
                $this->form_validation->setRule('TaxIdentificationNumber', 'TaxIdentificationNumber', 'required',['required' => 'Tax Identification Number is required along with Tax Identification Type']);            
            }

            if(!empty($_POST['TaxIdentificationNumber']) && empty($_POST['TaxIdentificationTypeID'])){
                $this->form_validation->setRule('TaxIdentificationTypeID', 'TaxIdentificationTypeID', 'required',['required' => 'Tax Identification Type is required along with Tax Identification Number']);            
            }

            $logo_extensions = ['png','jpg','jpeg','jfif'];

            if(!empty($_FILES['LogoPath']['name'])){
                $ext = pathinfo($_FILES['LogoPath']['name'], PATHINFO_EXTENSION);
                if(!in_array(strtolower($ext),$logo_extensions)){
                    $this->form_validation->setRule('LogoPath', 'Logo Path', 'required',['required' => 'Invalid file extension.']);
                }
                
                if($_FILES['LogoPath']['size'] == 0){
                    $this->form_validation->setRule('LogoPath', 'Logo Path', 'required',['required' => 'Invalid image with 0 bytes.']);
                }
                
                if($_FILES['LogoPath']['size'] > 2097152){
                    $this->form_validation->setRule('LogoPath', 'Logo Path', 'required',['required' => 'File size should be maximum of 2MB.']);
                }
            }

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $app_key = getenv('media_server_app_key');
                $app_secret = getenv('media_server_app_secret');
                
                $client_data = [
                    'ClientName' => $this->request->getPost('ClientName'),
                    'FirmTypeID' => ($this->request->getPost('FirmTypeID'))?$this->request->getPost('FirmTypeID'):null,
                    'CIN' => ($this->request->getPost('CIN'))?$this->request->getPost('CIN'):null,
                    'CompID' => $comp_id,
                    'TaxIdentificationTypeID' => ($this->request->getPost('TaxIdentificationTypeID'))?$this->request->getPost('TaxIdentificationTypeID'):null,
                    'TaxIdentificationNumber' => ($this->request->getPost('TaxIdentificationNumber'))?$this->request->getPost('TaxIdentificationNumber'):null,
                    'ClientRating' => ($this->request->getPost('ClientRating'))?$this->request->getPost('ClientRating'):'0'
                ];

                if(empty($client_id)){
                    $client_data['AddedBy'] = $this->userdata['ID'];
                    $client_data['AddedDate'] = date('Y-m-d H:i:s');
                }else{
                    $client_data['UpdatedBy'] = $this->userdata['ID'];
                    $client_data['UpdatedDate'] = date('Y-m-d H:i:s');
                }
                
                $client_id = $this->client_model->saveClient($client_data,$client_id);
                
                if(!empty($_POST['BusinessIndustryID'])){
                    for($i=0;$i<count($_POST['BusinessIndustryID']);$i++){
                        $client_business_industry_mapper_data[] = [
                            'ClientID' => $client_id,
                            'BusinessIndustryID' => $_POST['BusinessIndustryID'][$i]
                        ];   
                    }
                    
                    $this->client_model->saveClientBusinessIndustryBatch($client_business_industry_mapper_data, $client_id);
                }
                
                $client_user_data = [
                    'ClientID' => $client_id,
                    'ClientUserFirstName' => $this->request->getPost('ClientUserFirstName'),
                    'ClientUserLastName' => $this->request->getPost('ClientUserLastName'),
                    'ClientUserEmailID' => $this->request->getPost('ClientUserEmailID'),
                    'ClientUserContactNo' => $this->request->getPost('ClientUserContactNo'),
                    'IsPrimaryUser' => 1
                ];
                
                $client_user_id = (!empty($client_details['ClientUserID']))?$client_details['ClientUserID']:0;
                
                if(!empty($received_client_id)){
                    $client_user_data['UpdatedByRegisteredUser'] = $this->userdata['ID'];
                    $client_user_data['UpdatedDate'] = date('Y-m-d H:i:s');
                }else{
                    $client_user_data['AddedByRegisteredUser'] = $this->userdata['ID'];
                    $client_user_data['AddedDate'] = date('Y-m-d H:i:s');
                }
                
                $client_user_id = $this->client_model->saveClientUserData($client_user_data, $client_user_id);

                if(!empty($_POST['RoleID']) && is_array($_POST['RoleID'])){
                    for($i=0;$i<count($_POST['RoleID']);$i++){
                        $client_user_roles_mapper_data[] = [
                            'ClientUserID' => $client_user_id,
                            'RoleID' => $_POST['RoleID'][$i]
                        ];   
                    }
                    
                    $this->client_model->saveClientUserRoleMapperData($client_user_roles_mapper_data, $client_user_id);
                }

                $client_geography_data = [
                    'ClientID' => $client_id,
                    'CountryID' => $this->request->getPost('CountryID'),
                    'StateID' => $this->request->getPost('StateID'),
                    'CityID' => $this->request->getPost('CityID'),
                    'Address' => $this->request->getPost('Address'),
                    'LatLong' => $this->request->getPost('LatLong'),
                    'IsHeadOffice' => 1,
                ];
                
                $client_geography_id = (!empty($client_details['ClientGeographyID']))?$client_details['ClientGeographyID']:0;
                
                $client_geography_id = $this->client_model->saveClientGeography($client_geography_data, $client_geography_id);

                $client_user_geographic_data[] = [
                    'ClientUserID' => $client_user_id,
                    'ClientUserCountryID' => $this->request->getPost('CountryID'),
                    'ClientUserStateID' => $this->request->getPost('StateID')
                ];

                $this->client_model->saveClientUserGeographicalData($client_user_geographic_data, $client_user_id);

                /* Client Logo */

                if(!empty($_FILES['LogoPath']['name'])){
                    if(!empty($client_details['LogoPath'])){
                        $delete_media = curl_request(media_server('delete-media'),$media_delete_data = json_encode(
                            [
                                'app_key' => $app_key,
                                'app_secret' => $app_secret,
                                'bucket' => 'client-documents',
                                'path' => $client_details['LogoPath']
                            ]
                        ));
                    }
                    
                    
                    $client_logo_file_uploaded_data = upload_file('client-documents',$_FILES['LogoPath']);
                    
                    $client_logo_file_data = [
                        'LogoPath' => $client_logo_file_uploaded_data['data']['bucket_path'][0]
                    ];

                    
                    $this->client_model->saveClient($client_logo_file_data,$client_id);
                }
                
                $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Client saved successfully!']);
                $this->response->redirect(base_url('manage-clients'));
            }
        }
        
        if(!empty($client_details)){
            foreach($client_details as $form_key => $form_value){
                if(empty($_POST['BusinessIndustryID'])){
                    $_POST['BusinessIndustryID'] = explode(',',$client_details['BusinessIndustryID']);
                }

                if(empty($_POST['RoleID'])){
                    $_POST['RoleID'] = explode(',',$client_details['RoleID']);
                }
                    
                if(empty($_POST[$form_key])){
                    $_POST[$form_key] = $form_value;   
                }
            }
            
        }

        $data = [
            'client_id' => $client_id,
            'countries' => $this->region_model->fetchCountries(),
            'business_industries' => $this->user_model->businessIndustries($comp_id),
            'firm_types' => $this->user_model->fetchFirmTypes(),
            'roles' => $this->user_model->fetchRoles($comp_id),
            'tax_identification_types' => $this->finance_model->fetchTaxIdentificationTypes(),
            'add_bel_global_js' => base_url('assets/js/client.js')
        ];


        return default_view('\Modules\Clients\Views\save_client',$data);
    }
    
    public function deleteClient($client_id){

        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start deleting Clients.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $client_details = $this->client_model->fetchBasicClientDetails($client_id, $this->userdata['CompID']);
        if(!empty($client_details)){
            $client_data = [
                'ClientStatus' => 'Deleted',
                'ClientName' => $client_details['ClientName'].'~d',
                'CIN' => $client_details['CIN'].'~d'
            ];
            
            $this->client_model->saveClient($client_data,$client_id);
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Client deleted successfully!']);
        }else{
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Either the Client Does not Exist or Does not Belong to You.']);
        }

        $this->response->redirect(base_url('manage-clients'));
    }

    public function validateCIN($cin, $client_id){

        $check_cin = $this->client_model->checkClientCIN($cin, $client_id, $this->userdata['CompID']);

        if(!empty($check_cin)){
            $this->form_validation->set_message('validateCIN', 'This CIN already exists for client named '.$check_cin['ClientName'].'.');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    public function exportClients(){
        $app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];

        $client_data = $this->client_model->fetchClientFullData($this->userdata['CompID'],$this->userdata,$subscription_end_date);

        $headers = ['Client Name','Industry','Contact Person','Role','Contact No','Email ID','State','City'];

        $php_spreadsheets = new Php_spreadsheets();

        $php_spreadsheets->export_excel($headers,$client_data);

    }

    public function manageClientServiceTaxes($client_id){
        $comp_id = $this->userdata['CompID'];
        $data = [
            'client_id' => $client_id,
            'client_details' => $this->client_model->fetchClientData($client_id, $comp_id),
            'client_service_taxes' => $this->client_model->fetchClientServiceTaxes($client_id, $comp_id),
            'add_bel_global_js' => base_url('assets/js/client.js')
        ];

        return default_view('\Modules\Clients\Views\manage_client_service_taxes',$data);
    }

    public function saveClientServiceTax($client_id, $client_service_tax_id = 0){
        
        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start adding service tax for clients.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $comp_id = $this->userdata['CompID'];
        
        if(!empty($client_service_tax_id)){
            $client_service_tax_details = $this->client_model->fetchClientServiceTaxData($client_service_tax_id,$comp_id);
            if(empty($client_service_tax_details)){
                echo "Either the service tax does not exist or does not belong to you.";
                exit;
            }
        }

        if(!empty($_POST)){
            $this->form_validation->setRule('Label', 'Label', 'required|validateClientServiceTaxLabel['.$client_id.'-'.$client_service_tax_id.']');
            $this->form_validation->setRule('ServiceTaxTypeID', 'Service Tax Type', 'required');
            $this->form_validation->setRule('ServiceTaxNumber', 'Service Tax Number', 'required|validateClientServiceTaxNumber['.$client_id.'-'.$client_service_tax_id.']');
            $this->form_validation->setRule('BillingAddress', 'Billing Address', 'required');

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $client_service_tax_data = [
                    'ClientID' => $client_id,
                    'Label' => $this->request->getPost('Label'),
                    'ServiceTaxTypeID' => $this->request->getPost('ServiceTaxTypeID'),
                    'ServiceTaxNumber' => $this->request->getPost('ServiceTaxNumber'),
                    'BillingCountryID' => $this->request->getPost('BillingCountryID'),
                    'BillingStateID' => $this->request->getPost('BillingStateID'),
                    'BillingAddress' => $this->request->getPost('BillingAddress')
                ];

                if(empty($client_service_tax_id)){
                    $client_service_tax_data['AddedBy'] = $this->userdata['ID'];
                    $client_service_tax_data['AddedDate'] = date('Y-m-d H:i:s');
                }else{
                    $client_service_tax_data['UpdatedBy'] = $this->userdata['ID'];
                    $client_service_tax_data['UpdatedDate'] = date('Y-m-d H:i:s');
                }

                $this->client_model->saveClientServiceTax($client_service_tax_data, $client_service_tax_id);

                $this->session->setFlashdata('flashmsg',['status' => true, 'msg' => 'Service Tax saved successfully!']);

                $this->response->redirect(base_url('manage-client-service-taxes/'.$client_id));
            }
        }


        if(!empty($client_service_tax_details)){
            foreach($client_service_tax_details as $form_key => $form_value) {
                if(empty($_POST[$form_key])){
                    $_POST[$form_key] = $form_value;
                }
            }
        }

        $data = [
            'client_service_tax_id' => $client_service_tax_id,
            'client_id' => $client_id,
            'countries' => $this->region_model->fetchCountries(),
            'service_tax_types' => $this->finance_model->fetchServiceTaxTypes(),
            'add_bel_global_js' => base_url('assets/js/client.js')
        ];

        return default_view('\Modules\Clients\Views\save_client_service_tax',$data);
    }

    public function deleteClientServiceTax($client_service_tax_id){

        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start deleting Clients.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $comp_id = $this->userdata['CompID'];

        $client_service_tax_details = $this->client_model->fetchClientServiceTaxData($client_service_tax_id,$comp_id);

        if(!empty($client_service_tax_details)){
            $this->client_model->deleteClientServiceTax($client_service_tax_id);
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Client service tax deleted successfully!']);
        }else{
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Either the Client Does not Exist or Does not Belong to You.']);
        }

        $this->response->redirect(base_url('manage-client-service-taxes/'.$client_service_tax_details['ClientID']));
    }
}