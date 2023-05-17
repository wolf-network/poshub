<?php

namespace Modules\Management\Models;

use \CodeIgniter\Model;

class Region_model extends Model {
    function __construct()
	{
        $this->user_db = \Config\Database::connect('user_db');
        $this->db_name = $this->user_db->database;
	}

    public function fetchCountries(){
        $query = $this->user_db->table(COUNTRIES_MASTER)->select('CountryID,CountryName')
                          ->getWhere()
                          ->getResultArray();
        
        return $query;
    }
    
    public function fetchStates($country_id){

        $builder = $this->user_db->table(STATES_MASTER);

        if(!is_array($country_id)){
            $query = $builder->where('CountryID',$country_id);
        }else{
            $query = $builder->whereIn('CountryID',$country_id);
        }
        
        $query = $builder->select('StateID,StateName')
                          ->orderBy('StateName','ASC')
                          ->get()
                          ->getResultArray();
        
        return $query;
    }
    
    public function fetchCities($state_id){
        $builder = $this->user_db->table(CITIES_MASTER);

        if(!is_array($state_id)){
            $query = $builder->where('StateID',$state_id);
        }else{
            $query = $builder->whereIn('StateID',$state_id);
        }
        
        $query = $builder->select('CityID,CityName')
                          ->get()
                          ->getResultArray();
        
        return $query;
    }
}