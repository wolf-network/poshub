<?php
namespace Modules\Inventory\Models;

use \CodeIgniter\Model;

class Stock_model extends Model {

    function __construct()
	{
		parent::__construct();
        $this->user_db = \Config\Database::connect('user_db',TRUE);
        $this->db_name = $this->user_db->database;

        $this->inventory_db = \Config\Database::connect('inventory',TRUE);
        $this->inventory_db_name = $this->inventory_db->database;

        $this->finance_db = \Config\Database::connect('finance',TRUE);
        $this->finance_db_name = $this->finance_db->database;
	}

    public function saveInwardLogData($stock_inward_log_data){
        $this->inventory_db->table(STOCK_INWARD_LOGS)->insert($stock_inward_log_data);
        return $this->inventory_db->insertID();
    }

    public function fetchInwardHistory($comp_id,$logged_in_user_data,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false, $sort_by = '', $sort_order = ''){
        
        $builder = $this->inventory_db->table(STOCK_INWARD_LOGS.' SIL');

        if($count == false){
            $query = $builder->join(STOCK_INWARD_TAXES.' SIT','SIT.StockInwardHistoryID = SIL.StockInwardHistoryID','LEFT')
                             ->groupBy('SIL.StockInwardHistoryID')
                             ->limit($limit,$offset);
        }

        if(!empty($filter['VendorID'])){
            $query = $builder->where('SIL.VendorID',$filter['VendorID']);
        }

        if(!empty($filter['InwardDateFrom'])){
            $query = $builder->where('SIL.InwardDate >=',$filter['InwardDateFrom']);
        }

        if(!empty($filter['InwardDateTo'])){
            $query = $builder->where('SIL.InwardDate <=',$filter['InwardDateTo']);
        }
        
        if(!empty($filter['search_txt'])){
            $query = $builder->where('(SIL.Item like "%'.$filter['search_txt'].'%" or SIL.Qty like "%'.$filter['search_txt'].'%" or SIL.InwardDate like "%'.$filter['search_txt'].'%" or SIL.HSN like "%'.$filter['search_txt'].'%" or SIL.BuyingPricePerUnit like "%'.$filter['search_txt'].'%" or V.VendorName like "%'.$filter['search_txt'].'%")');
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $builder->orderBy($sort_by, $sort_order);
        }
        
        $select = ($count == false)?'SIL.Item,SIL.HSN,ROUND(SIL.BuyingPricePerUnit + (SIL.BuyingPricePerUnit * SUM(CASE WHEN SIT.TaxPercentage is not null then SIT.TaxPercentage ELSE 0 END)) / 100, 2) as BuyingPricePerUnit,SIL.Qty,SIL.InwardDate,V.VendorName,SIL.InvoiceNo,SIL.ExpiryDate,ROUND( (SIL.BuyingPricePerUnit * SIL.Qty) + (SIL.BuyingPricePerUnit * SIL.Qty) * (SUM(CASE WHEN SIT.TaxPercentage is not null then SIT.TaxPercentage ELSE 0 END)) / 100 ,2) as total_amount':'count(DISTINCT(SIL.StockInwardHistoryID)) as total_rows';
        $query = $builder->select($select)
                         ->join($this->db_name.'.'.VENDORS.' V','V.VendorID = SIL.VendorID','LEFT')
                         ->getWhere(['SIL.CompID' => $comp_id,'SIL.AddedDate <=' => $subscription_end_date])
                         ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function fetchOutwardHistory($comp_id,$logged_in_user_data,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false, $sort_by = '', $sort_order = ''){
        
        $builder = $this->finance_db->table(INVOICE_DETAILS.' ID');

        if($count == false){
            $query = $builder->groupBy('ID.InvoiceDetailID')
                             ->limit($limit,$offset);
        }

        if(!empty($filter['OutwardDateFrom'])){
            $query = $builder->where('I.ClientInvoiceDate >=',$filter['OutwardDateFrom']);
        }

        if(!empty($filter['OutwardDateTo'])){
            $query = $builder->where('I.ClientInvoiceDate <=',$filter['OutwardDateTo']);
        }
        
        if(!empty($filter['search_txt'])){
            $query = $builder->where('(ID.Particular like "%'.$filter['search_txt'].'%" or ID.HSN like "%'.$filter['search_txt'].'%" or ID.SerialNo like "%'.$filter['search_txt'].'%" or ID.Quantity like "%'.$filter['search_txt'].'%" or ID.PricePerUnit like "%'.$filter['search_txt'].'%" or ID.TotalTaxPercentage like "%'.$filter['search_txt'].'%" or ID.TotalAmount like "%'.$filter['search_txt'].'%") or I.ClientInvoiceDate like "%'.$filter['search_txt'].'%")');
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $builder->orderBy($sort_by, $sort_order);
        }
        
        $select = ($count == false)?'ID.Particular,ID.HSN,ID.SerialNo,ID.PricePerUnit,ID.Quantity,ID.TotalTaxPercentage,ROUND(ID.PricePerUnit * ID.Quantity * ID.TotalTaxPercentage / 100,2) as tax_amount,ID.TotalAmount,I.ClientInvoiceDate':'count(DISTINCT(ID.InvoiceDetailID)) as total_rows';
        $query = $builder->select($select)
                         ->join(INVOICE.' I','I.InvoiceID = ID.InvoiceID')
                         ->getWhere(['I.CompID' => $comp_id,'I.CreatedDate <=' => $subscription_end_date,'ID.ParticularType' => 'Good'])
                         ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function saveInwardOutwardReports($stock_inward_outward_report_data){
        $inward_stock_qty = $stock_inward_outward_report_data['InwardStockQty'];

        unset($stock_inward_outward_report_data['InwardStockQty']);

        $this->inventory_db->table(INWARD_OUTWARD_REPORTS)->set('InwardStockQty','InwardStockQty + '.$inward_stock_qty,FALSE)
                           ->update($stock_inward_outward_report_data,['ReportDate' => $stock_inward_outward_report_data['ReportDate'],'Item' => $stock_inward_outward_report_data['Item'],'CompID' => $stock_inward_outward_report_data['CompID']]);
         
        if($this->inventory_db->affectedRows() == 0){
            $item_model = new \Modules\Inventory\Models\Item_model();

            $fetched_item_data = $item_model->fetchItemDataViaItemName($stock_inward_outward_report_data['Item'],$stock_inward_outward_report_data['CompID']);

            if(!empty($fetched_item_data['Qty'])){
                $opening_stock_qty = $fetched_item_data['Qty'];
            }else{
                $opening_stock_qty = '0';
            }

            $stock_inward_outward_report_data['InwardStockQty'] = $inward_stock_qty;
            $this->inventory_db->table(INWARD_OUTWARD_REPORTS)
                               ->set('OpeningStockQty',$opening_stock_qty)
                               ->insert($stock_inward_outward_report_data);
        }
    }

    public function saveOutwardReports($stock_outward_report_data, $outward_stock_qty){

        $finance_model = new \Modules\Finance\Models\Finance_model();
        $item_qty = $finance_model->checkItemQty($stock_outward_report_data['Item'],$stock_outward_report_data['CompID']);

        $this->inventory_db->table(INWARD_OUTWARD_REPORTS)
                           ->set('ClosingStockQty',$item_qty['Qty'])
                           ->set('OutwardStockQty','OutwardStockQty + '.$outward_stock_qty,FALSE)
                           ->update($stock_outward_report_data,['ReportDate' => $stock_outward_report_data['ReportDate'],'Item' => $stock_outward_report_data['Item'],'CompID' => $stock_outward_report_data['CompID'] ]);

        if($this->inventory_db->affectedRows() == 0){
            $opening_stock = $item_qty['Qty'] + $outward_stock_qty;
            $closing_stock = $opening_stock - $outward_stock_qty;
            $this->inventory_db->table(INWARD_OUTWARD_REPORTS)
                               ->set('OutwardStockQty',$outward_stock_qty)
                               ->set('ClosingStockQty',$closing_stock)
                               ->set('OpeningStockQty',$opening_stock)
                               ->insert($stock_outward_report_data);
        }

        $next_date_stock_outward_report_data = [
            'CompID' => $stock_outward_report_data['CompID'],
            'Item' => $stock_outward_report_data['Item'],
            'HSN' => $stock_outward_report_data['HSN'],
            'OpeningStockQty' => $item_qty['Qty'],
            'ReportDate' => date('Y-m-d', strtotime("+1 day", strtotime($stock_outward_report_data['ReportDate'])))
        ];

        $this->inventory_db->table(INWARD_OUTWARD_REPORTS)->update($next_date_stock_outward_report_data,['ReportDate' => $next_date_stock_outward_report_data['ReportDate'],'Item' => $next_date_stock_outward_report_data['Item'],'CompID' => $next_date_stock_outward_report_data['CompID'] ]);

        if($this->inventory_db->affectedRows() == 0){
            $this->inventory_db->table(INWARD_OUTWARD_REPORTS)->insert($next_date_stock_outward_report_data);
        }
    }

    public function fetchInwardOutwardReports($comp_id,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false, $sort_by = '', $sort_order = ''){
        
        $builder = $this->inventory_db->table(INWARD_OUTWARD_REPORTS.' IOR');

        if($count == false){
            $query = $builder->groupBy('IOR.InwardOutwardReportID')
                              ->limit($limit,$offset);
        }

        if(!empty($filter['ReportDateFrom'])){
            $query = $builder->where('IOR.ReportDate >=',$filter['ReportDateFrom']);
        }

        if(!empty($filter['ReportDateTo'])){
            $query = $builder->where('IOR.ReportDate <=',$filter['ReportDateTo']);
        }

        if(empty($filter['ReportDateFrom']) && empty($filter['ReportDateTo'])){
            $query = $builder->where('IOR.ReportDate <=',$subscription_end_date);
        }
        
        if(!empty($filter['search_txt'])){
            $query = $builder->where('(IOR.Item like "%'.$filter['search_txt'].'%" or IOR.HSN like "%'.$filter['search_txt'].'%" or IOR.OpeningStockQty like "%'.$filter['search_txt'].'%" or IOR.InwardStockQty like "%'.$filter['search_txt'].'%" or IOR.OutwardStockQty like "%'.$filter['search_txt'].'%" or IOR.ClosingStockQty like "%'.$filter['search_txt'].'%" or IOR.ReportDate like "%'.$filter['search_txt'].'%")');
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $query = $builder->orderBy($sort_by, $sort_order);
        }else{
            $query = $builder->orderBy('IOR.InwardOutwardReportID', 'DESC');
        }
        
        $select = ($count == false)?'IOR.Item,IOR.HSN,IOR.OpeningStockQty,IOR.InwardStockQty,IOR.OutwardStockQty,IOR.ClosingStockQty,IOR.ReportDate':'count(DISTINCT(IOR.InwardOutwardReportID)) as total_rows';
        $query = $builder->select($select)
                                  ->getWhere(['IOR.CompID' => $comp_id])
                                  ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function checkBatchNo($batch_no,$comp_id, $item, $vendor_id){
        $query = $this->inventory_db->table(STOCK_INWARD_LOGS)
                                    ->select('BatchNo')
                                    ->getWhere(['BatchNo' => $batch_no,'CompID' => $comp_id,'Item' => $item,'VendorID' => $vendor_id])
                                    ->getRowArray();

        return $query;
    }

    /* Remaining Qty will be reduced whenever an item is sold or sales has been made against good */
    public function updateStockInwardLogQty($comp_id,$item,$qty){
        $query = $this->inventory_db->table(STOCK_INWARD_LOGS)
                                    ->set('RemainingQty','RemainingQty - '.$qty,FALSE)
                                    ->where('RemainingQty > 0')
                                    ->orderBy('StockInwardHistoryID','ASC')
                                    ->limit(1)
                                    ->update(['CompID' => $comp_id,'Item' => $item]);
    }

    public function fetchExpiringItemsCount($comp_id,$expiry_date, $expiry_days){
        $query = $this->inventory_db->table(STOCK_INWARD_LOGS)->select('count(StockInwardHistoryID) as total_expiring_items_count')
                                    ->where('ExpiryDate is not null')
                                    ->where('(RemainingQty is not null and RemainingQty > 0)')
                                    ->where('DATE_SUB(ExpiryDate, INTERVAL '.$expiry_days.') <= "'.$expiry_date.'"')
                                    ->getWhere(['CompID' => $comp_id,'ExpiryDate >=' => date('Y-m-d')])
                                    ->getRowArray();

        return $query;
    }

    public function fetchExpiringItems($comp_id,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false, $sort_by = '', $sort_order = ''){
        
        $builder = $this->inventory_db->table(STOCK_INWARD_LOGS.' SIL');

        if($count == false){
            $query = $builder->groupBy('SIL.StockInwardHistoryID')
                              ->limit($limit,$offset);
        }

        if(!empty($filter['VendorID'])){
            $query = $builder->where('SIL.VendorID',$filter['VendorID']);
        }

        if(!empty($filter['expiry_date'])){
            $expiry_date = $filter['expiry_date'];
            $expiry_days = $filter['expiry_days'];
            $query = $builder->where('DATE_SUB(SIL.ExpiryDate, INTERVAL '.$expiry_days.') <= "'.$expiry_date.'"');
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $builder->orderBy($sort_by, $sort_order);
        }else{
            $builder->orderBy('SIL.ExpiryDate', 'ASC');
        }
        
        $select = ($count == false)?'SIL.StockInwardHistoryID,SIL.Item,SIL.BatchNo,SIL.Qty,SIL.RemainingQty,SIL.ExpiryDate,V.VendorName,SIL.ManufacturingDate,SIL.ExpiryDate,SIL.InwardDate,(DATEDIFF(SIL.ExpiryDate, "'.date("Y-m-d").'")) as expiry_days_left':'count(DISTINCT(SIL.StockInwardHistoryID)) as total_rows';



        $query = $builder->select($select)
                                    ->join($this->db_name.'.'.VENDORS.' V','V.VendorID = SIL.VendorID','LEFT')
                                    ->where('(RemainingQty is not null and RemainingQty > 0)')
                                    ->getWhere(['SIL.CompID' => $comp_id,'SIL.ExpiryDate >=' => date('Y-m-d'),'SIL.AddedDate <=' => $subscription_end_date])
                                    ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function fetchExpiringItemsVendors($comp_id,$expiry_date = '',$expiry_days = ''){

        $builder = $this->inventory_db->table(STOCK_INWARD_LOGS.' SIL');

        if(!empty($expiry_days)){
            $query = $builder->where('DATE_SUB(SIL.ExpiryDate, INTERVAL '.$expiry_days.') <= "'.$expiry_date.'"');
        }

        $query = $builder->select('SIL.VendorID,V.VendorName')
                                    ->join($this->db_name.'.'.VENDORS.' V','V.VendorID = SIL.VendorID')
                                    ->where('SIL.ExpiryDate is not null')
                                    ->where('(SIL.RemainingQty is not null and SIL.RemainingQty > 0)')
                                    ->getWhere(['SIL.CompID' => $comp_id,'SIL.ExpiryDate >=' => date('Y-m-d')])
                                    ->getResultArray();

        return $query;
    }

    public function fetchInwardedStockData($stock_inward_history_id){
        $query = $this->inventory_db->table(STOCK_INWARD_LOGS.' SIL')
                                    ->select('V.VendorName,SIL.Item,SIL.BatchNo,SIL.ExpiryDate,SIL.RemainingQty')
                                    ->join($this->db_name.'.'.VENDORS.' V','V.VendorID = SIL.VendorID')
                                    ->getWhere()
                                    ->getRowArray();

        return $query;
    }

    public function saveReturnedExpiringItem($returned_expiring_item_data){
        $this->inventory_db->table(RETURNED_EXPIRING_STOCKS)->insert($returned_expiring_item_data);
    }

    public function fetchExpiringItemsReturnedVendors($comp_id){
        $query = $this->inventory_db->table(RETURNED_EXPIRING_STOCKS)
                                    ->select('DISTINCT(Vendor) as Vendor')
                                    ->getWhere(['CompID' => $comp_id])
                                    ->getResultArray();

        return $query;
    }

    public function fetchMinMaxReturnDates($comp_id){
        $query = $this->inventory_db->table(RETURNED_EXPIRING_STOCKS)
                                    ->select('MIN(ReturnDate) as min_return_date,MAX(ReturnDate) as max_return_date')
                                    ->getWhere(['CompID' => $comp_id])
                                    ->getRowArray();

        return $query;
    }

    public function fetchReturnedExpiringItems($comp_id,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false){
        
        $builder = $this->inventory_db->table(RETURNED_EXPIRING_STOCKS.' RES');

        if($count == false){
            $query = $builder->limit($limit,$offset);
        }

        if(!empty($filter['Vendor'])){
            $query = $builder->where('RES.Vendor',$filter['Vendor']);
        }

        if(!empty($filter['ReturnDateFrom'])){
            $query = $builder->where('RES.ReturnDate >=',$filter['ReturnDateFrom']);
        }

        if(!empty($filter['ReturnDateTo'])){
            $query = $builder->where('RES.ReturnDate <=',$filter['ReturnDateTo']);
        }
        
        $select = ($count == false)?'RES.Item,RES.BatchNo,RES.UnitsReturned,RES.ReturnDate,RES.Vendor,RES.VendorRepresentativeName,RES.VendorRepresentativeEmail':'count(DISTINCT(RES.ReturnedExpiringStockID)) as total_rows';



        $query = $builder->select($select)
                         ->orderBy('ReturnedExpiringStockID','ASC')
                         ->getWhere(['RES.CompID' => $comp_id])
                         ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function fetchExpiringItemsRemindersContactDetails($expiry_reminder_date){
        $query = $this->inventory_db->table(STOCK_INWARD_LOGS.' SIL')
                                    ->select('CM.CompID,CM.CompName,CM.EmailID')
                                    ->join($this->db_name.'.'.COMPANY_MASTER.' CM','CM.CompID = SIL.CompID')
                                    ->where('SIL.ExpiryDate is not null')
                                    ->where('(SIL.RemainingQty is not null and SIL.RemainingQty > 0)')
                                    ->where('CM.EmailID is not null')
                                    ->groupBy('SIL.CompID')
                                    ->getWhere(['SIL.ExpiryReminderDate' => $expiry_reminder_date])
                                    ->getResultArray();

        for ($i=0; $i <count($query); $i++) { 
            $query[$i]['stocks'] = $this->fetchExpiringItemsDetailsViaCompID($query[$i]['CompID'],$expiry_reminder_date);
        }

        return $query;
    }

    private function fetchExpiringItemsDetailsViaCompID($comp_id,$expiry_reminder_date){

        $query = $this->inventory_db->table(STOCK_INWARD_LOGS.' SIL')
                                    ->select('SIL.Item,SIL.HSN,V.VendorName,SIL.BatchNo,SIL.RemainingQty,SIL.ManufacturingDate,SIL.ExpiryDate')
                                    ->join($this->db_name.'.'.VENDORS.' V','V.VendorID = SIL.VendorID','LEFT')
                                    ->where('SIL.ExpiryDate is not null')
                                    ->where('(SIL.RemainingQty is not null and SIL.RemainingQty > 0)')
                                    ->groupBy('StockInwardHistoryID')
                                    ->getWhere(['SIL.CompID' => $comp_id,'SIL.ExpiryReminderDate' => $expiry_reminder_date])
                                    ->getResultArray();
        return $query;
    }

    public function UpdateInwardLogNextExpiryDate($mailing_comp_id,$stock_inward_log_data,$expiry_reminder_date){
        $this->inventory_db->table(STOCK_INWARD_LOGS)->update($stock_inward_log_data,['CompID' => $mailing_comp_id, 'ExpiryReminderDate' => $expiry_reminder_date]);
    }

    public function fetchExpiringItemsFullData($comp_id,$subscription_end_date,$filter = []){

        $builder = $this->inventory_db->table(STOCK_INWARD_LOGS.' SIL');

        if(!empty($filter['VendorID'])){
            $query = $builder->where('SIL.VendorID',$filter['VendorID']);
        }

        if(!empty($filter['expiry_date'])){
            $expiry_date = $filter['expiry_date'];
            $expiry_days = $filter['expiry_days'];
            $query = $builder->where('DATE_SUB(SIL.ExpiryDate, INTERVAL '.$expiry_days.') <= "'.$expiry_date.'"');
        }



        $query = $builder->select('SIL.Item,V.VendorName,SIL.BatchNo,SIL.RemainingQty,SIL.ManufacturingDate,SIL.ExpiryDate,SIL.ExpiryDate')
                         ->join($this->db_name.'.'.VENDORS.' V','V.VendorID = SIL.VendorID','LEFT')
                         ->where('(RemainingQty is not null and RemainingQty > 0)')
                         ->getWhere(['SIL.CompID' => $comp_id,'SIL.ExpiryDate >=' => date('Y-m-d'),'SIL.AddedDate <=' => $subscription_end_date])
                         ->getResultArray();
        
        return $query;
    }

    public function saveInwardTaxesData($stock_inward_taxes_data){
        $this->inventory_db->table(STOCK_INWARD_TAXES)->insertBatch($stock_inward_taxes_data);
    }

    public function fetchVendorInvoiceStockData($comp_id,$invoice_no, $vendor_id, $item){
        $query = $this->inventory_db->table(STOCK_INWARD_LOGS)
                                    ->select('StockInwardHistoryID')
                                    ->getWhere(['CompID' => $comp_id,'InvoiceNo' => $invoice_no,'VendorID' => $vendor_id,'Item' => $item])
                                    ->getRowArray();

        return $query;
    }
}