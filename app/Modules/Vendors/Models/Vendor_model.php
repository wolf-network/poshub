<?php

namespace Modules\Vendors\Models;

use \CodeIgniter\Model;

class Vendor_model extends Model {
    function __construct()
    {
        $this->user_db = \Config\Database::connect('user_db');
        $this->db_name = $this->user_db->database;
        $this->finance_db = \Config\Database::connect('finance');
        $this->finance_db_name = $this->finance_db->database;
    }

    public function checkDuplicateVendor($vendor_name,$comp_id,$vendor_id = 0){
        $query = $this->user_db->table(VENDORS)
                               ->select('VendorID')
                               ->getWhere(['VendorName' => $vendor_name,'VendorID !=' => $vendor_id,'VendorStatus !=' => 'Deleted','CompID' => $comp_id])
                               ->getRowArray();
        return $query;
    }
    
    public function saveVendor($vendor_data, $vendor_id = 0){
        if($vendor_id == 0){
            $this->user_db->table(VENDORS)->insert($vendor_data);
            return $this->user_db->insertID();
        }else{
            $this->user_db->table(VENDORS)->update($vendor_data,['VendorID' => $vendor_id]);
            return $vendor_id;
        }
    }
    
    public function fetchVendorDetails($vendor_id, $comp_id){
        $query = $this->user_db->table(VENDORS.' V')
                               ->select('V.VendorName,V.FirmTypeID,VG.VendorGeographyID,VG.CountryID,VG.StateID,VG.CityID,VG.Address,VU.VendorUserID,VU.VendorUserFirstName,VU.VendorUserLastName,VU.VendorUserEmailID,VU.VendorUserContactNo,GROUP_CONCAT(DISTINCT(VURM.RoleID)) as RoleID,VBD.VendorBankingDocumentID,VBD.AccountHolderName,VBD.AccountNo,VBD.AccountNo as ConfirmAccountNo,BD.BankDetailsID,BM.BankID,VBD.ChequeImgPath,V.TaxIdentificationTypeID,V.TaxIdentificationNumber,V.CIN')
                               ->join(VENDOR_GEOGRAPHY.' VG','VG.VendorID = V.VendorID and VG.IsHeadOffice = 1','left')
                               ->join(VENDOR_USERS.' VU','VU.VendorID = V.VendorID and VU.IsPrimaryUser = 1','left')
                               ->join(VENDOR_USER_ROLES_MAPPER.' VURM','VURM.VendorUserID = VU.VendorUserID','left')
                               ->join(VENDOR_BANKING_DOCUMENTS.' VBD','VBD.VendorID = V.VendorID','left')
                               ->join(BANK_DETAILS.' BD','BD.BankDetailsID = VBD.BankDetailsID','left')
                               ->join(BANKS_MASTER.' BM','BM.BankID = BD.BankID','left')
                               ->groupBy('V.VendorID')
                               ->getWhere(['V.VendorID' => $vendor_id,'V.CompID' => $comp_id])
                               ->getRowArray();
        
        return $query;
    }
    
    public function saveVendorServices($vendor_service_mapper_data){
        $this->user_db->table(VENDOR_SERVICE_MAPPER)->insertBatch($vendor_service_mapper_data);
    }
    
    public function deleteVendorServices($vendor_id){
        $this->user_db->table(VENDOR_SERVICE_MAPPER)->delete(['VendorID' => $vendor_id]);
    }
    
    public function fetchVendorServices($vendor_id){
        $query = $this->user_db->table(VENDOR_SERVICE_MAPPER)
                               ->select('ServiceID')
                               ->getWhere(['VendorID' => $vendor_id])
                               ->getResultArray();
        
        return $query;
    }
    
