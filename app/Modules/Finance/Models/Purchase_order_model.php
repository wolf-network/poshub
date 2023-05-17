<?php

namespace Modules\Finance\Models;

use \CodeIgniter\Model;

class Purchase_order_model extends Model {
    function __construct()
	{
        $this->user_db = \Config\Database::connect('user_db');
        $this->db_name = $this->user_db->database;

        $this->finance_db = \Config\Database::connect('finance');
        $this->finance_db_name = $this->finance_db->database;
	}

    public function savePurchaseOrder($purchase_order_data, $purchase_order_id = 0){
        if(empty($purchase_order_id)){
            $this->finance_db->table(PURCHASE_ORDER)->insert($purchase_order_data);
            return $this->finance_db->insertID();
        }else{
            $this->finance_db->table(PURCHASE_ORDER)->update($purchase_order_data,['PurchaseOrderID' => $purchase_order_id]);
            return $purchase_order_id;
        }
    }

    public function savePurchaseOrderDetailsBatch($purchase_order_details_data){
        $this->finance_db->table(PURCHASE_ORDER_DETAILS)->insertBatch($purchase_order_details_data);
    }

    public function fetchPurchaseOrderStatuses(){
        $query = $this->finance_db->table(PURCHASE_ORDER_STATUS_MASTER)
                                  ->get()
                                  ->getResultArray();

        return $query;

    }

    public function checkPurchaseOrderNo($purchase_order_no,$comp_id){
        $query = $this->finance_db->select('PurchaseOrderID')
                          ->getWhere(PURCHASE_ORDER,['PurchaseOrderNo' => $purchase_order_no,'CompID' => $comp_id])
                          ->getRowArray();

        return $query;
    }

