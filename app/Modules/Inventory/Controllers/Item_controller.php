<?php

namespace Modules\Inventory\Controllers;

class Item_controller extends \CodeIgniter\Controller {
    function __construct()
	{
        $this->session = \Config\Services::session();
        $this->userdata = $this->session->get('user_data');
        if(empty($this->userdata['ID'])){
            header('location:'.base_url().'login');
            exit();
        }

        $this->item_model = new \Modules\Inventory\Models\Item_model();
        $this->form_validation = \Config\Services::validation();
	}

    public function saveItem($item_id = 0){
        if(!empty($item_id) && $this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page.";
            exit;
        }

        $comp_id = $this->userdata['CompID'];

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start managing Items.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        if(!empty($item_id)){
            $item_details = $this->item_model->fetchItemData($item_id,$comp_id);
            if(empty($item_details)){
                echo "Either the item does not exist or does not belong to you.";
                exit;
            }
        }

        if(!empty($_POST)){
            $this->form_validation->setRule('Item', 'Item', 'required|duplicateItem['.$item_id.']');
            $this->form_validation->setRule('ItemType', 'Type', 'required|in_list[Good,Service]');
            $this->form_validation->setRule('BarcodeNo', 'Barcode No', 'duplicateBarcode['.$item_id.']');
            $this->form_validation->setRule('BuyingPrice', 'Buying Price', 'permit_empty|regex_match[/^\d*[.]?\d+$/m]',['regex_match' => 'Buying Price should either be decimal or number.']);
            $this->form_validation->setRule('Price', 'Price', 'required|regex_match[/^\d*[.]?\d+$/m]',['regex_match' => 'Price should either be decimal or number.']);
            $this->form_validation->setRule('HSN', 'HSN', 'required');
            $this->form_validation->setRule('Tax.*', 'Tax', 'required');
                    $this->form_validation->setRule('TaxPercentage.*', 'Tax Rate', 'required|numeric');

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $item_data = [
                    'CompID' => $comp_id,
                    'Item' => $this->request->getPost('Item'),
                    'ItemType' => $this->request->getPost('ItemType'),
                    'ItemCategoryMasterID' => ($this->request->getPost('ItemCategoryMasterID'))?$this->request->getPost('ItemCategoryMasterID'):null,
                    'BarcodeNo' => ($this->request->getPost('BarcodeNo'))?$this->request->getPost('BarcodeNo'):null,
                    'BuyingPrice' => ($this->request->getPost('BuyingPrice'))?$this->request->getPost('BuyingPrice'):null,
                    'Price' => $this->request->getPost('Price'),
                    'HSN' => $this->request->getPost('HSN')
                ];

                if(empty($item_id)){
                    $item_data['AddedBy'] = $this->userdata['ID'];
                    $item_data['AddedDate'] = date('Y-m-d H:i:s');
                }else{
                    $item_data['UpdatedBy'] = $this->userdata['ID'];
                    $item_data['UpdatedDate'] = date('Y-m-d H:i:s');
                }

                $item_id = $this->item_model->saveItem($item_data, $comp_id, $item_id);

                for($i=0;$i<count($_POST['Tax']);$i++){
                    if(!empty($_POST['Tax'][$i]) && isset($_POST['TaxPercentage'][$i])){
                        $item_tax_data = [
                            'ItemID' => $item_id,
                            'Tax' => $_POST['Tax'][$i],
                            'TaxPercentage' => (!empty($_POST['TaxPercentage'][$i]))?$_POST['TaxPercentage'][$i]:'0',
                        ];

                        $item_tax_data_arr[] = $item_tax_data;
                    }
                }

                if(!empty($item_tax_data_arr)){
                    $this->item_model->saveItemTaxesBulk($item_tax_data_arr); 
                }
                
                $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Item Saved Succesfully!']);
                $this->response->redirect(base_url('manage-items'));
            }
        }

        if(!empty($item_details)){
            foreach ($item_details as $form_key => $form_value) {
                if(empty($_POST[$form_key])){
                    $_POST[$form_key] = $form_value;
                }
            }

            $item_tax_details = $this->item_model->fetchItemTaxesDataViaItemID($item_id);

            for($i=0; $i <count($item_tax_details) ; $i++) { 
                foreach ($item_tax_details[$i] as $form_key => $form_value) {
                    if(empty($_POST[$form_key][$i])){
                        $_POST[$form_key][$i] = $form_value;
                    }
                }                  
            }
        }

        $app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];

        $data = [
            'item_categories' => $this->item_model->fetchAllItemCategories($comp_id,$subscription_end_date),
            'add_bel_global_js' => base_url('assets/js/item.js')
        ];
        return default_view('\Modules\Inventory\Views\save_item',$data);
    }

    public function manageItems(){
        $data = ['add_bel_global_js' => base_url('assets/js/item.js')];
        return default_view('\Modules\Inventory\Views\manage_items', $data);
    }

    public function manageItemCategories(){
        return default_view('\Modules\Inventory\Views\manage_item_categories');
    }

    public function saveItemCategory($item_category_master_id = 0){
        $comp_id = $this->userdata['CompID'];

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start managing Item Catgeories.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        if(!empty($item_category_master_id)){
            $item_category_details = $this->item_model->fetchItemCategoryData($item_category_master_id,$comp_id);
            if(empty($item_category_details)){
                echo "Either the item Category does not exist or does not belong to you.";
                exit;
            }
        }

        if(!empty($_POST)){
            $this->form_validation->setRule('ItemCategory', 'ItemCategory', 'required|duplicateItemCategory['.$item_category_master_id.']');

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $item_category_data = [
                    'CompID' => $comp_id,
                    'ItemCategory' => $this->request->getPost('ItemCategory'),
                ];

                if(empty($item_category_master_id)){
                    $item_category_data['AddedBy'] = $this->userdata['ID'];
                    $item_category_data['AddedDate'] = date('Y-m-d H:i:s');
                }else{
                    $item_category_data['UpdatedBy'] = $this->userdata['ID'];
                    $item_category_data['UpdatedDate'] = date('Y-m-d H:i:s');
                }

                $item_category_master_id = $this->item_model->saveItemCategory($item_category_data, $comp_id, $item_category_master_id);
                
                $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Item category Saved Succesfully!']);
                $this->response->redirect(base_url('manage-item-categories'));
            }
        }


        if(!empty($item_category_details)){
            foreach ($item_category_details as $form_key => $form_value) {
                $_POST[$form_key] = $form_value;
            }
        }

        $data = [
            'item_category_master_id' => $item_category_master_id,
            'add_bel_global_js' => base_url('assets/js/item.js')
        ];

        return default_view('\Modules\Inventory\Views\save_item_category',$data);
    }

    public function deleteItem($item_id){

        if($this->userdata['Privilege'] != 'Admin'){
            echo "Sorry, But you do not have authorization to access this page";
            exit;
        }

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start deleting Items.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $comp_id = $this->userdata['CompID'];

        $item_details = $this->item_model->fetchItemData($item_id,$comp_id);
        if(empty($item_details)){
            echo "Either the item does not exist or does not belong to you.";
            exit;
        }

        if(!empty($item_details)){
            $this->item_model->deleteItem($item_id,$comp_id);
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Item deleted successfully!']);
        }else{
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Either the Item Does not Exist or Does not Belong to You.']);
        }

        $this->response->redirect(base_url('manage-items'));
    }
}