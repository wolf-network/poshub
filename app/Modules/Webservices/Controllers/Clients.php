<?php 

namespace Modules\Webservices\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\respondTrait;

class Clients extends ResourceController {
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

        $this->client_model = new \Modules\Clients\Models\Client_model();
    }
    
    public function get_clients(){
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
                $sort_by = 'C.ClientName';
                break;
            case '2':
                $sort_by = 'BIM.BusinessIndustry';
                break;
            case '3':
                $sort_by = 'SM.StateName';
                break;
            case '4':
                $sort_by = 'CM.CityName';
                break;
            case '5':
                $sort_by = 'CU.ClientUserFirstName';
                break;
            case '6':
                $sort_by = 'RM.Role';
                break;
            case '7':
                $sort_by = 'CU.ClientUserContactNo';
                break;
            case '8':
                $sort_by = 'CU.ClientUserEmailID';
                break;
            case '9':
                $sort_by = 'RU.Name';
                break;
            
            default:
                // code...
                break;
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        $total_records = $this->client_model->fetchClientList($this->user_data['CompID'],$this->user_data,$subscription_end_date,0,0,$filter,true);
        $brief_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->client_model->fetchClientList($this->user_data['CompID'],$this->user_data,$subscription_end_date,$limit,$offset,$filter,0,$sort_by,$sort_order)
        ];
        
        return $this->respond($brief_list, 200);
    }

    public function get_client_data(){
        $client_id = $this->request->getGet('ClientID');
        if(empty($client_id)){
            return $this->respond([
                 'status' => false,
                 'msg' => 'Client ID is required',
                 'data' => []
             ], 501);
            return false;
        }else{
            $comp_id = $this->user_data['CompID'];
            $client_data = $this->client_model->fetchClientData($client_id, $comp_id);
            if(empty($client_data)){
                return $this->respond([
                     'status' => false,
                     'msg' => 'Either the client does not exist or does not belong to you.',
                     'data' => []
                 ], 404);
                return false;
            }else{
                return $this->respond([
                     'status' => true,
                     'msg' => 'Following are the client details.',
                     'data' => [
                        'client_data' => $client_data,
                        'client_service_taxes' => $this->client_model->fetchClientServiceTaxes($client_id, $comp_id)
                     ]
                 ], 200);
                return false;
            }
        }
    }

    public function get_all_clients(){
        $offset = $this->request->getGet('offset');
        $filter = [
            'search_txt' => $this->request->getGet('search_txt')
        ];

        $client_id = $this->request->getGet('client_id');
        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        return $this->respond([
            'status' => TRUE,
            'message' => 'Following are the clients',
            'data' => $this->client_model->fetchClients($this->user_data['CompID'],$subscription_end_date,30, $offset,$filter,false, $client_id),
        ], 200);
    }
}