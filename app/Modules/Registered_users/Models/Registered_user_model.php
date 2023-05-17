<?php

namespace Modules\Registered_users\Models;

use CodeIgniter\Model;

class Registered_user_model extends Model {

    function __construct()
	{
		parent::__construct();
        
        $this->user_db = \Config\Database::connect('user_db');
        $this->db_name = $this->user_db->database;

        $this->finance_db = \Config\Database::connect('finance');
        $this->finance_db_name = $this->finance_db->database;
	}

    public function duplicateUser($email_id,$user_id, $comp_id){
        $query = $this->user_db->table(REGISTERED_USERS)->select('ID')
                          ->getWhere(['ID !=' => $user_id,'EmailID' => $email_id, 'CompID' => $comp_id])
                          ->getRowArray();
        return $query;
    }

    public function saveUser($user_data,$user_id = 0){
        if(empty($user_id)){
            $this->user_db->table(REGISTERED_USERS)->insert($user_data);
            return $this->user_db->insertID();
        }else{
            $this->user_db->table(REGISTERED_USERS)->update($user_data,['ID' => $user_id]);
            return $user_id;
        }
    }

    public function saveUserRoles($user_roles_mapper_data,$user_id){
        $this->user_db->table(REGISTERED_USER_ROLE_MAPPER)->delete(['RegisteredUserID' => $user_id]);
        $this->user_db->table(REGISTERED_USER_ROLE_MAPPER)->insertBatch($user_roles_mapper_data);
    }

