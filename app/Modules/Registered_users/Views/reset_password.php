<div class="row">
	<div class="col-md-4 col-md-offset-4 col-xs-12">
		<div class="box box-success">
			<?php echo form_open_multipart('',['id' => 'reset_password_form']); ?>
			<div class="box-header">
                <h3 class="box-title">Reset Password</h3>
            </div>
            <div class="box-body">
            	<div class="row">
            		<div class="col-md-12">
            			<div class="form-group">
            				<label for="CurrentPassword">Current Password <span class="text-danger">*</span> </label>
            				<input type="password" name="CurrentPassword" id="CurrentPassword" class="form-control">
            				<span class="text-danger"><?php echo validation_show_error('CurrentPassword'); ?></span>
            			</div>

            			<div class="form-group">
            				<label for="Password">New Password <span class="text-danger">*</span> </label>
            				<input type="password" name="Password" id="Password" class="form-control">
            				<span class="text-danger"><?php echo validation_show_error('Password'); ?></span>
            			</div>

            			<div class="form-group">
            				<label for="ConfirmPassword">Confirm Password <span class="text-danger">*</span> </label>
            				<input type="password" name="ConfirmPassword" id="ConfirmPassword" class="form-control">
            				<span class="text-danger"><?php echo validation_show_error('ConfirmPassword'); ?></span>
            			</div>
            		</div>
            	</div>
            </div>
            <div class="box-footer">
            	<button class="btn btn-danger">Change Password</button>
            </div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>