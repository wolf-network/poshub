<?php

namespace Modules\Company\Models;

use CodeIgniter\Model;

class Company_model extends Model {
	function __construct()
	{
		parent::__construct();
        
        $this->user_db = \Config\Database::connect('user_db');
        $this->db_name = $this->user_db->database;

        $this->finance_db = \Config\Database::connect('finance');
        $this->finance_db_name = $this->finance_db->database;
	}

	public function fetchCompDetails($comp_id){
		$query = $this->user_db->table(COMPANY_MASTER.' C')
							   ->select('C.CompID,C.CompName,C.CompLogoPath,C.ContactNo,C.EmailID,C.CompProfileVideoPath,C.FirmTypeID,C.CIN,C.TaxIdentificationTypeID,C.TaxIdentificationNumber,FTM.FirmType,TITM.TaxIdentificationType,C.SignatureImgPath')
							   ->join(FIRM_TYPE_MASTER.' FTM','FTM.FirmTypeID = C.FirmTypeID','LEFT')
							   ->join(TAX_IDENTIFICATION_TYPE_MASTER.' TITM','TITM.TaxIdentificationTypeID = C.TaxIdentificationTypeID','LEFT')
					  		   ->limit(1)
				  	  		   ->getWhere(['C.CompID' => $comp_id])
				  			   ->getRowArray();
		
		return $query;
	}

	public function saveCompDetails($comp_data, $comp_id = 0){
		if(empty($comp_id)){
			$this->user_db->table(COMPANY_MASTER)->insert($comp_data);
			return $this->user_db->insertID();
		}else{
			$this->user_db->table(COMPANY_MASTER)->update($comp_data, ['CompID' => $comp_id]);
			return $comp_id;
		}
	}

	public function saveAddress($address_data, $address_id = 0){
		if(empty($address_id)){
			$this->user_db->table(COMPANY_ADDRESS)->insert($address_data);
		}else{
			$this->user_db->table(COMPANY_ADDRESS)->update($address_data,['CompanyAddressID' => $address_id]);
		}
	}

	public function fetchAddressesList($comp_id,$limit = 10,$offset = 0,$filter = [],$count = false){

		$builder = $this->user_db->table(COMPANY_ADDRESS.' CA');

		if($count == false){
	            $query = $builder->limit($limit,$offset);
	        }
	        
	        if(!empty($filter['search_txt'])){
	        	$search_txt = $this->user_db->escapeString($filter['search_txt']);
	            $query = $builder->where('(CA.Address like "%'.$search_txt.'%")');
	        }
	        
	        $select = ($count == false)?'CA.CompanyAddressID,CM.CountryName,SM.StateName,CYM.CityName,CA.Address,CA.GoogleMap':'count(CA.CompanyAddressID) as total_getRows';

	        $query = $builder->select($select)
    				  	   	 ->join(COUNTRIES_MASTER.' CM','CM.CountryID = CA.CountryID','left')
    				  	   	 ->join(STATES_MASTER.' SM','SM.StateID = CA.StateID','left')
    				  	   	 ->join(CITIES_MASTER.' CYM','CYM.CityID = CA.CityID','left')
		  			  	   	 ->getWhere(['CA.CompID' => $comp_id])
                      	   	 ->getResultArray();
	        
	        return ($count == false)?$query:$query[0]['total_getRows'];
	}

	public function fetchCompanyAddressDetails($company_address_id,$comp_id){
		$query = $this->user_db->table(COMPANY_ADDRESS)
							   ->select('CountryID,StateID,CityID,Address,GoogleMap,OfficeType')
						  	   ->getWhere(['CompanyAddressID' => $company_address_id,'CompID' => $comp_id])
						  	   ->getRowArray();

		return $query;
	}

	public function deleteAddress($company_address_id){
		$this->user_db->table(COMPANY_ADDRESS)->delete(['CompanyAddressID' => $company_address_id]);
	}

