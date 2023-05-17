<?php

namespace Modules\Finance\Controllers;

use \App\Libraries\Php_spreadsheets;

use TCPDF;

class Finance_reports_controller extends \CodeIgniter\Controller {
	public function __construct(){
		$this->session = \Config\Services::session();	
        $this->userdata = $this->session->get('user_data');
        if(empty($this->userdata['ID'])){
            header('location:'.base_url().'login');
        }

        $this->finance_model = new \Modules\Finance\Models\Finance_model();
        $this->finance_reports_model = new \Modules\Finance\Models\Finance_reports_model();
        $this->registered_user_model = new \Modules\Registered_users\Models\Registered_user_model();
	}

	public function financialReport(){
		$data = [
			'add_bel_global_js' => base_url('assets/js/finance.js')
		];
		return default_view('\Modules\Finance\Views\financial_report',$data);
	}

	public function gstr1(){
		$comp_id = $this->userdata['CompID'];
		$data = [
			'company_service_tax_master' => $this->finance_model->fetchCompanyServiceTaxMasterData($comp_id),
			'add_bel_global_js' => base_url('assets/js/finance.js')
		];
		return default_view('\Modules\Finance\Views\gstr1',$data);
	}

	public function exportGSTR1Excel(){
		
		$subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start exporting your GSTR-1 excel.']);
            $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

		$comp_id = $this->userdata['CompID'];

		$invoice_date_from = $this->request->getGet('InvoiceDateFrom');
		$invoice_date_to = $this->request->getGet('InvoiceDateTo');
		$gst_no = $this->request->getGet('CompanyGST');

		if(empty($gst_no)){
			$this->session->setFlashdata('flashmsg',['status' => false, 'msg' => 'Your GST No. is required']);
			return $this->response->redirect(base_url('gstr-1'));
			exit;
		}
		
		if(empty($invoice_date_from) || empty($invoice_date_to)){
			$this->session->setFlashdata('flashmsg',['status' => false, 'msg' => 'Invoice date from and invoice date to is required']);
			return $this->response->redirect(base_url('gstr-1'));
			exit;
		}else{
			
			$app = env('app');
            $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];

            $credit_note_model = new \Modules\Finance\Models\Credit_note_model();

			$filter = [
				'InvoiceDateFrom' => $invoice_date_from,
				'InvoiceDateTo' => $invoice_date_to,
				'CompanyGST' => $gst_no,
				'InvoiceType' => $this->request->getGet('InvoiceType'),
				'CreditNoteDateFrom' => $invoice_date_from,
        		'CreditNoteDateTo' => $invoice_date_to
			];

			$data['sheets'] = [
				[
					'sheet_title' => 'B2B',
					'headers' => ['HSN','Invoice Date','Invoice Value','Place of Supply','Reverse Charge','Applicable % of Tax Rate','Invoice Type','E-Commerce GSTIN','Rate','Taxable Value'],
					'data' => $this->finance_reports_model->fetchFullGSTR1B2BReports($comp_id,$subscription_end_date,$filter)
				],
				[
					'sheet_title' => 'B2C',
					'headers' => ['HSN','Type','Place of Supply','Applicable % of Tax Rate','E-Commerce GSTIN','Rate','Taxable Value'],
					'data' => $this->finance_reports_model->fetchFullGSTR1B2CReports($comp_id,$subscription_end_date,$filter)
				],
				[
					'sheet_title' => 'Credit Notes',
					'headers' => ['Client','Client Service Tax Identifier','Credit Note No','Credit Note Date','Original Invoice No','Particular','HSN/SAC','Qty','Price Per Unit','Taxable Amount','Taxes','Tax Amount','Total Amount','Payment Status','Reason'],
					'data' => $credit_note_model->fetchFullCreditNoteData($comp_id,$subscription_end_date,$filter)
				]
			];

			// echo "<pre>";
			// print_r($data);
			// exit;

	        $php_spreadsheets = new Php_spreadsheets();
	        $php_spreadsheets->export_excel([], $data);
		}
	}
}

?>