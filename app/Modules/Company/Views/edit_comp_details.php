<div class="row">
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title">Edit Company Details</h3>
				<div class="pull-right">
					<a href="https://www.youtube.com/watch?v=VidboF-1ZlE" target="_blank" class="btn btn-info">Watch tutorial</a>
				</div>
				<div class="clearfix"></div><br>
				<div class="row">
					<div class="col-md-3">
						<a href="<?php echo base_url('edit-company-bank-details'); ?>" class="btn bg-purple btn-block">
							Edit banking details
						</a>	
					</div>
					<div class="col-md-3">
						<a href="<?php echo base_url('manage-company-documents'); ?>" class="btn bg-navy btn-block">
							Manage company documents
						</a>
					</div>
					<div class="col-md-3">
						<a href="<?php echo base_url('manage-company-service-taxes'); ?>" class="btn bg-maroon btn-block">
							Manage service taxes
						</a>
					</div>
					<div class="col-md-3">
						<a href="<?php echo base_url('manage-addresses'); ?>" class="btn btn-info btn-block">
							Manage company address
						</a>
					</div>
				</div>
			</div>
			<?php echo form_open_multipart('',['id' => 'edit-comp-form']); ?>
			<div class="box-body">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Company Logo [max 2mb] [jpg | jpeg | png | jfif]</label><br>
							<input type="hidden" name="comp_logo_hidden" value="<?php echo set_value('CompLogoPath'); ?>">
							<input type="file" name="CompLogoPath" class="img_replace" data-img_prev_selector="#prev_logo" id="CompLogoPath" style="display: none;">
							<label for="CompLogoPath">
								<img id="prev_logo" src="<?php echo media_server(set_value('CompLogoPath')); ?>" alt="" height="100" width="100" onerror="this.onerror=null;this.src='<?php echo base_url('assets/img/upload-photo.png'); ?>';" class="pointer">
							</label>
							<span class="text-danger"><?php echo validation_show_error('CompLogoPath') ?></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="CompName">Company Name <span class="text-danger">*</span> </label>
							<div class="input-group">
			                 <span class="input-group-addon"><i class="fa fa-briefcase"></i></span>
			                 <input type="text" name="CompName" value="<?php echo set_value('CompName'); ?>" class="form-control" placeholder="Enter Company Name" id="CompName">
				            </div>
				            <span class="text-red"><?php echo validation_show_error('CompName') ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<label for="EmailID">Email <span class="text-danger">*</span></label>
						<div class="input-group">
		                 <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
		                 <input class="form-control" placeholder="EmailID" type="EmailID" name="EmailID" id="EmailID" value="<?php echo set_value('EmailID') ?>">
			            </div>
			            <span class="text-red"><?php echo validation_show_error('EmailID') ?></span>
					</div>
					<div class="col-md-4">
						<label for="ContactNo">Contact No <span class="text-danger">*</span></label>
						<div class="input-group">
		                 <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
		                 <input class="form-control" placeholder="Contact No" type="text" name="ContactNo" value="<?php echo set_value('ContactNo'); ?>">
			            </div>
			            <span class="text-red"><?php echo validation_show_error('ContactNo') ?></span>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="FirmTypeID">Firm Type <span class="text-danger">*</span></label>
							<select name="FirmTypeID" id="FirmTypeID" class="form-control">
								<option value="">Select Firm Type</option>
								<?php for($i=0;$i<count($firm_types); $i++){ ?>
									<option value="<?php echo $firm_types[$i]['FirmTypeID']; ?>" <?php echo ($firm_types[$i]['FirmTypeID'] == set_value('FirmTypeID'))?'selected':''; ?> >
										<?php echo $firm_types[$i]['FirmType']; ?>
									</option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="TaxIdentificationTypeID">Tax Identification Type</label>
		                    <select name="TaxIdentificationTypeID" id="TaxIdentificationTypeID" class="form-control">
		                      <option value="">Select Tax Identification Type</option>
		                      <?php for($i=0;$i<count($tax_identification_types);$i++){ ?>
		                        <option value="<?php echo $tax_identification_types[$i]['TaxIdentificationTypeID']; ?>" <?php echo($tax_identification_types[$i]['TaxIdentificationTypeID'] == set_value('TaxIdentificationTypeID'))?'selected':''; ?> ><?php echo $tax_identification_types[$i]['TaxIdentificationType']; ?></option>
		                      <?php } ?>
		                    </select>
		                    <span class="text-danger"><?php echo validation_show_error('TaxIdentificationTypeID'); ?></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
		                    <label for="TaxIdentificationNumber">Tax Identification Number</label>
		                    <input type="text" name="TaxIdentificationNumber" value="<?php echo set_value('TaxIdentificationNumber'); ?>" placeholder="" id="TaxIdentificationNumber" class="form-control">
		                    <span class="text-danger"><?php echo validation_show_error('TaxIdentificationNumber'); ?></span>
	                  	</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
		                    <label for="CIN">Company Identification Number (CIN/EIN, etc)</label>
		                    <input type="text" name="CIN" value="<?php echo set_value('CIN'); ?>" placeholder="" id="CIN" class="form-control">
		                    <span class="text-danger"><?php echo validation_show_error('CIN'); ?></span>
	                  	</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label>Upload Signature [max 2mb] [png] </label> <br>
							<label for="SignatureImgPath">
								<?php $img = (set_value('SignatureImgPath'))?media_server(set_value('SignatureImgPath')):base_url('assets/img/upload-photo.png'); ?>
								<img src="<?php echo $img; ?>" id="signature-img" width="100" height="100" class="pointer">
							</label>
							<input type="file" name="SignatureImgPath" id="SignatureImgPath" class="hide img_replace" data-img_prev_selector="#signature-img">
							<span class="text-danger"><?php echo validation_show_error('SignatureImgPath'); ?></span>
						</div>
					</div>
				</div>
			</div>
			<?php
                if($subscription_time_left['years'] >= 0 && $subscription_time_left['months'] >= 0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0 && $user_data['Privilege'] == 'Admin')
            	{ 
            ?>
			<div class="box-footer">
				<button type="submit" class="btn btn-success pull-right">Update Company Details</button>
			</div>
			<?php } ?>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>