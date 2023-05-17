<?php

namespace Modules\Finance\Models;

use CodeIgniter\Model;

class Finance_model extends Model {
    function __construct()
	{
		parent::__construct();
        
        $this->user_db = \Config\Database::connect('user_db');
        $this->db_name = $this->user_db->database;

        $this->finance_db = \Config\Database::connect('finance');
        $this->finance_db_name = $this->finance_db->database;

        $this->inventory_db = \Config\Database::connect('inventory');
        $this->inventory_db_name = $this->inventory_db->database;
	}

    public function fetchIFSC($bank_id,$limit=10,$offset = 0,$filters = []){

        $builder = $this->user_db->table(BANK_DETAILS);

        if(!empty($filters['search_txt'])){
            $query = $builder->where('(BankIFSC like "%'.$filters['search_txt'].'%" or BankBranch like "%'.$filters['search_txt'].'%")');
        }

        if(!empty($filters['BankDetailsID'])){
            if(is_array($filters['BankDetailsID'])){
                $query = $builder->whereIn('BankDetailsID',$filters['BankDetailsID']);
            }else{
                $query = $builder->where('BankDetailsID',$filters['BankDetailsID']);
            }
        }

        $query = $builder->select('BankDetailsID,BankIFSC,BankBranch')
                          ->limit($limit,$offset)
                          ->getWhere(['BankID' => $bank_id])
                          ->getResultArray();
        
        return $query;
    }
    
    public function fetchMICR($bank_details_id){
        $query = $this->user_db->table(BANK_DETAILS)
                               ->select('BankMICR')
                               ->getWhere(['BankDetailsID' => $bank_details_id])
                               ->getRowArray();
      
        return $query;
    }

    public function saveInvoice($invoice_data, $invoice_id = 0){
        if(empty($invoice_id)){
            $this->finance_db->table(INVOICE)->insert($invoice_data);
            return $this->finance_db->insertID();
        }else{
            $this->finance_db->table(INVOICE)->update($invoice_data,['InvoiceID' => $invoice_id]);
        }
    }

    public function saveInvoiceDetails($invoice_details_data){
        $this->finance_db->table(INVOICE_DETAILS)->insert($invoice_details_data);
        return $this->finance_db->insertID();
    }

    public function saveInvoiceDetailsTaxData($invoice_details_tax_data){
        $this->finance_db->table(INVOICE_DETAILS_TAX_DATA)->insertBatch($invoice_details_tax_data);
    }

