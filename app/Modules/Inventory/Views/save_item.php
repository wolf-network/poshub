<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <?php echo form_open('',['id' => 'save-item-form']); ?>
            <div class="box-header">
                <h3 class="box-title"><?php echo(!empty($item_id))?'Edit':'Add'; ?> Item</h3>
                <div class="pull-right">
                    <a href="https://www.youtube.com/watch?v=JH1vp5k5tpY" target="_blank" class="btn btn-info">Watch tutorial</a>
                    <a href="<?php echo base_url('manage-items'); ?>" class="btn btn-primary">Manage Items</a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="Item">Item <span class="text-danger">*</span></label>
                            <input type="text" name="Item" value="<?php echo set_value('Item'); ?>" placeholder="Enter Item Name" class="form-control">
                            <span class="text-danger"><?php echo validation_show_error('Item'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="BuyingPrice">Buying Price Per Unit (Pre Tax) </label>
                            <input type="text" name="BuyingPrice" value="<?php echo set_value('BuyingPrice'); ?>" placeholder="Enter Item Price" class="form-control">
                            <span class="text-danger"><?php echo validation_show_error('BuyingPrice'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="Price">Selling Price Per Unit (Pre Tax) <span class="text-danger">*</span></label>
                            <input type="text" name="Price" value="<?php echo set_value('Price'); ?>" placeholder="Enter Item Price" class="form-control">
                            <span class="text-danger"><?php echo validation_show_error('Price'); ?></span>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Type <span class="text-danger">*</span></label> <br>
                            <input type="radio" name="ItemType" value="Good" id="Good" <?php echo(set_value('ItemType') == 'Good')?'checked':''; ?> > Good
                            <input type="radio" name="ItemType" value="Service" id="Service" <?php echo(set_value('ItemType') == 'Service')?'checked':''; ?> > Service <br>
                            <span class="text-danger"><?php echo validation_show_error('ItemType'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="HSN">HSN/SAC Code <span class="text-danger">*</span></label>
                            <input type="text" name="HSN" value="<?php echo set_value('HSN'); ?>" placeholder="Enter Item HSN/SAC Code" class="form-control">
                            <span class="text-danger"><?php echo validation_show_error('HSN'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="BarcodeNo">Barcode No</label>
                            <input type="text" name="BarcodeNo" value="<?php echo set_value('BarcodeNo'); ?>" placeholder="Enter Barcode No" class="form-control">
                            <span class="text-danger"><?php echo validation_show_error('BarcodeNo'); ?></span>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ItemCategoryMasterID">Item Category <a href="javascript:void(0)" data-toggle="modal" data-target="#itemCategoryModal">Add Category</a> </label>
                            <select name="ItemCategoryMasterID" id="ItemCategoryMasterID" class="form-control">
                                <option value="">Select Category</option>
                                <?php for ($i=0; $i <count($item_categories) ; $i++) { ?><option value="<?php echo $item_categories[$i]['ItemCategoryMasterID']; ?>" <?php echo(set_value('ItemCategoryMasterID') == $item_categories[$i]['ItemCategoryMasterID'])?'selected':''; ?> ><?php echo $item_categories[$i]['ItemCategory']; ?></option><?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="pull-left">Taxes (On Selling)</h4>
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
                <button type="submit" class="btn btn-success pull-right">Save</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="itemCategoryModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Category</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="ItemCategory">Category name</label>
                    <input type="text" id="ItemCategory" class="form-control" value="<?php echo set_value('ItemCategory'); ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success save-item-category">Save</button>
            </div>
        </div>

    </div>
</div>