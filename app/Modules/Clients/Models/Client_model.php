<?php

namespace Modules\Clients\Models;

use \CodeIgniter\Model;

class Client_model extends Model {
    function __construct()
    {
        parent::__construct();
        
        $this->user_db = \Config\Database::connect('user_db');
        $this->db_name = $this->user_db->database;

        $this->finance_db = \Config\Database::connect('finance');
        $this->finance_db_name = $this->finance_db->database;
    }

    public function checkClient($client_name,$client_id, $comp_id){
        $query = $this->user_db->table(CLIENTS)
                               ->select('ClientID')
                               ->getWhere(['ClientName' => $client_name,'ClientID !=' => $client_id,'CompID' => $comp_id,'ClientStatus !=' => 'Deleted'])
                               ->getRowArray();
        return $query;
    }
    
    public function saveClient($client_data,$client_id = 0){
        if($client_id == 0){
            $this->user_db->table(CLIENTS)->insert($client_data);
            return $this->user_db->insertID();
        }else{
            $this->user_db->table(CLIENTS)->update($client_data,['ClientID' => $client_id]);
            return $client_id;
        }
    }
    
    public function fetchClientList($comp_id,$logged_in_user_data,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false, $sort_by = '', $sort_order = ''){
        
        $builder = $this->user_db->table(CLIENTS.' C');

        if($count == false){
            $query = $builder->groupBy('C.ClientID')
                              ->limit($limit,$offset);
        }
        
        if(!empty($filter['search_txt'])){
            $query = $builder->where('(C.ClientName like "%'.$filter['search_txt'].'%" or RU.Name like "%'.$filter['search_txt'].'%")');
        }

        if($logged_in_user_data['Privilege'] != 'Admin'){
            $query = $builder->where('(C.AddedBy = "'.$logged_in_user_data['ID'].'")');
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $builder->orderBy($sort_by, $sort_order);
        }
        
        $select = ($count == false)?'C.ClientID,C.ClientName,GROUP_CONCAT(DISTINCT(BIM.BusinessIndustry)) as BusinessIndustry,C.AddedBy,RU.Name,(CASE WHEN CU.ClientUserFirstName is not null THEN CU.ClientUserFirstName ELSE "" END) as ClientUserFirstName, (CASE WHEN CU.ClientUserLastName is not null THEN CU.ClientUserLastName ELSE "" END) as ClientUserLastName,(CASE WHEN CU.ClientUserContactNo is NOT NULL THEN CU.ClientUserContactNo ELSE "" END) as ClientUserContactNo,(CASE WHEN CU.ClientUserEmailID is NOT NULL THEN CU.ClientUserEmailID ELSE "" END) as ClientUserEmailID,SM.StateName,CM.CityName,GROUP_CONCAT(DISTINCT(RM.Role)) as Role,C.ClientRating':'count(DISTINCT(C.ClientID)) as total_rows';
        $query = $builder->select($select)
                         ->join(REGISTERED_USERS.' RU','RU.ID = C.AddedBy','left')
                         ->join(CLIENT_BUSINESS_INDUSTRY_MAPPER_DATA.' CBIMD','CBIMD.ClientID = C.ClientID','left')
                         ->join(BUSINESS_INDUSTRY_MASTER.' BIM','BIM.BusinessIndustryID = CBIMD.BusinessIndustryID','left')
                         ->join(CLIENT_USERS.' CU','CU.ClientID = C.ClientID','left')
                         ->join(CLIENT_USER_ROLES_MAPPER.' CURM','CURM.ClientUserID = CU.ClientUserID','left')
                         ->join(ROLES_MASTER.' RM','RM.RoleID = CURM.RoleID','left')
                         ->join(CLIENT_GEOGRAPHY.' CG','CG.ClientID = C.ClientID','left')
                         ->join(STATES_MASTER.' SM','SM.StateID = CG.StateID','left')
                         ->join(CITIES_MASTER.' CM','CM.CityID = CG.CityID','left')
                          
                         ->getWhere(['C.ClientStatus !=' => 'Deleted','C.CompID' => $comp_id,'C.AddedDate <=' => $subscription_end_date])
                         ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }
    
