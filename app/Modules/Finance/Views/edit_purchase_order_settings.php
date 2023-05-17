<div class="row">
	<div class="col-md-12">
		<?php echo form_open('',['id' => 'po-setting-form']); ?>
		<div class="box box-success">
			<div class="box-header">
				<h3 class="box-title">Edit PO Settings</h3>
				<div class="pull-right">
					<a href="<?php echo base_url('manage-purchase-orders'); ?>" class="btn btn-success">Manage PO</a>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="ShippingTermsAndConditions">Shipping Terms & Conditions</label>
							<textarea name="ShippingTermsAndConditions" id="ShippingTermsAndConditions" cols="30" rows="5" class="form-control"><?php echo set_value('ShippingTermsAndConditions'); ?></textarea>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="PaymentTerms">Payment Terms <span class="text-danger">*</span></label>
							<textarea name="PaymentTerms" id="PaymentTerms" cols="30" rows="5" class="form-control"><?php echo set_value('PaymentTerms'); ?></textarea>
							<span class="text-danger"><?php echo validation_show_error('PaymentTerms'); ?></span>
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				<button type="submit" class="btn btn-success pull-right">Save</button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>