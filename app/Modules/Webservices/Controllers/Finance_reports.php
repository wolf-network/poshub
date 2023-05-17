<?php

namespace Modules\Webservices\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\respondTrait;

class Finance_reports extends ResourceController {
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

        $this->finance_reports_model = new \Modules\Finance\Models\Finance_reports_model();        
        

        $this->form_validation = \Config\Services::validation();
	}

    public function get_gstr1(){

        if(!empty($_GET['InvoiceDateFrom']) || !empty($_GET['InvoiceDateTo'])){
            $offset = $this->request->getGet('iDisplayStart');

            $subscription_time_left = subscription_time_left();
            if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

                $limit = $this->request->getGet('iDisplayLength');
                $filter = [
                    'search_txt' => $this->request->getGet('sSearch'),
                    'CompanyGST' => $this->request->getGet('CompanyGST'),
                    'InvoiceDateFrom' => $this->request->getGet('InvoiceDateFrom'),
                    'InvoiceDateTo' => $this->request->getGet('InvoiceDateTo'),
                    'InvoiceType' => $this->request->getGet('InvoiceType'),
                ];
            }else{
                $limit = 10;
                $filter = [];
            }

            $app = env('app');
            $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];
            $comp_id = $this->user_data['CompID'];
            $total_records = $this->finance_reports_model->fetchGSTR1Reports($comp_id,$subscription_end_date,0,0,$filter,true);

            $gstr1_list = [
                'recordsTotal' => $total_records,
                'recordsFiltered' => $total_records,
                'data' => $this->finance_reports_model->fetchGSTR1Reports($comp_id,$subscription_end_date,$limit,$offset,$filter)
            ];

        }else{
            $gstr1_list = [
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ];
        }

        return $this->respond($gstr1_list, 200);
        
    }
}