	public function fetchOfficeTypes(){
		$query = $this->user_db->query( "SHOW COLUMNS FROM ".COMPANY_ADDRESS." WHERE Field = 'OfficeType'" )->getRow( 0 )->Type;
	    preg_match("/^enum\(\'(.*)\'\)$/", $query, $matches);
	    $office_types = explode("','", $matches[1]);
	    return $office_types;
	}

	public function saveCompanyDocuments($company_document_data){
		$this->user_db->table(COMPANY_DOCUMENTS)->insertBatch($company_document_data);
	}


	public function fetchCompanyDocumentsList($comp_id,$limit = 10,$offset = 0,$filter = [],$count = false){

		$builder = $this->user_db->table(COMPANY_DOCUMENTS.' CD');
        
        if($count == false){
            $query = $builder->groupBy('CD.CompanyDocumentID')
                              ->limit($limit,$offset);
        }
        
        if(!empty($filter['search_txt'])){
            $query = $builder->where('(CM.CountryName like "%'.$filter['search_txt'].'%" or SM.StateName like "%'.$filter['search_txt'].'%" or CD.DocumentName like "%'.$filter['search_txt'].'%")');
        }
        
        $select = ($count == false)?'CD.CompanyDocumentID,CD.DocumentName,CD.DocumentDescription,CD.DocumentFilePath,CM.CountryName,SM.StateName,CYM.CityName':'count(CD.CompanyDocumentID) as total_getRows';
        $query = $builder->select($select)
                      	 ->join(COUNTRIES_MASTER.' CM','CM.CountryID = CD.CountryID','left')
                         ->join(STATES_MASTER.' SM','SM.StateID = CD.StateID','left')
                         ->join(CITIES_MASTER.' CYM','CYM.CityID = CD.CityID','left')
                         ->getWhere(['CD.CompID' => $comp_id])
                         ->getResultArray();
        
        return ($count == false)?$query:$query[0]['total_getRows'];
    }

    public function fetchCompanyDocuments($comp_id){
    	$query = $this->user_db->table(COMPANY_DOCUMENTS)
    						   ->select('CONCAT(DocumentName,"-",DocumentDescription) as DocumentDescription')
    					  	   ->where_in('DocumentName',['CIN'])
    					  	   ->getWhere(['CompID' => $comp_id])
    					  	   ->getResultArray();
    	return $query;
    }

    public function fetchCompanyDocument($comp_id,$company_document_id){
    	$query = $this->user_db->table(COMPANY_DOCUMENTS)
    						   ->select('CompID,DocumentFilePath')
    						   ->getWhere(['CompanyDocumentID' => $company_document_id,'CompID' => $comp_id])
    						   ->getRowArray();
    	return $query;
    }

    public function deleteCompanyDocument($company_document_id){
    	$this->user_db->table(COMPANY_DOCUMENTS)->delete(['CompanyDocumentID' => $company_document_id]);
    }

    public function fetchCompanyDocumentsViaDocumentName($comp_id, $document_name){
    	$query = $this->user_db->table(COMPANY_DOCUMENTS)
    						   ->select('DocumentDescription')
    					  	   ->where_in('DocumentName',$document_name)
    					  	   ->where('DocumentDescription is not null')
    					  	   ->getWhere(['CompID' => $comp_id])
    					  	   ->getResultArray();
    	return $query;
    }

    public function saveCompanyBankingDetails($company_banking_data, $company_banking_detail_id = 0){
    	if(empty($company_banking_detail_id)){
    		$query = $this->user_db->table(COMPANY_BANKING_DETAILS)->insert($company_banking_data);
    		return $this->user_db->insertID();
    	}else{
    		$query = $this->user_db->table(COMPANY_BANKING_DETAILS)->update($company_banking_data,['CompanyBankingDetailID' => $company_banking_detail_id]);
    		return $company_banking_detail_id;
    	}
    }

