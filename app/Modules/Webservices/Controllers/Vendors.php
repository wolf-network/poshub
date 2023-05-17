<?php

namespace Modules\Webservices\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\respondTrait;

class Vendors extends ResourceController {
    function __construct()
    {
        $this->session = \Config\Services::session();
        $this->user_data = $this->session->get('user_data');
        if(empty($this->user_data['ID'])){
            return $this->respond([
                 'status' => true,
                 'msg' => 'Invalid User',
                 'data' => []
             ], 401);
            return false;
        }

        $this->vendor_model = new \Modules\Vendors\Models\Vendor_model();
    }
    
    public function get_vendors(){
        $offset = $this->request->getGet('iDisplayStart');

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

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
                $sort_by = 'V.VendorName';
                break;
            case '2':
                $sort_by = 'VU.VendorUserFirstName';
                break;
            case '3':
                $sort_by = 'VU.VendorUserEmailID';
                break;
            case '4':
                $sort_by = 'VU.VendorUserContactNo';
                break;
            case '5':
                $sort_by = 'RU.Name';
                break;
            
            default:
                // code...
                break;
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        $total_records = $this->vendor_model->fetchVendorList($this->user_data['CompID'],$subscription_end_date,0,0,$filter,true);
        $vendors_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->vendor_model->fetchVendorList($this->user_data['CompID'],$subscription_end_date,$limit,$offset,$filter,0,$sort_by,$sort_order)
        ];
        
        return $this->respond($vendors_list, 200);
    }
    
    public function get_vendor_documents(){
        $vendor_id = $this->request->getGet('VendorID');
        $offset = $this->request->getGet('iDisplayStart');
        
        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

            $limit = $this->request->getGet('iDisplayLength');
            $filter = [
                'search_txt' => $this->request->getGet('sSearch')
            ];
        }else{
            $limit = 10;
            $filter = [];
        }

        $total_records = $this->vendor_model->fetchVendorDocumentsList($vendor_id,$this->user_data['CompID'],0,0,$filter,true);
        $vendor_documents_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->vendor_model->fetchVendorDocumentsList($vendor_id,$this->user_data['CompID'],$limit,$offset,$filter)
        ];
        
        return $this->respond($vendor_documents_list, 200);
    }
    
    public function get_vendor_document_data(){
        $vendor_geography_id = $this->request->getGet('vendor_geography_id');
        if(empty($vendor_geography_id)){
            return $this->respond([
                 'status' => FALSE,
                 'msg' => 'Vendor Geography ID is required',
                 'error' => 'Vendor Geography ID is required'
             ], 501);
            
            return false;
        }else{
            $vendor_document_data = $this->vendor_model->fetchVendorDocumentData($vendor_geography_id);
            return $this->respond([
                 'status' => TRUE,
                 'msg' => 'Following are the vendor documents',
                 'data' => $vendor_document_data
             ], 200);
        }
    }

    public function get_all_vendors(){
        $offset = $this->request->getGet('offset');
        $filter = [
            'search_txt' => $this->request->getGet('search_txt')
        ];

        $vendor_name = $this->request->getGet('vendor_name');
        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        return $this->respond([
            'status' => TRUE,
            'message' => 'Following are the vendors',
            'data' => $this->vendor_model->fetchVendors($this->user_data['CompID'],$subscription_end_date,30, $offset,$filter,false, $vendor_name)
        ], 200);
    }

    public function get_vendor_data(){
        $vendor_id = $this->request->getGet('VendorID');
        if(empty($vendor_id)){
            return $this->respond([
                 'status' => false,
                 'msg' => 'Vendor ID is required',
                 'data' => []
             ], 501);
            return false;
        }else{
            $comp_id = $this->user_data['CompID'];
            $vendor_data = $this->vendor_model->fetchVendorData($vendor_id, $comp_id);
            if(empty($vendor_data)){
                return $this->respond([
                     'status' => false,
                     'msg' => 'Either the vendor does not exist or does not belong to you.',
                     'data' => []
                 ], 404);
                return false;
            }else{
                return $this->respond([
                     'status' => true,
                     'msg' => 'Following are the vendor details.',
                     'data' => [
                        'vendor_data' => $vendor_data,
                        'vendor_service_taxes' => $this->vendor_model->fetchVendorServiceTaxes($vendor_id, $comp_id)
                     ]
                 ], 200);
                return false;
            }
        }
    }
}