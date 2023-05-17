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
                <h3 class="pull-left box-title">View Debit Note Details</h3>
                <div class="pull-right">
                    <?php if($debit_note_data['PaymentStatus'] == 'Pending'){ ?>
                    <a href="<?php echo base_url('mark-debit-note-paid/'.$debit_note_id); ?>" class="btn btn-success <?php echo $btn_class; ?>">Mark as Received</a>
                    <?php } ?>
                    <a href="<?php echo base_url('download-debit-note/'.$debit_note_id); ?>" class="btn btn-warning" data-loader="false">Download Debit Note</a>
                    <a href="<?php echo base_url('manage-debit-notes'); ?>" class="btn btn-info">Manage Debit Notes</a>
                </div>
            </div>
            <div class="box-body">
                <div id="invoiceDiv">
                    <?php if(!empty($debit_note_data['CompLogoPath'])){ ?>
                    <div class="row">
                        <div class="col-md-4">
                            <img src="<?php echo media_server($debit_note_data['CompLogoPath']); ?>" alt="<?php echo $debit_note_data['CompName']; ?> Logo" class="img-responsive" width="100" height="100">
                        </div>
                    </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-6 col-xs-6">
                            <h5><strong><?php echo $debit_note_data['CompName'].' '.$debit_note_data['FirmType']; ?></strong></h5>
                            <p><?php echo $debit_note_data['ContactNo']; ?></p>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <div class="pull-right">
                                <h5><strong>Debit Note</strong></h5>
                                <div class="clearfix"></div>
                                <p>
                                    <b>Invoice No:</b> <?php echo $debit_note_data['InvoiceNo']; ?><br>
                                </p>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-md-6 col-xs-6">
                            <p><b>Vendor:</b></p>
                            <p>
                                <?php echo str_replace('~d','',$debit_note_data['VendorName']).' '.$debit_note_data['vendor_FirmType']; ?>,
                            </p>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <div class="pull-right">
                                <b>Debit Note No:</b> <?php echo $debit_note_data['DebitNoteNo']; ?><br>
                                <b>Debit Note Date:</b> <?php echo $debit_note_data['DebitNoteDate']; ?><br>
                                <b>Debt Remaining:</b> <?php echo($debit_note_data['PaymentStatus'] == 'Pending')?$debit_note_data['ReceivableAmount']:'0'; ?><br>
                            </div>
                        </div>
                    </div>
                    <br>
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
                                for($i=0;$i<count($debit_note_details);$i++){
                                    $qty = $debit_note_details[$i]['Quantity'];
                                    $price_per_unit = $debit_note_details[$i]['PricePerUnit'];
                                    $total_units_price = (!empty($qty))?$price_per_unit * $qty:$price_per_unit;
                                ?>
                                    <tr>
                                        <td><?php echo $i+1; ?></td>
                                        <td><?php echo $debit_note_details[$i]['Particular']; ?></td>
                                        <td><?php echo $debit_note_details[$i]['HSN']; ?></td>
                                        <td><?php echo(!empty($qty))?$qty:'NA'; ?></td>
                                        <td><?php echo $price_per_unit; ?></td>
                                        <td>
                                            <?php $cn_taxes = explode(',', $debit_note_details[$i]['taxes']);
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
                                    <td><b><?php echo $debit_note_data['ReceivableAmount']; ?>/-</b></td>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        <b class="pull-right">Total Amount in Words</b>
                                    </td>
                                    <td><b><?php echo numberToWords($debit_note_data['ReceivableAmount']); ?> only</b></td>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        <b class="pull-right">Debt Remaining</b>
                                    </td>
                                    <td><b><?php echo($debit_note_data['PaymentStatus'] == 'Pending')?$debit_note_data['ReceivableAmount']:'0'; ?></b></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="row">
                        <div class="col-md-3 col-xs-12 col-sm-12">
                            <?php if(!empty($debit_note_data['SignatureImgPath'])){ ?>
                            <img src="<?php echo media_server($debit_note_data['SignatureImgPath']); ?>" alt="" class="img-responsive">
                            <?php } ?>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>