    public function fetchCompanyBankingDetails($comp_id){
    	$query = $this->user_db->table(COMPANY_BANKING_DETAILS.' CBD')
    						   ->select('CBD.CompanyBankingDetailID,CBD.BankDetailsID,CBD.AccountHolderName,CBD.AccountNo,CBD.QRCode,BD.BankID,BM.BankName,BD.BankIFSC,CM.CompLogoPath')
    					  	   ->join(BANK_DETAILS.' BD','BD.BankDetailsID = CBD.BankDetailsID')
    					  	   ->join(BANKS_MASTER.' BM','BM.BankID = BD.BankID')
    					  	   ->join(COMPANY_MASTER.' CM','CM.CompID = CBD.CompID','LEFT')
    					  	   ->getWhere(['CBD.CompID' => $comp_id])
    					  	   ->getRowArray();

    	return $query;

    }

    public function fetchCompanyServiceTaxes($comp_id){
        $query = $this->finance_db->table(COMPANY_SERVICE_TAX_MASTER.' CSTM')->select('CSTM.CompanyServiceTaxMasterID,CSTM.ServiceTaxTypeID,CSTM.ServiceTaxIdentificationNumber,CSTM.RegisteredAddress,STTM.ServiceTaxType')
        				  ->join(SERVICE_TAX_TYPES_MASTER.' STTM','STTM.ServiceTaxTypeID = CSTM.ServiceTaxTypeID','left')
                          ->getWhere(['CSTM.CompID' => $comp_id])
                          ->getResultArray();

        return $query;
    }

    public function checkCompanyServiceTaxNumber($comp_id,$service_tax_number,$company_service_tax_id,$service_tax_type){
    	$query = $this->finance_db->table(COMPANY_SERVICE_TAX_MASTER)
    							  ->select('CompanyServiceTaxMasterID')
                          		  ->getWhere(['ServiceTaxIdentificationNumber' => $service_tax_number,'ServiceTaxTypeID' => $service_tax_type,'CompanyServiceTaxMasterID !=' => $company_service_tax_id,'CompID' => $comp_id])
                          		  ->getRowArray();

        return $query;
    }

    public function saveCompanyServiceTax($company_service_tax_data, $company_service_tax_id = 0){
        if(empty($company_service_tax_id)){
            $this->finance_db->table(COMPANY_SERVICE_TAX_MASTER)->insert($company_service_tax_data);
            return $this->finance_db->insertID();
        }else{
            $this->finance_db->table(COMPANY_SERVICE_TAX_MASTER)->update($company_service_tax_data,['CompanyServiceTaxMasterID' => $company_service_tax_id]);
            return $company_service_tax_id;
        }
    }

    public function fetchCompanyServiceTaxData($company_service_tax_id,$comp_id){
        $query = $this->finance_db->table(COMPANY_SERVICE_TAX_MASTER.' CSTM')
        						  ->select('CSTM.CompanyServiceTaxMasterID,CSTM.ServiceTaxIdentificationNumber,CSTM.ServiceTaxTypeID,CSTM.RegisteredAddress,CSTM.BillingCountryID,CSTM.BillingStateID')
                          		  ->getWhere(['CSTM.CompanyServiceTaxMasterID' => $company_service_tax_id,'CSTM.CompID' => $comp_id])
                          		  ->getRowArray();

        return $query;
    }

    public function deleteCompanyServiceTax($company_service_tax_id)
    {
        $this->finance_db->table(COMPANY_SERVICE_TAX_MASTER)->delete(['CompanyServiceTaxMasterID' => $company_service_tax_id]);
    }

    public function fetchAllAddresses($comp_id){
		$query = $this->user_db->table(COMPANY_ADDRESS.' CA')
							   ->select('CA.Address')
			  			  	   ->getWhere(['CA.CompID' => $comp_id])
                          	   ->getResultArray();
        
        return $query;
	}
}