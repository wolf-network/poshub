<?php 

namespace Modules\Webservices\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\respondTrait;

class Management extends ResourceController {
    function __construct()
	{
		$this->session = \Config\Services::session();
        $this->user_data = $this->session->get('user_data');
        if(empty($this->user_data['ID'])){
            return $this->respond([
                 'status' => false,
                 'msg' => 'Invalid User',
                 'data' => []
             ], 401);
            return false;
        }

        $this->roles_model = new \Modules\Management\Models\Roles_model();
        $this->industries_model = new \Modules\Management\Models\Industries_model();
        $this->services_model = new \Modules\Management\Models\Services_model();

        $this->form_validation = \Config\Services::validation();
	}

    public function save_role(){
        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <= 0 && $subscription_time_left['months'] <= 0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            return $this->respond([
                'status' => false,
                'msg' => 'Kindly re-new your subscription to start adding roles.',
                'error' => []
            ], 403);
        }

        $this->form_validation->setRule('Role', 'Role', 'required|validateRole[0]');

        if (!$this->form_validation->withRequest($this->request)->run()){
            return $this->respond([
                'status' => false,
                'msg' => 'Validation error',
                'err' => validation_errors()
            ], 501);
        }else{
            $role_data = [
                'Role' => $this->request->getPost('Role'),
                'CompID' => $this->user_data['CompID'],
                'AddedBy' => $this->user_data['ID'],
                'AddedDate' => date('Y-m-d H:i:s')
            ];

            $role_id = $this->roles_model->saveRole($role_data);

            return $this->respond([
                'status' => false,
                'msg' => 'Role added successfully!.',
                'data' => [
                    'RoleID' => $role_id
                ]
            ], 200);
        }
    }

    public function save_industry(){
        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <= 0 && $subscription_time_left['months'] <= 0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            return $this->respond([
                'status' => false,
                'msg' => 'Kindly re-new your subscription to start adding Business industries.',
                'error' => []
            ], 403);
        }

        $this->form_validation->setRule('BusinessIndustry', 'BusinessIndustry', 'required|validateBusinessIndustry[0]');

        if (!$this->form_validation->withRequest($this->request)->run()){
            return $this->respond([
                'status' => false,
                'msg' => 'Validation error',
                'err' => validation_errors()
            ], 501);
        }else{
            $business_industry_data = [
                'BusinessIndustry' => $this->request->getPost('BusinessIndustry'),
                'CompID' => $this->user_data['CompID'],
                'AddedBy' => $this->user_data['ID'],
                'AddedDate' => date('Y-m-d H:i:s')
            ];

            $business_industry_id = $this->industries_model->saveIndustry($business_industry_data);

            return $this->respond([
                'status' => false,
                'msg' => 'Industry added successfully!.',
                'data' => [
                    'BusinessIndustryID' => $business_industry_id
                ]
            ], 200);
        }
    }

    public function get_roles(){
        $offset = $this->request->getGet('iDisplayStart');

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

            $limit = $this->request->getGet('iDisplayLength');
            $filter = [
                'search_txt' => $this->request->getGet('sSearch')
            ];
        }else{
            $limit = 10;
            $filter = [];
        }

        $sort_by = '';
        $sort_order = $this->request->getGet('sSortDir_0');

        switch ($this->request->getGet('iSortCol_0')) {
            case '1':
                $sort_by = 'RM.Role';
                break;
            case '2':
                $sort_by = 'RU.Name';
                break;
            
            default:
                // code...
                break;
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        $total_records = $this->roles_model->fetchRolesList($this->user_data['CompID'],$subscription_end_date,0,0,$filter,true);

        $roles_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->roles_model->fetchRolesList($this->user_data['CompID'],$subscription_end_date,$limit,$offset,$filter,0,$sort_by,$sort_order)
        ];
        
        return $this->respond($roles_list, 200);
    }

    public function get_industries(){
        $offset = $this->request->getGet('iDisplayStart');

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

            $limit = $this->request->getGet('iDisplayLength');
            $filter = [
                'search_txt' => $this->request->getGet('sSearch')
            ];
        }else{
            $limit = 10;
            $filter = [];
        }

        $sort_by = '';
        $sort_order = $this->request->getGet('sSortDir_0');

        switch ($this->request->getGet('iSortCol_0')) {
            case '1':
                $sort_by = 'BIM.BusinessIndustry';
                break;
            case '2':
                $sort_by = 'RU.Name';
                break;
            
            default:
                // code...
                break;
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        $total_records = $this->industries_model->fetchIndustriesList($this->user_data['CompID'],$subscription_end_date,0,0,$filter,true);

        $industries_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->industries_model->fetchIndustriesList($this->user_data['CompID'],$subscription_end_date,$limit,$offset,$filter,0,$sort_by,$sort_order)
        ];
        
        return $this->respond($industries_list, 200);
    }

    public function save_service(){
        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <= 0 && $subscription_time_left['months'] <= 0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            return $this->respond([
                'status' => false,
                'msg' => 'Kindly re-new your subscription to start adding services.',
                'error' => []
            ], 403);
        }

        $this->form_validation->setRule('ServiceType', 'Service', 'required|validateService[0]');

        if (!$this->form_validation->withRequest($this->request)->run()){
            return $this->respond([
                'status' => false,
                'msg' => 'Validation error',
                'err' => validation_errors()
            ], 501);
        }else{
            $service_data = [
                'ServiceType' => $this->request->getPost('ServiceType'),
                'CompID' => $this->user_data['CompID'],
                'AddedBy' => $this->user_data['ID'],
                'AddedDate' => date('Y-m-d H:i:s')
            ];

            $service_id = $this->services_model->saveService($service_data);

            return $this->respond([
                'status' => false,
                'msg' => 'Service added successfully!.',
                'data' => [
                    'ServiceID' => $service_id
                ]
            ], 200);
        }
    }

    public function get_services(){
        $offset = $this->request->getGet('iDisplayStart');

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

            $limit = $this->request->getGet('iDisplayLength');
            $filter = [
                'search_txt' => $this->request->getGet('sSearch')
            ];
        }else{
            $limit = 10;
            $filter = [];
        }

        $sort_by = '';
        $sort_order = $this->request->getGet('sSortDir_0');

        switch ($this->request->getGet('iSortCol_0')) {
            case '1':
                $sort_by = 'SM.ServiceType';
                break;
            case '2':
                $sort_by = 'RU.Name';
                break;
            
            default:
                // code...
                break;
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        $total_records = $this->services_model->fetchServicesList($this->user_data['CompID'],$subscription_end_date,0,0,$filter,true);

        $services_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->services_model->fetchServicesList($this->user_data['CompID'],$subscription_end_date,$limit,$offset,$filter,0,$sort_by,$sort_order)
        ];
        
        return $this->respond($services_list, 200);
    }
}