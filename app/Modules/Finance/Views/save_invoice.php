<?php echo form_open('',['id' => 'invoice-form']); ?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title pull-left">Create Invoice</h3>
				<div class="pull-right">
					<a href="https://www.youtube.com/watch?v=4fxJoa8lR1s" target="_blank" class="btn btn-info">Watch tutorial</a>
					<a href="<?php echo base_url('manage-invoices'); ?>" class="btn btn-primary">Manage Invoices</a>
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
								<option value="">Select Address</option>
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
							<label for="">Your GST No./VAT No./Other  <a href="<?php echo base_url('add-company-service-tax'); ?>" target="_blank">Add Service Tax No</a> </label>
							<input type="text" value="<?php echo set_value('CompanyServiceTaxIdentificationNumber'); ?>" class="form-control CompanyServiceTaxIdentificationNumber" disabled>
							<input type="hidden" name="CompanyServiceTaxIdentificationNumber" id="CompanyServiceTaxIdentificationNumber" value="<?php echo set_value('CompanyServiceTaxIdentificationNumber'); ?>" class="form-control CompanyServiceTaxIdentificationNumber">
						</div>
					</div>
				</div>
				<div class="row">
					<input type="hidden" name="allClientsOffset" id="allClientsOffset" value="30">
					<div class="col-md-4">
						<div class="form-group">
							<label for="ClientID">Select Client <span class="text-danger">*</span>
								<a href="<?php echo base_url('add-client'); ?>" target="_blank">Add Client</a>
							</label>
							<div class="clients-container">
								<select name="ClientID" id="ClientID" class="form-control bs_multiselect" data-client_name="<?php echo set_value('ClientID'); ?>" data-client_service_tax_identification_number="<?php echo set_value('ClientServiceTaxIdentificationNumber'); ?>">
									<option value="">Select Client</option>
									<?php for($i=0;$i<count($clients);$i++){ ?>
										<option value="<?php echo $clients[$i]['ClientID']; ?>" <?php echo($clients[$i]['ClientID'] == set_value('ClientID'))?'selected':''; ?> >
											<?php echo $clients[$i]['ClientName']; ?>
										</option>
									<?php } ?>
								</select>
							</div>
							<span class="text-danger"><?php echo validation_show_error('ClientID'); ?></span>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="ClientServiceTaxIdentificationNumber">GST No./VAT No./Other <a href="javascript:void(0)" class="add-client-service-tax-no" target="_blank">Add Client's Service Tax No</a> </label>
							<select name="ClientServiceTaxIdentificationNumber" id="ClientServiceTaxIdentificationNumber" class="form-control bs_multiselect">
								<option value="">Select Service Tax No</option>
							</select>
							<span class="text-danger"><?php echo validation_show_error('ClientServiceTaxIdentificationNumber'); ?></span>
						</div>	
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="ServiceTaxType">Client Service Tax Type</label>
							<input type="text" name="ServiceTaxType" value="<?php echo set_value('ServiceTaxType'); ?>" id="ServiceTaxType" class="form-control" readonly>
							<input type="hidden" name="ServiceTaxTypeID" value="<?php echo set_value('ServiceTaxTypeID'); ?>" id="ServiceTaxTypeID">
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="ClientContactNo">Client Contact No <span class="text-danger">*</span> </label>
							<input type="text" name="ClientContactNo" value="<?php echo set_value('ClientContactNo'); ?>" id="ClientContactNo" class="form-control">
							<span class="text-danger"><?php echo validation_show_error('ClientContactNo'); ?></span>
						</div>	
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="InvoiceNo">Invoice No <span class="text-info">(Leave blank to auto generate)</span> </label>
							<input type="text" name="InvoiceNo" value="<?php echo set_value('InvoiceNo'); ?>" id="InvoiceNo" class="form-control">
							<span class="text-danger"><?php echo validation_show_error('InvoiceNo'); ?></span>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="ClientInvoiceDate">Invoice Date <span class="text-danger">*</span> </label>
							<input type="text" name="ClientInvoiceDate" id="ClientInvoiceDate" value="<?php echo set_value('ClientInvoiceDate'); ?>" class="form-control daterangepicker">
							<span class="text-danger"><?php echo validation_show_error('ClientInvoiceDate'); ?></span>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="ClientInvoiceDueDate">Due Date <span class="text-danger">*</span> </label>
							<input type="text" name="ClientInvoiceDueDate" value="<?php echo set_value('ClientInvoiceDueDate'); ?>" id="ClientInvoiceDueDate" class="form-control daterangepicker">
							<span class="text-danger"><?php echo validation_show_error('ClientInvoiceDueDate'); ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="CustomerNotes">Customer Notes </label>
							<input type="text" name="CustomerNotes" value="<?php echo set_value('CustomerNotes'); ?>" id="CustomerNotes" class="form-control">
							<span class="text-danger"><?php echo validation_show_error('CustomerNotes'); ?></span>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="ClientBillingAddress">Billing Address <span class="text-danger">*</span></label>
							<textarea name="ClientBillingAddress" id="ClientBillingAddress" cols="30" rows="5" class="form-control"><?php echo set_value('ClientBillingAddress'); ?></textarea>
							<span class="text-danger"><?php echo validation_show_error('ClientBillingAddress'); ?></span>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="ClientShippingAddress">Shipping Address</label>
							<textarea name="ClientShippingAddress" id="ClientShippingAddress" cols="30" rows="5" class="form-control"><?php echo set_value('ClientShippingAddress'); ?></textarea>
							<span class="text-danger"><?php echo validation_show_error('ClientShippingAddress'); ?></span>
						</div>
					</div>
					
					<div class="col-md-4">
						<div class="form-group">
							<?php $invoice_terms_and_conditions = (!empty($invoice_settings_details['TermsAndConditions']))?$invoice_settings_details['TermsAndConditions']:''; ?>
							<label for="TermsAndConditions">Terms & Conditions</label>
							<textarea name="TermsAndConditions" id="TermsAndConditions" cols="30" rows="5" class="form-control"><?php echo(!empty($_POST['TermsAndConditions']))?set_value('TermsAndConditions'):$invoice_terms_and_conditions; ?></textarea>
							<span class="text-danger"><?php echo validation_show_error('TermsAndConditions'); ?></span>
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
										<label for="">Amount</label>
										<input type="text" name="Amount[]" readonly value="<?php echo set_value('Amount['.$i.']'); ?>" class="form-control amount">
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
								<h4 class="pull-left">Deductibles at source</h4>
								<button type="button" class="btn bg-olive pull-right add-deductible">Add</button>
								<div class="clearfix"></div>
							</div>
							<div class="panel-body">
								<?php 
									$deductible_count = (!empty($_POST['DeductibleType']))?count($_POST['DeductibleType']):1;
									$deductible_percentage_total = 0;
									for($j = 0;$j<$deductible_count;$j++){
								?>
								<div class="row deductible-container">
									<div class="col-md-5 col-sm-5 col-xs-12">
										<div class="form-group">
											<label for="deductible-0">Deductible type</label>
											<input type="text" name="DeductibleType[]" value="<?php echo set_value('DeductibleType['.$j.']'); ?>" id="deductible-0" placeholder="E.g: TDS" class="form-control">
											<span class="text-danger"><?php echo validation_show_error('DeductibleType.'.$j); ?></span>
										</div>
									</div>
									<div class="col-md-5 col-sm-5 col-xs-12">
										<div class="form-group">
											<label for="deductible-0">Deductible Percentage</label>
											<div class="input-group">
												<input type="text" name="DeductiblePercentage[]" value="<?php echo set_value('DeductiblePercentage['.$j.']'); ?>" id="deductible-percentage-0" placeholder="E.g: 10" class="form-control deductible-percentage">
												<span class="input-group-addon">%</span>
											</div>
											<span class="text-danger"><?php echo validation_show_error('DeductiblePercentage.'.$j); ?></span>
										</div>
									</div>
									<div class="col-md-2 col-sm-2 col-xs-12">
										<div class="form-group remove-deductible-btn-container">
											<?php if($deductible_count > 1){ ?>
												<label for="">&nbsp;</label> <br> 
												<button type="button" class="btn btn-danger btn-xs btn-block remove-deductible-btn">
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
					<!-- ./col -->

					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading text-center"><b>Total Billing</b></div>
							<div class="panel-body">
								<table class="table table-bordered text-center">
									<tr>
										<th>Bill Amount</th>
										<th>Amount deductible at source</th>
										<th>Receivable Amount</th>
									</tr>
									<tr>
										<td class="total-bill-amt"></td>
										<td class="total-deductible-amt"></td>
										<td class="total-receivable-amt"></td>
									</tr>
								</table>
							</div>
						</div>	
					</div>
					<!-- ./col -->

				</div>
				<!-- ./row -->

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
				</div>
				<!-- ./row -->
			</div>
			<!-- ./box-body -->

			<div class="box-footer">
				<button class="btn btn-success pull-left">Save</button>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>

<?php echo view('\Modules\Finance\Views\add_item_modal'); ?>