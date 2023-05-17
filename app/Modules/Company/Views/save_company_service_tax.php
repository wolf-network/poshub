<div class="row">
	<div class="col-md-12">
        <div class="box box-success">
        	<?php echo form_open('',['id' => 'company-service-tax-form']); ?>
        	<div class="box-header">
                <h3 class="box-title"><?php echo ($company_service_tax_id == 0)?'Add':'Edit'; ?> Service Tax</h3>
                <div class="pull-right">
                	<a href="https://www.youtube.com/watch?v=X_gLahYe_S0" target="_blank" class="btn btn-info">Watch tutorial</a>
                	<a href="<?php echo base_url('manage-company-service-taxes'); ?>" class="btn btn-success">Manage Company Service Taxes</a>
                </div>
            </div>
            <div class="box-body">
            	<div class="row">
            		<div class="col-md-4">
            			<div class="form-group">
            				<label for="ServiceTaxTypeID">Service Tax Type <span class="text-danger">*</span></label>
							<select name="ServiceTaxTypeID" id="ServiceTaxTypeID" class="form-control">
                    			<option value="">Select Tax Type</option>
		                        <?php for($i=0;$i<count($service_tax_types);$i++){ ?>
		                          <option value="<?php echo $service_tax_types[$i]['ServiceTaxTypeID']; ?>" <?php  echo($service_tax_types[$i]['ServiceTaxTypeID'] == set_value('ServiceTaxTypeID'))?'selected':''; ?>>
		                              <?php echo $service_tax_types[$i]['ServiceTaxType']; ?>
	                          	</option>
		                        <?php } ?>
	                      	</select>
                  			<span class="text-danger"><?php echo validation_show_error('ServiceTaxTypeID'); ?></span>
            			</div>
            		</div>
            		<div class="col-md-4">
            			<div class="form-group">
            				<label for="ServiceTaxIdentificationNumber">Service Tax Number <span class="text-danger">*</span></label>
            				<input type="text" name="ServiceTaxIdentificationNumber" value="<?php echo set_value('ServiceTaxIdentificationNumber'); ?>" class="form-control">
            				<span class="text-danger"><?php echo validation_show_error('ServiceTaxIdentificationNumber'); ?></span>
            			</div>
            		</div>
            		<div class="col-md-4">
            			<div class="form-group">
            				<label for="BillingCountryID">Select country <span class="text-danger">*</span></label>
	                              	<select name="BillingCountryID" id="BillingCountryID" required class="form-control bs_multiselect country">
		                                <option value="">Select country</option>
		                                <?php for($i=0;$i<count($countries);$i++){ ?>
		                                <option value="<?php echo $countries[$i]['CountryID']; ?>" <?php echo($countries[$i]['CountryID'] == set_value('BillingCountryID'))?'selected':''; ?> ><?php echo $countries[$i]['CountryName']; ?></option>
		                                <?php } ?>
	                              	</select>
	                              	<span class="text-danger"><?php echo validation_show_error('BillingCountryID'); ?></span>
                          	</div>
                      	</div>
            	</div>
            	<div class="row">
            		<div class="col-md-4">
            			<div class="form-group">
            				<label for="BillingStateID">Select state <span class="text-danger">*</span></label>
	                              	<select name="BillingStateID" id="BillingStateID" required class="form-control bs_multiselect state" data-selected_state="<?php echo set_value('BillingStateID'); ?>">
	                              	</select>
	                              	<span class="text-danger"><?php echo validation_show_error('BillingStateID'); ?></span>
                          	</div>
            		</div>
            		<div class="col-md-4">
            			<label for="RegisteredAddress">Billing Address <span class="text-danger">*</span> </label>
            			<textarea name="RegisteredAddress" id="RegisteredAddress" cols="30" rows="5" class="form-control"><?php echo set_value('RegisteredAddress'); ?></textarea>
            			<span class="text-danger"><?php echo validation_show_error('RegisteredAddress'); ?></span>
            		</div>
            	</div>
            </div>
            <div class="box-footer"><button type="submit" class="btn btn-success pull-right">Save</button></div>
        	<?php echo form_close(); ?>
        </div>
    </div>
</div>