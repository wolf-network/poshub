<?php

namespace Modules\Finance\Models;

use \CodeIgniter\Model;

class Credit_note_model extends Model {
    function __construct()
	{
        $this->user_db = \Config\Database::connect('user_db');
        $this->db_name = $this->user_db->database;

        $this->finance_db = \Config\Database::connect('finance');
        $this->finance_db_name = $this->finance_db->database;

        $this->inventory_db = \Config\Database::connect('inventory');
        $this->inventory_db_name = $this->inventory_db->database;
	}

    public function checkSoldQty($invoice_id,$particular,$comp_id)
    {
        $query = $this->finance_db->table(INVOICE_DETAILS.' ID')
                                  ->select('ID.Quantity,ID.ParticularType')
                                  ->join(INVOICE.' I','I.InvoiceID = ID.InvoiceID','LEFT')
                                  ->getWhere(['ID.InvoiceID' => $invoice_id,'ID.Particular' => $particular,'I.CompID' => $comp_id])
                                  ->getRowArray();

        return $query;
    }

    public function fetchInvoiceParticularAmount($invoice_id,$particular){

        $builder = $this->finance_db->table(INVOICE_DETAILS.' ID');

        if(is_array($particular)){
            $where = '';
            for($i=0;$i<count($particular);$i++){
                $where .= 'ID.Particular = "'.$this->finance_db->escapeString($particular[$i]).'" or ';
            }

            $where = rtrim($where,'or ');

            $query = $builder->where('('.$where.')',NULL,FALSE);
        }else{
            $query = $builder->where('ID.Particular',$particular);
        }

        $query = $builder->select('ID.PricePerUnit,ID.TotalTaxPercentage,ID.Discount,ID.ParticularType,ID.HSN,ID.ItemCategory,ID.ParticularType,ID.BarcodeNo,GROUP_CONCAT(CONCAT(IDTD.Tax,"|",IDTD.TaxPercentage)) as taxes')
                                  ->join(INVOICE_DETAILS_TAX_DATA.' IDTD','IDTD.InvoiceDetailID = ID.InvoiceDetailID','LEFT')
                                  ->getWhere(['ID.InvoiceID' => $invoice_id]);

        if(is_array($particular)){
            return $query->getResultArray();
        }else{
            return $query->getRowArray();
        }
    }

    public function checkInvoice($invoice_id,$comp_id){
        $query = $this->finance_db->table(INVOICE)
                                  ->select('InvoiceID')
                                  ->getWhere(['InvoiceID' => $invoice_id])
                                  ->getRowArray();

        return $query;
    }

    public function saveCreditNote($credit_note_data, $credit_note_id = 0){
        if(empty($credit_note_id)){
            $this->finance_db->table(CREDIT_NOTES)->insert($credit_note_data);
            return $this->finance_db->insertID();
        }else{
            $this->finance_db->table(CREDIT_NOTES)->update($credit_note_data,['CreditNoteID' => $credit_note_id]);
            return $credit_note_id;
        }
    }

    public function saveCreditNoteDetails($credit_note_details){
        $this->finance_db->table(CREDIT_NOTE_DETAILS)->insert($credit_note_details);
        return $this->finance_db->insertID();
    }