    public function fetchVendorList($comp_id,$subscription_end_date,$limit = 10,$offset = 0,$filter = [],$count = false,$sort_by = '',$sort_order = ''){
        
        $builder = $this->user_db->table(VENDORS.' V');

        if($count == false){
            $query = $builder->groupBy('V.VendorID')
                              ->limit($limit,$offset);
        }
        
        if(!empty($filter['search_txt'])){
            $query = $builder->where('(V.VendorName like "%'.$filter['search_txt'].'%" or RU.Name like "%'.$filter['search_txt'].'%")');
        }

        if(!empty($sort_by) && !empty($sort_order)){
            $builder->orderBy($sort_by, $sort_order);
        }
        
        $select = ($count == false)?'V.VendorID,V.VendorName,RU.Name,VU.VendorUserFirstName,VU.VendorUserLastName,VU.VendorUserEmailID,VU.VendorUserContactNo':'count(DISTINCT(V.VendorID)) as total_rows';
        $query = $builder->select($select)
                          ->join(REGISTERED_USERS.' RU','RU.ID = V.RegisteredBy','left')
                          ->join(VENDOR_USERS.' VU','VU.VendorID = V.VendorID and VU.IsPrimaryUser = 1','left')
                          ->getWhere(['V.VendorStatus !=' => 'Deleted','V.CompID' => $comp_id,'V.RegisteredDate <=' => $subscription_end_date])
                          ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }
    
    public function fetchVendorDocumentsList($vendor_id,$comp_id,$limit = 10,$offset = 0,$filter = [],$count = false){
        
        $builder = $this->user_db->table(VENDOR_DOCUMENTS.' VD');

        if($count == false){
            $query = $builder->groupBy('VD.VendorGeographyID')
                              ->limit($limit,$offset);
        }
        
        if(!empty($filter['search_txt'])){
            $query = $builder->where('(CM.CountryName like "%'.$filter['search_txt'].'%")');
        }
        
        $select = ($count == false)?'VD.VendorDocumentID,VG.StateID,CM.CountryName,SM.StateName,VD.VendorGeographyID':'count(VD.VendorGeographyID) as total_rows';
        $query = $builder->select($select)
                         ->join(VENDORS.' V','V.VendorID = VD.VendorID','left')
                         ->join(VENDOR_GEOGRAPHY.' VG','VG.VendorGeographyID = VD.VendorGeographyID','left')
                         ->join(COUNTRIES_MASTER.' CM','CM.CountryID = VG.CountryID','left')
                         ->join(STATES_MASTER.' SM','SM.StateID = VG.StateID','left')
                         ->join(CITIES_MASTER.' CYM','CYM.CityID = VG.CityID','left')
                         ->getWhere(['VD.VendorID' => $vendor_id,'V.CompID' => $comp_id])
                         ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }
    
    public function saveVendorDocuments($vendor_document){
        $query = $this->user_db->table(VENDOR_DOCUMENTS)
                               ->select('VendorDocumentID')
                               ->getWhere(['VendorGeographyID' => $vendor_document['VendorGeographyID'],'DocumentName' => $vendor_document['DocumentName']])
                               ->getRowArray();
        
        $vendor_document_id = (!empty($query))?$query['VendorDocumentID']:0;

        if($vendor_document_id == 0){
            $this->user_db->table(VENDOR_DOCUMENTS)->insert($vendor_document);
            return $this->user_db->insertID();
        }else{
            $this->user_db->table(VENDOR_DOCUMENTS)->update($vendor_document,['VendorDocumentID' => $vendor_document_id]);
            return $vendor_document_id;
        }
    }
    
    public function saveVendorDocumentMediaData($vendor_document_media_data, $vendor_document_id = 0){

        
        if(empty($vendor_document_id)){
            if(!array_filter($vendor_document_media_data, 'is_array')){
                $this->user_db->table(VENDOR_DOCUMENT_MEDIA)->insert($vendor_document_media_data);
            }else{
                $this->user_db->table(VENDOR_DOCUMENT_MEDIA)->insertBatch($vendor_document_media_data);
            }
        }else{
            $this->user_db->table(VENDOR_DOCUMENT_MEDIA)->update($vendor_document_media_data,['VendorDocumentID' => $vendor_document_id]);
        }
    }
    
    public function fetchVendorDocumentData($vendor_geography_id,$document_name = []){

        $builder = $this->user_db->table(VENDOR_DOCUMENTS.' VD');

        if(!empty($document_description)){
            $query = $builder->whereIn('VD.DocumentName',$document_name);
        }
        $query = $builder->select('VD.VendorDocumentID,VD.DocumentName,VDM.VendorDocumentMediaPath,VD.DocumentDescription')
                          ->join(VENDOR_DOCUMENT_MEDIA.' VDM','VDM.VendorDocumentID = VD.VendorDocumentID','left')
                          ->groupBy('VD.VendorDocumentID')
                          ->getWhere(['VD.VendorGeographyID' => $vendor_geography_id])
                          ->getResultArray();
        return $query;
    }
    
