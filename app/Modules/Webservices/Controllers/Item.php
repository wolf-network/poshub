<?php 

namespace Modules\Webservices\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\respondTrait;

class Item extends ResourceController {
    function __construct()
    {
        $this->session = \Config\Services::session();
        $this->user_data = $this->session->get('user_data');
        if(empty($this->user_data)){
            return $this->respond([
                 'status' => true,
                 'msg' => 'Invalid User',
                 'data' => []
             ], 401);
            return false;
        }

        $this->item_model = new \Modules\Inventory\Models\Item_model();
        $this->form_validation = \Config\Services::validation();
    }

    public function get_items(){
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

        $sort_by = '';
        $sort_order = $this->request->getGet('sSortDir_0');

        switch ($this->request->getGet('iSortCol_0')) {
            case '1':
                $sort_by = 'I.Item';
                break;
            case '2':
                $sort_by = 'I.ItemType';
                break;
            case '3':
                $sort_by = 'I.Price';
                break;
            case '4':
                $sort_by = 'IT.TaxPercentage';
                break;
            case '5':
                $sort_by = 'I.HSN';
                break;
            
            default:
                // code...
                break;
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        $total_records = $this->item_model->fetchItemList($comp_id,$subscription_end_date,0,0,$filter,true);
        $item_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->item_model->fetchItemList($comp_id,$subscription_end_date,$limit,$offset,$filter,0,$sort_by,$sort_order)
        ];
        
        return $this->respond($item_list, 200);
    }

    public function save_item(){

        $subscription_time_left = subscription_time_left();
         if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){

            return $this->respond([
                 'status' => true,
                 'msg' => 'Kindly Re-new your subscription to start adding Items.',
                 'data' => []
            ], 403);
        }else{
            $comp_id = $this->user_data['CompID'];

            $this->form_validation->setRule('Item', 'Item', 'required|duplicateItem[0]');
            $this->form_validation->setRule('BuyingPrice', 'Buying Price', 'regex_match[/^\d*[.]?\d+$/m]',['regex_match' => 'Buying Price should either be decimal or number.']);
            $this->form_validation->setRule('Price', 'Price', 'required|regex_match[/^\d*[.]?\d+$/m]',['regex_match' => 'Price should either be decimal or number.']);
            $this->form_validation->setRule('ItemType', 'ItemType', 'required|in_list[Good,Service]');
            $this->form_validation->setRule('BarcodeNo', 'Barcode No', 'duplicateBarcode[0]');
            $this->form_validation->setRule('HSN', 'HSN', 'required');
            $this->form_validation->setRule('Tax.*', 'HSN', 'required');
            $this->form_validation->setRule('TaxPercentage.*', 'HSN', 'required|numeric');

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $item_data = [
                    'CompID' => $comp_id,
                    'Item' => $this->request->getPost('Item'),
                    'BuyingPrice' => $this->request->getPost('BuyingPrice'),
                    'Price' => $this->request->getPost('Price'),
                    'ItemType' => $this->request->getPost('ItemType'),
                    'BarcodeNo' => $this->request->getPost('BarcodeNo'),
                    'HSN' => $this->request->getPost('HSN'),
                    'AddedBy' => $this->user_data['ID'],
                    'AddedDate' => date('Y-m-d H:i:s')
                ];

                $item_id = $this->item_model->saveItem($item_data,$comp_id);

                for($i=0;$i<count($_POST['Tax']);$i++){
                    $item_tax_data = [
                        'ItemID' => $item_id,
                        'Tax' => $_POST['Tax'][$i],
                        'TaxPercentage' => $_POST['TaxPercentage'][$i],
                    ];

                    $item_tax_data_arr[] = $item_tax_data;
                }

                if(!empty($item_tax_data_arr)){
                    $this->item_model->saveItemTaxesBulk($item_tax_data_arr);
                }

                return $this->respond([
                    'status' => true,
                    'msg' => 'Item Added Successfully!',
                    'data' => $item_id
                 ], 200);
            }else{
                return $this->respond([
                    'status' => false,
                    'msg' => 'Kindly fix the following validation errors.',
                    'data' => [],
                    'err' => validation_errors()
                ],501);
            }
        }
    }

    public function get_item_categories(){
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

        $sort_by = '';
        $sort_order = $this->request->getGet('sSortDir_0');

        switch ($this->request->getGet('iSortCol_0')) {
            case '1':
                $sort_by = 'ICM.ItemCategory';
                break;
            
            default:
                // code...
                break;
        }

        $app = env('app');
        $subscription_end_date = $this->user_data['apps'][$app]['SubscriptionEndDate'];

        $total_records = $this->item_model->fetchItemCategoryList($comp_id,$subscription_end_date,0,0,$filter,true);
        $item_category_list = [
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $this->item_model->fetchItemCategoryList($comp_id,$subscription_end_date,$limit,$offset,$filter,0,$sort_by,$sort_order)
        ];
        
        return $this->respond($item_category_list, 200);
    }

    public function save_item_category(){

        $subscription_time_left = subscription_time_left();
         if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){

            return $this->respond([
                 'status' => true,
                 'msg' => 'Kindly Re-new your subscription to start adding Item Category.',
                 'data' => []
            ], 403);
        }else{
            $comp_id = $this->user_data['CompID'];

            $this->form_validation->setRule('ItemCategory', 'ItemCategory', 'required|duplicateItemCategory[0]');


            if ($this->form_validation->withRequest($this->request)->run())
            {
                $item_category_data = [
                    'ItemCategory' => $this->request->getPost('ItemCategory'),
                    'CompID' => $comp_id,
                    'AddedBy' => $this->user_data['ID'],
                    'AddedDate' => date('Y-m-d H:i:s')
                ];

                $item_category_master_id = $this->item_model->saveItemCategory($item_category_data,$comp_id);

                return $this->respond([
                    'status' => true,
                    'msg' => 'Item Category Added Successfully!',
                    'data' => [
                        'ItemCategoryMasterID' => $item_category_master_id
                    ]
                 ], 200);
            }else{
                return $this->respond([
                    'status' => false,
                    'msg' => 'Kindly fix the following validation errors.',
                    'data' => [],
                    'err' => validation_errors()
                ],501);
            }
        }
    }
}