    public function fetchCreditNotesList($comp_id,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false,$sort_by = '',$sort_order = ''){

        $builder = $this->finance_db->table(CREDIT_NOTES.' CN');

        if($count == false){
            $query = $builder->groupBy('CN.CreditNoteID')
                                      ->limit($limit,$offset);
        }

        if(!empty($filter['ClientID'])){
            $query = $builder->where('I.ClientID',$filter['ClientID']);
        }

        if(!empty($filter['CreditNoteDateFrom'])){
            $query = $builder->where('CN.CreditNoteDate >=',$filter['CreditNoteDateFrom']);
        }

        if(!empty($filter['CreditNoteDateTo'])){
            $query = $builder->where('CN.CreditNoteDate <=',$filter['CreditNoteDateTo']);
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $builder->orderBy($sort_by, $sort_order);
        }

        if(!empty($filter['search_txt'])){
            $search_txt = $this->finance_db->escapeString($filter['search_txt']);

            $query = $builder->where('(CN.CreditNoteNo like "%'.$search_txt.'%" or I.InvoiceNo like "%'.$search_txt.'%" or C.ClientName like "%'.$search_txt.'%" or  CN.CreditNoteDate like "%'.$search_txt.'%" or CN.PayableAmount like "%'.$search_txt.'%" or CN.PaymentStatus like "%'.$search_txt.'%" or I.InvoiceNo like "%'.$search_txt.'%")');
        }
        
        $select = ($count == false)?'CN.CreditNoteID,CN.CreditNoteNo,CN.CreditNoteDate,CN.PayableAmount,CN.PaymentStatus,CN.Reason,I.InvoiceNo,C.ClientName':'count(DISTINCT(CN.CreditNoteID)) as total_rows';
        $query = $builder->select($select)
                                  ->join(INVOICE.' I','I.InvoiceID = CN.InvoiceID')
                                  ->join($this->db_name.'.'.CLIENTS.' C','C.ClientID = I.ClientID','left')
                                  ->getWhere(['CN.CompID' => $comp_id,'CN.AddedDate <=' => $subscription_end_date])
                                  ->getResultArray();

        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function fetchCreditNoteBasicDetails($credit_note_id,$comp_id){
        $query = $this->finance_db->table(CREDIT_NOTES.' CN')
                                  ->select('CN.PaymentStatus')
                                  ->getWhere(['CN.CreditNoteID' => $credit_note_id,'CN.CompID' => $comp_id])
                                  ->getRowArray();

        return $query;
    }

    public function deleteCreditNote($credit_note_id, $comp_id){
        $this->finance_db->table(CREDIT_NOTES)->delete(['CreditNoteID' => $credit_note_id,'CompID' => $comp_id]);
    }

    public function fetchItemIDViaItemName($item_name, $comp_id){
        $query = $this->finance_db->select('ItemID')
                                  ->getWhere(ITEMS,['Item' => $item_name,'CompID' => $comp_id])
                                  ->getRowArray();

        return $query;
    }

    public function updateInventory($item_data){
        $this->inventory_db->table(ITEMS)
                           ->set('Qty','Qty + '.$item_data['Qty'],FALSE)
                           ->update(['UpdatedBy' => $item_data['AddedBy'],'AddedDate' => date('Y-m-d H:i:s')],['CompID' => $item_data['CompID'],'Item' => $item_data['Item']]);

        if($this->inventory_db->affectedRows() <= 0){
            $this->inventory_db->table(ITEMS)->insert($item_data);
        }

        $stock_model = new \Modules\Inventory\Models\Stock_model();

        $stock_inward_log_data = [
            'CompID' => $item_data['CompID'],
            'Item' => $item_data['Item'],
            'HSN' => $item_data['HSN'],
            'Qty' => $item_data['Qty'],
            'InwardDate' => date('Y-m-d H:i:s'),
            'AddedBy' => $item_data['AddedBy'],
            'AddedDate' => date('Y-m-d H:i:s')
        ];


        $stock_model->saveInwardLogData($stock_inward_log_data);

        $stock_inward_outward_report_data = [
            'CompID' => $item_data['CompID'],
            'Item' => $item_data['Item'],
            'HSN' => $item_data['HSN'],
            'InwardStockQty' => $item_data['Qty'],
            'ReportDate' => date('Y-m-d')
        ];

        $stock_model->saveInwardOutwardReports($stock_inward_outward_report_data);

    }

    public function fetchFullCreditNoteData($comp_id,$subscription_end_date, $filter = []){

        $builder = $this->finance_db->table(CREDIT_NOTE_DETAILS.' CND');

        if(!empty($filter['ClientID'])){
            $query = $builder->where('I.ClientID',$filter['ClientID']);
        }

        if(!empty($filter['CreditNoteDateFrom'])){
            $query = $builder->where('CN.CreditNoteDate >=',$filter['CreditNoteDateFrom']);
        }

        if(!empty($filter['CreditNoteDateTo'])){
            $query = $builder->where('CN.CreditNoteDate <=',$filter['CreditNoteDateTo']);
        }

        $query = $builder->select('CONCAT(COALESCE(C.ClientName), " " , COALESCE( (CASE WHEN FTM.FirmType is not null then FTM.FirmType ELSE "" END) ) ) as Client,(CONCAT(STTM.ServiceTaxType," - ",I.ClientServiceTaxIdentificationNumber)) as service_tax_identification,CN.CreditNoteNo,CN.CreditNoteDate,I.InvoiceNo,CND.Particular,CND.HSN,CND.Qty,CND.PricePerUnit,(CASE WHEN CND.Qty is not null THEN CND.PricePerUnit * CND.Qty ELSE CND.PricePerUnit END) as taxable_amount,(GROUP_CONCAT(CONCAT(CNDTD.Tax,":",CNDTD.TaxPercentage))) as taxes, (CASE WHEN CND.Qty is not null THEN TRUNCATE((CND.PricePerUnit * SUM(CNDTD.TaxPercentage) /100) * CND.Qty,2) ELSE TRUNCATE((CND.PricePerUnit * SUM(CNDTD.TaxPercentage) /100),2) END) as tax_amount,CN.PayableAmount,CN.PaymentStatus,CN.Reason')
                                  ->join(CREDIT_NOTES.' CN','CN.CreditNoteID = CND.CreditNoteID')
                                  ->join(INVOICE.' I','I.InvoiceID = CN.InvoiceID')
                                  ->join($this->db_name.'.'.CLIENTS.' C','C.ClientID = I.ClientID','left')
                                  ->join($this->db_name.'.'.FIRM_TYPE_MASTER.' FTM','FTM.FirmTypeID = C.FirmTypeID','LEFT')
                                  ->join(SERVICE_TAX_TYPES_MASTER.' STTM','STTM.ServiceTaxTypeID = I.ServiceTaxTypeID','left')
                                  ->join(CREDIT_NOTE_DETAILS_TAX_DATA.' CNDTD','CNDTD.CreditNoteDetailID = CND.CreditNoteDetailID','LEFT')
                                  ->groupBy('CND.CreditNoteDetailID')
                                  ->getWhere(['CN.CompID' => $comp_id,'CN.AddedDate <=' => $subscription_end_date])
                                  ->getResultArray();

        return $query;
    }

    public function fetchCreditNoteData($credit_note_id,$comp_id,$subscription_end_date){
        $query = $this->finance_db->table(CREDIT_NOTES.' CN')
                                  ->select('CN.CreditNoteNo,CN.CreditNoteDate,CN.PayableAmount,CN.PaymentStatus,CN.Reason,I.CompanyName,FTM.FirmType,I.CompanyContactNumber,I.CompanyAddress,CM.CompLogoPath,STTM.ServiceTaxType as company_tax_type,I.CompanyTaxIdentificationNumber,I.CompanyServiceTaxIdentificationNumber,I.InvoiceNo,C.ClientName,FTM_c.FirmType as client_FirmType,I.ClientBillingAddress,CM.SignatureImgPath,I.ClientContactNo')
                                  ->join(INVOICE.' I','I.InvoiceID = CN.InvoiceID')
                                  ->join($this->db_name.'.'.CLIENTS.' C','C.ClientID = I.ClientID','LEFT')
                                  ->join($this->db_name.'.'.COMPANY_MASTER.' CM','CM.CompID = CN.CompID')
                                  ->join($this->db_name.'.'.FIRM_TYPE_MASTER.' FTM','FTM.FirmTypeID = I.FirmTypeID')
                                  ->join($this->db_name.'.'.FIRM_TYPE_MASTER.' FTM_c','FTM_c.FirmTypeID = C.FirmTypeID','LEFT')
                                  ->join(SERVICE_TAX_TYPES_MASTER.' STTM','STTM.ServiceTaxTypeID = I.CompanyServiceTaxTypeID','left')
                                  ->join(SERVICE_TAX_TYPES_MASTER.' STTM_ct','STTM_ct.ServiceTaxTypeID = I.ServiceTaxTypeID','left')
                                  ->getWhere(['CN.CreditNoteID' => $credit_note_id, 'CN.CompID' => $comp_id,'CN.AddedDate <=' => $subscription_end_date])
                                  ->getRowArray();

        return $query;
    }

    public function fetchCreditNoteDetails($credit_note_id){
        $query = $this->finance_db->table(CREDIT_NOTE_DETAILS.' CND')
                                  ->select('CND.Particular,CND.HSN,CND.Qty,CND.PricePerUnit,GROUP_CONCAT(CONCAT(CNDTD.Tax,"|",CNDTD.TaxPercentage)) as taxes')
                                  ->join(CREDIT_NOTE_DETAILS_TAX_DATA.' CNDTD','CNDTD.CreditNoteDetailID = CND.CreditNoteDetailID','LEFT')
                                  ->groupBy('CND.CreditNoteDetailID')
                                  ->getWhere(['CND.CreditNoteID' => $credit_note_id])
                                  ->getResultArray();

        return $query;
    }

    public function fetchCreditNoteInvoiceDetails($invoice_id,$comp_id){
        $query = $this->finance_db->table(INVOICE_DETAILS.' ID')
                                  ->select('ID.Particular,ID.ParticularType,ID.BarcodeNo,ID.Quantity,ID.PricePerUnit,ID.TotalAmount,ID.HSN,ID.Discount,GROUP_CONCAT(CONCAT(IDTD.Tax,"-",IDTD.TaxPercentage)) as service_taxes,CND.returned_qty')
                                  ->join(INVOICE.' I','I.InvoiceID = ID.InvoiceID and I.CompID = '.$comp_id)
                                  ->join(INVOICE_DETAILS_TAX_DATA.' IDTD','IDTD.InvoiceDetailID = ID.InvoiceDetailID','LEFT')
                                  ->join('(
    SELECT CND.Particular,SUM(CND.Qty) as returned_qty FROM '.CREDIT_NOTE_DETAILS.' CND
LEFT JOIN '.CREDIT_NOTES.' CN ON CN.CreditNoteID = CND.CreditNoteID
WHERE CN.InvoiceID = "'.$invoice_id.'" AND CN.CompID = "'.$comp_id.'"
GROUP BY CND.Particular
) as CND','CND.Particular = ID.Particular','LEFT')
                                  ->groupBy('ID.InvoiceDetailID')
                                  ->having('CASE WHEN CND.returned_qty is not null THEN CND.returned_qty < ID.Quantity ELSE ID.InvoiceDetailID > 0 END',NULL,FALSE)
                                  ->getWhere(['I.InvoiceID' => $invoice_id,'I.CompID' => $comp_id])
                                  ->getResultArray();
        return $query;
    }

    public function checkReturnedQty($invoice_id,$particular,$comp_id){
        $query = $this->finance_db->table(INVOICE_DETAILS.' ID')
                                  ->select('ID.ParticularType,ID.Quantity,CND.returned_qty')
                                  ->join(INVOICE.' I','I.InvoiceID = ID.InvoiceID')
                                  ->join('(
    SELECT CND.Particular,SUM(CND.Qty) as returned_qty FROM '.CREDIT_NOTE_DETAILS.' CND
LEFT JOIN '.CREDIT_NOTES.' CN ON CN.CreditNoteID = CND.CreditNoteID
WHERE CN.InvoiceID = "'.$invoice_id.'" AND CN.CompID = "'.$comp_id.'" AND CND.Particular = "'.$particular.'"
GROUP BY CND.Particular
) as CND','CND.Particular = ID.Particular','LEFT')
                                  ->groupBy('ID.InvoiceDetailID')
                                  ->getWhere(['I.InvoiceID' => $invoice_id,'I.CompID' => $comp_id,'ID.Particular' => $particular])
                                  ->getRowArray();

        return $query;
    }

    public function saveCreditNoteDetailsTaxDataBatch($credit_note_details_tax_data){
        $this->finance_db->table(CREDIT_NOTE_DETAILS_TAX_DATA)->insertBatch($credit_note_details_tax_data);
    }

    public function checkPricePerUnit($invoice_id,$particular,$comp_id){
        $query = $this->finance_db->table(INVOICE_DETAILS.' ID')
                                  ->select('ID.PricePerUnit,ID.Discount')
                                  ->join(INVOICE.' I','I.InvoiceID = ID.InvoiceID')
                                  ->groupBy('ID.InvoiceDetailID')
                                  ->getWhere(['I.InvoiceID' => $invoice_id,'I.CompID' => $comp_id,'ID.Particular' => $particular])
                                  ->getRowArray();

        return $query;
    }
}