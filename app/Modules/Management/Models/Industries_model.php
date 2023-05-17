<?php

namespace Modules\Management\Models;

use \CodeIgniter\Model;

class Industries_model extends Model {
    function __construct()
	{
        $this->user_db = \Config\Database::connect('user_db');
        $this->db_name = $this->user_db->database;
	}

    public function saveIndustry($business_industry_data, $business_industry_id = 0){
        if(empty($business_industry_id)){
            $this->user_db->table(BUSINESS_INDUSTRY_MASTER)->insert($business_industry_data);
            return $this->user_db->insertID();
        }else{
            $this->user_db->table(BUSINESS_INDUSTRY_MASTER)->update($business_industry_data,['BusinessIndustryID' => $business_industry_id]);
            return $business_industry_id;
        }
    }

    public function checkIndustry($business_industry,$comp_id, $industry_id = 0){

        $builder = $this->user_db->table(BUSINESS_INDUSTRY_MASTER.' BIM');

        if(!empty($industry_id)){
            $query = $builder->where('BIM.BusinessIndustryID !=',$industry_id);
        }

        $query = $builder->select('BIM.BusinessIndustryID')
                         ->where('(BIM.BusinessIndustry = "'.$business_industry.'" or BIM.BusinessIndustry is null)')
                         ->get()
                         ->getRowArray();

        return $query;
    }

    public function fetchIndustryData($industry_id, $comp_id){
        $query = $this->user_db->table(BUSINESS_INDUSTRY_MASTER.' BIM')
                               ->select('BIM.BusinessIndustry')
                               ->getWhere(['BIM.BusinessIndustryID' => $industry_id,'BIM.CompID' => $comp_id])
                               ->getRowArray();
        return $query;
    }

    public function deleteBusinessIndustry($industry_id){
        $this->user_db->table(BUSINESS_INDUSTRY_MASTER)->delete(['BusinessIndustryID' => $industry_id]);
    }

    public function fetchIndustriesList($comp_id,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false,$sort_by = 0, $sort_order = 0){

        $builder = $this->user_db->table(BUSINESS_INDUSTRY_MASTER.' BIM');

        if($count == false){
            $query = $builder->groupBy('BIM.BusinessIndustryID')
                              ->limit($limit,$offset);
        }
        
        if(!empty($filter['search_txt'])){
            $search_txt = $this->user_db->escapeString($filter['search_txt']);
            $query = $this->user_db->where('(BIM.BusinessIndustry like "%'.$search_txt.'%" or RU.Name like "%'.$search_txt.'%")');
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $builder->orderBy($sort_by, $sort_order);
        }
        
        $select = ($count == false)?'BIM.BusinessIndustryID,BIM.BusinessIndustry,RU.Name':'count(DISTINCT(BIM.BusinessIndustryID)) as total_rows';
        $query = $builder->select($select)
                          ->join(REGISTERED_USERS.' RU','RU.ID = BIM.AddedBy','left')
                          ->getWhere(['BIM.CompID' => $comp_id,'BIM.AddedDate <=' => $subscription_end_date])
                          ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }
}