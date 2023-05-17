<?php

namespace Modules\Finance\Models;

use CodeIgniter\Model;

class Finance_reports_model extends Model {
    function __construct()
	{
		parent::__construct();
        
        $this->user_db = \Config\Database::connect('user_db');
        $this->db_name = $this->user_db->database;

        $this->finance_db = \Config\Database::connect('finance');
        $this->finance_db_name = $this->finance_db->database;
	}

    public function fetchGSTR1Reports($comp_id,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false){

        $builder = $this->finance_db->table(INVOICE.' I');

        if(!empty($filter['search_txt'])){
            $search_txt = $this->finance_db->escapeString($filter['search_txt']);
            $query = $builder->where('(I.InvoiceNo like "%'.$search_txt.'%" or I.ClientServiceTaxIdentificationNumber like "%'.$search_txt.'%")');
        }

        if(!empty($filter['CompanyGST'])){
            $query = $builder->where('I.CompanyServiceTaxIdentificationNumber',$filter['CompanyGST']);
        }

        if(!empty($filter['InvoiceDateFrom'])){
            $query = $builder->where('I.ClientInvoiceDate >=',$filter['InvoiceDateFrom']);
        }

        if(!empty($filter['InvoiceDateTo'])){
            $query = $builder->where('I.ClientInvoiceDate <=',$filter['InvoiceDateTo']);
        }

        if(!empty($filter['InvoiceType'])){
            if($filter['InvoiceType'] == 'b2c'){
                $query = $builder->like('I.InvoiceNo','POS','after');
            }

            if($filter['InvoiceType'] == 'b2b'){
                $query = $builder->notLike('I.InvoiceNo','POS','after');
            }
        }

        $query = $builder->join(CLIENT_SERVICE_TAX_TYPE_MAPPER.' CSTTM','CSTTM.ServiceTaxNumber = I.ClientServiceTaxIdentificationNumber','LEFT');

        if($count == false){
            $query = $builder->join($this->db_name.'.'.CLIENTS.' C','C.ClientID = I.ClientID','LEFT')
                             ->join($this->db_name.'.'.STATES_MASTER.' SM','SM.StateID = CSTTM.BillingStateID','LEFT')
                             ->join(COMPANY_SERVICE_TAX_MASTER.' CSTM','CSTM.ServiceTaxIdentificationNumber = I.CompanyServiceTaxIdentificationNumber','LEFT')
                             ->join($this->db_name.'.'.STATES_MASTER.' comp_SM','comp_SM.StateID = CSTM.BillingStateID','LEFT')
                             ->groupBy('ID.HSN,(CASE WHEN I.InvoiceNo like "POS%" THEN "b2c" else "b2b" END),I.ClientInvoiceDate, CSTTM.BillingStateID')
                             ->orderBy('I.ClientInvoiceDate','ASC')
                             ->limit($limit,$offset);
        }

        $select = ($count == false)?'ID.HSN,I.ClientServiceTaxIdentificationNumber,C.ClientName,I.ClientInvoiceDate,SUM(TotalAmount) as TotalAmount,CONCAT(LEFT((CASE WHEN I.InvoiceNo LIKE "POS%" THEN I.CompanyServiceTaxIdentificationNumber ELSE I.ClientServiceTaxIdentificationNumber END),2),"-",(CASE WHEN I.InvoiceNo LIKE "POS%" THEN comp_SM.StateName ELSE SM.StateName END)) as place_of_supply,(CASE WHEN I.InvoiceNo like "POS%" THEN "Regular B2C" ELSE "Regular B2B" END) as invoice_type,ID.TotalTaxPercentage,ROUND(SUM(PricePerUnit * CASE WHEN Quantity is not null THEN Quantity ELSE 1 END),2) as taxable_value':'count(DISTINCT ID.HSN,(CASE WHEN I.InvoiceNo like "POS%" THEN "b2c" else "b2b" END),I.ClientInvoiceDate,CSTTM.BillingStateID) as total_rows';

        $query = $builder->select($select)
                         ->join(INVOICE_DETAILS.' ID','ID.InvoiceID = I.InvoiceID','LEFT')
                         ->limit($limit,$offset)
                         ->getWhere(['I.CompID' => $comp_id,'I.CreatedDate <=' => $subscription_end_date])
                         ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function fetchFullGSTR1B2BReports($comp_id,$subscription_end_date,$filter = []){

        $builder = $this->finance_db->table(INVOICE.' I');        

        $query = $builder->select('ID.HSN,I.ClientInvoiceDate,SUM(TotalAmount) as TotalAmount,CONCAT(LEFT(I.ClientServiceTaxIdentificationNumber,2),"-",SM.StateName) as place_of_supply,"N" as reverse_charge,"" as applicable_tax_rate,"Regular B2B" as invoice_type,"" as e_commerce_gstin,ID.TotalTaxPercentage,SUM(PricePerUnit * CASE WHEN Quantity is not null THEN Quantity ELSE 1 END) as taxable_value')
                         ->join(INVOICE_DETAILS.' ID','ID.InvoiceID = I.InvoiceID','LEFT')
                         ->join($this->db_name.'.'.CLIENTS.' C','C.ClientID = I.ClientID','LEFT')
                         ->join(CLIENT_SERVICE_TAX_TYPE_MAPPER.' CSTTM','CSTTM.ServiceTaxNumber = I.ClientServiceTaxIdentificationNumber','LEFT')
                         ->join($this->db_name.'.'.STATES_MASTER.' SM','SM.StateID = CSTTM.BillingStateID','LEFT')
                         ->where('I.ClientInvoiceDate >=',$filter['InvoiceDateFrom'])
                         ->where('I.ClientInvoiceDate <=',$filter['InvoiceDateTo'])
                         ->notLike('I.InvoiceNo','POS','after')
                         ->groupBy('ID.HSN,I.ClientInvoiceDate,SM.StateID')
                         ->orderBy('I.ClientInvoiceDate','ASC')
                         ->getWhere(['I.CompID' => $comp_id,'I.CompanyServiceTaxIdentificationNumber' => $filter['CompanyGST'],'I.CreatedDate <=' => $subscription_end_date])
                         ->getResultArray();
        
        return $query;
    }

    public function fetchFullGSTR1B2CReports($comp_id,$subscription_end_date,$filter = []){

        $builder = $this->finance_db->table(INVOICE.' I');        

        $query = $builder->select('ID.HSN,"",CONCAT(LEFT(I.ClientServiceTaxIdentificationNumber,2),"-",SM.StateName) as place_of_supply,"" as applicable_tax_rate,"" as e_commerce_gstin,ID.TotalTaxPercentage,SUM(PricePerUnit * CASE WHEN Quantity is not null THEN Quantity ELSE 1 END) as taxable_value,"","" as e_commerce_gstin')
                         ->join(INVOICE_DETAILS.' ID','ID.InvoiceID = I.InvoiceID','LEFT')
                         ->join($this->db_name.'.'.CLIENTS.' C','C.ClientID = I.ClientID','LEFT')
                         ->join(CLIENT_SERVICE_TAX_TYPE_MAPPER.' CSTTM','CSTTM.ServiceTaxNumber = I.ClientServiceTaxIdentificationNumber','LEFT')
                         ->join($this->db_name.'.'.STATES_MASTER.' SM','SM.StateID = CSTTM.BillingStateID','LEFT')
                         ->where('I.ClientInvoiceDate >=',$filter['InvoiceDateFrom'])
                         ->where('I.ClientInvoiceDate <=',$filter['InvoiceDateTo'])
                         ->Like('I.InvoiceNo','POS','after')
                         ->groupBy('ID.HSN,I.ClientInvoiceDate,SM.StateID')
                         ->orderBy('I.ClientInvoiceDate','ASC')
                         ->getWhere(['I.CompID' => $comp_id,'I.CompanyServiceTaxIdentificationNumber' => $filter['CompanyGST'],'I.CreatedDate <=' => $subscription_end_date])
                         ->getResultArray();
        
        return $query;
    }
}