    public function saveVendorUser($vendor_user_data,$vendor_user_id = 0){
        if($vendor_user_id != 0){
            $this->user_db->table(VENDOR_USERS)->update($vendor_user_data,['VendorUserID' => $vendor_user_id]);
            return $vendor_user_id;
        }else{
            $this->user_db->table(VENDOR_USERS)->insert($vendor_user_data);
            return $this->user_db->insertID();
        }
    }
    
    public function saveVendorUserRolesBatch($vendor_user_roles_mapper_data,$vendor_user_id = 0){
        if(!empty($vendor_user_id)){
            $this->user_db->table(VENDOR_USER_ROLES_MAPPER)->delete($vendor_user_id);    
        }
        $this->user_db->table(VENDOR_USER_ROLES_MAPPER)->insertBatch($vendor_user_roles_mapper_data);
    }
    
    public function saveVendorBankingDocuments($vendor_banking_documents, $vendor_banking_document_id){
        if(!empty($vendor_banking_document_id)){
            $this->user_db->table(VENDOR_BANKING_DOCUMENTS)->update($vendor_banking_documents,['VendorBankingDocumentID' => $vendor_banking_document_id]);
        }else{
            $this->user_db->table(VENDOR_BANKING_DOCUMENTS)->insert($vendor_banking_documents);   
        }
    }

    public function saveVendorGeography($vendor_geography_data,$vendor_geography_id = 0){
        if(empty($vendor_geography_id)){
            $this->user_db->table(VENDOR_GEOGRAPHY)->insert($vendor_geography_data);
            return $this->user_db->insertID();
        }else{
            $this->user_db->table(VENDOR_GEOGRAPHY)->update($vendor_geography_data,['VendorGeographyID' => $vendor_geography_id]);
            return $vendor_geography_id;
        }
    }

    public function saveVendorGeographyIfNotExist($vendor_geography_data){
        $query = $this->user_db->table(VENDOR_GEOGRAPHY)
                               ->select('VendorGeographyID')
                               ->getWhere(['VendorID' => $vendor_geography_data['VendorID'], 'CountryID' => $vendor_geography_data['CountryID'], 'StateID' => $vendor_geography_data['StateID'],'CityID' => $vendor_geography_data['CityID']])
                               ->getRowArray();

        if(!empty($query)){
            return $query['VendorGeographyID'];
        }else{
            $this->user_db->table(VENDOR_GEOGRAPHY)->insert($vendor_geography_data);
            return $this->user_db->insertID();
        }
    }

    public function checkVendorCIN($cin, $vendor_id, $comp_id){
        $query = $this->user_db->table(VENDORS.' V')
                               ->select('VendorName')
                               ->getWhere(['V.CIN' => $cin,'V.VendorID !=' => $vendor_id,'CompID' => $comp_id])
                               ->getRowArray();
        return $query;
    }

    public function fetchBasicVendorDetails($vendor_id, $comp_id){
        $query = $this->user_db->table(VENDORS.' V')
                               ->select('V.VendorName,V.CIN')
                               ->getWhere(['V.VendorID' => $vendor_id,'V.CompID' => $comp_id])
                               ->getRowArray();
        
        return $query;
    }

    public function fetchVendorServiceTaxes($vendor_id,$comp_id){
        $query = $this->finance_db->table(VENDOR_SERVICE_TAX_TYPE_MAPPER.' VSTTM')
                                  ->select('VSTTM.VendorServiceTaxID,VSTTM.Label,STTM.ServiceTaxType,VSTTM.ServiceTaxNumber,VSTTM.BillingAddress,VSTTM.ServiceTaxTypeID')
                                  ->join($this->db_name.'.'.VENDORS.' V','V.VendorID = VSTTM.VendorID')
                                  ->join(SERVICE_TAX_TYPES_MASTER.' STTM','STTM.ServiceTaxTypeID = VSTTM.ServiceTaxTypeID','left')
                                  ->getWhere(['VSTTM.VendorID' => $vendor_id,'V.CompID' => $comp_id])
                                  ->getResultArray();

        return $query;
    }

