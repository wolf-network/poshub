<?php echo form_open('',['id' => 'credit-note-form']); ?>
<div class="row">
	<div class="col-md-12">
		<?php if(!empty(validation_errors())){ ?>
		<div class="alert alert-danger">
			Kindly fix the following errors and re-submit the form:
			<?php echo validation_list_errors(); ?>
		</div>
		<?php } ?>
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title pull-left">Create Credit Note</h3>
				<div class="pull-right">
					<a href="https://www.youtube.com/watch?v=iTKIGeIRX88" target="_blank" class="btn btn-info">Watch tutorial</a>
					<a href="<?php echo base_url('manage-credit-notes'); ?>" class="btn btn-primary">Manage Credit Notes</a>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					<input type="hidden" name="allClientsOffset" id="allClientsOffset" value="30">
					<div class="col-md-4">
						<div class="form-group">
							<label for="ClientID">Select Client <span class="text-danger">*</span>
								<a href="<?php echo base_url('add-client'); ?>" target="_blank">Add Client</a>
							</label>
							<div class="clients-container">
								<select name="ClientID" id="ClientID" class="form-control bs_multiselect" data-client_name="<?php echo set_value('ClientID'); ?>" data-invoice_id="<?php echo set_value('InvoiceID'); ?>">
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
							<label for="CreditNoteNo">Credit Note No. <span class="text-info">(Leave blank to auto generate)</span></label>
							<input type="text" name="CreditNoteNo" value="<?php echo set_value('CreditNoteNo'); ?>" id="CreditNoteNo" class="form-control">
							<span class="text-danger"><?php echo validation_show_error('CreditNoteNo'); ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="CreditNoteDate">Credit Note Date <span class="text-danger">*</span></label>
							<input type="text" name="CreditNoteDate" value="<?php echo set_value('CreditNoteDate'); ?>" class="form-control daterangepicker" data-max-date="<?php echo date('Y-m-d'); ?>">
							<span class="text-danger"><?php echo validation_show_error('CreditNoteDate'); ?></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="InvoiceID">Select Invoice No. <span class="text-danger">*</span> </label>
							<select name="InvoiceID" id="InvoiceID" class="form-control bs_multiselect">
								<option value="">Select Invoice No.</option>
							</select>
							<span class="text-danger"><?php echo validation_show_error('InvoiceID'); ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="ClientServiceTaxID">Client GST No./VAT No./Other </label>
							<input type="text" name="ClientServiceTaxID" value="" disabled id="ClientServiceTaxID" class="form-control">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="PaymentStatus">Payment Status <span class="text-danger">*</span></label>
							<select name="PaymentStatus" id="PaymentStatus" class="form-control">
								<option value="">Select Payment Status</option>
								<option value="Paid" <?php echo(set_value('PaymentStatus') == 'Paid')?'selected':''; ?>>Paid</option>
								<option value="Unpaid" <?php echo(set_value('PaymentStatus') == 'Unpaid')?'selected':''; ?>>Unpaid</option>
							</select>
							<span class="text-danger"><?php echo validation_show_error('PaymentStatus'); ?></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="BillingAddress">Billing Address</label>
							<textarea name="BillingAddress" id="BillingAddress" cols="30" rows="5" class="form-control" disabled></textarea>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="ShippingAddress">Shipping Address</label>
							<textarea name="ShippingAddress" id="ShippingAddress" cols="30" rows="5" class="form-control" disabled></textarea>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="Reason">Reason</label>
							<textarea name="Reason" id="Reason" cols="30" rows="5" class="form-control"><?php echo set_value('Reason'); ?></textarea>
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
			<div class="box-header">
				<div class="col-md-8 col-sm-6">
					<h3 class="box-title pull-left">Returns</h3>
				</div>
				<div class="col-md-4 col-sm-6">
					<input type="text" name="BarcodeNo" value="" id="BarcodeNo" class="form-control bg-yellow barcode-input" placeholder="Barcode No" autofocus>
					<p>Press enter, If you are entering the barcode manually</p>
				</div>
			</div>
			<div class="box-body">
				<div class="particular-return-container">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="pull-left">Detail <span class="detail-count">1</span></h4>
							<div class="pull-right">
								<button type="button" class="btn btn-success pull-left add-particular-return">Add Particular</button>
								<div class="remove-particular-return-btn-container pull-left">
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="">Particular <span class="text-danger">*</span></label>
										<div class="particular-selector-container">
											<select name="Particular[]" id="qty-<?php echo mt_rand('1111','9999'); ?>" class="form-control bs_multiselect particular-return-selector"><option value="">Select Item</option></select>
										</div>
										<input type="hidden" name="ParticularType[]" class="ParticularType">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="">HSN/SAC</label>
										<input type="text" name="" value="" class="form-control hsn-code" disabled>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="">Quantity</label>
										<input type="text" name="Quantity[]" value="" class="form-control qty">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="">Price Per Unit <span class="text-danger">*</span></label>
										<input type="text" name="PricePerUnit[]" value="" id="ppu-<?php echo mt_rand('1111','9999'); ?>" class="form-control price-per-unit">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="">Amount</label>
										<input type="text" name="" value="" class="form-control amount" disabled>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-4">
									<table class="table table-bordered table-striped text-center tax-table">
										<tr>
											<th colspan="3">Tax (Detail <span class="detail-count">1</span>)</th>
										</tr>
										<tr>
											<th>Tax Name</th>
											<th>Tax Rate (%)</th>
											<th>Tax Value</th>
										</tr>
									</table>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">Taxable Amount</span>
											<input type="text" name="Total" value="" readonly="" id="Total" class="form-control total">
									  	</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				<button class="btn btn-success pull-right">Save</button>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>