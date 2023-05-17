<div class="row">
	<div class="col-md-12">
		<div class="box box-success">
			<?php echo form_open('',['id' => 'tax-identification-form']); ?>
			<div class="box-header with-border">
				<h3 class="box-title pull-left"><?php echo(empty($company_service_tax_master_id))?'Add':'Edit'; ?> GST/VAT</h3>
				<a href="<?php echo base_url('manage-tax-identifications'); ?>" class="btn btn-success pull-right">Manage GST/VAT</a>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="ServiceTaxTypeID">Tax Type <span class="text-danger">*</span> </label>
							<select name="ServiceTaxTypeID" id="ServiceTaxTypeID" class="form-control">
								<option value="">Select Tax Type</option>
								<?php for($i=0;$i<count($tax_types);$i++){ ?>
									<option value="<?php echo $tax_types[$i]['ServiceTaxTypeID']; ?>" <?php echo($tax_types[$i]['ServiceTaxTypeID'] == set_value('ServiceTaxTypeID'))?'selected':''; ?>><?php echo $tax_types[$i]['ServiceTaxType']; ?></option>
								<?php } ?>
							</select>
							<span class="text-danger"><?php echo form_error('ServiceTaxTypeID'); ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="TaxIdentificationNumber">Tax Identification Number <span class="text-danger">*</span></label>
							<input type="text" name="TaxIdentificationNumber" value="<?php echo set_value('TaxIdentificationNumber'); ?>" id="TaxIdentificationNumber" class="form-control">
							<span class="text-danger"><?php echo form_error('TaxIdentificationNumber'); ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="RegisteredAddress">Registered Address <span class="text-danger">*</span></label>
							<textarea name="RegisteredAddress" id="RegisteredAddress" cols="30" rows="5" class="form-control"><?php echo set_value('RegisteredAddress'); ?></textarea>
							<span class="text-danger"><?php echo form_error('RegisteredAddress'); ?></span>
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