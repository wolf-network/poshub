<?php echo form_open('',['id' => 'invoice-form']); ?>

<div class="row">
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title pull-left">Point of Sale</h3>
				<div class="pull-right">
					<a href="https://www.youtube.com/watch?v=lgCfpuXIi_8" target="_blank" class="btn btn-info">Watch tutorial</a>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="CompanyAddress">Your billing Address <span class="text-danger">*</span> 
								<a href="<?php echo base_url('add-company-service-tax'); ?>" target="_blank">Add Registered Address</a>
							</label>
							<select name="CompanyAddress" id="CompanyAddress" class="form-control">
								<?php for($i=0;$i<count($company_service_tax_master);$i++){ ?>
									<option value="<?php echo $company_service_tax_master[$i]['RegisteredAddress']; ?>" data-service_tax_type="<?php echo $company_service_tax_master[$i]['ServiceTaxType']; ?>" data-service_tax_type_id="<?php echo $company_service_tax_master[$i]['ServiceTaxTypeID']; ?>" data-tax_identification="<?php echo $company_service_tax_master[$i]['ServiceTaxIdentificationNumber']; ?>" <?php echo($company_service_tax_master[$i]['RegisteredAddress'] == set_value('CompanyAddress'))?'selected':''; ?> >
										<?php echo $company_service_tax_master[$i]['RegisteredAddress']; ?>
									</option>
								<?php } ?>
							</select>
							<span class="text-danger"><?php echo validation_show_error('CompanyAddress'); ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="">Your Service Tax Type <a href="<?php echo base_url('add-company-service-tax'); ?>" target="_blank">Add Type</a> </label>
							<input type="text" id="CompanyServiceTaxType" class="form-control" value="<?php echo set_value('CompanyServiceTaxType'); ?>" disabled>
							<input type="hidden" name="CompanyServiceTaxTypeID" value="<?php echo set_value('CompanyServiceTaxTypeID'); ?>" id="CompanyServiceTaxTypeID" class="CompanyServiceTaxTypeID">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="">Your Service Tax No <a href="<?php echo base_url('add-company-service-tax'); ?>" target="_blank">Add Identification No</a> </label>
							<input type="text" value="<?php echo set_value('CompanyServiceTaxIdentificationNumber'); ?>" class="form-control CompanyServiceTaxIdentificationNumber" disabled>
							<input type="hidden" name="CompanyServiceTaxIdentificationNumber" id="CompanyServiceTaxIdentificationNumber" value="<?php echo set_value('CompanyServiceTaxIdentificationNumber'); ?>" class="form-control CompanyServiceTaxIdentificationNumber">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="PaymentModeID">Payment Mode <span class="text-danger">*</span></label>
							<select name="PaymentModeID" id="PaymentModeID" class="form-control">
								<option value="">Select Payment Mode</option>
								<?php for($i=0;$i<count($payment_modes);$i++){ ?>
									<option value="<?php echo $payment_modes[$i]['PaymentModeID']; ?>"><?php echo $payment_modes[$i]['PaymentMode']; ?></option>
								<?php } ?>
							</select>
							<span class="text-danger"><?php echo validation_show_error('PaymentModeID'); ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header with-border">
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
							$price_per_unit = (!empty(set_value('PricePerUnit['.$i.']')) && is_numeric(set_value('PricePerUnit['.$i.']')) )?set_value('PricePerUnit['.$i.']'):0;

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
					        						if($items[$j]['ItemType'] == 'Good'){
					        							$qty = ($items[$j]['Qty'] > 0)?$items[$j]['Qty'].' In Stock':'Out of stock';
					        						}else{
					        							$qty = 'NA';
					        						}
					        				?>
					        					<option value="<?php echo $items[$j]['Item']; ?>" <?php echo($items[$j]['Item'] == set_value('Particular['.$i.']'))?'selected':''; ; ?> data-price="<?php echo $items[$j]['Price']; ?>" data-hsn="<?php echo $items[$j]['HSN']; ?>" data-taxes="<?php echo $items[$j]['taxes']; ?>" data-barcode_no="<?php echo $items[$j]['BarcodeNo']; ?>" data-item_type="<?php echo $items[$j]['ItemType']; ?>" data-item_category="<?php echo(!empty($items[$j]['ItemCategory']))?$items[$j]['ItemCategory']:''; ?>" data-item_qty="<?php echo $items[$j]['Qty']; ?>" <?php echo(($items[$j]['Qty'] <= 0 && $items[$j]['ItemType'] == 'Good'))?'disabled':''; ?> >
					        						<?php echo $items[$j]['Item'].' ('.$qty.')'; ?> 
					        					</option>
					        				<?php } ?>
					        			</select>
					        			<input type="hidden" name="ItemType[]" value="<?php echo set_value('ItemType['.$i.']'); ?>" class="item-type">
					        			<input type="hidden" name="ItemCategory[]" value="<?php echo set_value('ItemCategory['.$i.']'); ?>" class="item-category">
					        			<input type="hidden" name="BarcodeNo[]" value="<?php echo set_value('BarcodeNo['.$i.']'); ?>" class="barcode-no">
										<span class="text-danger"><?php echo validation_show_error('Particular.'.$i); ?></span>
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
										<label for="">Amount</label>
										<input type="text" name="Amount[]" readonly value="<?php echo set_value('Amount.'.$i); ?>" class="form-control amount">
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="Discount">Discount (%)</label>
										<input type="text" class="form-control discount" name="Discount[]" value="<?php echo set_value('Discount['.$i.']'); ?>" placeholder="Discount">
										<span class="text-danger"><?php echo validation_show_error('Discount.'.$i); ?></span>
									</div>
								</div>
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
										<label for="SerialNo">Serial No</label>
										<input type="text" name="SerialNo[]" value="<?php echo set_value('SerialNo['.$i.']') ?>" class="form-control">
									</div>

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

				<div class="row">
					<div class="col-md-6">
            			<div class="panel panel-default">
			                <div class="panel-heading">
			                    <h4 class="pull-left">Additional Charges </h4>
			                    <button type="button" class="btn bg-olive pull-right add-charges">Add</button>
			                    <div class="clearfix"></div>
			                </div>
			                <div class="panel-body">
			                    <?php 
			                        $additional_charge_count = (!empty($_POST['AdditionalChargeType']))?count($_POST['AdditionalChargeType']):1;
			                        for($j = 0;$j<$additional_charge_count;$j++){
			                    ?>
			                    <div class="row charge-container">
			                        <div class="col-md-5 col-sm-5 col-xs-12">
			                            <div class="form-group">
			                                <label for="">Charge Type</label>
			                                <input type="text" name="AdditionalChargeType[]" value="<?php echo set_value('AdditionalChargeType['.$j.']'); ?>" id="AdditionalChargeType-<?php echo $j; ?>" class="charge-type form-control" placeholder="E.g: Shipping">
			                                <span class="text-danger"><?php echo validation_show_error('AdditionalChargeType.'.$j); ?></span>
			                            </div>
			                        </div>
			                        <div class="col-md-5 col-sm-5 col-xs-12">
			                            <label for="">Rate </label>
			                            <div class="form-group">
			                                <input type="text" name="AdditionalCharge[]" value="<?php echo set_value('AdditionalCharge['.$j.']'); ?>" id="charge-<?php echo $j; ?>" class="form-control charge">
			                                <span class="text-danger"><?php echo validation_show_error('AdditionalCharge.'.$j); ?></span>
			                            </div>
			                        </div>
			                        <div class="col-md-2 col-sm-2 col-xs-12">
			                            <div class="form-group remove-charge-btn-container">
			                            </div>
			                        </div>
			                    </div>
			                    <?php } ?>
			                </div>
			            </div>
        			</div>
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading text-center"><b>Total Billing</b></div>
							<div class="panel-body">
								<table class="table table-bordered text-center">
									<tr>
										<th>Total Particular amount</th>
										<th>Receivable Amount</th>
									</tr>
									<tr>
										<td class="total-particular-amt-post-tax"></td>
										<td class="total-receivable-amt"></td>
									</tr>
								</table>
							</div>
						</div>	
					</div>
					<!-- ./col -->

				</div>
				<!-- ./row -->
			</div>
			<div class="box-footer">
				<button class="btn btn-success pull-left">Save</button>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>

<?php echo view('\Modules\Finance\Views\add_item_modal'); ?>