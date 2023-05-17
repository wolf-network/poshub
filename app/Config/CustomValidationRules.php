<?php

namespace Config;

class CustomValidationRules
{

    function __construct()
    {
        $this->user_model = new \Modules\Layouts\Models\User_model();
        $this->registered_user_model = new \Modules\Registered_users\Models\Registered_user_model();
        $this->client_model = new \Modules\Clients\Models\Client_model();
        $this->vendor_model = new \Modules\Vendors\Models\Vendor_model();
        $this->item_model = new \Modules\Inventory\Models\Item_model();
        $this->stock_model = new \Modules\Inventory\Models\Stock_model();
        $this->finance_model = new \Modules\Finance\Models\Finance_model();
        $this->credit_note_model = new \Modules\Finance\Models\Credit_note_model();
        $this->company_model = new \Modules\Company\Models\Company_model();

        $this->request = \Config\Services::request();
        $this->session = \Config\Services::session();
        $this->userdata = $this->session->get('user_data');
    }

    public function validateCompany($comp_name, ?string &$error = null): bool
    {
        $company_details = $this->user_model->fetchCompDetailsViaName($comp_name);

        if(empty($company_details)){
            $error = lang('Company with this name does not exist in our database.');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    public function validateDuplicateCompany($comp_name, ?string &$error = null): bool
    {
        $firm_type_id = $this->request->getPost('FirmTypeID');
        $company_details = $this->user_model->fetchCompDetailsViaNameAndFirmType($comp_name,$firm_type_id);

        if(!empty($company_details)){
            $error = lang('Company with this name and firm type already exists in our database.');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    public function duplicateUser($email_id,$registered_user_id, $data, ?string &$error = null){

        $duplicate_user = $this->registered_user_model->duplicateUser($email_id,$registered_user_id, $this->userdata['CompID']);
        if(!empty($duplicate_user)){
            $error = lang('An user with same Email ID already exists in the database.');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    public function validateClient($client_name,$client_id, $data, ?string &$error = null){
        $check_client = $this->client_model->checkClient($client_name,$client_id,$this->userdata['CompID']);
        if (!empty($check_client))
        {
            $error = 'This Client already exists in the database.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function validateClientCIN($cin, $client_id, $data, ?string &$error = null){

        $check_cin = $this->client_model->checkClientCIN($cin, $client_id, $this->userdata['CompID']);

        if(!empty($check_cin)){
            $error = 'This CIN already exists for client named '.$check_cin['ClientName'].'.';
            return FALSE;
        }else{
            return TRUE;
        }
    }

    public function validateClientServiceTaxNumber($service_tax_number,$client_data, $data, ?string &$error = null){
        $client_data_arr = explode('-',$client_data);
        $client_id = $client_data_arr[0];
        $client_service_tax_id = $client_data_arr[1];
        
        $service_tax_type = $this->request->getPost('ServiceTaxTypeID');
        $check_client_service_tax_number = $this->client_model->checkClientServiceTaxNumber($service_tax_number,$client_service_tax_id,$client_id,$service_tax_type);
        if (!empty($check_client_service_tax_number))
        {
            $error = 'This Service Tax number already exists against this client in the database.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function validateClientServiceTaxLabel($label,$client_data, $data, ?string &$error = null){
        $client_data_arr = explode('-',$client_data);
        $client_id = $client_data_arr[0];
        $client_service_tax_id = $client_data_arr[1];
        
        $check_client_service_tax_label = $this->client_model->checkClientServiceTaxLabel($label,$client_service_tax_id,$client_id);
        if (!empty($check_client_service_tax_label))
        {
            $error = 'This Label already exists against a service tax for this client in the database.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function duplicateVendor($vendor_name,$vendor_id, $data, ?string &$error = null){
        $check_client = $this->vendor_model->checkDuplicateVendor($vendor_name,$this->userdata['CompID'],$vendor_id);
        if (!empty($check_client))
        {
            $error = 'This Vendor already exists in the database.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function validateVendorCIN($cin, $vendor_id, $data, ?string &$error = null){

        $check_cin = $this->vendor_model->checkVendorCIN($cin, $vendor_id, $this->userdata['CompID']);

        if(!empty($check_cin)){
            $error = 'This CIN already exists for vendor named '.$check_cin['VendorName'].'.';
            return FALSE;
        }else{
            return TRUE;
        }
    }

    public function validateVendorServiceTaxLabel($label,$vendor_data, $data, ?string &$error = null){
        $vendor_data_arr = explode('-',$vendor_data);
        $vendor_id = $vendor_data_arr[0];
        $vendor_service_tax_id = $vendor_data_arr[1];
        
        $check_vendor_service_tax_label = $this->vendor_model->checkVendorServiceTaxLabel($label,$vendor_service_tax_id,$vendor_id);
        if (!empty($check_vendor_service_tax_label))
        {
            $error = 'This Label already exists against a service tax for this vendor in the database.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function validateVendorServiceTaxNumber($service_tax_number,$vendor_data, $data, ?string &$error = null){
        $vendor_data_arr = explode('-',$vendor_data);
        $vendor_id = $vendor_data_arr[0];
        $vendor_service_tax_id = $vendor_data_arr[1];
        
        $service_tax_type = $this->request->getPost('ServiceTaxTypeID');
        $check_vendor_service_tax_number = $this->vendor_model->checkVendorServiceTaxNumber($service_tax_number,$vendor_service_tax_id,$vendor_id,$service_tax_type);
        if (!empty($check_vendor_service_tax_number))
        {
            $error = 'This Service Tax number already exists against this vendor in the database.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function duplicateItem($item,$item_id, $data, ?string &$error = null){
        $check_item = $this->item_model->checkDuplicateItem($item,$this->userdata['CompID'],$item_id);
        if (!empty($check_item))
        {
            $error = 'This Item already exists in the database.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function duplicateBarcode($barcode_no,$item_id, $data, ?string &$error = null){
        if(!empty($barcode_no)){
            $check_barcode = $this->item_model->checkDuplicateBarcode($barcode_no,$this->userdata['CompID'],$item_id);
            if (!empty($check_barcode))
            {
                $error = 'This Barcode already exists for an item in the database.';
                return FALSE;
            }
            else
            {
                return TRUE;
            }
        }else{
            return true;
        }
    }

    public function duplicateItemCategory($item_category,$item_category_master_id, $data, ?string &$error = null){

        $check_item_category = $this->item_model->checkDuplicateItemCategory($item_category,$this->userdata['CompID']);
        if (!empty($check_item_category))
        {
            $error = 'This Item category already exists in the database.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function duplicateBatchNo($batch_no, ?string &$error = null): bool{
        $item = $this->request->getPost('Item');
        $vendor_id = $this->request->getPost('VendorID');

        $fetched_batch_no = $this->stock_model->checkBatchNo($batch_no,$this->userdata['CompID'],$item,$vendor_id);
        
        if (!empty($fetched_batch_no))
        {
            $error = 'This Batch No already exists for this item and against this vendor in the database.';
            return FALSE;
        }else
        {
            return TRUE;
        }
    }

    public function checkQty($qty,$item, $data, ?string &$error = null){
        $check_item_qty = $this->finance_model->checkItemQty($item,$this->userdata['CompID']);

        if (!empty($check_item_qty) && $check_item_qty['ItemType'] != 'Good'){
            return TRUE;
        }

        if (!empty($check_item_qty) && $check_item_qty['Qty'] == 0)
        {
            $error = 'This Item is out of stock.';
            return FALSE;
        } else if(!empty($check_item_qty) && $check_item_qty['Qty'] < $qty){

            $error = 'Your selected qty exceeds the limit of qty in stock.';

            return FALSE;
        } else if(empty($check_item_qty)){
            $error = 'Either the selected particular does not exist or does not belong to you.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function duplicateInvoice($invoice_no, ?string &$error = null): bool{
        
        $check_invoice = $this->finance_model->checkInvoiceNo($invoice_no,$this->userdata['CompID']);
        
        if (!empty($check_invoice))
        {
            $error = 'This Invoice No already exists in the database.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function duplicatePurchaseOrder($purchase_order_no, ?string &$error = null): bool{

        $check_purchase_order = $this->purchase_order_model->checkPurchaseOrderNo($purchase_order_no,$this->userdata['CompID']);
        if (!empty($check_purchase_order))
        {
            $error = 'This Purchase Order No already exists in the database.';
            
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function checkSoldQty($qty,$extra_data, $data, ?string &$error = null){
        $comp_id = $this->userdata['CompID'];
        $extra_data_arr = explode('|',$extra_data);
        $particular_index = $extra_data_arr[0];
        $invoice_id = $extra_data_arr[1];
        $particular = $extra_data_arr[2];

        $check_item_qty = $this->credit_note_model->checkSoldQty($invoice_id,$particular,$comp_id);

        if (!empty($check_item_qty) && $check_item_qty['ParticularType'] != 'Good'){
            return TRUE;
        }

        if(!empty($check_item_qty) && $check_item_qty['Quantity'] < $qty){
            $error = 'Only '.$check_item_qty['Quantity'].' items can be returned for Particular['.$particular_index.'] .';
            return FALSE;
        } else if(empty($check_item_qty)){
            $error = 'Either the selected particular does not exist or does not belong to you.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function checkReturnableQty($qty,$extra_data, $data, ?string &$error = null){
        $comp_id = $this->userdata['CompID'];
        $extra_data_arr = explode('|',$extra_data);
        $particular_index = $extra_data_arr[0];
        $invoice_id = $extra_data_arr[1];
        $particular = $extra_data_arr[2];

        $check_item_qty = $this->credit_note_model->checkReturnedQty($invoice_id,$particular,$comp_id);

        if (!empty($check_item_qty) && $check_item_qty['ParticularType'] != 'Good'){
            return TRUE;
        }

        if(!empty($check_item_qty)){
            $returnable_qty = $check_item_qty['Quantity'] - $check_item_qty['returned_qty'];

            if($returnable_qty <= 0){
                $error = 'All items for '.$particular.' has been returned for particular['.$particular_index.'] .';
                return FALSE;
            }else{
                return TRUE;
            }

        } else if(empty($check_item_qty)){
            $error = 'Either the selected particular does not exist or does not belong to you.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function checkPricePerUnit($price_per_unit,$extra_data, $data, ?string &$error = null){
        $comp_id = $this->userdata['CompID'];
        $extra_data_arr = explode('|',$extra_data);
        $particular_index = $extra_data_arr[0];
        $invoice_id = $extra_data_arr[1];
        $particular = $extra_data_arr[2];

        $check_price_per_unit = $this->credit_note_model->checkPricePerUnit($invoice_id,$particular,$comp_id);

        if(!empty($check_price_per_unit)){
            $pre_discount_amt = $check_price_per_unit['PricePerUnit'];
            $discount = $check_price_per_unit['Discount'];
            $discounted_amt = $pre_discount_amt * $discount / 100;
            $refundable = $pre_discount_amt - $discounted_amt;

            if($refundable < $price_per_unit){
                $error = 'Price Per Unit cannot be more than '.$refundable.' for '.$particular.' for particular['.$particular_index.'] .';
                return FALSE;
            }else{
                return TRUE;
            }

        } else if(empty($check_price_per_unit)){
            $error = 'Either the selected particular does not exist or does not belong to you.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function checkInvoiceID($invoice_id, ?string &$error = null): bool{
        $comp_id = $this->userdata['CompID'];

        $check_invoice = $this->credit_note_model->checkInvoice($invoice_id,$comp_id);

        if(empty($check_invoice)){
            $error = 'Either the invoice does not exist or does not belong to you.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function validateCompanyServiceTaxNumber($service_tax_number,$company_service_tax_id, $data, ?string &$error = null){
        
        $comp_id = $this->userdata['CompID'];
        $service_tax_type = $this->request->getPost('ServiceTaxTypeID');
        $check_company_service_tax_number = $this->company_model->checkCompanyServiceTaxNumber($comp_id,$service_tax_number,$company_service_tax_id,$service_tax_type);
        if (!empty($check_company_service_tax_number))
        {
            $error = 'This Service Tax number already exists in the database.';
            
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function validateRole($role,$role_id, $data, ?string &$error = null){
        $roles_model = new \Modules\Management\Models\Roles_model();

        $check_role = $roles_model->checkRole($role,$this->userdata['CompID'],$role_id);
        if(!empty($check_role))
        {
            $error = 'This Role already exists in the database.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function validateBusinessIndustry($business_industry, $industry_id, $data, ?string &$error = null){
        $industries_model = new \Modules\Management\Models\Industries_model();

        $check_industry = $industries_model->checkIndustry($business_industry,$this->userdata['CompID'],$industry_id);
        if(!empty($check_industry))
        {
            $error = 'This Industry already exists in the database.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function validateService($service,$service_id, $data, ?string &$error = null){

        $services_model = new \Modules\Management\Models\Services_model();
        $check_service = $services_model->checkService($service,$this->userdata['CompID'],$service_id);
        if(!empty($check_service))
        {
            $error = 'This Service already exists in the database.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function validateCurrentPassword($current_password, ?string &$error = null): bool{
        $app_id = getenv('app_id');

        $username = $this->userdata['EmailID'];
        $comp_id = $this->userdata['CompID'];
        $user_data = $this->user_model->fetchUserData($username,md5($current_password),$comp_id,$app_id);

        if (empty($user_data))
        {
            $error = 'Current password not valid.';
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function validateVendorInvoice($invoice_no,$vendor_id, $data, ?string &$error = null){
        if(empty($vendor_id)){
            return true;
        }else{

            $comp_id = $this->userdata['CompID'];
            $expense_model = new \Modules\Finance\Models\Expense_model();
            $vendor_invoice_data = $expense_model->fetchVendorInvoiceData($comp_id,$invoice_no, $vendor_id);

            if(!empty($vendor_invoice_data)){
                $error = 'This Invoice No. already exists against this vendor.';
                return FALSE;
            }else{
                return true;
            }
        }
    }

    public function validateVendorInvoiceStockData($invoice_no,$vendor_id, $data, ?string &$error = null){
        if(empty($vendor_id)){
            return true;
        }else{
            $comp_id = $this->userdata['CompID'];
            $vendor_invoice_data = $this->stock_model->fetchVendorInvoiceStockData($comp_id,$invoice_no, $vendor_id,$data['Item']);

            if(!empty($vendor_invoice_data)){
                $error = 'This Invoice No. already exists against this vendor for the selected item.';
                return FALSE;
            }else{
                return true;
            }
        }
    }

    public function validateDebitNoteNo($debit_note_no, ?string &$error = null): bool
    {
        if(!empty($debit_note_no)){
            $comp_id = $this->userdata['CompID'];
            $debit_note_model = new \Modules\Finance\Models\Debit_note_model();
            $debit_note_details = $debit_note_model->fetchDebitNoteDetailsViaDebitNoteNo($debit_note_no,$comp_id);

            if(!empty($debit_note_details)){
                $error = lang('This Debit Note No. already exists in the system please use a new Debit Note No.');
                return FALSE;
            }else{
                return TRUE;
            }
        }else{
            return TRUE;
        }
    }
    
    public function duplicateExpenseHead($expense_heading, ?string &$error = null): bool
    {
        $expense_model = new \Modules\Finance\Models\Expense_model();
        $check_expense_heading = $expense_model->checkDuplicateExpenseHeading($expense_heading,$this->userdata['CompID']);
        if (!empty($check_expense_heading))
        {
            $error = lang('This Expense Head already exists in the database.');;
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
}