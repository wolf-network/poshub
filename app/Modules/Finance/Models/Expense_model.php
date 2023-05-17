<?php

namespace Modules\Finance\Models;

use \CodeIgniter\Model;

class Expense_model extends Model {
    function __construct()
	{
		parent::__construct();
        
        $this->user_db = \Config\Database::connect('user_db',TRUE);
        $this->db_name = $this->user_db->database;

        $this->finance_db = \Config\Database::connect('finance',TRUE);
        $this->finance_db_name = $this->finance_db->database;
	}

    public function saveExpense($expense_data, $expense_id = 0){
        if(empty($expense_id)){
            $this->finance_db->table(EXPENSES)->insert($expense_data);
            return $this->finance_db->insertID();
        }else{
            $this->finance_db->table(EXPENSES)->update($expense_data,['ExpenseID' => $expense_id]);
            return $expense_id;
        }
    }

    public function fetchExpenseHeads($comp_id = 0){
        $query = $this->finance_db->table(EXPENSE_HEADS_MASTER)
                                  ->select('ExpenseHeadMasterID,ExpenseHead')
                                  ->where('(CompID is null or CompID = "'.$comp_id.'")')
                                  ->orderBy('ExpenseHead','ASC')
                                  ->get()
                                  ->getResultArray();

        return $query;
    }