    public function fetchVendorServiceTaxData($vendor_service_tax_id,$comp_id){
        $query = $this->finance_db->table(VENDOR_SERVICE_TAX_TYPE_MAPPER.' VSTTM')
                                  ->select('VSTTM.Label,VSTTM.ServiceTaxTypeID,VSTTM.ServiceTaxNumber,VSTTM.VendorID,VSTTM.BillingCountryID,VSTTM.BillingStateID,VSTTM.BillingAddress')
                                  ->join($this->db_name.'.'.VENDORS.' V','V.VendorID = VSTTM.VendorID')
                                  ->getWhere(['VSTTM.VendorServiceTaxID' => $vendor_service_tax_id,'V.CompID' => $comp_id])
                                  ->getRowArray();

        return $query;
    }

    public function checkVendorServiceTaxNumber($service_tax_number,$vendor_service_tax_id,$vendor_id,$service_tax_type)
    {
        $query = $this->finance_db->table(VENDOR_SERVICE_TAX_TYPE_MAPPER)
                                  ->select('VendorServiceTaxID')
                                  ->getWhere(['ServiceTaxNumber' => $service_tax_number,'ServiceTaxTypeID' => $service_tax_type,'VendorID' => $vendor_id,'VendorServiceTaxID !=' => $vendor_service_tax_id])
                                  ->getRowArray();

        return $query;
    }

    public function saveVendorServiceTax($vendor_service_tax_data, $vendor_service_tax_id = 0){
        if(empty($vendor_service_tax_id)){
            $this->finance_db->table(VENDOR_SERVICE_TAX_TYPE_MAPPER)->insert($vendor_service_tax_data);
            return $this->finance_db->insertID();
        }else{
            $this->finance_db->table(VENDOR_SERVICE_TAX_TYPE_MAPPER)->update($vendor_service_tax_data,['VendorServiceTaxID' => $vendor_service_tax_id]);
            return $vendor_service_tax_id;
        }
    }

    public function deleteVendorServiceTax($vendor_service_tax_id)
    {
        $this->finance_db->table(VENDOR_SERVICE_TAX_TYPE_MAPPER)->delete(['VendorServiceTaxID' => $vendor_service_tax_id]);
    }

    public function checkVendorServiceTaxLabel($label,$vendor_service_tax_id,$vendor_id)
    {
        $query = $this->finance_db->table(VENDOR_SERVICE_TAX_TYPE_MAPPER)
                                  ->select('VendorServiceTaxID')
                                  ->getWhere(['Label' => $label,'VendorID' => $vendor_id,'VendorServiceTaxID !=' => $vendor_service_tax_id])
                                  ->getRowArray();

        return $query;
    }

    public function fetchVendors($comp_id,$subscription_end_date,$limit = 30,$offset = 0,$filter = [],$count = false, $vendor_id = ''){
        
        $builder = $this->user_db->table(VENDORS.' V');

        if($count == false){
            $query = $builder->groupBy('V.VendorID')
                              ->limit($limit,$offset);
        }
        
        if(!empty($filter['search_txt'])){
            $query = $builder->where('(V.VendorName like "%'.$filter['search_txt'].'%")');
        }

        if(!empty($vendor_id)){
            $query = $builder->orderBy('(CASE WHEN V.VendorID = "'.$vendor_id.'" THEN 1 ELSE V.VendorName END)','ASC');
        }else{
            $query = $builder->orderBy('V.VendorName','ASC');
        }
        
        $select = ($count == false)?'V.VendorID,V.VendorName':'count(V.VendorID) as total_rows';
        $query = $builder->select($select)
                          ->getWhere(['V.CompID' => $comp_id,'V.RegisteredDate <=' => $subscription_end_date,'VendorStatus !=' => 'Deleted'])
                          ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_rows'];
    }

    public function fetchVendorData($vendor_id, $comp_id){
        $query = $this->user_db->table(VENDOR_USERS.' VU')
                               ->select('V.VendorName,VU.VendorUserContactNo')
                               ->join(VENDORS.' V','V.VendorID = VU.VendorID','LEFT')
                               ->getWhere(['VU.VendorID' => $vendor_id,'V.CompID' => $comp_id])
                               ->getRowArray();
        return $query;
    }
}