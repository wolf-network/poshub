<div class="row">
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title">Edit Banking Details</h3>
				<div class="pull-right">
					<a href="https://www.youtube.com/watch?v=VidboF-1ZlE" target="_blank" class="btn btn-info">Watch tutorial</a>
				</div>
			</div>
			<?php echo form_open_multipart('',['id' => 'company_bank_details_form']); ?>
			<div class="box-body">
				<div class="row">
                  	<div class="col-md-4">
                      	<div class="form-group">
                          	<label for="BankID">Bank Name <span class="text-danger">*</span></label>
                          	<select name="BankID" id="BankID" class="bank_id form-control bs_multiselect">
                              	<option value="">Select Bank Name</option>
	                              <?php for($i=0;$i<count($bank_details);$i++){ ?>
                              	<option value="<?php echo $bank_details[$i]['BankID']; ?>" <?php echo($bank_details[$i]['BankID'] == set_value('BankID'))?'selected':''; ?> >
	                                  <?php echo $bank_details[$i]['BankName']; ?>
                              	</option>
                              	<?php } ?>
                          	</select>
                          	<span class="text-danger"><?php echo validation_show_error('BankID'); ?></span>
                      	</div>
                  	</div>
                  	<div class="col-md-4">
                      	<div class="form-group bank_details_container">
                          	<label for="BankDetailsID">IFSC Code <span class="text-danger">*</span></label>
	                          
                              	<select name="BankDetailsID" id="BankDetailsID" class="bank_details form-control bs_multiselect" data-selected_bank_details_id="<?php echo set_value('BankDetailsID'); ?>" data-offset="0">
                                  	<option value="">Select IFSC Code</option>
                              	</select>
	                          
	                          	<span class="text-danger"><?php echo validation_show_error('BankDetailsID'); ?></span>
                      	</div>
                  	</div>
                  	<div class="col-md-4">
                      	<div class="form-group">
                      		<label for="AccountHolderName">Account Holder Name <span class="text-danger">*</span></label>
                          	<input type="text" name="AccountHolderName" value="<?php echo set_value('AccountHolderName'); ?>" id="AccountHolderName" class="form-control">
                          	<span class="text-danger"><?php echo validation_show_error('AccountHolderName'); ?></span>
                      	</div>
                  	</div>
	              </div>
	              <div class="row">
                  	<div class="col-md-4">
                      	<div class="form-group">
                          	<label for="AccountNo">Account No <span class="text-danger">*</span></label>
                          	<input type="password" name="AccountNo" value="<?php echo set_value('AccountNo'); ?>" id="AccountNo" class="form-control">
                          	<span class="text-danger"><?php echo validation_show_error('AccountNo'); ?></span>
                      	</div>
                  	</div>
                  	<div class="col-md-4">
                      	<div class="form-group">
                          	<label for="ConfirmAccountNo">Confirm Account No <span class="text-danger">*</span></label>
                          	<input type="text" name="ConfirmAccountNo" value="<?php echo set_value('ConfirmAccountNo'); ?>" id="ConfirmAccountNo" class="form-control">
                          	<span class="text-danger"><?php echo validation_show_error('ConfirmAccountNo'); ?></span>
                      	</div>
                  	</div>
                  	<div class="col-md-4">
                      	<div class="form-group">
                          	<label for="QRCode">QR Code [jpg|png|jpeg|jfif] [max file size: 2MB] </label>
                          	<br>
                          	<input type="file" id="QRCode" name="QRCode">
                          	<span class="text-danger"><?php echo validation_show_error('QRCode'); ?></span>
                          	<br>
                          	<?php if(!empty(set_value('QRCode'))) { ?>
                          	<img src="<?php echo media_server(set_value('QRCode')); ?>" alt="" class="img-responsive">
                          	
                          	<span class="pull-left">&nbsp;</span>
                          	<?php } ?>
                      	</div>
                  	</div>
	              </div>
			</div>
			<?php
                if($subscription_time_left['years'] >= 0 && $subscription_time_left['months'] >= 0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0 && $user_data['Privilege'] == 'Admin')
            	{ 
            ?>
			<div class="box-footer">
				<button type="submit" class="btn btn-success pull-right">Update Banking Details</button>
			</div>
			<?php } ?>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>