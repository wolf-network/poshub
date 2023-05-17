<?php 

namespace Modules\Webservices\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\respondTrait;

class Company extends ResourceController {
    function __construct()
	{
        $this->session = \Config\Services::session();
        $this->user_data = $this->session->get('user_data');
        if(empty($this->user_data)){
            return $this->respond([
                'status' => false,
                'msg' => 'Invalid User!',
                'data' => [],
             ],401);
        }

        $this->company_model = new \Modules\Company\Models\Company_model();
	}

    public function get_addresses(){
        $comp_id = $this->user_data['CompID'];
        
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

        $total_records = $this->company_model->fetchAddressesList($comp_id,0,0,$filter,true);
        $addresses_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->company_model->fetchAddressesList($comp_id,$limit,$offset,$filter)
        ];
        
        return $this->respond($addresses_list, 200);
    }

    public function get_company_documents(){
        $comp_id = $this->user_data['CompID'];
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

        $total_records = $this->company_model->fetchCompanyDocumentsList($comp_id,0,0,$filter,true);
        $addresses_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->company_model->fetchCompanyDocumentsList($comp_id,$limit,$offset,$filter)
        ];
        
        return $this->respond($addresses_list, 200);
    }
}