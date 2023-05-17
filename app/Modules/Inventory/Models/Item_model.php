<?php

namespace Modules\Inventory\Models;

use \CodeIgniter\Model;

class Item_model extends Model {

    function __construct()
	{   
        $this->inventory_db = \Config\Database::connect('inventory');
        $this->inventory_db_name = $this->inventory_db->database;
	}

	public function saveItem($item_data, $comp_id, $item_id = 0){
		if(empty($item_id)){
			$this->inventory_db->table(ITEMS)->insert($item_data);
			return $this->inventory_db->insertID();
		}else{
			$this->inventory_db->table(ITEMS)->update($item_data,['ItemID' => $item_id,'CompID' => $comp_id]);
			return $item_id;
		}
	}

	public function saveItemTaxesBulk($item_tax_data_arr){
		$item_id = $item_tax_data_arr[0]['ItemID'];
		$this->inventory_db->table(ITEM_TAXES)->delete(['ItemID' => $item_id]);
		$this->inventory_db->table(ITEM_TAXES)->insertBatch($item_tax_data_arr);
	}

	public function fetchItemList($comp_id,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false,$sort_by = '',$sort_order = ''){
            
        $builder = $this->inventory_db->table(ITEMS.' I');

        if($count == false){
           $query = $builder->groupBy('I.ItemID')
                                     ->limit($limit,$offset);
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $query = $builder->orderBy($sort_by, $sort_order);
        }else{
            $query = $builder->orderBy('I.Item','ASC');
        }
        
        if(!empty($filter['search_txt'])){
           $query = $builder->inventory_db->where('(I.Item like "%'.$filter['search_txt'].'%" or I.Price like "%'.$filter['search_txt'].'%" or I.HSN like "%'.$filter['search_txt'].'%" or ICM.ItemCategory like "%'.$filter['search_txt'].'%")');
        }
        
        $select = ($count == false)?'I.ItemID,I.Item,I.ItemType,ICM.ItemCategory,I.BuyingPrice,I.Price,I.HSN,I.BarcodeNo,(GROUP_CONCAT(CONCAT(IT.Tax,":",IT.TaxPercentage,"%") SEPARATOR " | ")) as taxes':'count(DISTINCT(I.ItemID)) as total_rows';
       	$query = $builder->select($select)
       							  ->join(ITEM_TAXES.' IT','IT.ItemID = I.ItemID','LEFT')
                                  ->join(ITEM_CATEGORY_MASTER.' ICM','ICM.ItemCategoryMasterID = I.ItemCategoryMasterID','LEFT')
                          		  ->getWhere(['I.IsDeleted !=' => '1','I.CompID' => $comp_id,'I.AddedDate <=' => $subscription_end_date])
                                  ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function fetchItemData($item_id,$comp_id){
    	$query = $this->inventory_db->table(ITEMS.' I')
                                    ->select('I.Item,I.Price,I.Qty,I.HSN,I.ItemType,I.BarcodeNo,I.ItemCategoryMasterID')
    				 	            ->getWhere(['I.ItemID' => $item_id,'I.CompID' => $comp_id])
    				 	            ->getRowArray();

    	return $query;
    }

    public function fetchItemTaxesDataViaItemID($item_id){
    	$query = $this->inventory_db->table(ITEM_TAXES.' IT')
                                    ->select('IT.Tax,IT.TaxPercentage')
    					  		    ->getWhere(['IT.ItemID' => $item_id])
    					  		    ->getResultArray();

    	return $query;
    }

    public function checkDuplicateItem($item_name,$comp_id, $item_id = 0){
        $query = $this->inventory_db->table(ITEMS)
                                    ->select('ItemID')
                                    ->getWhere(['Item' => $item_name,'CompID' => $comp_id,'ItemID !=' => $item_id])
                                    ->getRowArray();

        return $query;
 	}

 	public function fetchAllItems($comp_id){
 		$query = $this->inventory_db->table(ITEMS.' I')->select('I.Item,I.BuyingPrice,I.Price,I.HSN,I.ItemType,I.Qty,(GROUP_CONCAT(CONCAT(IT.Tax,":",IT.TaxPercentage) SEPARATOR "|")) as taxes,ITM.ItemCategory,I.BarcodeNo')
 										  ->join(ITEM_TAXES.' IT','IT.ItemID = I.ItemID','LEFT')
                                          ->join(ITEM_CATEGORY_MASTER.' ITM','ITM.ItemCategoryMasterID = I.ItemCategoryMasterID','LEFT')
 										  ->groupBy('I.ItemID')
                                          ->orderBy('I.Item','ASC')
 										  ->getWhere(['I.CompID' => $comp_id])
 										  ->getResultArray();
 		return $query;
 	}

    public function fetchAllGoods($comp_id){
        $query = $this->inventory_db->table(ITEMS.' I')
                                    ->select('I.ItemID,I.Item,I.BuyingPrice,I.Price,I.HSN,I.ItemType,I.Qty,(GROUP_CONCAT(CONCAT(IT.Tax,":",IT.TaxPercentage) SEPARATOR "|")) as taxes,ITM.ItemCategory,I.BarcodeNo')
                                          ->join(ITEM_CATEGORY_MASTER.' ITM','ITM.ItemCategoryMasterID = I.ItemCategoryMasterID','LEFT')
                                    ->join(ITEM_TAXES.' IT','IT.ItemID = I.ItemID','LEFT')
                                    ->groupBy('I.ItemID')
                                    ->orderBy('I.Item','ASC')
                                    ->getWhere(['I.CompID' => $comp_id,'I.ItemType' => 'Good'])
                                    ->getResultArray();
        return $query;
    }

    public function checkDuplicateBarcode($barcode_no,$comp_id, $item_id = 0){
        $query = $this->inventory_db->table(ITEMS)
                                    ->select('ItemID')
                                    ->getWhere(['BarcodeNo' => $barcode_no,'CompID' => $comp_id,'ItemID !=' => $item_id])
                                    ->getRowArray();

        return $query;
    }

    public function fetchItemDataViaItemName($item,$comp_id){
        $query = $this->inventory_db->table(ITEMS.' I')
                                    ->select('I.Item,I.Price,I.Qty,I.HSN,I.ItemType,I.BarcodeNo')
                                    ->getWhere(['I.Item' => $item,'I.CompID' => $comp_id])
                                    ->getRowArray();

        return $query;
    }

    public function saveItemCategory($item_category_data, $comp_id, $item_category_master_id = 0){
      if(empty($item_category_master_id)){
        $this->inventory_db->table(ITEM_CATEGORY_MASTER)->insert($item_category_data);
        return $this->inventory_db->insertID();
      }else{
        $this->inventory_db->table(ITEM_CATEGORY_MASTER)->update($item_category_data,['ItemCategoryMasterID' => $item_category_master_id,'CompID' => $comp_id]);
        return $item_category_master_id;
      }
    }

    public function checkDuplicateItemCategory($item_category,$comp_id, $item_category_master_id = 0){
        $query = $this->inventory_db->table(ITEM_CATEGORY_MASTER)
                                    ->select('ItemCategoryMasterID')
                                    ->getWhere(['ItemCategory' => $item_category,'CompID' => $comp_id,'ItemCategoryMasterID !=' => $item_category_master_id])
                                    ->getRowArray();

        return $query;
    }

    public function fetchItemCategoryList($comp_id,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false,$sort_by = '',$sort_order = ''){
        
        $builder = $this->inventory_db->table(ITEM_CATEGORY_MASTER.' ICM');

        if($count == false){
           $query = $builder->groupBy('ICM.ItemCategoryMasterID')
                                       ->limit($limit,$offset);
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $builder->orderBy($sort_by, $sort_order);
        }else{
            $builder->orderBy('ICM.ItemCategoryMasterID','ASC');
        }
        
        if(!empty($filter['search_txt'])){
           $query = $builder->where('(ICM.ItemCategory like "%'.$filter['search_txt'].'%")');
        }
        
        $select = ($count == false)?'ICM.ItemCategoryMasterID,ICM.ItemCategory':'count(DISTINCT(ICM.ItemCategoryMasterID)) as total_rows';
        $query = $builder->select($select)
                         ->getWhere(['ICM.CompID' => $comp_id,'ICM.AddedDate <=' => $subscription_end_date])
                         ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function fetchAllItemCategories($comp_id,$subscription_end_date){
      $query = $this->inventory_db->table(ITEM_CATEGORY_MASTER.' ICM')
                                  ->select('ICM.ItemCategoryMasterID,ICM.ItemCategory')
                                  ->where('(CompID is null or CompID = "'.$comp_id.'")')
                                  ->getWhere(['ICM.AddedDate <=' => $subscription_end_date])
                                  ->getResultArray();

      return $query;
    }

    public function deleteItem($item_id, $comp_id){
        $this->inventory_db->table(ITEMS)->delete(['ItemID' => $item_id,'CompID' => $comp_id]);
    }

    public function fetchItemCategoryData($item_category_master_id, $comp_id){
        $query = $this->inventory_db->table(ITEM_CATEGORY_MASTER)
                                    ->select('ItemCategoryMasterID')
                                    ->getWhere(['ItemCategoryMasterID' => $item_category_master_id,'CompID' => $comp_id])
                                    ->getRowArray();

        return $query;
    }

    public function fetchItemCategoryIDViaItemCategory($item_category,$comp_id){
        $query = $this->inventory_db->table(ITEM_CATEGORY_MASTER)
                                    ->select('ItemCategoryMasterID')
                                    ->getWhere(['ItemCategory' => $item_category,'CompID' => $comp_id])
                                    ->getRowArray();

        return $query;
    }

    public function reduceItemQty($comp_id, $item, $qty){
        $this->inventory_db->table(ITEMS)
                           ->set('Qty','(CASE WHEN Qty >= '.$qty.' THEN Qty - '.$qty.' ELSE Qty END)',FALSE)
                           ->where('CompID',$comp_id)
                           ->where('Item',$item)
                           ->update();
    }
}