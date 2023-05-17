<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header">
                <h3 class="pull-left box-title">Invoice Details</h3>
                <div class="pull-right">
                    <a href="<?php echo base_url('manage-receipts/'.$invoice_id) ?>" class="btn bg-maroon">Manage Receipts</a>
                    <!-- <button type="button" class="btn btn-warning print">Download Invoice</button> -->
                    <button type="button" class="btn bg-blue invoice-modal-btn" data-toggle="modal" data-target="#mailInvoiceModal">Mail Invoice</button>
                    <a href="<?php echo base_url('download-invoice/'.$invoice_id); ?>" class="btn btn-warning" data-loader="false">Download Invoice</a>
                    <a href="<?php echo base_url('manage-invoices'); ?>" class="btn btn-info">Manage Invoices</a>
                </div>
            </div>
            <div class="box-body pd-3" >
                <div id="invoiceDiv">
                    <?php if(!empty($company_banking_details['CompLogoPath'])){ ?>
                    <div class="row">
                        <div class="col-md-4">
                            <img src="<?php echo media_server($company_banking_details['CompLogoPath']); ?>" alt="<?php echo $invoice_data['CompanyName'] ?> Logo" class="img-responsive" width="100" height="100">
                        </div>
                    </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-6 col-xs-6">
                            <h5><strong><?php echo $invoice_data['CompanyName'].' '.$invoice_data['FirmType']; ?></strong></h5>
                            <p><?php echo $invoice_data['CompanyAddress']; ?></p>
                            <p><?php echo $invoice_data['CompanyContactNumber']; ?></p>
                        </div>

                        <div class="col-md-6 col-xs-6">
                            <div class="pull-right">
                                <h5><strong>Tax Invoice</strong></h5>
                                <div class="clearfix"></div>
                                <p>
                                    <b>Invoice No:</b> <?php echo $invoice_data['InvoiceNo']; ?><br>
                                    <?php if(!empty($invoice_data['company_tax_type'])){ ?>
                                    <b><?php echo $invoice_data['company_tax_type']; ?>:</b> <?php echo $invoice_data['CompanyServiceTaxIdentificationNumber']; ?>
                                    <?php } ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row pd-t-1">
                        <div class="col-md-3 col-xs-6">
                            <?php if(!empty($invoice_data['ClientName'])){ ?>
                                <p><b>Bill To:</b></p>
                                <p>
                                    <?php echo str_replace('~d','',$invoice_data['ClientName']).' '.$invoice_data['client_FirmType']; ?>,
                                    <br>
                                    <?php echo $invoice_data['ClientBillingAddress']; ?>
                                </p>
                            <?php }else{ ?>
                            &nbsp;
                            <?php } ?>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <?php if(!empty($invoice_data['ClientShippingAddress'])){ ?>
                            <p><b>Ship To:</b></p>
                            <p>
                                <?php echo str_replace('~d','',$invoice_data['ClientName']).' '.$invoice_data['client_FirmType']; ?>,
                                <br>
                                <?php echo $invoice_data['ClientShippingAddress']; ?>
                            </p>
                            <?php } ?>
                        </div>
                        <div class="clearfix hidden-lg"></div>
                        <div class="col-md-3 col-xs-6">
                            <p class="white">
                                <b>Contact No:</b> <br>
                                <b>Invoice Date:</b> <br>
                                <b>Due Date:</b> <br>
                                <?php if(!empty($invoice_data['ServiceTaxType'])){ ?>
                                    <b>Customer <?php echo $invoice_data['ServiceTaxType']; ?>:</b>
                                <?php } ?>
                            </p>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <p class="">
                                <?php echo $invoice_data['ClientContactNo']; ?> <br>
                                <?php echo date('d M Y',strtotime($invoice_data['ClientInvoiceDate'])); ?> <br>
                                <?php echo date('d M Y',strtotime($invoice_data['ClientInvoiceDueDate'])); ?> <br>

                                <?php echo(!empty($invoice_data['ServiceTaxType']))?$invoice_data['ClientServiceTaxIdentificationNumber']:''; ?>
                            </p>
                        </div>
                    </div>

                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered billing-table">
                                    <tr>
                                        <th>Sr</th>
                                        <th>Particulars</th>
                                        <th>HSN/SAC</th>
                                        <th>Qty</th>
                                        <th>Pre Tax Total Amount</th>
                                        <th>Discount</th>
                                        <th>Total Amount</th>
                                    </tr>
                                    <?php 
                                        $pre_tax_total_amount = 0;
                                        $post_tax_total_amount = 0;
                                        $total_paid_amount = 0;
                                        for($i=0;$i<count($invoice_details_data);$i++){ 
                                    ?>
                                        <tr>
                                            <td><?php echo $i+1; ?></td>
                                            <td><?php echo $invoice_details_data[$i]['Particular']; ?></td>
                                            <td><?php echo $invoice_details_data[$i]['HSN']; ?></td>
                                            <td><?php echo(!empty($invoice_details_data[$i]['Quantity']))?$invoice_details_data[$i]['Quantity']:'NA'; ?></td>
                                            <td>
                                                <?php 
                                                    if(!empty($invoice_details_data[$i]['Quantity'])){
                                                        $pre_tax_amount = $invoice_details_data[$i]['PricePerUnit'] * $invoice_details_data[$i]['Quantity'];
                                                    }else{
                                                        $pre_tax_amount = $invoice_details_data[$i]['PricePerUnit'];
                                                    }

                                                    $pre_tax_amount = $pre_tax_amount - ($pre_tax_amount * $invoice_details_data[$i]['Discount'] / 100);

                                                    $pre_tax_total_amount += $pre_tax_amount;
                                                    echo round($pre_tax_amount,2,PHP_ROUND_HALF_DOWN); 
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $invoice_details_data[$i]['Discount']; ?>%
                                            </td>
                                            <td>
                                                <?php 
                                                    $post_tax_total_amount += $invoice_details_data[$i]['TotalAmount'];
                                                    echo $invoice_details_data[$i]['TotalAmount']; 
                                                ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered taxes-table">
                                    <tr>
                                        <th>Sr</th>
                                        <th>Particulars</th>
                                        <th colspan="2" class="text-center">Taxes</th>
                                        <th>Taxable Amount</th>
                                    </tr>
                                    <?php 
                                    $total_tax_percentage = 0;
                                    $total_taxable_amount = 0;
                                    for($i=0;$i<count($invoice_details_tax_data);$i++){ ?>
                                        <tr>
                                            <td><?php echo $i+1; ?></td>
                                            <td><?php echo $invoice_details_tax_data[$i]['Particular']; ?></td>
                                            <td><?php echo $invoice_details_tax_data[$i]['Tax']; ?></td>
                                            <td>
                                                <?php echo(!empty($invoice_details_tax_data[$i]['TaxPercentage']))?$invoice_details_tax_data[$i]['TaxPercentage'].'%':''; ?>
                                                    
                                            </td>
                                            <td>
                                                <?php 
                                                    if(!empty($invoice_details_tax_data[$i]['TaxPercentage'])){

                                                        if(!empty($invoice_details_tax_data[$i]['Quantity'])){

                                                            $total_amount = $invoice_details_tax_data[$i]['PricePerUnit'] * $invoice_details_tax_data[$i]['Quantity'];
                                                        }else{
                                                            $total_amount = $invoice_details_tax_data[$i]['PricePerUnit'];
                                                        }


                                                        $total_amount = $total_amount - ($total_amount * $invoice_details_tax_data[$i]['Discount'] / 100);
                                                       
                                                       $taxable_amount = floatval($total_amount * $invoice_details_tax_data[$i]['TaxPercentage'] / 100);

                                                       echo round($taxable_amount,2,PHP_ROUND_HALF_DOWN);
                                                       $total_taxable_amount += $taxable_amount;
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php 
                                        $total_tax_percentage += $invoice_details_tax_data[$i]['TaxPercentage']; 
                                        } 
                                    ?>
                                    
                                    <tr>
                                        <td colspan="4" class="text-right">
                                            <b>Total Taxable Amount</b>
                                        </td>
                                        <td>
                                            <b>
                                                <?php 
                                                    echo round($total_taxable_amount,2,PHP_ROUND_HALF_DOWN);
                                                ?>
                                            </b>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <?php if(!empty($invoice_data['CustomerNotes'])){ ?>
                    <div class="row">
                        <div class="col-md-12">
                            <b>Notes:</b> <?php echo $invoice_data['CustomerNotes']; ?>
                        </div>
                    </div>
                    <?php } ?>

                    <hr>
                    <div class="row">
                        <div class="col-md-3 col-xs-12">
                            <?php if(!empty($invoice_data['SignatureImgPath'])){ ?>
                            <img src="<?php echo media_server($invoice_data['SignatureImgPath']); ?>" alt="" style="height: 100px;width: 100%;">
                            <?php }else{ ?>
                                <a href="<?php echo base_url('edit-comp-details'); ?>" class="btn btn-success btn-block" target="_blank">Add Signature</a>
                            <?php } ?>
                            <br>
                            <br>
                            <hr style="border: 1px solid #000;">
                        </div>
                        <?php if(!empty($deductibles_data)){ ?>
                        <div class="col-md-4 col-xs-12">
                            <table class="table table-bordered table-striped text-center">
                                <tr>
                                    <th colspan="2">Deductibles at source</th>
                                </tr>
                                <?php for($i=0;$i<count($deductibles_data);$i++){ ?>
                                <tr>
                                    <td><?php echo $deductibles_data[$i]['DeductibleType']; ?></td>
                                    <td><?php echo $deductibles_data[$i]['DeductiblePercentage']; ?>%</td>
                                </tr>
                                <?php } ?>
                            </table>
                            <b>Note:</b> Deductibles at source will not be shown in downloaded invoice.
                        </div>    
                        <?php } ?>
                        <div class="col-md-4 col-xs-12">
                            <div class="">
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th colspan="2" class="text-center">Total</th>
                                    </tr>
                                    <tr>
                                        <td>Pre Tax Total</td>
                                        <td><?php echo $pre_tax_total_amount; ?>/-</td>
                                    </tr>
                                    <tr>
                                        <td>Post Tax Total</td>
                                        <td>
                                           <?php echo $post_tax_total_amount; ?>/-
                                        </td>
                                    </tr>
                                    <?php if(!empty($invoice_additional_charges_data)){ 
                                        for($i=0;$i<count($invoice_additional_charges_data);$i++){
                                    ?>
                                        <tr>
                                            <td><?php echo $invoice_additional_charges_data[$i]['AdditionalChargeType']; ?></td>
                                            <td><?php echo $invoice_additional_charges_data[$i]['Additionalcharge']; ?>/-</td>
                                        </tr>
                                    <?php } } ?>
                                    <tr>
                                        <td>Total Receivable Amount</td>
                                        <td><?php echo $invoice_data['TotalPayableAmount']; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <br>
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th colspan="<?php echo(!empty($company_banking_details['QRCode']))?'3':'2'; ?>" class="text-center">
                                        Payment Options
                                        <?php if(empty($company_banking_details)){ ?>
                                        <a href="<?php echo base_url('edit-company-bank-details'); ?>" class="btn btn-success pull-right" targe="_blank">Add Bank Details</a>
                                        <?php } ?>
                                    </th>
                                </tr>
                                <?php if(!empty($company_banking_details)){ ?>
                                <tr>
                                    <th colspan="2" class="text-center">Account Details</th>
                                    <?php if(!empty($company_banking_details['QRCode'])){ ?>
                                        <th class="text-center">QR Code</th>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <td>Bank Name</td>
                                    <td><?php echo $company_banking_details['BankName']; ?></td>
                                    <?php if(!empty($company_banking_details['QRCode'])){ ?>
                                    <td rowspan="4">
                                        <center>
                                            <img src="<?php echo media_server($company_banking_details['QRCode']); ?>" alt="" width="250">
                                        </center>
                                    </td>
                                <?php } ?>
                                </tr>
                                <tr>
                                   <td>A/c Holder Name</td>
                                   <td><?php echo $company_banking_details['AccountHolderName']; ?></td>
                                </tr>
                                <tr>
                                    <td>A/c No</td>
                                    <td><?php echo $company_banking_details['AccountNo']; ?></td>
                                </tr>
                                <tr>
                                    <td>IFSC</td>
                                    <td><?php echo $company_banking_details['BankIFSC']; ?></td>
                                </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                </div>

                <a href="<?php echo base_url('download-invoice/'.$invoice_id); ?>" download class="btn btn-warning">Download Invoice</a>

            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="mailInvoiceModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Receipents</h4>
      </div>
      <div class="modal-body">
        <form action="javascript:void(0)" id="mailForm">
            <input type="hidden" name="InvoiceNo" id="InvoiceNo" value="<?php echo $invoice_data['InvoiceNo']; ?>">
            <input type="hidden" name="InvoiceID" id="InvoiceID" value="<?php echo $invoice_id; ?>">
            <div class="form-group">
                <label for="Recipents">Recipent</label>
                <input type="text" name="Recipents" value="<?php echo $invoice_data['ClientUserEmailID']; ?>" id="Recipents" class="form-control">
                <span class="text-danger"><?php echo validation_show_error('Recipents'); ?></span>
            </div>
            <div class="form-group">
                <label for="CC">CC</label>
                <input type="text" name="CC" value="<?php echo set_value('CC'); ?>" id="CC" class="form-control">
                <span class="text-danger"><?php echo validation_show_error('CC'); ?></span>
            </div>
            <div class="form-group">
                <label for="BCC">BCC</label>
                <input type="text" name="BCC" value="<?php echo set_value('BCC'); ?>" id="BCC" class="form-control">
                <span class="text-danger"><?php echo validation_show_error('BCC'); ?></span>
            </div>
            <div class="form-group">
                <label for="Subject">Subject</label>
                <input type="text" name="Subject" value="Invoice - <?php echo $invoice_data['InvoiceNo']; ?>" id="Subject" class="form-control">
                <span class="text-danger"><?php echo validation_show_error('Subject'); ?></span>
            </div>
            <div class="form-group">
                <label for="Content">Message</label>
                <textarea name="Content" id="Content" class="form-control ckeditor" cols="30" rows="10">Dear <?php echo(!empty($invoice_data['ClientName']))?$invoice_data['ClientName']:'Customer'; ?>, <br>

                            We hope this message finds you well. <br><br>

                            Please find the attached invoice with an invoice number of <?php echo $invoice_data['InvoiceNo']; ?> This invoice is due on <?php echo $invoice_data['ClientInvoiceDueDate']; ?> <br><br>

                            Don’t hesitate to reach out to us if you have any questions about the invoice or product/service. We'll be more than happy to help. <br><br>

                            Kind regards, <br>

                            <b>Your friends at <?php echo $invoice_data['CompanyName']; ?></b></textarea>
                <span class="text-danger"><?php echo validation_show_error('Content'); ?></span>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success mail-invoice-btn">Send Mail</button>
      </div>
    </div>

  </div>
</div>