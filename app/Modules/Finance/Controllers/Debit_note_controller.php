<?php

namespace Modules\Finance\Controllers;
use TCPDF;

class Debit_note_controller extends \CodeIgniter\Controller {
	public function __construct(){
        $this->session = \Config\Services::session();
        $this->userdata = $this->session->get('user_data');
        if(empty($this->userdata['ID'])){
            header('location:'.base_url().'login');
        }

        $this->debit_note_model = new \Modules\Finance\Models\Debit_note_model();
        $this->vendor_model = new \Modules\Vendors\Models\Vendor_model();
        $this->form_validation = \Config\Services::validation();
	}

    public function saveDebitNote()
    {
        $comp_id = $this->userdata['CompID'];

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 &&  $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start creating debit notes.']);
            return $this->response->redirect(base_url('plan-renewal'));
            exit;
        }

        $posted_vendor_id = $this->request->getPost('VendorID');

        if(!empty($_POST)){
            $this->form_validation->setRule('VendorID', 'Vendor', 'required');
            $this->form_validation->setRule('DebitNoteNo', 'Debit Note No', 'permit_empty|validateDebitNoteNo');
            $this->form_validation->setRule('InvoiceNo', 'Invoice No.', 'required');
            $this->form_validation->setRule('DebitNoteDate', 'Debit Note Date', 'required');
            $this->form_validation->setRule('PaymentStatus', 'Payment Status', 'required|in_list[Received,Pending]');
            $this->form_validation->setRule('Particular.*', 'Particular', 'required');
            $this->form_validation->setRule('Quantity.*', 'Quantity', 'required|regex_match[/^\d*[.]?\d+$/m]',['regex_match' => 'Quantity should either be decimal or number.']);
            $this->form_validation->setRule('PricePerUnit.*', 'Price Per Unit', 'required|regex_match[/^\d*[.]?\d+$/m]|greater_than[0]',['regex_match' => 'Price Per Unit should either be decimal or number.']);
            $this->form_validation->setRule('Tax.*', 'Tax', 'required');
            $this->form_validation->setRule('TaxPercentage.*', 'Tax Rate', 'required');

            if ($this->form_validation->withRequest($this->request)->run())
            {
                $item_model = new \Modules\Inventory\Models\Item_model();
                $debit_note_data = [
                    'VendorID' => $posted_vendor_id,
                    'CompID' => $comp_id,
                    'DebitNoteNo' => ($this->request->getPost('DebitNoteNo'))?$this->request->getPost('DebitNoteNo'):'DN-'.date('Ymd').'-'.mt_rand(111,999),
                    'DebitNoteDate' => $this->request->getPost('DebitNoteDate'),
                    'InvoiceNo' => ($this->request->getPost('InvoiceNo'))?$this->request->getPost('InvoiceNo'):null,
                    'CreditNoteNo' => ($this->request->getPost('CreditNoteNo'))?$this->request->getPost('CreditNoteNo'):null,
                    'PaymentStatus' => $this->request->getPost('PaymentStatus'),
                    'Remarks' => ($this->request->getPost('Remarks'))?$this->request->getPost('Remarks'):null,
                    'AddedBy' => $this->userdata['ID'],
                    'AddedDate' => date('Y-m-d')
                ];

                $debit_note_id = $this->debit_note_model->saveDebitNote($debit_note_data);
                $total_receivable_amount = 0;

                for($i=0;$i<count($_POST['Particular']);$i++){
                    $particular_tax_key = $_POST['ParticularTaxKey'][$i];
                    $debit_note_details = [
                        'DebitNoteID' => $debit_note_id,
                        'Particular' => $_POST['Particular'][$i],
                        'HSN' => $_POST['HSN'][$i],
                        'Quantity' => $_POST['Quantity'][$i],
                        'PricePerUnit' => $_POST['PricePerUnit'][$i],
                    ];

                    $debit_note_details_id = $this->debit_note_model->saveDebitNoteDetails($debit_note_details);

                    $particular_price = $_POST['PricePerUnit'][$i] * $_POST['Quantity'][$i];
                    $total_tax_percentage = 0;

                    for($j=0;$j<count($_POST['Tax'][$particular_tax_key]);$j++){
                        $debit_note_details_tax_data[] = [
                            'DebitNoteDetailID' => $debit_note_details_id,
                            'Tax' => $_POST['Tax'][$particular_tax_key][$j],
                            'TaxPercentage' => $_POST['TaxPercentage'][$particular_tax_key][$j],
                        ];

                       $total_tax_percentage += ($particular_price * $_POST['TaxPercentage'][$particular_tax_key][$j] / 100);
                    }
                    
                    $particular_price = $particular_price + $total_tax_percentage;

                    if($_POST['ItemType'][$i] == 'Good' && $debit_note_data['DebitNoteDate'] == date('Y-m-d')){
                        $item_model->reduceItemQty($comp_id, $debit_note_details['Particular'], $debit_note_details['Quantity']);

                        $stock_model = new \Modules\Inventory\Models\Stock_model();

                        $stock_outward_report_data = [
                            'CompID' => $comp_id,
                            'Item' => $debit_note_details['Particular'],
                            'HSN' => $debit_note_details['HSN'],
                            'ReportDate' => date('Y-m-d')
                        ];

                        $stock_model->saveOutwardReports($stock_outward_report_data,$debit_note_details['Quantity']);
                    }

                    $total_receivable_amount += $particular_price;
                }

                $this->debit_note_model->saveDebitNoteDetailsTaxData($debit_note_details_tax_data);

                $debit_note_updated_data['ReceivableAmount'] = $total_receivable_amount;

                $this->debit_note_model->saveDebitNote($debit_note_updated_data,$debit_note_id);

                $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Debit note saved successfully!']);
                $this->response->redirect(base_url('manage-debit-notes'));

            }
        }

