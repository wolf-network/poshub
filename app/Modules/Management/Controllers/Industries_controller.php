<?php

namespace Modules\Management\Controllers;

class Industries_controller extends \CodeIgniter\Controller {
    function __construct()
    {
        $this->session = \Config\Services::session();   
        $this->userdata = $this->session->get('user_data');
        if(empty($this->userdata['ID'])){
            header('location:'.base_url().'login');
        }
        
        $this->industries_model = new \Modules\Management\Models\Industries_model();
        $this->form_validation = \Config\Services::validation();
    }

    public function manageIndustries(){
        $data = [
            'add_bel_global_js' => base_url('assets/js/management.js')
        ];

        return default_view('\Modules\Management\Views\manage_industries',$data);
    }

    public function saveIndustry($industry_id = 0){
        
        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <= 0 && $subscription_time_left['months'] <= 0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            return 'Kindly re-new your subscription to start adding roles.';
            exit;
        }

        if(!empty($industry_id)){
            $industry_details = $this->industries_model->fetchIndustryData($industry_id,$this->userdata['CompID']);
        }

        if(!empty($_POST)){
            $this->form_validation->setRule('BusinessIndustry', 'Business Industry', 'required|validateBusinessIndustry['.$industry_id.']');

            if ($this->form_validation->withRequest($this->request)->run()){
                $business_industry_data = [
                    'BusinessIndustry' => $this->request->getPost('BusinessIndustry'),
                    'CompID' => $this->userdata['CompID']
                ];

                if(empty($industry_id)){
                    $business_industry_data['AddedBy'] = $this->userdata['ID'];
                    $business_industry_data['AddedDate'] = date('Y-m-d H:i:s');
                }else{
                    $business_industry_data['UpdatedBy'] = $this->userdata['ID'];
                    $business_industry_data['UpdatedDate'] = date('Y-m-d H:i:s');
                }

                $business_industry_id = $this->industries_model->saveIndustry($business_industry_data, $industry_id);

                $this->session->setFlashdata('flashmsg',['status' => true, 'msg' => 'Industry saved successfully!']);
                $this->response->redirect(base_url('manage-industries'));
            }
        }

        if(!empty($industry_id)){
            foreach ($industry_details as $form_key => $form_value) {
                $_POST[$form_key] = $form_value;
            }
        }

        $data = [
            'industry_id' => $industry_id
        ];

        return default_view('\Modules\Management\Views\save_industry', $data);
    }

    public function validateBusinessIndustry($business_industry, $industry_id){
        $check_industry = $this->industries_model->checkIndustry($business_industry,$this->userdata['CompID'],$industry_id);
        if(!empty($check_industry))
        {
            $this->form_validation->set_message('validateBusinessIndustry', 'This Industry already exists in the database.');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function deleteIndustry($industry_id){

        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start deleting Industries.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $industry_details = $this->industries_model->fetchIndustryData($industry_id, $this->userdata['CompID']);
        if(!empty($industry_details)){
            $this->industries_model->deleteBusinessIndustry($industry_id);
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Industry deleted successfully!']);
        }else{
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Either the Industry Does not Exist or Does not Belong to You.']);
        }

        $this->response->redirect(base_url('manage-industries'));
    }
}