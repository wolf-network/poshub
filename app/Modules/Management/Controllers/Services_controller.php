<?php

namespace Modules\Management\Controllers;

class Services_controller extends \CodeIgniter\Controller {
    function __construct()
    {
        $this->session = \Config\Services::session();   
        $this->userdata = $this->session->get('user_data');
        if(empty($this->userdata['ID'])){
            header('location:'.base_url().'login');
        }

        $this->services_model = new \Modules\Management\Models\Services_model();
        $this->form_validation = \Config\Services::validation();
    }

    public function manageServices(){
        $data = [
            'add_bel_global_js' => base_url('assets/js/management.js')
        ];
        return default_view('\Modules\Management\Views\manage_services',$data);
    }

    public function saveService($service_id = 0){
        
        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <= 0 && $subscription_time_left['months'] <= 0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->response([
                'status' => false,
                'msg' => 'Kindly re-new your subscription to start adding services.',
                'error' => []
            ], 403);
        }

        if(!empty($service_id)){
            $service_details = $this->services_model->fetchServiceData($service_id,$this->userdata['CompID']);
            if(empty($service_details)){
                show_404();
            }
        }

        if(!empty($_POST)){
            $this->form_validation->setRule('ServiceType', 'Service', 'required|validateService['.$service_id.']');

            if ($this->form_validation->withRequest($this->request)->run()){
                $service_data = [
                    'ServiceType' => $this->request->getPost('ServiceType'),
                    'CompID' => $this->userdata['CompID']
                ];

                if(empty($service_id)){
                    $service_data['AddedBy'] = $this->userdata['ID'];
                    $service_data['AddedDate'] = date('Y-m-d H:i:s');
                }else{
                    $service_data['UpdatedBy'] = $this->userdata['ID'];
                    $service_data['UpdatedDate'] = date('Y-m-d H:i:s');
                }

                $service_id = $this->services_model->saveService($service_data, $service_id);

                $this->session->setFlashdata('flashmsg',['status' => true, 'msg' => 'Role saved successfully!']);
                $this->response->redirect(base_url('manage-services'));
            }
        }


        if(!empty($service_id)){
            foreach ($service_details as $form_key => $form_value) {
                $_POST[$form_key] = $form_value;
            }
        }

        $data = [
            'service_id' => $service_id
        ];

        return default_view('\Modules\Management\Views\save_service', $data);
    }

    public function deleteService($service_id){

        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start deleting Services.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $service_details = $this->services_model->fetchServiceData($service_id, $this->userdata['CompID']);
        if(!empty($service_details)){
            $this->services_model->deleteService($service_id);
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Role deleted successfully!']);
        }else{
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Either the Service Does not Exist or Does not Belong to You.']);
        }

        $this->response->redirect(base_url('manage-services'));
    }
}