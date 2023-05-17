<?php 
	namespace Modules\Layouts\Models;

	use CodeIgniter\Model;

	class User_model extends Model
	{
		
		function __construct()
		{
			parent::__construct();
        
	        $this->db = \Config\Database::connect('default');

	        $this->user_db = \Config\Database::connect('user_db');
	        $this->db_name = $this->user_db->database;

	        $this->finance_db = \Config\Database::connect('finance');
	        $this->finance_db_name = $this->finance_db->database;

	        $this->inventory_db = \Config\Database::connect('inventory');
	        $this->inventory_db_name = $this->inventory_db->database;
		}

		public function fetchUserData($username, $password, $comp_id, $device_id = ''){
	        $query = $this->user_db->table(REGISTERED_USERS.' RU')->select('RU.ID,RU.Name,RU.EmailID,RU.CompID,RU.InsertedBy,PM.Privilege,RU.Status,RU.Mobile,RU.ReferralCode,DATE_FORMAT(RU.InsertedDate,"%d %M %Y") as InsertedDate')
	                               ->join(PRIVILEGES_MASTER.' PM','PM.PrivilegeID = RU.PrivilegeID','left')
	                               ->groupBy('RU.ID')
	                               ->getWhere(['RU.EmailID' => $username, 'Password' => $password,'RU.Deleted !=' => '1','RU.CompID' => $comp_id])
	                               ->getRowArray();

	        if(!empty($query)){
	            $query['apps'] = $this->fetchEmployeeApps($query['ID']);
	            $query['app_id'] = array_column($query['apps'],'AppID');
	        }

	        return $query;
	    }

	    public function fetchEmployeeApps($registered_user_id){
	        $query = $this->user_db->table(REGISTERED_USER_APP_MAPPER.' RUAM')->select('A.AppID,A.App,A.IconPath,A.UserURL,RUAM.SubscriptionEndDate,SP.PlanName')
	                          ->join(APPS.' A','A.AppID = RUAM.AppID','left')
	                          ->join(SUBSCRIPTION_PLANS.' SP','SP.SubscriptionPlanID = RUAM.SubscriptionPlanID','LEFT')
	                          ->whereIn('A.App',['CRM','POS'])
	                          ->getWhere(['RUAM.RegisteredUserID' => $registered_user_id])
	                          ->getResultArray();
	        $apps = [];
	        if(!empty($query)){
	            for($i=0;$i<count($query);$i++){
	                $apps[$query[$i]['App']] = $query[$i];
	            }
	        }

	        // echo "<pre>";
	        // print_r($apps);
	        // exit;

	        return $apps;
	    }
	    
	    public function fetchFirmTypes(){
	        $query = $this->user_db->table(FIRM_TYPE_MASTER)
	        					   ->get()
	        					   ->getResultArray();
	        
	        return $query;
	    }
	    
	    public function fetchRoles($comp_id = 0){
	        $query = $this->user_db->table(ROLES_MASTER)->where('(CompID is null or CompID = "'.$comp_id.'")')
	                               ->get()
	                               ->getResultArray();
	        
	        return $query;
	    }
	    
	    public function fetchBanks(){
	        $query = $this->user_db->table(BANKS_MASTER)
	        				  	   ->get()
	                          	   ->getResultArray();
	        return $query;
	    }
	    
	    public function fetchServices(){
	        $query = $this->user_db->table(SERVICE_MASTER)
	        					   ->get()
	                          	   ->getResultArray();
	        
	        return $query;
	    }
	    
	    public function businessIndustries($comp_id = 0){
	        $query = $this->user_db->table(BUSINESS_INDUSTRY_MASTER)
	        					   ->where('(CompID is null or CompID = "'.$comp_id.'")')
	                               ->get()
	                               ->getResultArray();
	        
	        return $query;
	    }

	    public function fetchPrivileges(){
	        $query = $this->user_db->table(PRIVILEGES_MASTER)
	        				  	   ->get()
	                          	   ->getResultArray();

	        return $query;
	    }

	    public function fetchCompDetailsViaName($comp_name){
	        $query = $this->user_db->table(COMPANY_MASTER.' CM')
	        					   ->select('CompID')
	                               ->getWhere(['CM.CompName' => $comp_name])
	                               ->getRowArray();

	        return $query;
	    }

	    public function fetchUserDataViaUserName($username, $comp_id){
	        $query = $this->user_db->table(REGISTERED_USERS.' RU')->select('RU.ID,RU.Name,RU.EmailID,RU.CompID,RU.InsertedBy,PM.Privilege,RU.Status')
	                               ->join(PRIVILEGES_MASTER.' PM','PM.PrivilegeID = RU.PrivilegeID','left')
	                               ->getWhere(['RU.EmailID' => $username ,'RU.Deleted !=' => '1','RU.CompID' => $comp_id])
	                               ->getRowArray();
	        return $query;
	    }

	    public function fetchCompDetailsViaNameAndFirmType($comp_name,$firm_type_id){
	        $query = $this->user_db->table(COMPANY_MASTER.' CM')
	        					   ->select('CompID')
	                          	   ->getWhere(['CM.CompName' => $comp_name,'FirmTypeID' => $firm_type_id])
	                          	   ->getRowArray();

	        return $query;
	    }

	    public function fetchPrivilegeIDViaPrivelege($privilege){
	        $query = $this->user_db->table(PRIVILEGES_MASTER)->getWhere(['Privilege' => $privilege])
	                               ->getRowArray();

	        return $query;
	    }

	    public function fetchUserIDViaRefferalCode($referral_code){
	        $query = $this->user_db->table(REGISTERED_USERS.' RU')
	        					   ->select('RU.ID')
	                          	   ->getWhere(['RU.ReferralCode' => $referral_code])
	                          	   ->getRowArray();

	        return (!empty($query))?$query['ID']:null;
	    }

	    public function fetchAllApps(){
	        $query = $this->user_db->table(APPS.' A')
	        					   ->select('A.AppID,A.App,A.IconPath,A.UserURL')
	                               ->whereIn('A.App',['CRM','POS'])
	                               ->get()
	                               ->getResultArray();
	        return $query;
	    }
	}

?>