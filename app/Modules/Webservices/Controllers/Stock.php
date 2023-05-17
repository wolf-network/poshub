<?php

namespace Modules\Webservices\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Stock extends ResourceController {
    function __construct()
    {
        $this->session = \Config\Services::session();
        $this->user_data = $this->session->get('user_data');
        if(empty($this->user_data['ID'])){
            return $this->respond([
                'status' => 401,
                'msg' => 'Invoice Date is required!',
                'error' => '',
             ]);
        }

        $this->stock_model = new \Modules\Inventory\Models\Stock_model();
    }

    public function get_inward_logs(){
        $offset = $this->request->getGet('iDisplayStart');

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

            $limit = $this->request->getGet('iDisplayLength');
            $filter = [
                'search_txt' => $this->request->getGet('sSearch'),
                'VendorID' => $this->request->getGet('VendorID'),
                'InwardDateFrom' => $this->request->getGet('InwardDateFrom'),
                'InwardDateTo' => $this->request->getGet('InwardDateTo'),
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
                $sort_by = 'SIL.Item';
                break;
            case '3':
                $sort_by = 'SIL.HSN';
                break;
            case '4':
                $sort_by = 'SIL.BuyingPricePerUnit';
                break;
            case '5':
                $sort_by = 'SIL.Qty';
                break;
            case '6':
                $sort_by = '(SIL.BuyingPricePerUnit * SIL.Qty)';
                break;
            case '7':
                $sort_by = 'SIL.InvoiceNo';
                break;
            case '7':
                $sort_by = 'SIL.InwardDate';
                break;
            default:
                // code...
                break;
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        $total_records = $this->stock_model->fetchInwardHistory($this->user_data['CompID'],$this->user_data,$subscription_end_date,0,0,$filter,true);
        $inward_history_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->stock_model->fetchInwardHistory($this->user_data['CompID'],$this->user_data,$subscription_end_date,$limit,$offset,$filter,0,$sort_by,$sort_order)
        ];
        
        return $this->respond($inward_history_list, 200);
    }

    public function get_outward_logs(){
        $offset = $this->request->getGet('iDisplayStart');

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

            $limit = $this->request->getGet('iDisplayLength');
            $filter = [
                'search_txt' => $this->request->getGet('sSearch'),
                'OutwardDateFrom' => $this->request->getGet('OutwardDateFrom'),
                'OutwardDateTo' => $this->request->getGet('OutwardDateTo')
            ];
        }else{
            $limit = 10;
            $filter = [];
        }

        $sort_by = '';
        $sort_order = $this->request->getGet('sSortDir_0');

        switch ($this->request->getGet('iSortCol_0')) {
            case '1':
                $sort_by = 'ID.Particular';
                break;
            case '2':
                $sort_by = 'ID.HSN';
                break;
            case '3':
                $sort_by = 'ID.SerialNo';
                break;
            case '4':
                $sort_by = 'ID.PricePerUnit';
                break;
            case '5':
                $sort_by = 'ID.Quantity';
                break;
            case '6':
                $sort_by = 'ID.TotalTaxPercentage';
                break;
            case '7':
                $sort_by = '(ID.PricePerUnit * ID.Quantity * ID.TotalTaxPercentage)';
                break;
            case '8':
                $sort_by = 'ID.TotalAmount';
                break;
            case '9':
                $sort_by = 'I.ClientInvoiceDate';
                break;
            default:
                // code...
                break;
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        $total_records = $this->stock_model->fetchOutwardHistory($this->user_data['CompID'],$this->user_data,$subscription_end_date,0,0,$filter,true);
        $outward_history_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->stock_model->fetchOutwardHistory($this->user_data['CompID'],$this->user_data,$subscription_end_date,$limit,$offset,$filter,0,$sort_by,$sort_order)
        ];
        