    public function fetchUserList($comp_id,$app_id,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false,$prevent_user_id = 0,$sort_by = '',$sort_order = ''){
        
        $builder = $this->user_db->table(REGISTERED_USERS.' RU');

        if($count == false){
            $query = $builder->groupBy('RU.ID')
                              ->limit($limit,$offset);
        }

        if(!empty($prevent_user_id)){
            $query = $builder->where('RU.ID !=',$prevent_user_id);
        }
        
        if(!empty($filter['search_txt'])){
            $query = $builder->where('(RU.Name like "%'.$filter['search_txt'].'%" or RU.EmailID like "%'.$filter['search_txt'].'%" or RU.Gender like "%'.$filter['search_txt'].'%" or RU.InsertedDate like "%'.$filter['search_txt'].'%" or ERU.Name like "%'.$filter['search_txt'].'%")');
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $builder->orderBy($sort_by, $sort_order);
        }
        
        $select = ($count == false)?'RU.ID,RU.Name,RU.EmailID,RU.Gender,RU.InsertedDate,ERU.Name as AddedBy,RU.Status,(CASE WHEN DATE_FORMAT(SubscriptionEndDate,"%Y%m%d%H%i%s") > DATE_FORMAT("'.date('YmdHis').'","%Y%m%d%H%i%s") THEN DATE_FORMAT(RUAM.SubscriptionEndDate, "%d %b %Y - %H:%i:%s") ELSE "Re-new" END) as SubscriptionEndDate':'count(DISTINCT(RU.ID)) as total_rows';
        $query = $builder->select($select)
                          ->join(REGISTERED_USERS.' ERU','ERU.ID = RU.InsertedBy','left')
                          ->join(REGISTERED_USER_APP_MAPPER.' RUAM','RUAM.RegisteredUserID = RU.ID','left')
                          ->where('(RU.Deleted != 1 or RU.Deleted is NULL)',NULL,FALSE)
                          ->getWhere(['RU.CompID' => $comp_id,'RU.InsertedDate <=' => $subscription_end_date,'RUAM.AppID' => $app_id])
                          ->getResultArray();
        // echo $this->user_db->last_query();
        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function fetchUserDetails($user_id, $comp_id){
        $query = $this->user_db->table(REGISTERED_USERS.' RU')
                               ->select('RU.ID,RU.Name,RU.EmailID,RU.Mobile,RU.Gender,GROUP_CONCAT(DISTINCT(RURM.RoleID)) as RoleID,RU.PrivilegeID,RU.InsertedBy,CM.CompName,FTM.FirmType,CSTM.ServiceTaxTypeID,CSTM.ServiceTaxIdentificationNumber,STTM.ServiceTaxType,RU.ReferredBy')
                              ->join(REGISTERED_USER_ROLE_MAPPER.' RURM','RURM.RegisteredUserID = RU.ID','left')
                              ->join(COMPANY_MASTER.' CM','CM.CompID = RU.CompID','LEFT')
                              ->join(FIRM_TYPE_MASTER.' FTM','FTM.FirmTypeID = CM.FirmTypeID','LEFT')
                              ->join($this->finance_db_name.'.'.COMPANY_SERVICE_TAX_MASTER.' CSTM','CSTM.CompID = CM.CompID','LEFT')
                              ->join($this->finance_db_name.'.'.SERVICE_TAX_TYPES_MASTER.' STTM','STTM.ServiceTaxTypeID = CSTM.ServiceTaxTypeID','LEFT')
                              ->groupBy('RU.ID')
                              ->getWhere(['RU.ID' => $user_id,'RU.CompID' => $comp_id])
                              ->getRowArray();
        return $query;
    }

    public function UpdateUserStatus($user_id){
        $this->user_db->table(REGISTERED_USERS)
                      ->set('Status','CASE WHEN Status = "Active" THEN "Disabled" ELSE "Active" END',FALSE)
                      ->where('ID',$user_id)
                      ->update();
    }

    public function saveUserApps($registered_user_app_mapper, $update = true){
        if($update == true){
            $this->user_db->table(REGISTERED_USER_APP_MAPPER)->update($registered_user_app_mapper,['AppID' => $registered_user_app_mapper['AppID'],'RegisteredUserID' => $registered_user_app_mapper['RegisteredUserID']]);
        }else{
            $this->user_db->table(REGISTERED_USER_APP_MAPPER)->insert($registered_user_app_mapper);
        }
    }

    public function saveUserSubscriptionLogs($user_subscription_log_data){
        $this->user_db->table(REGISTERED_USER_SUBSCRIPTION_LOGS)->insert($user_subscription_log_data);
    }

    public function saveReminder($reminder_data){
        $this->user_db->table(REMINDER)->insert($reminder_data);
        return $this->user_db->insertID();
    }

    public function fetchReminders($user_id){
        $query = $this->user_db->table(REMINDER)->select('ReminderID,Task,ReminderDate')
                          ->orderBy('ReminderDate','ASC')
                          ->limit(5)
                          ->getWhere(['AddedBy' => $user_id,'ReminderDate >=' => date('Y-m-d H:i:s')])
                          ->getResultArray();

        return $query;
    }

    public function fetchAllReminders($user_id,$limit = 10,$offset = 0,$filter = [],$count = false){
        $builder = $this->user_db->table(REMINDER.' R');

        if($count == false){
            $query = $builder->groupBy('R.ReminderID')
                              ->orderBy('R.ReminderDate','DESC')
                              ->limit($limit,$offset);
        }
        
        if(!empty($filter['search_txt'])){
            $query = $builder->where('(R.Task like "%'.$this->db->escapeString($filter['search_txt']).'%")');
        }

        $select = ($count == false)?'R.ReminderID,R.Task,R.ReminderDate':'count(R.ReminderID) as total_rows';
        $query = $builder->select($select)
                          ->getWhere(['R.AddedBy' => $user_id])
                          ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function fetchUserBasicDetails($user_id){
        $query = $this->user_db->table(REGISTERED_USERS.' RU')->select('RU.ID,RU.CommissionPercentage')
                          ->groupBy('RU.ID')
                          ->getWhere(['RU.ID' => $user_id])
                          ->getRowArray();
        return $query;
    }

    public function fetchBasicStatisticsCount($comp_id)
    {
        $query = $this->user_db->table(CLIENTS)->select('COUNT(ClientID) as total_clients,(SELECT COUNT(VendorID) from '.VENDORS.' where CompID = "'.$comp_id.'" and VendorStatus != "Deleted") as total_vendors,(SELECT COUNT(ID) from '.REGISTERED_USERS.' WHERE CompID = "'.$comp_id.'" and Deleted = "0") as total_users')
                          ->getWhere(['CompID' => $comp_id,'ClientStatus !=' => 'Deleted'])
                          ->getRowArray();

        return $query;
    }

    public function fetchRoleIDViaRole($role){
        $query = $this->user_db->table(ROLES_MASTER)
                               ->select('RoleID')
                               ->getWhere(['Role' => $role])
                               ->getRowArray();

        return $query;
    }

    public function fetchAllUsers($comp_id,$app_id){
        $query = $this->user_db->table(REGISTERED_USERS.' RU')->select('RU.ID,RU.Name')
                          ->join(REGISTERED_USERS.' ERU','ERU.ID = RU.InsertedBy','left')
                          ->join(REGISTERED_USER_APP_MAPPER.' RUAM','RUAM.RegisteredUserID = RU.ID','left')
                          ->where('(RU.Deleted != 1 or RU.Deleted is NULL)',NULL,FALSE)
                          ->getWhere(['RU.CompID' => $comp_id,'RUAM.AppID' => $app_id])
                          ->getResultArray();

        return $query;
    }

    public function saveRegisteredUserBankDetails($bank_details){
        $this->finance_db->table(REGISTERED_USER_BANK_ACCOUNT_DETAILS)->insert($bank_details);
    }

    public function UserExistingBankAccountDetails($registered_user_id){
        $query = $this->finance_db->table(REGISTERED_USER_BANK_ACCOUNT_DETAILS)
                                  ->select('RegisteredUserBankAccountDetailID')
                                  ->getWhere(['RegisteredUserID' => $registered_user_id])
                                  ->getRowArray();

        return $query;

    }

    public function fetchUserBankAccountDetails($registered_user_id){
        $query = $this->finance_db->table(REGISTERED_USER_BANK_ACCOUNT_DETAILS.' RUBAD')
                                  ->select('RUBAD.AccountNumber,BM.BankName,BD.BankIFSC,RUBAD.AccountType,RUBAD.AccountHolderName')
                                  ->join($this->db_name.'.'.BANKS_MASTER.' BM','BM.BankID = RUBAD.BankID')
                                  ->join($this->db_name.'.'.BANK_DETAILS.' BD','BD.BankDetailsID = RUBAD.BankDetailsID','LEFT')
                                  ->getWhere(['RUBAD.RegisteredUserID' => $registered_user_id])
                                  ->getRowArray();

        return $query;
    }

    public function fetchReferralDetails($registered_user_id,$commission_percentage,$limit = 10,$offset = 0,$count = false){

        $builder = $this->finance_db->table(TRANSACTIONS.' T');

        if($count == false){
            $query = $builder->groupBy('T.RegisteredUserID')
                                   ->orderBy('T.PaymentReceivedDate','DESC')
                                   ->limit($limit,$offset);
        }

        $select = ($count == false)?'RU.Name,RU.InsertedDate,T.PaymentReceivedDate,SUM(T.PlanAmount) as PlanAmount,ROUND(SUM(T.PlanAmount * T.ReferrerCommissionPercentage / 100),2) as earned_amount':'count(DISTINCT(T.RegisteredUserID)) as total_rows';

        $query  = $builder->select($select)
                          ->join($this->db_name.'.'.REGISTERED_USERS.' RU','RU.ID = T.RegisteredUserID')
                          ->getWhere(['RU.ReferredBy' => $registered_user_id,'T.Status' => 'paid','DATE_FORMAT(T.PaymentReceivedDate,"%Y-%m")' => date('Y-m')])
                          ->getResultArray();

        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function fetchReferralEarnings($registered_user_id,$commission_percentage){
        $query = $this->finance_db->table(TRANSACTIONS.' T')
                                  ->select('ROUND(SUM(T.PlanAmount * T.ReferrerCommissionPercentage / 100),2) as total_earnings, ROUND(SUM(CASE WHEN DATE_FORMAT(T.PaymentReceivedDate,"%Y-%m") = "'.date('Y-m').'" THEN (T.PlanAmount * T.ReferrerCommissionPercentage / 100) ELSE 0 END) ,2) as monthly_earning')
                                  ->join($this->db_name.'.'.REGISTERED_USERS.' RU','RU.ID = T.RegisteredUserID')
                                  ->getWhere(['RU.ReferredBy' => $registered_user_id,'T.Status' => 'paid'])
                                  ->getRowArray();
        return $query;
    }
}