    public function fetchExpenseList($comp_id,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false,$sort_by = '',$sort_order = ''){

        $builder = $this->finance_db->table(EXPENSES.' E');

        if($count == false){
            $query = $builder->groupBy('E.ExpenseID')
                             ->limit($limit,$offset);
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $builder->orderBy($sort_by, $sort_order);
        }

        if(!empty($filter['VendorID'])){
            $query = $builder->where('E.VendorID',$filter['VendorID']);
        }
        
        if(!empty($filter['search_txt'])){
            $search_txt = $this->finance_db->escapeString($filter['search_txt']);
            $query = $builder->where('(EHM.ExpenseHead like "%'.$search_txt.'%" or E.ExpenseDate like "%'.$search_txt.'%" or RU.Name like "%'.$search_txt.'%" or V.VendorName like "%'.$search_txt.'%")');
        }

        if(!empty($filter['ExpenseDateFrom'])){
            $query = $builder->where('E.ExpenseDate >=',$filter['ExpenseDateFrom']);
        }

        if(!empty($filter['ExpenseDateTo'])){
            $query = $builder->where('E.ExpenseDate <=',$filter['ExpenseDateTo']);
        }

        if(!empty($filter['TaxAmount'])){
            switch ($filter['TaxAmount']) {
                case 'Taxable':
                    $query = $builder->where('ET.TaxPercentage is not null and ET.TaxPercentage > 0');
                    break;
                case 'Non-Taxable':
                    $query = $builder->where('(ET.TaxPercentage is null or ET.TaxPercentage = 0)');
                    break;
            }
        }

        
        $select = ($count == false)?'E.ExpenseID,EHM.ExpenseHead,E.ExpenseDate,ROUND((E.ExpenseAmount) + (E.ExpenseAmount * SUM(CASE WHEN ET.TaxPercentage is not null then ET.TaxPercentage ELSE 0 END) / 100), 2) as ExpenseAmount,ROUND((E.ExpenseAmount * SUM(CASE WHEN ET.TaxPercentage is not null then ET.TaxPercentage ELSE 0 END)) / 100, 2) as TaxAmount,E.AttachedDocumentPath,E.Remarks,RU.Name,V.VendorName':'count(DISTINCT(E.ExpenseID)) as total_rows';
        $query = $builder->select($select)
                                  ->join($this->db_name.'.'.REGISTERED_USERS.' RU','RU.ID = E.AddedBy','left')
                                  ->join(EXPENSE_HEADS_MASTER.' EHM','EHM.ExpenseHeadMasterID = E.ExpenseHeadMasterID','left')
                                  ->join($this->db_name.'.'.VENDORS.' V','V.VendorID = E.VendorID','left')
                                  ->join(EXPENSE_TAXES.' ET','ET.ExpenseID = E.ExpenseID','LEFT')
                                  ->getWhere(['E.CompID' => $comp_id,'E.AddedDate <=' => $subscription_end_date])
                                  ->getResultArray();

        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function fetchExpenseData($expense_id, $comp_id){
        $query = $this->finance_db->table(EXPENSES.' E')
                                  ->select('E.AttachedDocumentPath')
                                  ->getWhere(['E.ExpenseID' => $expense_id,'E.CompID' => $comp_id])
                                  ->getRowArray();

        return $query;
    }

    public function deleteExpense($expense_id){
        $this->finance_db->table(EXPENSES.' E')->delete(['ExpenseID' => $expense_id]);
    }

    public function saveExpenseHeading($expense_heading_data){
        $this->finance_db->table(EXPENSE_HEADS_MASTER)->insert($expense_heading_data);
        return $this->finance_db->InsertID();
    }

    public function checkDuplicateExpenseHeading($expense_heading,$comp_id){
        $query = $this->finance_db->table(EXPENSE_HEADS_MASTER.' EHM')
                                  ->select('EHM.ExpenseHead')
                                  ->where('(EHM.CompID = "'.$comp_id.'" or EHM.CompID is null)')
                                  ->getWhere(['EHM.ExpenseHead' => $expense_heading])
                                  ->getRowArray();

        return $query;
    }

    public function fetcExpenseHeadDataViaExpenseHeadName($expense_head_name){
        $query = $this->finance_db->table(EXPENSE_HEADS_MASTER)
                                  ->select('ExpenseHeadMasterID')
                                  ->getWhere(['ExpenseHead' => $expense_head_name])
                                  ->getRowArray();

        return $query;
    }

    public function fetchFullExpenseData($comp_id,$subscription_end_date,$filter = []){

        $builder = $this->finance_db->table(EXPENSES.' E');

        if(!empty($filter['VendorID'])){
            $query = $builder->where('E.VendorID',$filter['VendorID']);
        }

        if(!empty($filter['ExpenseDateFrom'])){
            $query = $builder->where('E.ExpenseDate >=',$filter['ExpenseDateFrom']);
        }

        if(!empty($filter['ExpenseDateTo'])){
            $query = $builder->where('E.ExpenseDate <=',$filter['ExpenseDateTo']);
        }

        if(!empty($filter['TaxAmount'])){
            switch ($filter['TaxAmount']) {
                case 'Taxable':
                    $query = $builder->where('ET.TaxPercentage is not null and ET.TaxPercentage > 0');
                    break;
                case 'Non-Taxable':
                    $query = $builder->where('(ET.TaxPercentage is null or ET.TaxPercentage = 0)');
                    break;
            }
        }

        $query = $builder->select('EHM.ExpenseHead,V.VendorName,E.ExpenseDate,E.ExpenseAmount,GROUP_CONCAT(CONCAT(ET.Tax,":",ET.TaxPercentage,"%") SEPARATOR " | ") as taxes,ROUND((E.ExpenseAmount * SUM(CASE WHEN ET.TaxPercentage is not null then ET.TaxPercentage ELSE 0 END)) / 100, 2) as TaxAmount,ROUND(E.ExpenseAmount + (E.ExpenseAmount * SUM(CASE WHEN ET.TaxPercentage is not null then ET.TaxPercentage ELSE 0 END)) / 100, 2) as TotalExpenseAmount,E.Remarks')
                         ->join(EXPENSE_HEADS_MASTER.' EHM','EHM.ExpenseHeadMasterID = E.ExpenseHeadMasterID','left')
                         ->join($this->db_name.'.'.VENDORS.' V','V.VendorID = E.VendorID','left')
                         ->join(EXPENSE_TAXES.' ET','ET.ExpenseID = E.ExpenseID','LEFT')
                         ->groupBy('E.ExpenseID')
                         ->getWhere(['E.CompID' => $comp_id,'E.AddedDate <=' => $subscription_end_date])
                         ->getResultArray();

        return $query;
    }

    public function saveExpenseTaxesData($expense_taxes_data){
        $this->finance_db->table(EXPENSE_TAXES)->insertBatch($expense_taxes_data);
    }

    public function fetchVendorInvoiceData($comp_id,$invoice_no, $vendor_id){
        $query = $this->finance_db->table(EXPENSES)
                                  ->select('ExpenseID')
                                  ->getWhere(['CompID' => $comp_id,'InvoiceNo' => $invoice_no,'VendorID' => $vendor_id])
                                  ->getRowArray();

        return $query;
    }
}