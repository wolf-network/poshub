<div class="row">
	<div class="col-md-12">
		<div class="box box-success">
			<?php echo form_open_multipart('',['id' => 'expense-form']); ?>
			<div class="box-header with-border">
				<h3 class="box-title pull-left">Add Expense</h3>
				<div class="pull-right">
					<a href="https://www.youtube.com/watch?v=Cw7YuTfkqd4" target="_blank" class="btn btn-info">Watch tutorial</a>
					<a href="<?php echo base_url('view-expenses'); ?>" class="btn btn-primary">View Expenses</a>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="ExpenseHeadMasterID">Expense Head <span class="text-danger">*</span> <a href="javascript:void(0)" data-toggle="modal" data-target="#saveExpenseHeadingModal">Add Expense Heading</a> </label>
							<div class="expense-heading-container">
								<select name="ExpenseHeadMasterID" id="ExpenseHeadMasterID" class="form-control bs_multiselect">
									<option value="">Select Expense Head</option>
									<?php for($i=0;$i<count($expense_heads);$i++){ ?>
										<option value="<?php echo $expense_heads[$i]['ExpenseHeadMasterID']; ?>" <?php echo($expense_heads[$i]['ExpenseHeadMasterID'] == set_value('ExpenseHeadMasterID'))?'selected':''; ?>><?php echo $expense_heads[$i]['ExpenseHead']; ?></option>
									<?php } ?>
								</select>
								<span class="text-danger"><?php echo validation_show_error('ExpenseHeadMasterID'); ?></span>
							</div>
						</div>
					</div>
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
							<label for="ExpenseDate">Expense Date <span class="text-danger">*</span></label>
							<input type="text" name="ExpenseDate" value="<?php echo set_value('ExpenseDate'); ?>" class="form-control daterangepicker" id="ExpenseDate">
							<span class="text-danger"><?php echo validation_show_error('ExpenseDate'); ?></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="ExpenseAmount">Total Expense Amount (Pre Tax) <span class="text-danger">*</span></label>
							<div class="form-group">
								<input id="ExpenseAmount" type="text" name="ExpenseAmount" value="<?php echo set_value('ExpenseAmount'); ?>" placeholder="Total Expense Amount" class="form-control" autocomplete="off">
								<span class="text-danger"><?php echo validation_show_error('ExpenseAmount'); ?></span> 
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="InvoiceNo">Invoice No</label>
							<input type="text" name="InvoiceNo" value="<?php echo set_value('InvoiceNo'); ?>" id="InvoiceNo" class="form-control">
							<span class="text-danger"><?php echo validation_show_error('InvoiceNo'); ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="AttachedDocumentPath">Attach Document [max 2mb]</label> <br>
							<input type="file" name="AttachedDocumentPath">
							<span class="text-danger"><?php echo validation_show_error('AttachedDocumentPath'); ?></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="Remarks">Remark</label>
							<textarea name="Remarks" id="Remarks" class="form-control" cols="30" rows="5"><?php echo set_value('Remarks'); ?></textarea>
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
			                                    <label for="">Tax <span class="text-danger">*</span></label>
			                                    <input type="text" name="Tax[]" value="<?php echo set_value('Tax['.$j.']'); ?>" class="form-control" placeholder="E.g: GST/VAT">
			                                    <span class="text-danger"><?php echo validation_show_error('Tax.'.$j); ?></span>
			                                </div>
			                            </div>
			                            <div class="col-md-5 col-xs-6">
			                                <label for="">Tax Rate(%) <span class="text-danger">*</span></label>
			                                <div class="form-group">
			                                    <div class="input-group">
			                                        <input type="text" name="TaxPercentage[]" value="<?php echo set_value('TaxPercentage['.$j.']'); ?>" placeholder="E.g: 18" class="form-control tax-percentage">
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

<!-- Save Expense Modal -->
<div id="saveExpenseHeadingModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
  	<form action="javascript:void(0)" id="itemForm" class="item-form">
	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Add expense heading</h4>
	      </div>
	      <div class="modal-body">
	        <div class="row">
	        	<div class="col-md-12">
	        		<div class="form-group">
	        			<label for="ExpenseHead">Expense Head <span class="text-danger">*</span> </label>
	        			<input type="text" name="ExpenseHead" value="" id="ExpenseHead-field" class="form-control">
	        		</div>
	        	</div>
	        </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-success save-expense-heading"> Save </button>
	      </div>
	    </div>
	</form>
  </div>
</div>