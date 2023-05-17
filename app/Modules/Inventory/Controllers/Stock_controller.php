<?php

namespace Modules\Inventory\Controllers;

use App\Libraries\Php_spreadsheets;

class Stock_controller extends \CodeIgniter\Controller {
    function __construct()
	{
		$this->session = \Config\Services::session();
        
        $this->userdata = $this->session->get('user_data');
        if(empty($this->userdata['ID'])){
            header('location:'.base_url().'login');
            exit();
        }

        $this->stock_model = new \Modules\Inventory\Models\Stock_model();
        $this->item_model = new \Modules\Inventory\Models\Item_model();
        $this->vendor_model = new \Modules\Vendors\Models\Vendor_model();

        $this->form_validation = \Config\Services::validation();
	}

    public function saveStock(){
        $comp_id = $this->userdata['CompID'];
        $subscription_time_left = subscription_time_left();

        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start adding stocks.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];

        if(!empty($_POST)){
            $this->form_validation->setRule('Item', 'Item', 'required');
            $this->form_validation->setRule('InvoiceNo', 'Invoice No', 'permit_empty|validateVendorInvoiceStockData['.$this->request->getPost('VendorID').']');
            $this->form_validation->setRule('BuyingPricePerUnit', 'Buying Price Per Unit', 'required|regex_match[/^\d*[.]?\d+$/m]',['regex_match' => 'Paid Amount should either be decimal or number.']);
            $this->form_validation->setRule('Price', 'Price', 'required|regex_match[/^\d*[.]?\d+$/m]',['regex_match' => 'Paid Amount should either be decimal or number.']);
            $this->form_validation->setRule('Qty', 'Qty', 'required|numeric');
            $this->form_validation->setRule('BatchNo', 'Batch No', 'required|duplicateBatchNo');
            $this->form_validation->setRule('Tax.*', 'Tax', 'required');
            $this->form_validation->setRule('TaxPercentage.*', 'Tax Percentage', 'required');

            if($this->request->getPost('ExpiryDate')){
                if(date('Y-m-d', strtotime($this->request->getPost('ExpiryDate'))) < date('Y-m-d') ){
                    $this->form_validation->setRule('ExpiryDate', 'Expiry Date', 'valid_email',['valid_email' => 'Expiry Date should be greater then current date']);
                }
            }

            if(!empty($_POST['ExpiryReminderDate'])){
                if (date('Y-m-d',strtotime($_POST['ExpiryReminderDate'])) < date('Y-m-d')) {
                    $this->form_validation->setRule('ExpiryReminderDate', 'Start Expiry Reminder From', 'valid_email',['valid_email' => 'Start Expiry Reminder From should be greater then current date.']);
                }

                if (date('Y-m-d',strtotime($_POST['ExpiryReminderDate'])) > date('Y-m-d',strtotime($_POST['ExpiryDate']))) {
                    $this->form_validation->setRule('ExpiryReminderDate', 'Start Expiry Reminder From', 'valid_email',['valid_email' => 'Start Expiry Reminder From cannot be greater then expiry date.']);
                }
            }

            $item = $this->request->getPost('Item');
            
            if(!empty($item)){
                $item_details = $this->item_model->fetchItemDataViaItemName($item,$comp_id);
                if(empty($item_details)){
                    echo "Either the item does not exist or does not belong to you.";
                    exit;
                }
            }

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $item_id = $this->request->getPost('ItemID');
                $qty = $this->request->getPost('Qty');

                $item_data = [
                    'BuyingPrice' => $this->request->getPost('BuyingPricePerUnit'),
                    'Price' => $this->request->getPost('Price'),
                    'Qty' => $item_details['Qty'] + $qty,
                    'UpdatedBy' => $this->userdata['ID'],
                    'UpdatedDate' => date('Y-m-d H:i:s'),
                ];

                $stock_inward_log_data = [
                    'VendorID' => (!empty($this->request->getPost('VendorID')))?$this->request->getPost('VendorID'):null,
                    'Item' => $item,
                    'HSN' => (!empty($item_details['HSN']))?$item_details['HSN']:null,
                    'BatchNo' => ($this->request->getPost('BatchNo'))?$this->request->getPost('BatchNo'):null,
                    'BuyingPricePerUnit' => $this->request->getPost('BuyingPricePerUnit'),
                    'Qty' => $qty,
                    'RemainingQty' => $qty,
                    'InwardDate' => date('Y-m-d H:i:s'),
                    'CompID' => $comp_id,
                    'InvoiceNo' => (!empty($this->request->getPost('InvoiceNo')))?$this->request->getPost('InvoiceNo'):null,
                    'ExpiryDate' => (!empty($this->request->getPost('ExpiryDate')))?$this->request->getPost('ExpiryDate'):null,
                    'ExpiryReminderDate' => (!empty($this->request->getPost('ExpiryReminderDate')))?$this->request->getPost('ExpiryReminderDate'):null,
                    'NextReminderDate' => (!empty($this->request->getPost('ExpiryReminderDate')))?$this->request->getPost('ExpiryReminderDate'):null,
                    'ManufacturingDate' => (!empty($this->request->getPost('ManufacturingDate')))?$this->request->getPost('ManufacturingDate'):null,
                    'AddedBy' => $this->userdata['ID'],
                    'AddedDate' => date('Y-m-d H:i:s'),
                ];

                $stock_inward_history_id = $this->stock_model->saveInwardLogData($stock_inward_log_data);


                $stock_inward_report_data = [
                    'CompID' => $comp_id,
                    'Item' => $item,
                    'HSN' => $stock_inward_log_data['HSN'],
                    'InwardStockQty' => $qty,
                    'ReportDate' => date('Y-m-d')
                ];

                $this->stock_model->saveInwardOutwardReports($stock_inward_report_data);

                $this->item_model->saveItem($item_data, $comp_id, $item_id);

                $expense_model = new \Modules\Finance\Models\Expense_model();

                $expense_head_master_id = $expense_model->fetcExpenseHeadDataViaExpenseHeadName('Stock Purchase');

                $vendor_data = $this->vendor_model->fetchVendorData($stock_inward_log_data['VendorID'], $comp_id);
                
                $vendor_name = (!empty($vendor_data['VendorName']))?$vendor_data['VendorName']:'Unknown Vendor';

                $expense_data = [
                    'ExpenseHeadMasterID' => $expense_head_master_id['ExpenseHeadMasterID'],
                    'CompID' => $this->userdata['CompID'],
                    'VendorID' => $stock_inward_log_data['VendorID'],
                    'ExpenseDate' => date('Y-m-d H:i:s'),
                    'ExpenseAmount' => $stock_inward_log_data['BuyingPricePerUnit'] * $stock_inward_log_data['Qty'],
                    'InvoiceNo' => $stock_inward_log_data['InvoiceNo'],
                    'Remarks' => $stock_inward_log_data['Qty'].' qty of '.$item.' Stocks inwarded from '.$vendor_name,
                    'AddedBy' => $this->userdata['ID'],
                    'AddedDate' => date('Y-m-d H:i:s')
                ];

                $expense_id = $expense_model->saveExpense($expense_data);

                for($i=0;$i<count($_POST['Tax']);$i++){
                    if(!empty($_POST['Tax'][$i]) && isset($_POST['TaxPercentage'][$i])){
                        $stock_inward_taxes_data = [
                            'StockInwardHistoryID' => $stock_inward_history_id,
                            'Tax' => $_POST['Tax'][$i],
                            'TaxPercentage' => (!empty($_POST['TaxPercentage'][$i]))?$_POST['TaxPercentage'][$i]:'0',
                        ];

                        $expenses_taxes_data = [
                            'ExpenseID' => $expense_id,
                            'Tax' => $_POST['Tax'][$i],
                            'TaxPercentage' => (!empty($_POST['TaxPercentage'][$i]))?$_POST['TaxPercentage'][$i]:'0',
                        ];

                        $stock_inward_taxes_data_arr[] = $stock_inward_taxes_data;
                        $expenses_taxes_data_arr[] = $expenses_taxes_data;
                    }
                }

                $this->stock_model->saveInwardTaxesData($stock_inward_taxes_data_arr);
                $expense_model->saveExpenseTaxesData($expenses_taxes_data_arr);

                $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Stock Updated Successfully!']);
                $this->response->redirect(base_url('add-stock'));
            }
        }

        $posted_vendor_id = $this->request->getPost('VendorID');

        $data = [
            'vendors' => $this->vendor_model->fetchVendors($comp_id,$subscription_end_date,30,0,[],false,$posted_vendor_id),
            'goods' => $this->item_model->fetchAllGoods($comp_id),
            'add_bel_global_js' => base_url('assets/js/finance.js')
        ];
        return default_view('\Modules\Inventory\Views\save_stock',$data);
    }

    public function stockInwardHistory(){
        $data = [
            'vendor_id' => $this->request->getGet('vendor_id'),
            'add_bel_global_js' => base_url('assets/js/item.js')
        ];

        return default_view('\Modules\Inventory\Views\stock_inward_history',$data);
    }

    public function stockOutwardHistory(){
        return default_view('\Modules\Inventory\Views\stock_outward_history');
    }

    public function stockInwardOutwardReports(){
        $data = [
            'add_bel_global_js' => base_url('assets/js/item.js')
        ];

        return default_view('\Modules\Inventory\Views\stock_inward_outward_reports', $data);
    }

    public function viewExpiringItems(){
        $comp_id = $this->userdata['CompID'];
        
        $data = [
            'vendors' => $this->stock_model->fetchExpiringItemsVendors($comp_id),
            'add_bel_global_js' => base_url('assets/js/finance.js')
        ];

        return default_view('\Modules\Inventory\Views\view_expiring_items',$data);
    }

    public function viewReturnedExpiringItems(){
        $comp_id = $this->userdata['CompID'];
        
        $data = [
            'vendors' => $this->stock_model->fetchExpiringItemsReturnedVendors($comp_id),
            'min_max_return_dates' => $this->stock_model->fetchMinMaxReturnDates($comp_id),
            'add_bel_global_js' => base_url('assets/js/finance.js')
        ];

        return default_view('\Modules\Inventory\Views\view_returned_expiring_items', $data);
    }

    public function exportExpiringItems(){
        $comp_id = $this->userdata['CompID'];
        $app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];
        $expiry_days_arr = ['7 days','14 days','28 days','30 days','3 months','6 months','1 year'];

        if(!empty($_GET['ExpiryDays']) && in_array($expiry_days, $expiry_days_arr)){
            $expiry_days = $this->request->getGet('ExpiryDays');
            $expiry_days = str_replace('s','',$expiry_days);
            $expiry_date = date('Y-m-d', strtotime('+'.$expiry_days));
        }else{
            $expiry_days = '';
            $expiry_date = '';
        }


        $filter = [
            'VendorID' => $this->request->getGet('VendorID'),
            'expiry_date' => $expiry_date,
            'expiry_days' => $expiry_days,
        ];

        $expiring_items_data = $this->stock_model->fetchExpiringItemsFullData($comp_id,$subscription_end_date,$filter);

        $headers = ['Item','Vendor','Batch No','Returnable Units','Manufacturing Date','Expiry Date'];

        $php_spreadsheets = new Php_spreadsheets();
        $php_spreadsheets->export_excel($headers,$expiring_items_data);
    }
}

?>