<?php

namespace Modules\Finance\Models;

use \CodeIgniter\Model;

class Debit_note_model extends Model {
    function __construct()
	{
        
        $this->user_db = \Config\Database::connect('user_db');
        $this->db_name = $this->user_db->database;

        $this->finance_db = \Config\Database::connect('finance');
        $this->finance_db_name = $this->finance_db->database;

        $this->inventory_db = \Config\Database::connect('inventory');
        $this->inventory_db_name = $this->inventory_db->database;
	}

    public function saveDebitNote($debit_note_data, $debit_note_id = 0){
        if(empty($debit_note_id)){
            $this->finance_db->table(DEBIT_NOTES)->insert($debit_note_data);
            return $this->finance_db->insertID();
        }else{
            $this->finance_db->table(DEBIT_NOTES)->update($debit_note_data,['DebitNoteID' => $debit_note_id]);
            return $debit_note_id;
        }
    }

    public function saveDebitNoteDetails($debit_note_details){
        $this->finance_db->table(DEBIT_NOTE_DETAILS)->insert($debit_note_details);
        return $this->finance_db->insertID();
    }

    public function saveDebitNoteDetailsTaxData($debit_note_details_tax_data){
        $this->finance_db->table(DEBIT_NOTE_DETAILS_TAX_DATA)->insertBatch($debit_note_details_tax_data);
    }