    public function fetchPurchaseOrderList($comp_id,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false,$sort_by = '',$sort_order = ''){

        $builder = $this->finance_db->table(PURCHASE_ORDER.' PO');

        if($count == false){
            $query = $builder->groupBy('PO.PurchaseOrderID')
                                      ->limit($limit,$offset);
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $builder->orderBy($sort_by, $sort_order);
        }
        
        if(!empty($filter['search_txt'])){
            $query = $builder->where('(V.VendorName like "%'.$this->finance_db->escapeString($filter['search_txt']).'%" or PO.PurchaseOrderNo like "%'.$this->finance_db->escapeString($filter['search_txt']).'%")');
        }

        if(!empty($filter['DeliveryDateFrom'])){
            $query = $builder->where('PO.DeliveryDate >=',$filter['DeliveryDateFrom']);
        }

        if(!empty($filter['DeliveryDateTo'])){
            $query = $builder->where('PO.DeliveryDate <=',$filter['DeliveryDateTo']);
        }

        if(!empty($filter['PurchaseOrderStatusID'])){
            $query = $builder->where('POSM.PurchaseOrderStatusID',$filter['PurchaseOrderStatusID']);
        }
        
        $select = ($count == false)?'PO.PurchaseOrderID,PO.PurchaseOrderNo,PO.VendorContactNo,PO.DeliveryDate,V.VendorName,PO.TotalAmount,POSM.PurchaseOrderStatus':'count(DISTINCT(PO.PurchaseOrderID)) as total_rows';
        $query = $builder->select($select)
                         ->join($this->db_name.'.'.VENDORS.' V','V.VendorID = PO.VendorID','left')
                         ->join(PURCHASE_ORDER_STATUS_MASTER.' POSM','POSM.PurchaseOrderStatusID = PO.PurchaseOrderStatusID','left')
                         ->getWhere(['PO.CompID' => $comp_id,'PO.AddedDate <=' => $subscription_end_date])
                         ->getResultArray();

        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function fetchPurchaseOrderData($purchase_order_id,$comp_id){
        $query = $this->finance_db->table(PURCHASE_ORDER.' PO')
                                  ->select('PO.CompanyName,FTM_c.FirmType as CompFirmType,PO.CompanyContactNumber,PO.CompanyAddress,STTM.ServiceTaxType as CompanyServiceTaxType,PO.CompanyServiceTaxIdentificationNumber,PO.PurchaseOrderNo,V.VendorName,FTM.FirmType,PO.VendorContactNo,STTM_v.ServiceTaxType,PO.VendorServiceTaxIdentificationNumber,PO.VendorBillingAddress,PO.ShippingAddress,PO.ShippingTermsAndConditions,PO.PaymentTerms,PO.DeliveryDate,PO.TotalAmount,POSM.PurchaseOrderStatus,PO.PurchaseOrderStatusID,PO.CancelationRemark')
                                  ->join($this->db_name.'.'.VENDORS.' V','V.VendorID = PO.VendorID','left')
                                  ->join(PURCHASE_ORDER_STATUS_MASTER.' POSM','POSM.PurchaseOrderStatusID = PO.PurchaseOrderStatusID','left')
                                  ->join($this->db_name.'.'.FIRM_TYPE_MASTER.' FTM','FTM.FirmTypeID = V.FirmTypeID','left')
                                  ->join($this->db_name.'.'.FIRM_TYPE_MASTER.' FTM_c','FTM_c.FirmTypeID = PO.FirmTypeID','left')
                                  ->join(SERVICE_TAX_TYPES_MASTER.' STTM','STTM.ServiceTaxTypeID = PO.ServiceTaxTypeID','left')
                                  ->join(SERVICE_TAX_TYPES_MASTER.' STTM_v','STTM_v.ServiceTaxTypeID = PO.ServiceTaxTypeID','left')
                                  ->getWhere(['PO.PurchaseOrderID' => $purchase_order_id,'PO.CompID' => $comp_id])
                                  ->getRowArray();

        return $query;
    }

    public function fetchPurchaseOrderDetails($purchase_order_id){
        $query = $this->finance_db->table(PURCHASE_ORDER_DETAILS)
                                  ->select('Particular,HSN,Quantity,PricePerUnit')
                                  ->getWhere(['PurchaseOrderID' => $purchase_order_id])
                                  ->getResultArray();
        return $query;
    }

    public function fetchPurchaseOrderBasicData($purchase_order_id,$comp_id){
        $query = $this->finance_db->table(PURCHASE_ORDER.' PO')
                                  ->select('PO.PurchaseOrderStatusID,PO.PurchaseOrderID,POSM.PurchaseOrderStatus')
                                  ->join(PURCHASE_ORDER_STATUS_MASTER.' POSM','POSM.PurchaseOrderStatusID = PO.PurchaseOrderStatusID','left')
                                  ->getWhere(['PO.PurchaseOrderID' => $purchase_order_id,'PO.CompID' => $comp_id])
                                  ->getRowArray();

        return $query;
    }

    public function fetchPurchaseOrderStatusViaID($purchase_order_status_id){
        $query = $this->finance_db->table(PURCHASE_ORDER_STATUS_MASTER)
                                  ->select('PurchaseOrderStatus')
                                  ->getWhere(['PurchaseOrderStatusID' => $purchase_order_status_id])
                                  ->getRowArray();

        return $query;
    }

    public function savePurchaseOrderSettings($purchase_order_setting_data,$purchase_order_setting_id){

        if(!empty($purchase_order_setting_id)){
            $this->finance_db->table(PURCHASE_ORDER_SETTINGS)->update($purchase_order_setting_data,['PurchaseOrderSettingID' => $purchase_order_setting_id]);
        }else{
            $this->finance_db->table(PURCHASE_ORDER_SETTINGS)->insert($purchase_order_setting_data);
        }
    }

    public function fetchPOSettings($comp_id){
        $query = $this->finance_db->table(PURCHASE_ORDER_SETTINGS)
                                  ->select('PurchaseOrderSettingID,ShippingTermsAndConditions,PaymentTerms')
                                  ->getWhere(['CompID' => $comp_id])
                                  ->getRowArray();

        return $query;
    }
}