    public function fetchClientData($client_id, $comp_id){
        $query = $this->user_db->table(CLIENTS.' C')
                               ->select('C.*,CG.ClientGeographyID,CG.CountryID,CG.StateID,CG.CityID,GROUP_CONCAT(DISTINCT(CBIMD.BusinessIndustryID)) as BusinessIndustryID,CG.Address,CU.ClientUserID,CU.ClientUserFirstName,CU.ClientUserLastName,CU.ClientUserContactNo,CU.ClientUserEmailID,GROUP_CONCAT(DISTINCT(CURM.RoleID)) as RoleID')
                               ->join(CLIENT_GEOGRAPHY.' CG','CG.ClientID = C.clientID and CG.IsHeadOffice = 1','left')
                               ->join(CLIENT_BUSINESS_INDUSTRY_MAPPER_DATA.' CBIMD','CBIMD.ClientID = C.ClientID','left')
                               ->join(CLIENT_USERS.' CU','CU.ClientID = C.ClientID','left')
                               ->join(CLIENT_USER_ROLES_MAPPER.' CURM','CURM.ClientUserID = CU.ClientUserID','left')
                               ->groupBy('C.ClientID')
                               ->getWhere(['C.ClientID' => $client_id,'C.CompID' => $comp_id])
                               ->getRowArray();
        return $query;
    }
    
    public function saveClientBusinessIndustryBatch($client_industry_mapper_data,$client_id){
        $this->user_db->table(CLIENT_BUSINESS_INDUSTRY_MAPPER_DATA)->delete($client_id);    
        
        $this->user_db->table(CLIENT_BUSINESS_INDUSTRY_MAPPER_DATA)->insertBatch($client_industry_mapper_data);
    }

    public function saveClientGeography($client_geography_data,$client_geography_id = 0){
        if(empty($client_geography_id)){
            $this->user_db->table(CLIENT_GEOGRAPHY)->insert($client_geography_data);
            return $this->user_db->insertID();
        }else{
            $this->user_db->table(CLIENT_GEOGRAPHY)->update($client_geography_data,['ClientGeographyID' => $client_geography_id]);
            return $client_geography_id;
        }
    }
    
    public function saveClientUserData($client_user_data, $client_user_id = 0){
        if(empty($client_user_id)){
            $this->user_db->table(CLIENT_USERS)->insert($client_user_data);
            return $this->user_db->insertID();
        }else{
            $this->user_db->table(CLIENT_USERS)->update($client_user_data,['ClientUserID' => $client_user_id]);
            return $client_user_id;
        }
    }
    
    public function saveClientUserRoleMapperData($client_user_role_mapper_data, $client_user_id){
        $this->user_db->table(CLIENT_USER_ROLES_MAPPER)->delete(['ClientUserID' => $client_user_id]);
        $this->user_db->table(CLIENT_USER_ROLES_MAPPER)->insertBatch($client_user_role_mapper_data);
    }




    public function fetchClientFullDetails($client_id,$comp_id){
        $query = $this->user_db->table(CLIENTS.' C')
                               ->select('C.ClientName,CG.ClientGeographyID,FTM.FirmType,CG.Address,GROUP_CONCAT(DISTINCT(BusinessIndustry)) as BusinessIndustry,CU.ClientUserFirstName,CU.ClientUserLastName,CU.ClientUserEmailID,CU.ClientUserContactNo,GROUP_CONCAT(DISTINCT(RM.Role)) as Roles,BM.BankName,CBD.AccountHolderName,CBD.AccountNo,CBD.ChequeImgPath,BD.BankBranch,BD.BankIFSC,BD.BankMICR')
                               ->join(FIRM_TYPE_MASTER.' FTM','FTM.FirmTypeID = C.FirmTypeID','left')
                               ->join(CLIENT_GEOGRAPHY.' CG','CG.ClientID = C.ClientID and CG.IsHeadOffice = 1','left')
                               ->join(CLIENT_BUSINESS_INDUSTRY_MAPPER_DATA.' CBIMD','CBIMD.ClientID = C.ClientID')
                               ->join(BUSINESS_INDUSTRY_MASTER.' BIM','BIM.BusinessIndustryID = CBIMD.BusinessIndustryID','left')
                               ->join(CLIENT_USERS.' CU','CU.ClientID = C.ClientID and IsPrimaryUser = 1','left')
                               ->join(CLIENT_USER_ROLES_MAPPER.' CURM','CURM.ClientUserID = CU.ClientUserID','left')
                               ->join(ROLES_MASTER.' RM','RM.RoleID = CURM.RoleID','left')
                               ->join(CLIENT_BANKING_DOCUMENTS.' CBD','CBD.ClientID = C.ClientID','left')
                               ->join(BANK_DETAILS.' BD','BD.BankDetailsID = CBD.BankDetailsID','left')
                               ->join(BANKS_MASTER.' BM','BM.BankID = BD.BankID','left')
                               ->groupBy('C.ClientID')
                               ->getWhere(['C.ClientID' => $client_id,'C.CompID' => $comp_id])
                               ->getRowArray();
        
        return $query;
    }


