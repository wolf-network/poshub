<?php echo form_open('',['id' => 'debit-note-form']); ?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title pull-left">Create Debit Note</h3>
				<div class="pull-right">
					<a href="https://www.youtube.com/watch?v=X1fPj9qqFpI" target="_blank" class="btn btn-info">Watch tutorial</a>
					<a href="<?php echo base_url('manage-debit-notes'); ?>" class="btn btn-primary">Manage Debit Notes</a>
				</div>
				<br>
				<b>Notes:
				<ol>
					<li>This will automatically reduce the goods from your inventory and make an outward entry if it is greater than or equivalent to the entered quantity</li>
					<li>The above action will only be applied if the debit note date is equal to/matches current date</li>
				</ol></b>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-4">
			            <input type="hidden" name="allVendorsOffset" id="allVendorsOffset" value="30">
			            <div class="form-group">
			                <label for="VendorID">Select Vendor <span class="text-danger">*</span>
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
							<label for="InvoiceNo">Invoice No. <span class="text-danger">*</span> </label>
							<input type="text" name="InvoiceNo" value="<?php echo set_value('InvoiceNo'); ?>" id="InvoiceNo" class="form-control">
							<span class="text-danger"><?php echo validation_show_error('InvoiceNo'); ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="CreditNoteNo">Credit Note No</label>
							<input type="text" name="CreditNoteNo" value="<?php echo set_value('CreditNoteNo'); ?>" id="CreditNoteNo" class="form-control">
							<span class="text-danger"><?php echo validation_show_error('CreditNoteNo'); ?></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="DebitNoteNo">Debit Note No. <span class="text-info">(Leave blank to auto generate)</span></label>
							<input type="text" name="DebitNoteNo" value="<?php echo set_value('DebitNoteNo'); ?>" id="DebitNoteNo" class="form-control">
							<span class="text-danger"><?php echo validation_show_error('DebitNoteNo'); ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="DebitNoteDate">Debit Note Date <span class="text-danger">*</span></label>
							<input type="text" name="DebitNoteDate" value="<?php echo set_value('DebitNoteDate'); ?>" class="form-control daterangepicker" data-max-date="<?php echo date('Y-m-d'); ?>">
							<span class="text-danger"><?php echo validation_show_error('DebitNoteDate'); ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="PaymentStatus">Payment Status <span class="text-danger">*</span></label>
							<select name="PaymentStatus" id="PaymentStatus" class="form-control">
								<option value="">Select Payment Status</option>
								<option value="Received" <?php echo(set_value('PaymentStatus') == 'Received')?'selected':''; ?>>Received</option>
								<option value="Pending" <?php echo(set_value('PaymentStatus') == 'Pending')?'selected':''; ?>>Pending</option>
							</select>
							<span class="text-danger"><?php echo validation_show_error('PaymentStatus'); ?></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="Remarks">Remarks</label>
							<textarea name="Remarks" id="Remarks" class="form-control" cols="30" rows="5"><?php echo set_value('Remarks'); ?></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="box box-success">
			<div class="box-header">
				<div class="col-md-8 col-sm-6">
					<h3 class="box-title pull-left">Billing</h3>
				</div>
				<div class="col-md-4 col-sm-6">
					<input type="text" name="BarcodeNo" value="" id="BarcodeNo" class="form-control bg-yellow barcode-input" placeholder="Barcode No" autofocus>
					<p>Press enter, If you are entering the barcode manually</p>
				</div>
			</div>
			<div class="box-body">
				<div class="particular-container">
					<?php 
						$bill_particulars_count = (!empty($_POST['Particular']))?count($_POST['Particular']):1; 
						for($i=0;$i<$bill_particulars_count;$i++){
							$random_particular_tax_key = (!empty($_POST['ParticularTaxKey']))?$_POST['ParticularTaxKey'][$i]:mt_rand(1897,2022);
							$qty = (!empty(set_value('Quantity['.$i.']')))?set_value('Quantity['.$i.']'):0;
							$price_per_unit = (!empty(set_value('PricePerUnit['.$i.']')))?set_value('PricePerUnit['.$i.']'):0;

							$total_price = $qty * $price_per_unit;
					?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="pull-left">Detail <?php echo $i+1; ?> </h4>
							<div class="pull-right">
								<button type="button" class="btn btn-success pull-left add-particular">Add Particular</button>
								<div class="remove-particular-btn-container pull-left ">
									<?php if($bill_particulars_count > 0){ ?>
										<button type="button" class="btn btn-danger remove-particular-btn">Remove</button>
									<?php } ?>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-4">
									<input type="hidden" name="ParticularTaxKey[]" value="<?php echo $random_particular_tax_key; ?>" class="particular-tax-key">
									<div class="form-group">
										<label for="">Particular <span class="text-danger">*</span> <a href="javascript:void(0)" data-toggle="modal" data-target="#saveItemModal">Add Item</a> </label>
										
										<select name="Particular[]" id="particluar-<?php echo $random_particular_tax_key; ?>" class="form-control particular-selector bs_multiselect">
					        				<option value="">Select Item</option>
					        				<?php 
					        					for($j=0;$j<count($items);$j++){
					        				?>
					        					<option value="<?php echo $items[$j]['Item']; ?>" <?php echo($items[$j]['Item'] == set_value('Particular['.$i.']'))?'selected':''; ; ?> data-hsn="<?php echo $items[$j]['HSN']; ?>" data-taxes="<?php echo $items[$j]['taxes']; ?>" data-barcode_no="<?php echo $items[$j]['BarcodeNo']; ?>" data-item_type="<?php echo $items[$j]['ItemType']; ?>" data-item_category="<?php echo(!empty($items[$j]['ItemCategory']))?$items[$j]['ItemCategory']:''; ?>" data-item_qty="<?php echo $items[$j]['Qty']; ?>" <?php echo(($items[$j]['Qty'] <= 0 && $items[$j]['ItemType'] == 'Good'))?'disabled':''; ?> >
					        						<?php echo $items[$j]['Item']; ?> 
					        					</option>
					        				<?php } ?>
					        			</select>
										<span class="text-danger"><?php echo validation_show_error('Particular.'.$i); ?></span>
										<input type="hidden" name="ItemType[]" value="<?php echo set_value('ItemType['.$i.']'); ?>" class="item-type">
										<input type="hidden" name="ItemCategory[]" value="<?php echo set_value('ItemCategory['.$i.']'); ?>" class="item-category">
										<input type="hidden" name="BarcodeNo[]" value="<?php echo set_value('BarcodeNo['.$i.']'); ?>" class="barcode-no">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="">HSN/SAC <span class="text-danger">*</span></label>
										<input type="text" name="HSN[]" value="<?php echo set_value('HSN['.$i.']'); ?>" id="hsn-<?php echo $random_particular_tax_key; ?>" class="form-control hsn-code">
										<span class="text-danger"><?php echo validation_show_error('HSN.'.$i); ?></span>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="">Quantity <span class="text-danger">*</span></label>
										<input type="text" name="Quantity[]" value="<?php echo set_value('Quantity['.$i.']'); ?>" min="1" id="qty-<?php echo $random_particular_tax_key; ?>" class="form-control qty">
										<span class="text-danger"><?php echo validation_show_error('Quantity.'.$i); ?></span>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="">Price per unit <span class="text-danger">*</span></label>
										<input type="text" name="PricePerUnit[]" value="<?php echo set_value('PricePerUnit['.$i.']'); ?>" min="1" id="price-per-unit-<?php echo $random_particular_tax_key; ?>" class="form-control price-per-unit">
										<span class="text-danger"><?php echo validation_show_error('PricePerUnit.'.$i); ?></span>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="">Taxable Amount</label>
										<input type="text" name="Amount[]" readonly value="<?php echo set_value('Amount['.$i.']'); ?>" class="form-control amount">
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-6">
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="pull-left">Taxes (Detail <?php echo $i+1; ?>) </h4>
											<button type="button" class="btn bg-olive pull-right add-tax">Add</button>
											<div class="clearfix"></div>
										</div>
										<div class="panel-body">
											<?php 
												$tax_count = (!empty($_POST['Tax'][$random_particular_tax_key]))?count($_POST['Tax'][$random_particular_tax_key]):1;
												$tax_percentage_total = 0;
												for($j = 0;$j<$tax_count;$j++){
													$tax_percentage_total += (set_value('TaxPercentage['.$random_particular_tax_key.']['.$j.']'))?set_value('TaxPercentage['.$random_particular_tax_key.']['.$j.']'):0;
											?>
											<div class="row tax-container">
												<div class="col-md-5 col-sm-5 col-xs-12">
													<div class="form-group">
														<label for="">Tax Name <span class="text-danger">*</span></label>
														<input type="text" name="Tax[<?php echo $random_particular_tax_key; ?>][]" value="<?php echo set_value('Tax['.$random_particular_tax_key.']['.$j.']'); ?>" required id="tax-<?php echo $random_particular_tax_key; ?>" class="tax form-control" placeholder="E.g: GST/VAT">
														<span class="text-danger"><?php echo validation_show_error('Tax.'.$random_particular_tax_key.'.'.$j); ?></span>
													</div>
												</div>
												<div class="col-md-5 col-sm-5 col-xs-12">
													<label for="">Tax Rate (%) <span class="text-danger">*</span></label>
													<div class="form-group">
														<div class="input-group">
														    <input type="text" name="TaxPercentage[<?php echo $random_particular_tax_key; ?>][]" value="<?php echo set_value('TaxPercentage['.$random_particular_tax_key.']['.$j.']'); ?>" required id="tax-percentage-<?php echo $random_particular_tax_key; ?>" placeholder="E.g: 18" class="form-control tax-percentage">
														    <span class="input-group-addon">%</span>
													  	</div>
													  	<span class="text-danger"><?php echo validation_show_error('TaxPercentage.'.$random_particular_tax_key.'.'.$j); ?></span>
													</div>
												</div>
												<div class="col-md-2 col-sm-2 col-xs-12">
													<div class="form-group remove-tax-btn-container">
														<?php if($tax_count > 1){ ?>
															<label for="">&nbsp;</label> <br> 
															<button type="button" class="btn btn-danger btn-xs btn-block remove-tax-btn">
																<i class="fa fa-minus hidden-xs"></i>
																<span class="hidden-lg hidden-md hidden-sm">Remove</span>
															</button>
														<?php } ?>
													</div>
												</div>
											</div>
											<?php } ?>
										</div>
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">Total</span>
											<?php
												$tax_percentage_amount = $total_price * $tax_percentage_total / 100;
												$total_bill = $total_price + $tax_percentage_amount;
											?>
										    <input type="text" name="Total" value="<?php echo $total_bill; ?>" readonly id="Total" class="form-control total">
									  	</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- ./row -->
					<?php } ?>
				</div>
				<!-- ./particular-container -->
			</div>
			<!-- ./box-body -->

			<div class="box-footer">
				<button class="btn btn-success pull-right">Save</button>
			</div>
		</div>
<?php echo form_close(); ?>