        return $this->respond($outward_history_list, 200);
    }

    public function get_inward_outward_reports(){
        $offset = $this->request->getGet('iDisplayStart');

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

            $limit = $this->request->getGet('iDisplayLength');
            $filter = [
                'search_txt' => $this->request->getGet('sSearch'),
                'ReportDateFrom' => $this->request->getGet('ReportDateFrom'),
                'ReportDateTo' => $this->request->getGet('ReportDateTo'),
            ];
        }else{
            $limit = 10;
            $filter = [];
        }

        $sort_by = '';
        $sort_order = $this->request->getGet('sSortDir_0');

        switch ($this->request->getGet('iSortCol_0')) {
            case '1':
                $sort_by = 'IOR.Item';
                break;
            case '2':
                $sort_by = 'IOR.HSN';
                break;
            case '3':
                $sort_by = 'IOR.OpeningStockQty';
                break;
            case '4':
                $sort_by = 'IOR.InwardStockQty';
                break;
            case '5':
                $sort_by = 'IOR.OutwardStockQty';
                break;
            case '6':
                $sort_by = 'IOR.ClosingStockQty';
                break;
            case '7':
                $sort_by = 'IOR.ReportDate';
                break;
            default:
                // code...
                break;
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        $total_records = $this->stock_model->fetchInwardOutwardReports($this->user_data['CompID'],$subscription_end_date,0,0,$filter,true);
        $inward_outward_report_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->stock_model->fetchInwardOutwardReports($this->user_data['CompID'],$subscription_end_date,$limit,$offset,$filter,0,$sort_by,$sort_order)
        ];
        
        return $this->respond($inward_outward_report_list, 200);
    }

    public function get_expiring_items_count(){
        $expiry_days = $this->request->getGet('ExpiryDays');

        if(empty($expiry_days)){
            return $this->respond([
                'status' => 401,
                'msg' => 'Report Date is required!',
                'error' => '',
             ]);
        }

        $expiry_days_arr = ['7 days','14 days','28 days','30 days','3 months','6 months','1 year'];
        if(!in_array($expiry_days, $expiry_days_arr)){
            return $this->respond([
                'status' => 501,
                'msg' => 'Expiring Date is not valid',
                'error' => '',
             ]);
        }

        $comp_id = $this->user_data['CompID'];
        $expiry_days = str_replace('s','',$expiry_days);
        $expiry_date = date('Y-m-d', strtotime('+'.$expiry_days));

        return $this->respond([
            'status' => 200,
            'msg' => 'Following is the count of total expiring items!',
            'data' => $this->stock_model->fetchExpiringItemsCount($comp_id,$expiry_date, $expiry_days),
         ]);
    }

    public function get_expiring_items(){
        $offset = $this->request->getGet('iDisplayStart');

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

            $limit = $this->request->getGet('iDisplayLength');
            $expiry_days = $this->request->getGet('ExpiryDays');

            $expiry_days_arr = ['7 days','14 days','28 days','30 days','3 months','6 months','1 year'];
            if(in_array($expiry_days, $expiry_days_arr)){
                $expiry_days = str_replace('s','',$expiry_days);
                $expiry_date = date('Y-m-d', strtotime('+'.$expiry_days));
            }else{
                $expiry_date = '';
            } 

            $filter = [
                'search_txt' => $this->request->getGet('sSearch'),
                'VendorID' => $this->request->getGet('VendorID'),
                'expiry_date' => $expiry_date,
                'expiry_days' => $expiry_days
            ];
        }else{
            $limit = 10;
            $filter = [];
        }

        $sort_by = '';
        $sort_order = $this->request->getGet('sSortDir_0');

        switch ($this->request->getGet('iSortCol_0')) {
            case '1':
                $sort_by = 'SIL.Item';
                break;
            case '2':
                $sort_by = 'V.VendorName';
                break;
            case '3':
                $sort_by = 'SIL.BatchNo';
                break;
            case '4':
                $sort_by = 'SIL.RemainingQty';
                break;
            case '5':
                $sort_by = 'SIL.ExpiryDate';
                break;
            default:
                // code...
                break;
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        $total_records = $this->stock_model->fetchExpiringItems($this->user_data['CompID'],$subscription_end_date,0,0,$filter,true);
        $expiring_items_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->stock_model->fetchExpiringItems($this->user_data['CompID'],$subscription_end_date,$limit,$offset,$filter,0,$sort_by,$sort_order)
        ];
        
        return $this->respond($expiring_items_list, 200);
    }

    public function get_expiring_items_vendors(){
        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

            $expiry_days = $this->request->getGet('ExpiryDays');

            $expiry_days_arr = ['7 days','14 days','28 days','30 days','3 months','6 months','1 year'];
            if(in_array($expiry_days, $expiry_days_arr)){
                $expiry_days = str_replace('s','',$expiry_days);
                $expiry_date = date('Y-m-d', strtotime('+'.$expiry_days));
            }else{
                $expiry_date = '';
            }

            $comp_id = $this->user_data['CompID'];

            return $this->respond([
                'status' => true,
                'msg' => 'Following are the vendors list of expiring items.',
                'data' => $this->stock_model->fetchExpiringItemsVendors($comp_id,$expiry_date, $expiry_days),
            ], 200);
        }else{
            return $this->respond([
                'status' => true,
                'msg' => 'Please re-new your subscription to access the vendors list of expiring items.',
                'err' => '',
            ], 403);
        }
    }

    public function save_returned_expiring_item_post(){
        $this->form_validation->set_rules('StockInwardHistoryID', 'Stock Inward History ID', 'required|numeric');

        $units_returned_extra_validation = '';

        if(!empty($_POST['StockInwardHistoryID'])){
            $inwarded_stock_data = $this->stock_model->fetchInwardedStockData($_POST['StockInwardHistoryID']);
            $units_returned_extra_validation = '|less_than_equal_to['.$inwarded_stock_data['RemainingQty'].']';
        }

        $this->form_validation->set_rules('UnitsReturned', 'Units Returned', 'required|numeric'.$units_returned_extra_validation,['less_than_equal_to' => 'You can only return '.$inwarded_stock_data['RemainingQty'].' units from this batch.']);

        $this->form_validation->set_rules('ReturnDate', 'Return Date', 'required');

        if(!empty($_POST['ReturnDate'])){
            if (date('Y-m-d',strtotime($_POST['ReturnDate'])) > date('Y-m-d')) {
                $this->form_validation->set_rules('ReturnDate', 'Return Date', 'valid_email',['valid_email' => 'Return Date should be greater then current date.']);            
            }

            if(!empty($inwarded_stock_data['ExpiryDate'])){
                if (date('Y-m-d',strtotime($_POST['ReturnDate'])) > $inwarded_stock_data['ExpiryDate']) {
                    $this->form_validation->set_rules('ReturnDate', 'Return Date', 'valid_email',['valid_email' => 'Return Date cannot be greater then expiry date, which is '.$inwarded_stock_data['ExpiryDate'].'.']);            
                }
            }
        }

        $this->form_validation->set_rules('VendorRepresentativeName', 'Vendor representative name', 'required');
        $this->form_validation->set_rules('VendorRepresentativeEmail', 'Vendor representative email', 'required');

        if ($this->form_validation->run() != FALSE)
        {
            $this->load->model('finance/finance_model');

            $comp_id = $this->user_data['CompID'];
            $item = $inwarded_stock_data['Item'];
            $units_returned = $this->input->post('UnitsReturned');

            $returned_expiring_item_data = [
                'CompID' => $comp_id,
                'Item' => $item,
                'Vendor' => $inwarded_stock_data['VendorName'],
                'BatchNo' => $inwarded_stock_data['BatchNo'],
                'UnitsReturned' => $units_returned,
                'ReturnDate' => $this->input->post('ReturnDate'),
                'VendorRepresentativeName' => $this->input->post('VendorRepresentativeName'),
                'VendorRepresentativeEmail' => $this->input->post('VendorRepresentativeEmail'),
                'AddedBy' => $this->user_data['ID'],
                'AddedDate' => date('Y-m-d H:i:s')
            ];

            $this->stock_model->saveReturnedExpiringItem($returned_expiring_item_data);
            $this->stock_model->updateStockInwardLogQty($comp_id,$item,$units_returned);
            $this->finance_model->reduceItemQty($item,$units_returned,$comp_id);

            return $this->respond([
                'status' => true,
                'msg' => 'Returned expiring item data saved successfully!',
                'data' => [],
            ],200);
        }else{
            return $this->respond([
                'status' => false,
                'msg' => 'Kindly fix the following validation errors.',
                'data' => [],
                'err' => $this->form_validation->error_array()
            ],501);
        }
    }

    public function get_returned_expiring_items(){
        $offset = $this->request->getGet('iDisplayStart');

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){

            $limit = $this->request->getGet('iDisplayLength');
            $expiry_days = $this->request->getGet('ExpiryDays');

            $expiry_days_arr = ['7 days','14 days','28 days','30 days','3 months','6 months','1 year'];
            if(in_array($expiry_days, $expiry_days_arr)){
                $expiry_days = str_replace('s','',$expiry_days);
                $expiry_date = date('Y-m-d', strtotime('+'.$expiry_days));
            }else{
                $expiry_date = '';
            } 

            $filter = [
                'search_txt' => $this->request->getGet('sSearch'),
                'Vendor' => $this->request->getGet('Vendor'),
                'ReturnDateFrom' => $this->request->getGet('ReturnDateFrom'),
                'ReturnDateTo' => $this->request->getGet('ReturnDateTo'),
            ];
        }else{
            $limit = 10;
            $filter = [];
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        $total_records = $this->stock_model->fetchReturnedExpiringItems($this->user_data['CompID'],$subscription_end_date,0,0,$filter,true);
        $returned_expiring_items_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->stock_model->fetchReturnedExpiringItems($this->user_data['CompID'],$subscription_end_date,$limit,$offset,$filter,0)
        ];
        
        return $this->respond($returned_expiring_items_list, 200);
    }
}