    public function saveClientUserGeographicalData($client_user_geographic_data, $client_user_id = 0){
        if($client_user_id != 0){
            $this->user_db->table(CLIENT_USER_GEOGRAPHIC_DATA)->delete(['ClientUserID' => $client_user_id]);
        }
        $this->user_db->table(CLIENT_USER_GEOGRAPHIC_DATA)->insertBatch($client_user_geographic_data);
    }

    public function checkClientCIN($cin, $client_id, $comp_id){
        $query = $this->user_db->table(CLIENTS.' C')
                               ->select('ClientName')
                               ->where('C.CIN is not null')
                               ->getWhere(['C.CIN' => $cin,'ClientID !=' => $client_id,'CompID' => $comp_id])
                               ->getRowArray();
        return $query;
    }

    public function fetchClientFullData($comp_id,$logged_in_user_data,$subscription_end_date){

        $builder = $this->user_db->table(CLIENTS.' C');

        if($logged_in_user_data['Privilege'] != 'Admin'){
            $query = $builder->where('(C.AddedBy = "'.$logged_in_user_data['ID'].'")');
        }
    
        $query = $builder->select('C.ClientName,GROUP_CONCAT(DISTINCT(BIM.BusinessIndustry)) as BusinessIndustry,CONCAT(CU.ClientUserFirstName," ",CU.ClientUserLastName) as ContactPerson,GROUP_CONCAT(DISTINCT(RM.Role)) as Role,CU.ClientUserContactNo,CU.ClientUserEmailID,SM.StateName,CM.CityName')
                          ->join(REGISTERED_USERS.' RU','RU.ID = C.AddedBy','left')
                          ->join(CLIENT_BUSINESS_INDUSTRY_MAPPER_DATA.' CBIMD','CBIMD.ClientID = C.ClientID','left')
                          ->join(BUSINESS_INDUSTRY_MASTER.' BIM','BIM.BusinessIndustryID = CBIMD.BusinessIndustryID','left')
                          ->join(CLIENT_USERS.' CU','CU.ClientID = C.ClientID','left')
                          ->join(CLIENT_USER_ROLES_MAPPER.' CURM','CURM.ClientUserID = C.ClientID','left')
                          ->join(ROLES_MASTER.' RM','RM.RoleID = CURM.RoleID','left')
                          ->join(CLIENT_GEOGRAPHY.' CG','CG.ClientID = C.ClientID','left')
                          ->join(STATES_MASTER.' SM','SM.StateID = CG.StateID','left')
                          ->join(CITIES_MASTER.' CM','CM.CityID = CG.CityID','left')
                          ->orderBy('C.ClientName','ASC')
                          ->groupBy('C.ClientID')
                          ->getWhere(['C.ClientStatus !=' => 'Deleted','C.CompID' => $comp_id,'C.AddedDate <=' => $subscription_end_date])
                          ->getResultArray();
        
        return $query;
    }

