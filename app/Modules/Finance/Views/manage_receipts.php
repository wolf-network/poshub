<?php 
    $btn_class = '';
    $datatable_hide_class = '';
    if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
        $btn_class .= 'disabled';
        $datatable_hide_class = 'datatable-hide-search';
    }
?>

<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header">
                <h3 class="pull-left box-title">Manage Receipts</h3>
                <div class="pull-right">
                    <a href="https://www.youtube.com/watch?v=TBsrcrizovg" target="_blank" class="btn btn-info">Watch tutorial</a>
                    <?php if($invoice_data['TotalPayableAmount'] > $invoice_receipt_data['TotalPaidAmount']){ ?>
                    <a href="<?php echo base_url('create-receipt/'.$invoice_id); ?>" class="btn btn-success <?php echo $btn_class; ?>">Create Receipt</a>
                    <?php } ?>
                    <a href="<?php echo base_url('manage-invoice-details/'.$invoice_id); ?>" class="btn bg-maroon <?php echo $btn_class; ?>">Invoice Details</a>
                    <a href="<?php echo base_url('manage-invoices'); ?>" class="btn btn-primary <?php echo $btn_class; ?>">Manage Invoices</a>
                </div>
            </div>
            <div class="box-body pd-3">
                <div id="invoiceDiv">
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
                                        <b><?php echo $invoice_data['company_tax_type']; ?>:</b> <?php echo $invoice_data['CompanyTaxIdentificationNumber']; ?>
                                    <?php } ?>
                                </p>
                            </div>
                        </div>
                    </div> 

                    <hr>

                    <div class="row bg-comp-primary pd-t-1">
                        <div class="col-md-3 col-xs-6">
                            <?php if(!empty($invoice_data['ClientName'])){ ?>
                            <p><b>Bill To:</b></p>
                            <p>
                                <?php echo $invoice_data['ClientName']; ?>,
                                <br>
                                <?php echo $invoice_data['ClientBillingAddress']; ?>
                            </p>
                            <?php } ?>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <?php if(!empty($invoice_data['ClientShippingAddress'])){ ?>
                            <p><b>Ship To:</b></p>
                            <p>
                                <?php echo $invoice_data['ClientName']; ?>,
                                <br>
                                <?php echo $invoice_data['ClientShippingAddress']; ?>
                            </p>
                            <?php } ?>
                        </div>
                        <div class="clearfix hidden-lg"></div>
                        <div class="col-md-4 col-xs-6">
                            <div class="pull-right">
                                <p>
                                    <?php if(!empty($invoice_data['ClientContactNo'])){?>
                                    <b>Contact No:</b> <br>
                                    <?php } ?>
                                    <b>Invoice Date:</b> <br>
                                    <b>Due Date:</b> <br>
                                    <?php if(!empty($invoice_data['ServiceTaxTypeID'])){ ?>
                                    <b>Customer <?php echo $invoice_data['ServiceTaxTypeID']; ?>:</b>
                                    <?php } ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-6">
                            <p class="">
                                <?php echo(!empty($invoice_data['ClientContactNo']))?$invoice_data['ClientContactNo'].'<br>':''; ?>
                                <?php echo date('d M Y',strtotime($invoice_data['ClientInvoiceDate'])); ?> <br>
                                <?php echo date('d M Y',strtotime($invoice_data['ClientInvoiceDueDate'])); ?> <br>
                                <?php echo(!empty($invoice_data['ServiceTaxTypeID']))?$invoice_data['ClientServiceTaxIdentificationNumber']:''; ?>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="table table-responsive">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>Sr</th>
                                <th>Receipt No</th>
                                <th>Paid Amount</th>
                                <th>Receipt Date</th>
                                <th>Receipt Created Date</th>
                                <th>Action</th>
                            </tr>
                            <?php 
                                if(!empty($receipts)){
                                    for($i=0;$i<count($receipts);$i++){
                            ?>
                            <tr>
                                <td><?php echo $i+1; ?></td>
                                <td><?php echo $receipts[$i]['ReceiptNo']; ?></td>
                                <td><?php echo $receipts[$i]['PaidAmount']; ?></td>
                                <td><?php echo $receipts[$i]['ReceiptDate']; ?></td>
                                <td><?php echo $receipts[$i]['AddedDate']; ?></td>
                                <td>
                                    <a href="<?php echo base_url('view-receipt-details/'.$receipts[$i]['ReceiptID']); ?>" class="btn btn-info btn-xs" > <i class="fa fa-eye"></i> </a>
                                </td>
                            </tr>
                            <?php } } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>