        $app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];
        $item_model = new \Modules\Inventory\Models\Item_model();
        
        $data = [
            'vendors' => $this->vendor_model->fetchVendors($comp_id,$subscription_end_date,30,0,[],false,$posted_vendor_id),
            'items' => $item_model->fetchAllItems($comp_id),
            'add_bel_global_js' => [base_url('assets/js/debit_note.js'),base_url('assets/js/finance.js')]
        ];
        
        return default_view('\Modules\Finance\Views\save_debit_note', $data);
    }

    public function manageDebitNotes(){
        
        $data = [
            'vendor_id' => $this->request->getGet('VendorID'),
            'add_bel_global_js' => base_url('assets/js/debit_note.js')
        ];

        return default_view('\Modules\Finance\Views\manage_debit_notes', $data);
    }

    public function manageDebitNoteDetails($debit_note_id){
        $app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];
        $comp_id = $this->userdata['CompID'];

        $debit_note_data = $this->debit_note_model->fetchDebitNoteData($debit_note_id,$comp_id,$subscription_end_date);

        if(empty($debit_note_data)){
            echo "Either the debit note does not exist, does not belong to you or your subscription might have ended.";
            exit;
        }

        $data = [
            'debit_note_id' => $debit_note_id,
            'debit_note_data' => $debit_note_data,
            'debit_note_details' => $this->debit_note_model->fetchDebitNoteDetails($debit_note_id,$comp_id),
            'add_bel_global_js' => base_url('assets/js/debit_note.js')
        ];

        return default_view('\Modules\Finance\Views\manage_debit_note_details', $data);
    }

    public function downloadDebitNote($debit_note_id){
        $app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];
        $comp_id = $this->userdata['CompID'];

        $debit_note_data = $this->debit_note_model->fetchDebitNoteData($debit_note_id,$comp_id,$subscription_end_date);

        if(empty($debit_note_data)){
            echo "Either the debit note does not exist, does not belong to you or your subscription might have ended.";
            exit;
        }

        $data = [
            'debit_note_data' => $debit_note_data,
            'debit_note_details' => $this->debit_note_model->fetchDebitNoteDetails($debit_note_id,$comp_id)
        ];

        $html = view('\Modules\Finance\Views\download_debit_note',$data);

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set font
        $pdf->SetFont('helvetica', '', 9);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // ---------------------------------------------------------

        // add a page
        $pdf->AddPage();

        

        // print a block of text using Write()
        $pdf->writeHTML($html, true, false, true, false, '');

        // ---------------------------------------------------------

        //Close and output PDF document
        $pdf->Output($debit_note_data['DebitNoteNo'].'.pdf', 'D');
    }

    public function deleteDebitNote($debit_note_id){
        $comp_id = $this->userdata['CompID'];
        $debit_note_basic_data = $this->debit_note_model->fetchDebitNoteBasicDetails($debit_note_id,$comp_id);

        if(empty($debit_note_basic_data)){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'either the debit note does not exist or does not belong to you.']);
        }

        if(!empty($debit_note_basic_data) && $debit_note_basic_data['PaymentStatus'] == 'Received'){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Sorry, This debit note cannot be delete because payment against this debit note has been received.']);
        }

        if(!empty($debit_note_basic_data) && $debit_note_basic_data['PaymentStatus'] == 'Pending'){
            $this->debit_note_model->deleteDebitNote($debit_note_id,$comp_id);
            $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Debit note deleted successfully!']);
        }

        $this->response->redirect(base_url('manage-debit-notes'));
    }

    public function markDebitNotePaid($debit_note_id){
        $comp_id = $this->userdata['CompID'];
        $debit_note_details = $this->debit_note_model->fetchDebitNoteBasicDetails($debit_note_id,$comp_id);
        if(empty($debit_note_details)){
            echo "Either this debit note does not exist or does not belong to you.";
            exit;
        }else{

            if($debit_note_details['PaymentStatus'] == 'Received'){
                $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'This debit not has already been marked as received.']);
            }else{
                $debit_note_data = [
                    'PaymentStatus' => 'Received'
                ];

                $this->debit_note_model->saveDebitNote($debit_note_data,$debit_note_id);
                $this->session->setFlashdata('flashmsg',['status' => true,'msg' => 'Marked debit note as received']);
            }

            $this->response->redirect(base_url('manage-debit-note-details/'.$debit_note_id));
        }
    }

    public function exportDebitNotes(){

        $subscription_time_left = subscription_time_left();
        if($subscription_time_left['years'] <= 0 && $subscription_time_left['months'] <= 0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
            $this->session->setFlashdata('flashmsg',['status' => false,'msg' => 'Kindly Re-new your subscription to start exporting credit notes.']);
            $this->response->redirect(base_url('plan-renewal'));
        }

        $comp_id = $this->userdata['CompID'];
        $vendor_id = $this->request->getGet('VendorID');

        $filter = [
            'VendorID' => $vendor_id,
            'DebitNoteDateFrom' => $this->request->getGet('DebitNoteDateFrom'),
            'DebitNoteDateTo' => $this->request->getGet('DebitNoteDateTo')
        ];

        $headers = ['Vendor','Debit Note No','Debit Note Date','Invoice No','Particular','HSN/SAC','Qty','Price Per Unit','Taxable Amount','Taxes','Tax Amount','Total Amount','Payment Status','Remarks'];

        $app = env('app');
        $subscription_end_date = $this->userdata['apps'][$app]['SubscriptionEndDate'];

        $full_debit_notes_data = $this->debit_note_model->fetchFullDebitNoteData($comp_id,$subscription_end_date,$filter);

        $php_spreadsheets = new Php_spreadsheets();
        $php_spreadsheets->export_excel($headers, $full_debit_notes_data);
    }
}

?>