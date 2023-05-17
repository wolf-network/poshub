<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <?php echo form_open('',['id' => 'save-stock-form']); ?>
            <div class="box-header">
                <div class="col-md-2 col-xs-6">
                    <h3 class="box-title">Add Stock</h3>
                </div>
                <div class="col-md-4 col-xs-6">
                    <input type="text" name="BarcodeNo" value="" id="BarcodeNo" class="form-control bg-yellow barcode-input" placeholder="Barcode No" autofocus>
                    <p>Press enter, If you are entering the barcode manually</p>
                </div>
                <div class="col-md-6">
                    <a href="https://www.youtube.com/watch?v=hjINI-Y-NyY" target="_blank" class="btn btn-info pull-right">Watch tutorial</a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <input type="hidden" name="allVendorsOffset" id="allVendorsOffset" value="30">
                        <div class="form-group">
                            <label for="VendorID">Select Vendor
                                <a href="<?php echo base_url('add-vendor'); ?>" target="_blank">Add Vendor</a>
                            </label>
                            <div class="vendors-container">
                                <select name="VendorID" id="VendorID" class="form-control bs_multiselect" data-vendor_id="<?php echo set_value('VendorID'); ?>">
                                    <option value="">Select Vendor</option>
                                    <?php for($i=0;$i<count($vendors);$i++){ ?>
                                        <option value="<?php echo $vendors[$i]['VendorID']; ?>" <?php echo($vendors[$i]['VendorID'] == set_value('VendorID'))?'selected':''; ?> >
                                            <?php echo $vendors[$i]['VendorName']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <span class="text-danger"><?php echo validation_show_error('VendorID'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ItemID">Select Item (Goods)<span class="text-danger">*</span></label>
                            <select name="ItemID" id="ItemID" class="form-control bs_multiselect particular-selector">
                                <option value="">Select Item</option>
                                <?php for($i=0;$i<count($goods);$i++){ ?>
                                    <option value="<?php echo $goods[$i]['ItemID']; ?>" data-hsn="<?php echo $goods[$i]['HSN']; ?>" data-buying_price="<?php echo $goods[$i]['BuyingPrice']; ?>" data-price="<?php echo $goods[$i]['Price']; ?>" data-barcode_no="<?php echo $goods[$i]['BarcodeNo']; ?>" <?php echo(set_value('ItemID') == $goods[$i]['ItemID'])?'selected':''; ?> ><?php echo $goods[$i]['Item']; ?></option>
                                <?php } ?>
                            </select>
                            <input type="hidden" name="Item" value="<?php echo set_value('Item') ?>" class="item">
                            <span class="text-danger"><?php echo validation_show_error('Item'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="HSN">HSN/SAC</label>
                            <input type="text" name="HSN" value="<?php echo set_value('HSN'); ?>" class="form-control hsn-code" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="InvoiceNo">Invoice No</label>
                            <input name="InvoiceNo" value="<?php echo set_value('InvoiceNo'); ?>" id="InvoiceNo" class="form-control">
                            <span class="text-danger"><?php echo validation_show_error('InvoiceNo'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="BatchNo">Batch No <span class="text-danger">*</span></label>
                            <input name="BatchNo" value="<?php echo set_value('BatchNo'); ?>" id="BatchNo" class="form-control">
                            <span class="text-danger"><?php echo validation_show_error('BatchNo'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="Qty">Qty <span class="text-danger">*</span></label>
                            <input type="text" name="Qty" value="<?php echo set_value('Qty'); ?>" class="form-control">
                            <span class="text-danger"><?php echo validation_show_error('Qty'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ManufacturingDate">Manufacturing Date</label>
                            <input name="ManufacturingDate" value="<?php echo set_value('ManufacturingDate'); ?>" id="ManufacturingDate" class="form-control daterangepicker">
                            <span class="text-danger"><?php echo validation_show_error('ManufacturingDate'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ExpiryDate">Expiry Date</label>
                            <input name="ExpiryDate" value="<?php echo set_value('ExpiryDate'); ?>" id="ExpiryDate" class="form-control daterangepicker" data-min-date="<?php echo date('Y-m-d'); ?>">
                            <span class="text-danger"><?php echo validation_show_error('ExpiryDate'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ExpiryReminderDate">Start Expiry Reminder From</label>
                            <input name="ExpiryReminderDate" value="<?php echo set_value('ExpiryReminderDate'); ?>" id="ExpiryReminderDate" class="form-control daterangepicker" data-min-date="<?php echo date('Y-m-d'); ?>">
                            <span class="text-danger"><?php echo validation_show_error('ExpiryReminderDate'); ?></span>
                            <b>Note: Reminders will be sent weekly from the set date unless you mark the expiring stocks as returned.</b>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="BuyingPricePerUnit">Buying Price per Unit (Pre Tax) <span class="text-danger">*</span></label>
                            <input type="text" name="BuyingPricePerUnit" value="<?php echo set_value('BuyingPricePerUnit'); ?>" id="BuyingPricePerUnit" class="form-control buying-price">
                            <span class="text-danger"><?php echo validation_show_error('BuyingPricePerUnit'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="Price">Selling Price per Unit (Pre Tax) <span class="text-danger">*</span></label>
                            <input type="text" name="Price" value="<?php echo set_value('Price'); ?>" id="Price" class="form-control price-per-unit">
                            <span class="text-danger"><?php echo validation_show_error('Price'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="pull-left">Taxes (On Buying)</h4>
                                <button type="button" class="btn bg-olive pull-right add-tax">Add</button>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <?php 
                                $tax_count = (!empty($_POST['Tax']))?count($_POST['Tax']):1;
                                $tax_percentage_total = 0;
                                for($j = 0;$j<$tax_count;$j++){
                                    $tax_percentage_total += (set_value('TaxPercentage['.$j.']') && is_numeric(set_value('TaxPercentage['.$j.']')))?set_value('TaxPercentage['.$j.']'):0;
                                    ?>
                                    <div class="row">
                                        <div class="col-md-5 col-xs-4">
                                            <div class="form-group">
                                                <label for="">Tax Name <span class="text-danger">*</span></label>
                                                <input type="text" name="Tax[]" value="<?php echo set_value('Tax['.$j.']'); ?>" class="form-control" placeholder="E.g: GST/VAT">
                                                <span class="text-danger"><?php echo validation_show_error('Tax.'.$j); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-6">
                                            <label for="">Tax Rate (%) <span class="text-danger">*</span></label>
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input type="text" name="TaxPercentage[]" value="<?php echo set_value('TaxPercentage['.$j.']'); ?>" class="form-control tax-percentage">
                                                    <span class="input-group-addon">%</span>
                                                </div>  
                                            </div>
                                            <span class="text-danger"><?php echo validation_show_error('TaxPercentage.'.$j); ?></span>
                                        </div>
                                        <div class="col-md-2 col-xs-1">
                                            <div class="form-group remove-tax-btn-container">
                                                <?php if($tax_count > 1){ ?>
                                                    <label for="">&nbsp;</label> <br>
                                                    <button class="btn btn-danger btn-xs remove-tax-btn"><i class="fa fa-minus"></i></button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button class="btn btn-success pull-right">Add</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>