    public function fetchDebitNotesList($comp_id,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false,$sort_by = '',$sort_order = ''){

        $builder = $this->finance_db->table(DEBIT_NOTES.' DN');

        if($count == false){
            $query = $builder->groupBy('DN.DebitNoteID')
                                      ->limit($limit,$offset);
        }

        if(!empty($filter['VendorID'])){
            $query = $builder->where('DN.VendorID',$filter['VendorID']);
        }

        if(!empty($filter['DebitNoteDateFrom'])){
            $query = $builder->where('DN.DebitNoteDate >=',$filter['DebitNoteDateFrom']);
        }

        if(!empty($filter['DebitNoteDateTo'])){
            $query = $builder->where('DN.DebitNoteDate <=',$filter['DebitNoteDateTo']);
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $builder->orderBy($sort_by, $sort_order);
        }

        if(!empty($filter['search_txt'])){
            $search_txt = $this->finance_db->escapeString($filter['search_txt']);

            $query = $builder->where('(DN.DebitNoteNo like "%'.$search_txt.'%" or DN.InvoiceNo like "%'.$search_txt.'%" or V.VendorName like "%'.$search_txt.'%" or DN.DebitNoteDate like "%'.$search_txt.'%" or DN.ReceivableAmount like "%'.$search_txt.'%" or DN.PaymentStatus like "%'.$search_txt.'%")');
        }
        
        $select = ($count == false)?'DN.DebitNoteID,DN.DebitNoteNo,DN.DebitNoteDate,DN.ReceivableAmount,DN.PaymentStatus,DN.Remarks,DN.InvoiceNo,V.VendorName':'count(DISTINCT(DN.DebitNoteID)) as total_rows';
        $query = $builder->select($select)
                                  ->join($this->db_name.'.'.VENDORS.' V','V.VendorID = DN.VendorID','left')
                                  ->getWhere(['DN.CompID' => $comp_id,'DN.AddedDate <=' => $subscription_end_date])
                                  ->getResultArray();

        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function fetchDebitNoteData($debit_note_id,$comp_id,$subscription_end_date){
        $query = $this->finance_db->table(DEBIT_NOTES.' DN')
                                  ->select('DN.DebitNoteNo,DN.DebitNoteDate,DN.ReceivableAmount,DN.PaymentStatus,DN.InvoiceNo,DN.Remarks,CM.CompName,FTM.FirmType,CM.ContactNo,CM.CompLogoPath,V.VendorName,FTM_c.FirmType as vendor_FirmType,CM.SignatureImgPath')
                                  ->join($this->db_name.'.'.COMPANY_MASTER.' CM','CM.CompID = DN.CompID')
                                  ->join($this->db_name.'.'.VENDORS.' V','V.VendorID = DN.VendorID')
                                  ->join($this->db_name.'.'.FIRM_TYPE_MASTER.' FTM','FTM.FirmTypeID = CM.FirmTypeID')
                                  ->join($this->db_name.'.'.FIRM_TYPE_MASTER.' FTM_c','FTM_c.FirmTypeID = V.FirmTypeID','LEFT')
                                  ->getWhere(['DN.DebitNoteID' => $debit_note_id, 'DN.CompID' => $comp_id,'DN.AddedDate <=' => $subscription_end_date])
                                  ->getRowArray();

        return $query;
    }

    public function fetchDebitNoteDetails($debit_note_id){
        $query = $this->finance_db->table(DEBIT_NOTE_DETAILS.' DND')
                                  ->select('DND.Particular,DND.HSN,DND.Quantity,DND.PricePerUnit,GROUP_CONCAT(CONCAT(DNDTD.Tax,"|",DNDTD.TaxPercentage)) as taxes')
                                  ->join(DEBIT_NOTE_DETAILS_TAX_DATA.' DNDTD','DNDTD.DebitNoteDetailID = DND.DebitNoteDetailID','LEFT')
                                  ->groupBy('DND.DebitNoteDetailID')
                                  ->getWhere(['DND.DebitNoteID' => $debit_note_id])
                                  ->getResultArray();

        return $query;
    }

    public function fetchDebitNoteBasicDetails($debit_note_id,$comp_id){
        $query = $this->finance_db->table(DEBIT_NOTES.' DN')
                                  ->select('DN.PaymentStatus')
                                  ->getWhere(['DN.DebitNoteID' => $debit_note_id,'DN.CompID' => $comp_id])
                                  ->getRowArray();

        return $query;
    }

    public function deleteDebitNote($debit_note_id, $comp_id){
        $this->finance_db->table(DEBIT_NOTES)->delete(['DebitNoteID' => $debit_note_id,'CompID' => $comp_id]);
    }

    public function fetchFullDebitNoteData($comp_id,$subscription_end_date, $filter = []){

        $builder = $this->finance_db->table(DEBIT_NOTE_DETAILS.' DND');

        if(!empty($filter['VendorID'])){
            $query = $builder->where('DN.VendorID',$filter['VendorID']);
        }

        if(!empty($filter['DebitNoteDateFrom'])){
            $query = $builder->where('DN.DebitNoteDate >=',$filter['DebitNoteDateFrom']);
        }

        if(!empty($filter['DebitNoteDateTo'])){
            $query = $builder->where('DN.DebitNoteDate <=',$filter['DebitNoteDateTo']);
        }

        $query = $builder->select('CONCAT(COALESCE(V.VendorName), " " , COALESCE( (CASE WHEN FTM.FirmType is not null then FTM.FirmType ELSE "" END) ) ) as Vendor,DN.DebitNoteNo,DN.DebitNoteDate,DN.InvoiceNo,DND.Particular,DND.HSN,DND.Quantity,DND.PricePerUnit,(CASE WHEN DND.Quantity is not null THEN DND.PricePerUnit * DND.Quantity ELSE DND.PricePerUnit END) as taxable_amount,(GROUP_CONCAT(CONCAT(DNDTD.Tax,":",DNDTD.TaxPercentage))) as taxes, (CASE WHEN DND.Quantity is not null THEN TRUNCATE((DND.PricePerUnit * SUM(DNDTD.TaxPercentage) /100) * DND.Quantity,2) ELSE TRUNCATE((DND.PricePerUnit * SUM(DNDTD.TaxPercentage) /100),2) END) as tax_amount,DN.ReceivableAmount,DN.PaymentStatus,DN.Remarks')
                                  ->join(DEBIT_NOTES.' DN','DN.DebitNoteID = DND.DebitNoteID')
                                  ->join($this->db_name.'.'.VENDORS.' V','V.VendorID = DN.VendorID','left')
                                  ->join($this->db_name.'.'.FIRM_TYPE_MASTER.' FTM','FTM.FirmTypeID = V.FirmTypeID','LEFT')
                                  ->join(DEBIT_NOTE_DETAILS_TAX_DATA.' DNDTD','DNDTD.DebitNoteDetailID = DND.DebitNoteDetailID','LEFT')
                                  ->groupBy('DND.DebitNoteDetailID')
                                  ->getWhere(['DN.CompID' => $comp_id,'DN.AddedDate <=' => $subscription_end_date])
                                  ->getResultArray();

        return $query;
    }

    public function fetchDebitNoteDetailsViaDebitNoteNo($debit_note_no,$comp_id){
        $query = $this->finance_db->table(DEBIT_NOTES.' DN')
                                  ->select('DN.PaymentStatus')
                                  ->getWhere(['DN.DebitNoteNo' => $debit_note_no,'DN.CompID' => $comp_id])
                                  ->getRowArray();

        return $query;
    }
}

?>