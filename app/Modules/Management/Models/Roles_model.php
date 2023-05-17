<?php

namespace Modules\Management\Models;

use \CodeIgniter\Model;

class Roles_model extends Model {
    function __construct()
	{
		parent::__construct();
        
        $this->user_db = \Config\Database::connect('user_db');
        $this->db_name = $this->user_db->database;
	}

    public function fetchRolesList($comp_id,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false,$sort_by = '',$sort_order = ''){

        $builder = $this->user_db->table(ROLES_MASTER.' RM');

        if($count == false){
            $query = $builder->groupBy('RM.RoleID')
                             ->limit($limit,$offset);
        }
        
        if(!empty($filter['search_txt'])){
            $search_txt = $builder->escapeString($filter['search_txt']);
            $query = $builder->where('(RM.Role like "%'.$search_txt.'%" or RU.Name like "%'.$search_txt.'%")');
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $builder->orderBy($sort_by, $sort_order);
        }
        
        $select = ($count == false)?'RM.RoleID,RM.Role,RU.Name':'count(DISTINCT(RM.RoleID)) as total_rows';

        $query = $builder->select($select)
                         ->join(REGISTERED_USERS.' RU','RU.ID = RM.AddedBy','left')
                         ->getWhere(['RM.CompID' => $comp_id,'RM.AddedDate <=' => $subscription_end_date])
                         ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function saveRole($role_data, $role_id = 0){
        if(empty($role_id)){
            $this->user_db->table(ROLES_MASTER)->insert($role_data);
            return $this->user_db->insertID();
        }else{
            $this->user_db->table(ROLES_MASTER)->update($role_data,['RoleID' => $role_id]);
            return $role_id;
        }
    }

    public function checkRole($role,$comp_id, $role_id = 0){

        $builder = $this->user_db->table(ROLES_MASTER.' RM');

        if(!empty($role_id)){
            $query = $builder->where('RM.RoleID !=',$role_id);
        }

        $query = $builder->select('RM.RoleID')
                         ->where('(RM.Role = "'.$role.'" or RM.Role is null)')
                         ->get()
                         ->getRowArray();

        return $query;
    }

    public function fetchRoleData($role_id, $comp_id){
        $query = $this->user_db->table(ROLES_MASTER.' RM')
                               ->select('RM.Role')
                               ->getWhere(['RM.RoleID' => $role_id,'RM.CompID' => $comp_id])
                               ->getRowArray();

        return $query;
    }

    public function deleteRole($role_id){
        $this->user_db->table(ROLES_MASTER)->delete(['RoleID' => $role_id]);
    }
}