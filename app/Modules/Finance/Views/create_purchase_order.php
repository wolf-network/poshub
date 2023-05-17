<?php echo form_open('',['id' => 'purchase-order-form']); ?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title pull-left">Create Purchase Order</h3>
				<div class="pull-right">
					<a href="https://www.youtube.com/watch?v=FpLhXoddUO4" target="_blank" class="btn btn-info">Watch tutorial</a>
					<a href="<?php echo base_url('manage-purchase-orders'); ?>" class="btn btn-primary">Manage Purchase Orders</a>
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
							<label for="">Your Service Tax No <a href="<?php echo base_url('add-company-service-tax'); ?>" target="_blank">Add Identification No</a> </label>
							<input type="text" value="<?php echo set_value('CompanyServiceTaxIdentificationNumber'); ?>" class="form-control CompanyServiceTaxIdentificationNumber" disabled>
							<input type="hidden" name="CompanyServiceTaxIdentificationNumber" id="CompanyServiceTaxIdentificationNumber" value="<?php echo set_value('CompanyServiceTaxIdentificationNumber'); ?>" class="form-control CompanyServiceTaxIdentificationNumber">
						</div>
					</div>
				</div>
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
							<label for="VendorServiceTaxIdentificationNumber">Vendor Service Tax No <a href="javascript:void(0)" class="add-vendor-service-tax-no">Add Service Tax No</a> </label>
							<select name="VendorServiceTaxIdentificationNumber" id="VendorServiceTaxIdentificationNumber" class="form-control bs_multiselect" data-selected_vendor_service_tax_identification_number="<?php echo set_value('VendorServiceTaxIdentificationNumber'); ?>" >
								<option value="">Select Service Tax No</option>
							</select>
							<span class="text-danger"><?php echo validation_show_error('VendorServiceTaxIdentificationNumber'); ?></span>
						</div>	
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="ServiceTaxType">Vendor Service Tax Type</label>
							<input type="text" name="ServiceTaxType" value="<?php echo set_value('ServiceTaxType'); ?>" id="ServiceTaxType" class="form-control" readonly>
							<input type="hidden" name="ServiceTaxTypeID" value="<?php echo set_value('ServiceTaxTypeID'); ?>" id="ServiceTaxTypeID">
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="VendorContactNo">Vendor Contact No <span class="text-danger">*</span> </label>
							<input type="text" name="VendorContactNo" value="<?php echo set_value('VendorContactNo'); ?>" id="VendorContactNo" class="form-control">
							<span class="text-danger"><?php echo validation_show_error('VendorContactNo'); ?></span>
						</div>	
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="PurchaseOrderNo">PO No <span class="text-info">(Leave blank to auto generate)</span> </label>
							<input type="text" name="PurchaseOrderNo" value="<?php echo set_value('PurchaseOrderNo'); ?>" id="PurchaseOrderNo" class="form-control">
							<span class="text-danger"><?php echo validation_show_error('PurchaseOrderNo'); ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="DeliveryDate">Delivery Date <span class="text-danger">*</span> </label>
							<input type="text" name="DeliveryDate" id="DeliveryDate" value="<?php echo set_value('DeliveryDate'); ?>" class="form-control daterangepicker">
							<span class="text-danger"><?php echo validation_show_error('DeliveryDate'); ?></span>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="VendorBillingAddress">Vendor Billing Address</label>
							<textarea name="VendorBillingAddress" id="VendorBillingAddress" cols="30" rows="5" class="form-control"><?php echo set_value('VendorBillingAddress'); ?></textarea>
							<span class="text-danger"><?php echo validation_show_error('VendorBillingAddress'); ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<?php $shipping_terms_and_conditions = (!empty($purchase_order_settings_details['ShippingTermsAndConditions']))?$purchase_order_settings_details['ShippingTermsAndConditions']:''; ?>
							<label for="ShippingTermsAndConditions">Shipping Terms & Conditions</label>
							<textarea name="ShippingTermsAndConditions" id="ShippingTermsAndConditions" cols="30" rows="5" class="form-control"><?php echo(!empty($_POST['ShippingTermsAndConditions']))?set_value('ShippingTermsAndConditions'):$shipping_terms_and_conditions; ?></textarea>
							<span class="text-danger"><?php echo validation_show_error('ShippingTermsAndConditions'); ?></span>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<?php $payment_terms = (!empty($purchase_order_settings_details['PaymentTerms']))?$purchase_order_settings_details['PaymentTerms']:''; ?>
							<label for="PaymentTerms">Payment Terms <span class="text-danger">*</span></label>
							<textarea name="PaymentTerms" id="PaymentTerms" cols="30" rows="5" class="form-control"><?php echo(!empty($_POST['PaymentTerms']))?set_value('PaymentTerms'):$payment_terms; ?></textarea>
							<span class="text-danger"><?php echo validation_show_error('PaymentTerms'); ?></span>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="ShippingAddress">Shipping Address <a href="<?php echo base_url('add-address'); ?>" target="_blank">Add Address</a> </label>
							<select name="ShippingAddress" id="ShippingAddress" class="form-control bs_multiselect">
								<option value="">Select Shipping Address</option>
								<?php for($i=0;$i<count($company_addresses);$i++){ ?>
									<option value="<?php echo $company_addresses[$i]['Address']; ?>" <?php echo($company_addresses[$i]['Address'] == set_value('ShippingAddress'))?'selected':''; ?> ><?php echo $company_addresses[$i]['Address']; ?></option>
								<?php } ?>
							</select>
							<span class="text-danger"><?php echo validation_show_error('ShippingAddress'); ?></span>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label for="PurchaseOrderStatusID">Purchase Order Status <span class="text-danger">*</span></label>
							<select name="PurchaseOrderStatusID" id="PurchaseOrderStatusID" class="form-control">
								<option value="">Select Status</option>
								<?php for($i=0;$i<count($purchase_order_status);$i++){ ?>
									<option value="<?php echo $purchase_order_status[$i]['PurchaseOrderStatusID']; ?>" <?php echo($purchase_order_status[$i]['PurchaseOrderStatusID'] == set_value('PurchaseOrderStatusID'))?'selected':''; ?> ><?php echo $purchase_order_status[$i]['PurchaseOrderStatus']; ?></option>
								<?php } ?>
							</select>
							<span class="text-danger"><?php echo validation_show_error('PurchaseOrderStatusID'); ?></span>
						</div>
					</div>
				</div>

			</div>
		</div>

		<div class="box box-success">
			<div class="box-header">
				<div class="row">
					<div class="col-md-4">
						<h3 class="box-title pull-left">Orders</h3>
					</div>
					<div class="col-md-4">&nbsp;</div>
					<div class="col-md-4">
						<input type="text" name="BarcodeNo" value="" id="po-BarcodeNo" class="form-control bg-yellow barcode-input" placeholder="Barcode No" autofocus>
						<p>Press enter, If you are entering the barcode manually</p>	
					</div>
				</div>
			</div>
			<div class="box-body">
				<div class="po-particular-container">
					<?php
						$total_bill = 0; 
						$bill_particulars_count = (!empty($_POST['Particular']))?count($_POST['Particular']):1; 
						for($i=0;$i<$bill_particulars_count;$i++){
							$random_particular_tax_key = (!empty($_POST['ParticularTaxKey']))?$_POST['ParticularTaxKey'][$i]:mt_rand(1897,2022);
							$qty = (!empty(set_value('Quantity['.$i.']')) && is_numeric(set_value('Quantity['.$i.']')) )?set_value('Quantity['.$i.']'):0;
							$price_per_unit = (!empty(set_value('PricePerUnit['.$i.']')) && is_numeric(set_value('PricePerUnit['.$i.']')) )?set_value('PricePerUnit['.$i.']'):0;

							$total_price = $qty * $price_per_unit;
							$total_bill += $total_price;
					?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="pull-left">Detail <?php echo $i+1; ?> </h4>
							<div class="pull-right">
								<button type="button" class="btn bg-olive add-po-particular pull-left">Add Particular</button>
								<div class="remove-po-particular-btn-container pull-left"></div>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-4">
									<input type="hidden" name="ParticularTaxKey[]" value="<?php echo $random_particular_tax_key; ?>" class="particular-tax-key">
									<div class="form-group">
										<label for="">Particular <span class="text-danger">*</span> <a href="javascript:void(0)" data-toggle="modal" data-target="#saveItemModal">Add Item</a> </label>
										
										<select name="Particular[]" id="particluar-<?php echo $random_particular_tax_key; ?>" class="form-control po-particular-selector bs_multiselect">
					        				<option value="">Select Item</option>
					        				<?php 
					        					for($j=0;$j<count($items);$j++){ 
					        				?>
					        					<option value="<?php echo $items[$j]['Item']; ?>" <?php echo($items[$j]['Item'] == set_value('Particular['.$i.']'))?'selected':''; ; ?> data-price="<?php echo $items[$j]['Price']; ?>" data-buying_price="<?php echo $items[$j]['BuyingPrice']; ?>" data-hsn="<?php echo $items[$j]['HSN']; ?>" data-taxes="<?php echo $items[$j]['taxes']; ?>" data-item_type="<?php echo $items[$j]['ItemType']; ?>" >
					        						<?php echo $items[$j]['Item']; ?> 
					        					</option>
					        				<?php } ?>
					        			</select>
										<span class="text-danger"><?php echo validation_show_error('Particular.'.$i); ?></span>
										<input type="hidden" name="ItemType[]" value="<?php echo set_value('ItemType['.$i.']'); ?>" class="item-type">
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
										<input type="text" name="Quantity[]" value="<?php echo set_value('Quantity['.$i.']'); ?>" min="1" id="qty-<?php echo $random_particular_tax_key; ?>" class="form-control qty" <?php echo(set_value(('ItemType['.$i.']')) != 'Good')?'readonly':''; ?> >
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
										<input type="text" name="Amount[]" readonly value="<?php echo set_value('Amount['.$i.']'); ?>" class="form-control amount total">
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
					<div class="col-md-6">&nbsp;</div>
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading text-center"><b>Total Billing</b></div>
							<div class="panel-body">
								<table class="table table-bordered text-center">
									<tr>
										<th>Bill Amount</th>
									</tr>
									<tr>
										<td class="total-bill-amt"><?php echo $total_bill; ?></td>
									</tr>
								</table>
							</div>
						</div>	
					</div>
					<!-- ./col -->
				</div>
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