<?php

namespace Modules\Webservices\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\respondTrait;

class Basic extends ResourceController {
    function __construct()
	{
		$this->region_model = new \Modules\Management\Models\Region_model();
	}
    
    public function get_states(){
        $country_id = $this->request->getGet('country_id');
        $region = $this->request->getGet('region');

        if(empty($country_id)){
            return $this->respond([
                'status' => false,
                'msg' => 'Country ID is required',
                'data' => []
            ], 501);
        }else{
            $states = $this->region_model->fetchStates($country_id);
            return $this->respond([
                'status' => true,
                'msg' => 'Following are the states',
                'data' => $states
            ], 200);
        }
    }
    
    public function get_cities(){
        $state_id = $this->request->getGet('state_id');
        if(empty($state_id)){
            return $this->respond([
                'status' => false,
                'msg' => 'State ID is required',
                'data' => []
            ], 501);
        }else{
            $cities = $this->region_model->fetchCities($state_id);
            return $this->respond([
                'status' => true,
                'msg' => 'Following are the cities',
                'data' => $cities
            ], 200);
        }
    }
}