    public function fetchInvoiceList($comp_id,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false,$sort_by = '',$sort_order = ''){

        $builder = $this->finance_db->table(INVOICE.' I');

        if($count == false){
            $query = $builder->groupBy('I.InvoiceID')
                             ->limit($limit,$offset);
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $builder->orderBy($sort_by, $sort_order);
        }

        if(!empty($filter['ClientID'])){
            $query = $builder->where('I.ClientID',$filter['ClientID']);
        }
        
        if(!empty($filter['search_txt'])){
            $query = $builder->where('(C.ClientName like "%'.$this->finance_db->escapeString($filter['search_txt']).'%" or I.InvoiceNo like "%'.$this->finance_db->escapeString($filter['search_txt']).'%")');
        }

        if(!empty($filter['InvoiceDateFrom'])){
            $query = $builder->where('I.ClientInvoiceDate >=',$filter['InvoiceDateFrom']);
        }

        if(!empty($filter['InvoiceDateTo'])){
            $query = $builder->where('I.ClientInvoiceDate <=',$filter['InvoiceDateTo']);
        }

        if(!empty($filter['DueDateFrom'])){
            $query = $builder->where('I.ClientInvoiceDueDate >=',$filter['DueDateFrom']);
        }

        if(!empty($filter['amountFilter'])){

            switch ($filter['amountFilter']) {
                case 'received':
                    $query = $builder->where('(I.TotalPayableAmount) = R.TotalPaidAmount',NULL,FALSE);
                    break;
                case 'receivables':
                    $query = $builder->where('(R.TotalPaidAmount < (I.TotalPayableAmount) or R.TotalPaidAmount is null)',NULL,FALSE);
                    break;
                
                default:
                    // code...
                    break;
            }
        }
        
        $select = ($count == false)?'I.InvoiceID,I.InvoiceNo,I.ClientContactNo,I.ClientInvoiceDate,I.ClientInvoiceDueDate,C.ClientName,I.TotalPayableAmount as TotalAmount,SUM(CASE WHEN R.TotalPaidAmount is not null THEN R.TotalPaidAmount ELSE 0 END) as PaidAmount,(I.TotalPayableAmount) - SUM(CASE WHEN R.TotalPaidAmount is not null THEN R.TotalPaidAmount ELSE 0 END) as OutstandingAmount,ReceiptID':'count(DISTINCT(I.InvoiceID)) as total_rows';
        $query = $builder->select($select)
                         ->join($this->db_name.'.'.CLIENTS.' C','C.ClientID = I.ClientID','left')
                         ->join('(select ReceiptID,InvoiceID,(SUM(CASE WHEN PaidAmount is not null THEN PaidAmount ELSE 0 END)) as TotalPaidAmount from '.RECEIPTS.' group by InvoiceID) as R','R.InvoiceID = I.InvoiceID','LEFT')
                         ->getWhere(['I.IsDeleted !=' => '1','I.CompID' => $comp_id,'I.CreatedDate <=' => $subscription_end_date])
                         ->getResultArray();

        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function fetchEmployeeExistingSubscriptionDetails($registered_user_id, $comp_id, $app_id){
        $query = $this->user_db->table(REGISTERED_USER_APP_MAPPER.' RUAM')
                               ->select('RUAM.SubscriptionEndDate')
                               ->join(REGISTERED_USERS.' RU','RU.ID = RUAM.RegisteredUserID','left')
                               ->getWhere(['RUAM.RegisteredUserID' => $registered_user_id, 'RUAM.AppID' => $app_id,'RU.CompID' => $comp_id])
                               ->getRowArray();

        return $query;
    }

    public function fetchPlanDetailsViaPlanName($app_id,$plan_name){
        $query = $this->user_db->table(SUBSCRIPTION_PLANS)->select('SubscriptionPlanID,Duration,DurationType')
                               ->limit(1)
                               ->getWhere(['AppID' => $app_id,'PlanName' => $plan_name])
                               ->getRowArray();

        return $query;
    }

    public function fetchSubscriptionPlans($app_id){
        $query = $this->user_db->table(SUBSCRIPTION_PLANS)
                                 ->getWhere(['PlanName !=' => 'Trial','AppID' => $app_id])->getResultArray();

        return $query;
    }

    public function fetchPlanDetailsViaPlanID($plan_id, $app_id){
        $query = $this->user_db->table(SUBSCRIPTION_PLANS.' SP')
                               ->select('SP.SubscriptionPlanID,SP.Duration,SP.DurationType,A.App,SP.TotalAmount,SP.PlanName,SP.TaxPercentage')
                               ->join(APPS.' A','A.AppID = SP.AppID','left')
                               ->getWhere(['SP.SubscriptionPlanID' => $plan_id, 'SP.TotalAmount !=' => '0','SP.AppID' => $app_id])
                               ->getRowArray();

        return $query;
    }

    public function saveTransactionData($transaction_data){
        $this->finance_db->table(TRANSACTIONS)->insert($transaction_data);
    }

    public function fetchTransactionDataViaOrderID($order_id, $payment_gateway){
        $query = $this->finance_db->table(TRANSACTIONS)
                                  ->select('Status,PaymentReceivedDate')
                                  ->getWhere(['OrderID' => $order_id])
                                  ->getRowArray();

        return $query;
    }

    public function fetchInvoiceData($invoice_id,$comp_id){
        $query = $this->finance_db->table(INVOICE.' I')
                                  ->select('I.InvoiceNo,I.CompanyName,I.CompanyAddress,STTM.ServiceTaxType as company_tax_type,I.CompanyTaxIdentificationNumber,I.CompanyServiceTaxIdentificationNumber,FTM.FirmType,I.CreatedDate,I.ClientInvoiceDate,I.ClientInvoiceDueDate,STTM_ct.ServiceTaxType,I.ClientServiceTaxIdentificationNumber,I.ClientBillingAddress,I.ClientShippingAddress,I.ClientContactNo,C.ClientName,FTM_c.FirmType as client_FirmType,I.CustomerNotes,I.CompanyContactNumber,TITM.TaxIdentificationType,C.TaxIdentificationNumber,I.TotalPayableAmount,CU.ClientUserEmailID,CM.SignatureImgPath')
                                  ->join($this->db_name.'.'.FIRM_TYPE_MASTER.' FTM','FTM.FirmTypeID = I.FirmTypeID','LEFT')
                                  ->join($this->db_name.'.'.CLIENTS.' C','C.ClientID = I.ClientID','LEFT')
                                  ->join($this->db_name.'.'.FIRM_TYPE_MASTER.' FTM_c','FTM_c.FirmTypeID = C.FirmTypeID','LEFT')
                                  ->join('(SELECT ClientID, ClientUserEmailID from '.$this->db_name.'.'.CLIENT_USERS.' GROUP BY ClientID) as CU','CU.ClientID = C.ClientID','LEFT')
                                  ->join(SERVICE_TAX_TYPES_MASTER.' STTM','STTM.ServiceTaxTypeID = I.CompanyServiceTaxTypeID','left')
                                  ->join(SERVICE_TAX_TYPES_MASTER.' STTM_ct','STTM_ct.ServiceTaxTypeID = I.ServiceTaxTypeID','left')
                                  ->join($this->db_name.'.'.COMPANY_MASTER.' CM','CM.CompID = I.CompID','left')
                                  ->join($this->db_name.'.'.TAX_IDENTIFICATION_TYPE_MASTER.' TITM','TITM.TaxIdentificationTypeID = C.TaxIdentificationTypeID','LEFT')
                                  ->groupBy('I.InvoiceID')
                                  ->getWhere(['I.InvoiceID' => $invoice_id,'I.CompID' => $comp_id])
                                  ->getRowArray();

        return $query;
    }

    public function fetchInvoiceDetails($invoice_id,$comp_id){
        $query = $this->finance_db->table(INVOICE_DETAILS.' ID')
                                  ->select('ID.Particular,ID.ParticularType,ID.BarcodeNo,ID.Quantity,ID.PricePerUnit,ID.TotalAmount,ID.HSN,ID.Discount,GROUP_CONCAT(CONCAT(IDTD.Tax,"-",IDTD.TaxPercentage)) as service_taxes')
                                  ->join(INVOICE.' I','I.InvoiceID = ID.InvoiceID and I.CompID = '.$comp_id)
                                  ->join(INVOICE_DETAILS_TAX_DATA.' IDTD','IDTD.InvoiceDetailID = ID.InvoiceDetailID','LEFT')
                                  ->groupBy('ID.InvoiceDetailID')
                                  ->getWhere(['I.InvoiceID' => $invoice_id,'I.CompID' => $comp_id])
                                  ->getResultArray();

        return $query;
    }

    public function fetchInvoiceDetailsTaxData($invoice_id,$comp_id){
        $query = $this->finance_db->table(INVOICE_DETAILS_TAX_DATA.' IDTD')
                                  ->select('ID.Particular,IDTD.Tax,IDTD.TaxPercentage,ID.Quantity,ID.PricePerUnit,ID.Discount')
                                  ->join(INVOICE_DETAILS.' ID','ID.InvoiceDetailID = IDTD.InvoiceDetailID')
                                  ->join(INVOICE.' I','I.InvoiceID = ID.InvoiceID')
                                  ->getWhere(['ID.InvoiceID' => $invoice_id,'I.CompID' => $comp_id])
                                  ->getResultArray();

        return $query;
    }

    public function fetchTotalSales($comp_id, $filter = [], $client_id = 0){
        $builder = $this->finance_db->table(INVOICE.' I');

        $subquery_where = '';
        if(!empty($filter['InvoiceDateFrom'])){
          $query = $builder->where('DATE_FORMAT(I.ClientInvoiceDate,"%Y-%m-%d") >=',$filter['InvoiceDateFrom']);
          $subquery_where .= ' and DATE_FORMAT(CreditNoteDate,"%Y-%m-%d") >= "'.$filter['InvoiceDateFrom'].'"';
        }

        if(!empty($filter['InvoiceDateTo'])){
          $query = $builder->where('DATE_FORMAT(I.ClientInvoiceDate,"%Y-%m-%d") <=',$filter['InvoiceDateTo']);
          $subquery_where .= ' and DATE_FORMAT(CreditNoteDate,"%Y-%m-%d") <= "'.$filter['InvoiceDateTo'].'"';
        }

        if(!empty($client_id)){
            $query = $builder->where('I.ClientID',$client_id);
        }

        $query = $builder->select('(SUM(I.TotalPayableAmount) - (SELECT CASE WHEN SUM(PayableAmount) is not null THEN SUM(PayableAmount) ELSE 0 END from '.CREDIT_NOTES.' where CompID = "'.$comp_id.'" '.$subquery_where.'  )) as total_price,SUM(R.PaidAmount) as total_received_amount')
                                  ->join('(SELECT InvoiceID,SUM(PaidAmount) as PaidAmount from '.RECEIPTS.' GROUP BY InvoiceID) as R','R.InvoiceID = I.InvoiceID','LEFT')
                                  ->getWhere(['I.CompID' => $comp_id,'I.IsDeleted !=' => '1'])
                                  ->getRowArray();
            
        return $query;
    }

    public function fetchInvoiceSettings($comp_id){
        $query = $this->finance_db->table(INVOICE_SETTINGS.' I')
                                  ->select('I.InvoiceSettingID,I.TermsAndConditions')
                                  ->getWhere(['I.CompID' => $comp_id])
                                  ->getRowArray();

        return $query;
    }

    public function saveInvoiceSettings($invoice_settings_data, $invoice_setting_id = 0){
        if(empty($invoice_setting_id)){
            $this->finance_db->table(INVOICE_SETTINGS)->insert($invoice_settings_data);
        }else{
            $this->finance_db->table(INVOICE_SETTINGS)->update($invoice_settings_data,['InvoiceSettingID' => $invoice_setting_id]);
        }
    }

    public function fetchServiceTaxTypes(){
        $query = $this->finance_db->table(SERVICE_TAX_TYPES_MASTER)
                                  ->select('ServiceTaxTypeID,ServiceTaxType')
                                  ->get()
                                  ->getResultArray();

        return $query;
    }

    public function saveReceipt($receipt_data){
        $this->finance_db->table(RECEIPTS)->insert($receipt_data);
        return $this->finance_db->insertID();
    }

    public function fetchReceipts($invoice_id){
        $query = $this->finance_db->table(RECEIPTS.' R')
                                  ->select('R.ReceiptID,R.ReceiptNo,R.PaidAmount,R.ReceiptDate,R.AddedDate')
                                  ->getWhere(['R.InvoiceID' => $invoice_id,'R.AddedDate <=' => date('Y-m-d H:i:s')])
                                  ->getResultArray();

        return $query;
    }

    public function fetchPaymentModes(){
        $query = $this->finance_db->table(PAYMENT_MODE_MASTER)
                                  ->get()
                                  ->getResultArray();

        return $query;
    }

    public function fetchInvoiceDetailsData($invoice_id){
        $query = $this->finance_db->table(INVOICE_DETAILS.' ID')
                                  ->select('SUM(ID.TotalAmount) as TotalAmount')
                                  ->getWhere(['ID.InvoiceID' => $invoice_id])
                                  ->getRowArray();

        return $query;
    }

    public function fetchInvoiceReceiptData($invoice_id){
        $query = $this->finance_db->table(RECEIPTS.' R')
                                  ->select('SUM(R.PaidAmount) as TotalPaidAmount')
                                  ->getWhere(['R.InvoiceID' => $invoice_id])
                                  ->getRowArray();

        return $query;
    }

    public function fetchReceiptData($receipt_id){
        $query = $this->finance_db->table(RECEIPTS.' R')
                                  ->select('R.ReceiptNo,R.PaidAmount,R.ReceiptDate,PMM.PaymentMode,R.AddedDate,I.InvoiceID,I.InvoiceNo,C.ClientName,I.ClientBillingAddress')
                                  ->join(PAYMENT_MODE_MASTER.' PMM','PMM.PaymentModeID = R.PaymentModeID','LEFT')
                                  ->join(INVOICE.' I','I.InvoiceID = R.InvoiceID','LEFT')
                                  ->join($this->db_name.'.'.CLIENTS.' C','C.ClientID = I.ClientID','LEFT')
                                  ->getWhere(['R.ReceiptID' => $receipt_id])
                                  ->getRowArray();

        return $query;
    }

    public function saveInvoiceDeductiblesData($invoice_deductibles_data){
        $this->finance_db->table(INVOICE_DEDUCTIBLES)->insertBatch($invoice_deductibles_data);
    }

    public function fetchDeductiblesDetails($invoice_id){
        $query = $this->finance_db->table(INVOICE_DEDUCTIBLES)
                                  ->select('DeductibleType,DeductiblePercentage')
                                  ->getWhere(['InvoiceID' => $invoice_id])
                                  ->getResultArray();

        return $query;
    }

    public function saveInvoiceAdditionalChargesData($invoice_additional_charges_data){
        $this->finance_db->table(INVOICE_ADDITIONAL_CHARGES)->insertBatch($invoice_additional_charges_data);
    }

    public function fetchInvoiceAdditionalChargesData($invoice_id){
        $query = $this->finance_db->table(INVOICE_ADDITIONAL_CHARGES)
                                  ->select('AdditionalChargeType,Additionalcharge')
                                  ->getWhere(['InvoiceID' => $invoice_id])
                                  ->getResultArray();

        return $query;;
    }

    public function fetchFinanceReports($comp_id,$filter){

        $receipt_where = '';
        $expense_where = '';
        $credit_note_where = '';
        $debit_note_where = '';

        $builder = $this->finance_db->table(INVOICE.' I');

        if(!empty($filter['FromDate'])){
            $query = $builder->where('DATE_FORMAT(I.ClientInvoiceDate,"%Y-%m-%d") >=',$filter['FromDate']);
            $receipt_where .= " AND DATE_FORMAT(ReceiptDate,'%Y-%m-%d') >= '".$filter['FromDate']."' ";
            $expense_where .= " AND DATE_FORMAT(ExpenseDate,'%Y-%m-%d') >= '".$filter['FromDate']."' ";
            $credit_note_where .= " AND DATE_FORMAT(CreditNoteDate,'%Y-%m-%d') >= '".$filter['FromDate']."' ";
            $debit_note_where .= " AND DATE_FORMAT(DebitNoteDate,'%Y-%m-%d') >= '".$filter['FromDate']."' ";
        }

        if(!empty($filter['ToDate'])){
            $query = $builder->where('DATE_FORMAT(I.ClientInvoiceDate,"%Y-%m-%d") <=',$filter['ToDate']);
            $receipt_where .= " AND DATE_FORMAT(ReceiptDate,'%Y-%m-%d') <= '".$filter['ToDate']."'";
            $expense_where .= " AND DATE_FORMAT(ExpenseDate,'%Y-%m-%d') <= '".$filter['ToDate']."'";
            $credit_note_where .= " AND DATE_FORMAT(CreditNoteDate,'%Y-%m-%d') <= '".$filter['ToDate']."'";
            $debit_note_where .= " AND DATE_FORMAT(DebitNoteDate,'%Y-%m-%d') <= '".$filter['ToDate']."'";
        }

        $query = $builder->select("(SUM(I.TotalPayableAmount) - (CASE WHEN CN.PayableAmount is not null THEN CN.PayableAmount ELSE 0 END) ) as total_sales, SUM(ID.total_service_tax  - (CASE WHEN total_credit_note_taxable_amount is not null THEN total_credit_note_taxable_amount ELSE 0 END)) as total_service_tax,(SUM(R.PaidAmount)) as total_received,(select ROUND( SUM(E.ExpenseAmount) + SUM(E.ExpenseAmount * ET.TaxPercentage / 100),2) - (SELECT (CASE WHEN SUM(ReceivableAmount) THEN SUM(ReceivableAmount) ELSE 0 END) from ".DEBIT_NOTES." where CompID = '".$comp_id."' ".$debit_note_where.") from expenses E LEFT JOIN (select ExpenseID,SUM(TaxPercentage) as TaxPercentage from expense_taxes GROUP BY ExpenseID) as ET on ET.ExpenseID = E.ExpenseID where CompID='".$comp_id."'".$expense_where." ) as expenses,CN.total_credit_note_taxable_amount")
                          ->join('(SELECT InvoiceID,SUM(( CASE WHEN Quantity is NOT NULL THEN PricePerUnit * Quantity ELSE PricePerUnit END)*(TotalTaxPercentage/100)) as total_service_tax FROM '.INVOICE_DETAILS.' GROUP BY InvoiceID) AS ID ','ID.InvoiceID = I.InvoiceID','LEFT')
                          ->join('(SELECT InvoiceID,SUM(PaidAmount) as PaidAmount FROM receipts GROUP BY InvoiceID) as R','R.InvoiceID = ID.InvoiceID','LEFT')
                          ->join('(SELECT CN.InvoiceID,SUM(CN.PayableAmount) as PayableAmount,(SUM( CASE WHEN CND.Qty is not null THEN CND.PricePerUnit * CND.Qty ELSE CND.PricePerUnit END) * (CASE WHEN SUM(CNDTD.TaxPercentage) is not null THEN SUM(CNDTD.TaxPercentage) ELSE 0 END) / 100 ) as total_credit_note_taxable_amount FROM credit_notes CN LEFT JOIN credit_note_details CND ON CND.CreditNoteID = CN.CreditNoteID LEFT JOIN credit_note_details_tax_data CNDTD ON CNDTD.CreditNoteDetailID = CND.CreditNoteDetailID where CompID = "'.$comp_id.'" GROUP BY CN.InvoiceID) as CN','CN.InvoiceID = I.InvoiceID','LEFT')
                          ->getWhere(['I.CompID' => $comp_id])
                          ->getRowArray();
                          
        return $query;
    }

    public function fetchCompanyServiceTaxMasterDetails($comp_id,$company_service_tax_master_id){
        $query = $this->finance_db->select('ServiceTaxTypeID,TaxIdentificationNumber,RegisteredAddress')
                                  ->getWhere(COMPANY_SERVICE_TAX_MASTER,['CompID' => $comp_id, 'CompanyServiceTaxMasterID' => $company_service_tax_master_id])
                                  ->getRowArray();
        return $query;
    }


    public function checkInvoiceNo($invoice_no,$comp_id){
        $query = $this->finance_db->table(INVOICE.' I')
                                  ->select('I.InvoiceID')
                                  ->getWhere(['I.InvoiceNo' => $invoice_no,'I.CompID' => $comp_id])
                                  ->getRowArray();

        return $query;
    }

    public function fetchCompanyServiceTaxMasterData($comp_id){
        $query = $this->finance_db->table(COMPANY_SERVICE_TAX_MASTER.' CSTM')
                                  ->select('CSTM.CompanyServiceTaxMasterID,STTM.ServiceTaxType,CSTM.ServiceTaxIdentificationNumber,CSTM.RegisteredAddress,CSTM.ServiceTaxTypeID')
                                  ->join(SERVICE_TAX_TYPES_MASTER.' STTM','STTM.ServiceTaxTypeID = CSTM.ServiceTaxTypeID')
                                  ->getWhere(['CSTM.CompID' => $comp_id])
                                  ->getResultArray();

        return $query;
    }

    public function fetchTaxIdentificationTypes(){
        $query = $this->user_db->table(TAX_IDENTIFICATION_TYPE_MASTER)
                               ->get()
                               ->getResultArray();

        return $query;
    }

    public function fetchBasicInvoiceData($invoice_id,$comp_id){
        $query = $this->finance_db->table(INVOICE.' I')
                                  ->select('I.InvoiceNo,I.CompanyName,I.CompanyAddress,I.CompanyServiceTaxTypeID,I.CompanyTaxIdentificationNumber,I.CompanyServiceTaxIdentificationNumber,I.CreatedDate,I.ClientInvoiceDate,I.ClientInvoiceDueDate,I.ClientServiceTaxIdentificationNumber,I.ClientBillingAddress,I.ClientShippingAddress,I.ClientContactNo,I.CustomerNotes,I.CompanyContactNumber,I.TotalPayableAmount,I.ClientID')
                                  ->join(SERVICE_TAX_TYPES_MASTER.' STTM_ct','STTM_ct.ServiceTaxTypeID = I.ServiceTaxTypeID','left')
                                  ->getWhere(['I.InvoiceID' => $invoice_id,'I.CompID' => $comp_id])
                                  ->getRowArray();

        return $query;
    }

    public function deleteInvoice($invoice_id){
        $this->finance_db->delete(INVOICE,['InvoiceID' => $invoice_id]);
    }

    public function fetchFullInvoiceData($comp_id,$subscription_end_date,$filter = []){

        $builder = $this->finance_db->table(INVOICE_DETAILS.' ID');

        if(!empty($filter['ClientID'])){
            $query = $builder->where('I.ClientID',$filter['ClientID']);
        }

        if(!empty($filter['InvoiceDateFrom'])){
            $query = $builder->where('I.ClientInvoiceDate >=',$filter['InvoiceDateFrom']);
        }

        if(!empty($filter['InvoiceDateTo'])){
            $query = $builder->where('I.ClientInvoiceDate <=',$filter['InvoiceDateTo']);
        }

        if(!empty($filter['DueDateFrom'])){
            $query = $builder->where('I.ClientInvoiceDueDate >=',$filter['DueDateFrom']);
        }

        if(!empty($filter['amountFilter'])){

            switch ($filter['amountFilter']) {
                case 'received':
                    $query = $builder->where('(I.TotalPayableAmount) = R.TotalPaidAmount',NULL,FALSE);
                    break;
                case 'receivables':
                    $query = $builder->where('(R.TotalPaidAmount < (I.TotalPayableAmount) or R.TotalPaidAmount is null)',NULL,FALSE);
                    break;
                
                default:
                    // code...
                    break;
            }
        }

        $query = $builder->select('I.InvoiceNo,I.ClientInvoiceDate,I.ClientInvoiceDueDate,I.CompanyServiceTaxIdentificationNumber, CONCAT(COALESCE(C.ClientName), " " , COALESCE( (CASE WHEN FTM.FirmType is not null then FTM.FirmType ELSE "" END) ) ) as Client,CONCAT(TITM.TaxIdentificationType," - ",C.TaxIdentificationNumber) as ClientTaxIdentifier,CONCAT(STTM_ct.ServiceTaxType," - ",I.ClientServiceTaxIdentificationNumber) as ClientServiceTaxIdentifier,ID.Particular,ID.HSN,ID.PricePerUnit,ID.Quantity,GROUP_CONCAT(CONCAT(IDTD.Tax," - ", IDTD.TaxPercentage)) AS Taxes,ID.TotalTaxPercentage,ROUND(((CASE WHEN ID.Quantity THEN ID.PricePerUnit * ID.Quantity ELSE ID.PricePerUnit END) * ID.TotalTaxPercentage / 100),2) as TotalTaxAmount,ID.TotalAmount')
                                  ->join(INVOICE.' I','I.InvoiceID = ID.InvoiceID')
                                  ->join($this->db_name.'.'.CLIENTS.' C','C.ClientID = I.ClientID','LEFT')
                                  ->join($this->db_name.'.'.FIRM_TYPE_MASTER.' FTM','FTM.FirmTypeID = C.FirmTypeID','LEFT')
                                  ->join(SERVICE_TAX_TYPES_MASTER.' STTM_ct','STTM_ct.ServiceTaxTypeID = I.ServiceTaxTypeID','LEFT')
                                  ->join($this->db_name.'.'.TAX_IDENTIFICATION_TYPE_MASTER.' TITM','TITM.TaxIdentificationTypeID = C.TaxIdentificationTypeID','LEFT')
                                  ->join(INVOICE_DETAILS_TAX_DATA.' IDTD','IDTD.InvoiceDetailID = ID.InvoiceDetailID','LEFT')
                                  ->join('(select ReceiptID,InvoiceID,(SUM(CASE WHEN PaidAmount is not null THEN PaidAmount ELSE 0 END)) as TotalPaidAmount from '.RECEIPTS.' group by InvoiceID) as R','R.InvoiceID = I.InvoiceID','LEFT')
                                  ->groupBy('ID.InvoiceDetailID')
                                  ->orderBy('ID.InvoiceDetailID','ASC')
                                  ->getWhere(['I.IsDeleted !=' => '1','I.CompID' => $comp_id,'I.CreatedDate <=' => $subscription_end_date])
                                  ->getResultArray();

        return $query;
    }

    public function reduceItemQty($item,$qty,$comp_id){
        $this->inventory_db->table(ITEMS)
                           ->set('Qty','Qty - '.$qty,FALSE)
                           ->where('Item',$item)
                           ->where('CompID',$comp_id)
                           ->update();
    }

    public function checkItemQty($item,$comp_id){
        $query = $this->inventory_db->table(ITEMS)
                                    ->select('Qty,ItemType')
                                    ->getWhere(['Item' => $item, 'CompID' => $comp_id])
                                    ->getRowArray();

        return $query;
    }

    public function fetchMOMSalesGrowth($comp_id,$year){
        $query = $this->finance_db->table(INVOICE.' I')->select('DATE_FORMAT(ClientInvoiceDate,"%b") as month,(SUM(TotalPayableAmount) - (SELECT CASE WHEN SUM(PayableAmount) is not null THEN SUM(PayableAmount) ELSE 0 END from '.CREDIT_NOTES.' where CompID = "'.$comp_id.'" AND DATE_FORMAT(CreditNoteDate,"%Y") = "'.$year.'"  )) as total_payable_amount')
                          ->groupBy('DATE_FORMAT(ClientInvoiceDate,"%Y-%m")')
                          ->getWhere(['CompID' => $comp_id,'DATE_FORMAT(ClientInvoiceDate,"%Y")' => $year])
                          ->getResultArray();

        return $query;
    }

    public function CategoryWiseSalesReport($comp_id,$year){
      $query = $this->finance_db->table(INVOICE_DETAILS.' ID')->select('ID.ItemCategory,ROUND((SUM(ID.PricePerUnit * ID.Quantity)) - (SELECT CASE WHEN SUM(PayableAmount) is not null THEN SUM(PayableAmount) ELSE 0 END from '.CREDIT_NOTES.' where CompID = "'.$comp_id.'" AND DATE_FORMAT(CreditNoteDate,"%Y") = "'.$year.'"  ), 2) as total_sales')
                          ->join(INVOICE.' I','I.InvoiceID = ID.InvoiceID','LEFT')
                          ->groupBy('ID.ItemCategory')
                          ->getWhere(['I.CompID' => $comp_id,'DATE_FORMAT(I.ClientInvoiceDate,"%Y")' => $year])
                          ->getResultArray();

      return $query;
    }

    public function fetchInvoices($comp_id, $client_id){
        $query = $this->finance_db->table(INVOICE)
                                  ->select('InvoiceID,InvoiceNo')
                                  ->getWhere(['CompID' => $comp_id,'ClientID' => $client_id])
                                  ->getResultArray();

        return $query;
    }

    public function fetchRevenue($comp_id,$date){
        $query = $this->finance_db->table(INVOICE)
                                  ->select('SUM(TotalPayableAmount) as TotalPayableAmount')
                                  ->getWhere(['CompID' => $comp_id,'ClientInvoiceDate' => $date])
                                  ->getRowArray();

        return (!empty($query))?$query['TotalPayableAmount']:'0';
    }
}