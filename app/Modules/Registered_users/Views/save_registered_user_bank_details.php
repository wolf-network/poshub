<div class="row">
	<div class="col-md-5 col-md-offset-3">
		<?php echo form_open(); ?>
	 	<div class="box box-success">
			<div class="box-header">
				<h3 class="box-title">Your Bank Account Details</h3>
			</div>
			<div class="box-body">
				<div class="form-group">
					<label for="AccountNumber">Account Number <span class="text-danger">*</span></label>
					<input type="text" name="AccountNumber" value="<?php echo set_value('AccountNumber') ?>" class="form-control" id="AccountNumber">
					<span class="text-danger"><?php echo validation_show_error('AccountNumber'); ?></span>
				</div>
				<div class="form-group">
					<label for="BankID">Bank Name <span class="text-danger">*</span></label>
					<select name="BankID" id="BankID" class="form-control bank_id bs_multiselect">
						<option value="">Select Bank Name</option>
                          <?php for($i=0;$i<count($bank_details);$i++){ ?>
                        <option value="<?php echo $bank_details[$i]['BankID']; ?>" <?php echo($bank_details[$i]['BankID'] == set_value('BankID'))?'selected':''; ?> ><?php echo $bank_details[$i]['BankName']; ?></option><?php } ?>
					</select>
					<span class="text-danger"><?php echo validation_show_error('BankID'); ?></span>
				</div>
				<div class="form-group bank_details_container">
					<label for="BankDetailsID">IFSC Code <span class="text-danger">*</span></label>
					<select name="BankDetailsID" id="BankDetailsID" class="bank_details form-control bs_multiselect" data-selected_bank_details_id="<?php echo set_value('BankDetailsID'); ?>" data-offset="0">
						<option value="">Select IFSC Code</option>
					</select>
					<span class="text-danger"><?php echo validation_show_error('BankDetailsID'); ?></span>
				</div>
				<div class="form-group">
					<label for="AccountType">Account Type <span class="text-danger">*</span></label>
					<select name="AccountType" id="AccountType" class="form-control" data-offset="0">
						<option value="">Select Account Type</option>
						<option value="Savings" <?php echo(set_value('AccountType') == 'Savings')?'selected':''; ?> >Savings</option>
						<option value="Current" <?php echo(set_value('AccountType') == 'Current')?'selected':''; ?> >Current</option>
					</select>
					<span class="text-danger"><?php echo validation_show_error('AccountType'); ?></span>
				</div>
				<div class="form-group">
					<label for="AccountHoldersName">Account Holder's Name <span class="text-danger">*</span> </label>
					<input type="text" name="AccountHoldersName" value="<?php echo set_value('AccountHoldersName') ?>" class="form-control" id="AccountHoldersName">
					<span class="text-danger"><?php echo validation_show_error('AccountHoldersName'); ?></span>
				</div>
			</div>
			<div class="box-footer">
				<button type="submit" class="btn btn-success btn-block">Save</button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>