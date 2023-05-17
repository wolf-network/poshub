<div class="row">
	<div class="col-md-12">
		<div class="box box-success">
			<?php echo form_open('',['id' => 'save-company-address']); ?>
			<div class="box-header with-border">
				<h3 class="box-title pull-left">Add Address</h3>
				<a href="<?php echo base_url('manage-addresses'); ?>" class="btn btn-info pull-right">
					Manage company Address
				</a>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="CountryID">Select Country <span class="text-danger">*</span></label>
							<select name="CountryID" id="CountryID" class="form-control country bs_multiselect">
								<option value="">Select Country</option>
								<?php for($i=0;$i<count($countries);$i++){ ?>
									<option value="<?php echo $countries[$i]['CountryID']; ?>" <?php echo(set_value('CountryID') == $countries[$i]['CountryID'])?'selected':''; ?> >
										<?php echo $countries[$i]['CountryName']; ?>
									</option>
								<?php } ?>
							</select>
							<span class="text-danger"><?php echo validation_show_error('CountryID'); ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="StateID">Select State <span class="text-danger">*</span></label>
							<select name="StateID" id="StateID" class="form-control state bs_multiselect" data-selected_state="<?php echo set_value('StateID'); ?>">
								<option value="">Select State</option>
							</select>
							<span class="text-danger"><?php echo validation_show_error('StateID'); ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="CityID">Select City <span class="text-danger">*</span></label>
							<select name="CityID" id="CityID" class="form-control city bs_multiselect" data-selected_city="<?php echo set_value('CityID'); ?>">
								<option value="">Select City</option>
							</select>
							<span class="text-danger"><?php echo validation_show_error('CityID'); ?></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="OfficeType">Office Type</label>
							<select name="OfficeType" id="OfficeType" class="form-control">
								<?php for($i=0;$i<count($office_types);$i++){ ?>
								<option value="<?php echo $office_types[$i]; ?>" <?php echo(set_value('OfficeType') == $office_types[$i])?'selected':''; ?> ><?php echo $office_types[$i]; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="Address">Address <span class="text-danger">*</span></label>
							<textarea name="Address" id="Address" class="form-control" cols="30" rows="5"><?php echo set_value('Address'); ?></textarea>
							<span class="text-danger"><?php echo validation_show_error('Address'); ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="GoogleMap">Google Map</label>
							<input type="text" name="GoogleMap" value="<?php echo set_value('GoogleMap'); ?>" id="GoogleMap" class="form-control">
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				<button type="submit" class="btn btn-success pull-right">Save Address</button>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>