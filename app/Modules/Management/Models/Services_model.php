<?php

namespace Modules\Management\Models;

use \CodeIgniter\Model;

class Services_model extends Model {
    function __construct()
	{
		parent::__construct();
        
        $this->user_db = \Config\Database::connect('user_db');
        $this->db_name = $this->user_db->database;
	}

	public function fetchServicesList($comp_id,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false,$sort_by = '',$sort_order = ''){

        $builder = $this->user_db->table(SERVICE_MASTER.' SM');

        if($count == false){
            $query = $builder->groupBy('SM.ServiceID')
                             ->limit($limit,$offset);
        }
        
        if(!empty($filter['search_txt'])){
            $search_txt = $filter['search_txt'];
            $query = $builder->where('(SM.ServiceType like "%'.$search_txt.'%" or RU.Name like "%'.$search_txt.'%")');
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $builder->orderBy($sort_by, $sort_order);
        }
        
        $select = ($count == false)?'SM.ServiceID,SM.ServiceType,RU.Name':'count(DISTINCT(SM.ServiceID)) as total_rows';
        $query = $builder->select($select)
                         ->join(REGISTERED_USERS.' RU','RU.ID = SM.AddedBy','left')
                         ->getWhere(['SM.CompID' => $comp_id,'SM.AddedDate <=' => $subscription_end_date])
                         ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function saveService($service_data, $service_id = 0){
        if(empty($service_id)){
            $this->user_db->table(SERVICE_MASTER)->insert($service_data);
            return $this->user_db->insertID();
        }else{
            $this->user_db->table(SERVICE_MASTER)->update($service_data,['ServiceID' => $service_id]);
            return $service_id;
        }
    }

    public function checkService($service,$comp_id, $service_id = 0){

        $builder = $this->user_db->table(SERVICE_MASTER.' SM');

        if(!empty($service_id)){
            $query = $builder->where('SM.ServiceID !=',$service_id);
        }

        $query = $builder->select('SM.ServiceID')
                          ->where('(SM.ServiceType = "'.$service.'" or SM.ServiceType is null)')
                          ->get()
                          ->getRowArray();

        return $query;
    }

    public function fetchServiceData($service_id, $comp_id){
        $query = $this->user_db->table(SERVICE_MASTER.' SM')
                               ->select('SM.ServiceType')
                               ->getWhere(['SM.ServiceID' => $service_id,'SM.CompID' => $comp_id])
                               ->getRowArray();

        return $query;
    }

    public function deleteService($service_id){
        $this->user_db->table(SERVICE_MASTER)->delete(['ServiceID' => $service_id]);
    }
}