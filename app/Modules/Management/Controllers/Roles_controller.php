<?php

namespace Modules\Management\Controllers;

class Roles_controller extends \CodeIgniter\Controller {
    function __construct()
    {
        
        $this->session = \Config\Services::session();    
        $this->userdata = $this->session->get('user_data');
        if(empty($this->userdata['ID'])){
            header('location:'.base_url().'login');
        }
        
        $this->roles_model = new \Modules\Management\Models\Roles_model();
        $this->form_validation = \Config\Services::validation();
    }
    
    public function manageRoles(){
        
        $data = [
            'add_bel_global_js' => base_url('assets/js/management.js')
        ];

        return default_view('\Modules\Management\Views\manage_roles',$data);
    }

    public function saveRole($role_id = 0){
        
        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <= 0 && $subscription_time_left['months'] <= 0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->response([
                'status' => false,
                'msg' => 'Kindly re-new your subscription to start adding roles.',
                'error' => []
            ], 403);
        }

        if(!empty($role_id)){
            $role_details = $this->roles_model->fetchRoleData($role_id,$this->userdata['CompID']);
            if(empty($role_details)){
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }

        if(!empty($_POST)){
            $this->form_validation->setRule('Role', 'Role', 'required|validateRole['.$role_id.']');

            if ($this->form_validation->withRequest($this->request)->run()){
                $role_data = [
                    'Role' => $this->request->getPost('Role'),
                    'CompID' => $this->userdata['CompID']
                ];

                if(empty($role_id)){
                    $role_data['AddedBy'] = $this->userdata['ID'];
                    $role_data['AddedDate'] = date('Y-m-d H:i:s');
                }else{
                    $role_data['UpdatedBy'] = $this->userdata['ID'];
                    $role_data['UpdatedDate'] = date('Y-m-d H:i:s');
                }

                $role_id = $this->roles_model->saveRole($role_data, $role_id);

                $this->session->setFlashdata('flashmsg',['status' => true, 'msg' => 'Role saved successfully!']);
                $this->response->redirect(base_url('manage-roles'));
            }
        }

        if(!empty($role_details)){
            foreach ($role_details as $form_key => $form_value) {
                $_POST[$form_key] = $form_value;
            }
        }

        $data = [
            'role_id' => $role_id
        ];

        return default_view('\Modules\Management\Views\save_role', $data);
    }

    public function validateRole($role,$role_id){
        $check_role = $this->roles_model->checkRole($role,$this->userdata['CompID'],$role_id);
        if(!empty($check_role))
        {
            $this->form_validation->set_message('validateRole', 'This Role already exists in the database.');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function deleteRole($role_id){

        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start deleting Roles.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $role_details = $this->roles_model->fetchRoleData($role_id, $this->userdata['CompID']);
        if(!empty($role_details)){
            $this->roles_model->deleteRole($role_id);
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Role deleted successfully!']);
        }else{
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Either the Role Does not Exist or Does not Belong to You.']);
        }

        $this->response->redirect(base_url('manage-roles'));
    }
}