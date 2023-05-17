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
                <h3 class="pull-left box-title">View Credit Note Details</h3>
                <div class="pull-right">
                    <?php if($credit_note_data['PaymentStatus'] == 'Unpaid'){ ?>
                    <a href="<?php echo base_url('mark-credit-note-paid/'.$credit_note_id); ?>" class="btn btn-success <?php echo $btn_class; ?>">Mark as Paid</a>
                    <?php } ?>
                    <a href="<?php echo base_url('download-credit-note/'.$credit_note_id); ?>" class="btn btn-warning" data-loader="false">Download Credit Note</a>
                    <a href="<?php echo base_url('manage-credit-notes'); ?>" class="btn btn-info">Manage Credit Notes</a>
                </div>
            </div>
            <div class="box-body">
                <div id="invoiceDiv">
                    <?php if(!empty($credit_note_data['CompLogoPath'])){ ?>
                    <div class="row">
                        <div class="col-md-4">
                            <img src="<?php echo media_server($credit_note_data['CompLogoPath']); ?>" alt="<?php echo $credit_note_data['CompanyName'] ?> Logo" class="img-responsive" width="100" height="100">
                        </div>
                    </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-6 col-xs-6">
                            <h5><strong><?php echo $credit_note_data['CompanyName'].' '.$credit_note_data['FirmType']; ?></strong></h5>
                            <p><?php echo $credit_note_data['CompanyAddress']; ?></p>
                            <p><?php echo $credit_note_data['CompanyContactNumber']; ?></p>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <div class="pull-right">
                                <h5><strong>Credit Note</strong></h5>
                                <div class="clearfix"></div>
                                <p>
                                    <b>Original Invoice No:</b> <?php echo $credit_note_data['InvoiceNo']; ?><br>
                                    <?php if(!empty($credit_note_data['company_tax_type'])){ ?>
                                    <b><?php echo $credit_note_data['company_tax_type']; ?>:</b> <?php echo $credit_note_data['CompanyServiceTaxIdentificationNumber']; ?>
                                    <?php } ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-md-6 col-xs-6">
                            <p><b>Customer:</b></p>
                            <p>
                                <?php echo str_replace('~d','',$credit_note_data['ClientName']).' '.$credit_note_data['client_FirmType']; ?>,
                                <br>
                                <?php echo $credit_note_data['ClientBillingAddress']; ?>
                            </p>
                            <p><?php echo $credit_note_data['ClientContactNo']; ?></p>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <div class="pull-right">
                                <b>Credit Note No:</b> <?php echo $credit_note_data['CreditNoteNo']; ?><br>
                                <b>Credit Note Date:</b> <?php echo $credit_note_data['CreditNoteDate']; ?><br>
                                <b>Credits Remaining:</b> <?php echo($credit_note_data['PaymentStatus'] == 'Unpaid')?$credit_note_data['PayableAmount']:'0'; ?><br>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th>Sr</th>
                                    <th>Particulars</th>
                                    <th>HSN/SAC</th>
                                    <th>Qty</th>
                                    <th>Price Per Unit</th>
                                    <th>Taxes</th>
                                    <th>Tax Amount</th>
                                    <th>Taxable Amount</th>
                                    <th>Total Amount</th>
                                </tr>
                                <?php 
                                for($i=0;$i<count($credit_note_details);$i++){
                                    $qty = $credit_note_details[$i]['Qty'];
                                    $price_per_unit = $credit_note_details[$i]['PricePerUnit'];
                                    $total_units_price = (!empty($qty))?$price_per_unit * $qty:$price_per_unit;
                                ?>
                                    <tr>
                                        <td><?php echo $i+1; ?></td>
                                        <td><?php echo $credit_note_details[$i]['Particular']; ?></td>
                                        <td><?php echo $credit_note_details[$i]['HSN']; ?></td>
                                        <td><?php echo(!empty($qty))?$qty:'NA'; ?></td>
                                        <td><?php echo $price_per_unit; ?></td>
                                        <td>
                                            <?php $cn_taxes = explode(',', $credit_note_details[$i]['taxes']);
                                                $taxable_amount = 0;
                                                for($j=0;$j<count($cn_taxes);$j++){
                                                    $cn_taxes_split = explode('|',$cn_taxes[$j]);
                                                    $taxable_amount += (!empty($cn_taxes_split[1]))?$cn_taxes_split[1]:0;
                                                    echo str_replace('|',' : ',$cn_taxes[$j]).'%<br>';
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                $taxable_amount = $total_units_price * $taxable_amount / 100; 

                                                echo $taxable_amount;
                                            ?>
                                        </td>
                                        <td><?php echo $total_units_price; ?></td>
                                        <td><?php echo $total_units_price + $taxable_amount; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="8">
                                        <b class="pull-right">Total</b>
                                    </td>
                                    <td><b><?php echo $credit_note_data['PayableAmount']; ?>/-</b></td>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        <b class="pull-right">Total Amount in Words</b>
                                    </td>
                                    <td><b><?php echo numberToWords($credit_note_data['PayableAmount']); ?> only</b></td>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        <b class="pull-right">Credits Remaining</b>
                                    </td>
                                    <td><b><?php echo($credit_note_data['PaymentStatus'] == 'Unpaid')?$credit_note_data['PayableAmount']:'0'; ?></b></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="row">
                        <div class="col-md-3 col-xs-12 col-sm-12">
                            <img src="<?php echo media_server($credit_note_data['SignatureImgPath']); ?>" alt="" class="img-responsive">
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>