    public function fetchClients($comp_id,$subscription_end_date,$limit = 30,$offset = 0,$filter = [],$count = false, $client_id = 0){
        
        $builder = $this->user_db->table(CLIENTS.' C');

        if($count == false){
            $query = $builder->groupBy('C.ClientID')
                              ->limit($limit,$offset);
        }
        
        if(!empty($filter['search_txt'])){
            $query = $builder->where('(C.ClientName like "%'.$filter['search_txt'].'%")');
        }

        if(!empty($client_id)){
            $query = $builder->orderBy('(CASE WHEN ClientID = "'.$client_id.'" THEN 1 ELSE 2 END)','ASC');
        }
        
        $select = ($count == false)?'C.ClientID,C.ClientName':'count(C.ClientID) as total_rows';
        $query = $builder->select($select)
                          ->getWhere(['C.ClientStatus !=' => 'Deleted','C.CompID' => $comp_id,'C.AddedDate <=' => $subscription_end_date])
                          ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function fetchBasicClientDetails($client_id, $comp_id){
        $query = $this->user_db->table(CLIENTS.' C')
                               ->select('C.CIN,C.ClientName,C.FirmTypeID')
                               ->getWhere(['C.ClientID' => $client_id,'C.CompID' => $comp_id])
                               ->getRowArray();
        return $query;
    }

    public function fetchClientServiceTaxes($client_id, $comp_id){
        $query = $this->finance_db->table(CLIENT_SERVICE_TAX_TYPE_MAPPER.' CSTTM')
                                  ->select('CSTTM.ClientServiceTaxID,CSTTM.Label,STTM.ServiceTaxType,CSTTM.ServiceTaxNumber,CSTTM.BillingAddress,CSTTM.ServiceTaxTypeID')
                                  ->join($this->db_name.'.'.CLIENTS.' C','C.ClientID = CSTTM.ClientID')
                                   ->join(SERVICE_TAX_TYPES_MASTER.' STTM','STTM.ServiceTaxTypeID = CSTTM.ServiceTaxTypeID','left')
                                  ->getWhere(['CSTTM.ClientID' => $client_id,'C.CompID' => $comp_id])
                                  ->getResultArray();

        return $query;
    }

    public function fetchClientServiceTaxData($client_service_tax_id,$comp_id){
        $query = $this->finance_db->table(CLIENT_SERVICE_TAX_TYPE_MAPPER.' CSTTM')
                                  ->select('CSTTM.Label,CSTTM.ServiceTaxTypeID,CSTTM.ServiceTaxNumber,CSTTM.ClientID,CSTTM.BillingCountryID,CSTTM.BillingStateID,CSTTM.BillingAddress')
                                  ->join($this->db_name.'.'.CLIENTS.' C','C.ClientID = CSTTM.ClientID')
                                  ->getWhere(['CSTTM.ClientServiceTaxID' => $client_service_tax_id,'C.CompID' => $comp_id])
                                  ->getRowArray();

        return $query;
    }

    public function checkClientServiceTaxNumber($service_tax_number,$client_service_tax_id,$client_id,$service_tax_type)
    {
        $query = $this->finance_db->table(CLIENT_SERVICE_TAX_TYPE_MAPPER)
                                  ->select('ClientServiceTaxID')
                                  ->getWhere(['ServiceTaxNumber' => $service_tax_number,'ServiceTaxTypeID' => $service_tax_type,'ClientID' => $client_id,'ClientServiceTaxID !=' => $client_service_tax_id])
                                  ->getRowArray();

        return $query;
    }

    public function saveClientServiceTax($client_service_tax_data, $client_service_tax_id = 0){
        if(empty($client_service_tax_id)){
            $this->finance_db->table(CLIENT_SERVICE_TAX_TYPE_MAPPER)->insert($client_service_tax_data);
            return $this->finance_db->insertID();
        }else{
            $this->finance_db->table(CLIENT_SERVICE_TAX_TYPE_MAPPER)->update($client_service_tax_data,['ClientServiceTaxID' => $client_service_tax_id]);
            return $client_service_tax_id;
        }
    }

    public function deleteClientServiceTax($client_service_tax_id)
    {
        $this->finance_db->table(CLIENT_SERVICE_TAX_TYPE_MAPPER)->delete(['ClientServiceTaxID' => $client_service_tax_id]);
    }

    public function checkClientServiceTaxLabel($label,$client_service_tax_id,$client_id)
    {
        $query = $this->finance_db->table(CLIENT_SERVICE_TAX_TYPE_MAPPER)
                                  ->select('ClientServiceTaxID')
                                  ->getWhere(['Label' => $label,'ClientID' => $client_id,'ClientServiceTaxID !=' => $client_service_tax_id])
                                  ->getRowArray();

        return $query;
    }
}