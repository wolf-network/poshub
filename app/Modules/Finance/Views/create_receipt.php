<div class="row">
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header">
				<h3 class="box-title pull-left">Invoice Details</h3>
				<div class="pull-right">
					<a href="https://www.youtube.com/watch?v=TBsrcrizovg" target="_blank" class="btn btn-info">Watch tutorial</a>
					<a href="<?php echo base_url('manage-receipts/'.$invoice_id); ?>" class="btn btn-primary">Manage Receipts</a>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-3 col-xs-6">
                        <p><b>Bill To:</b></p>
                        <p>
                            <?php echo $invoice_data['ClientName']; ?>,
                            <br>
                            <?php echo $invoice_data['ClientBillingAddress']; ?>
                        </p>
                    </div>
                    <div class="col-md-4 col-xs-6">
                        <table class="table table-bordered table-striped">
                        	<tr>
                        		<th>Total Amount</th>
                        		<td><?php echo $invoice_data['TotalPayableAmount']; ?></td>
                        	</tr>
                        	<tr>
                        		<th>Total Paid Amount</th>
                        		<td><?php echo $invoice_receipt_data['TotalPaidAmount']; ?></td>
                        	</tr>
                        	<tr>
                        		<?php $total_payable_amount = $invoice_data['TotalPayableAmount'] - $invoice_receipt_data['TotalPaidAmount']; ?>
                        		<th>Total Payable Amount</th>
                        		<td><?php echo $total_payable_amount; ?></td>
                        	</tr>
                        </table>
                    </div>
				</div>
			</div>
		</div>
		<?php if($total_payable_amount > 0){ ?>
		<div class="box box-success">
			<?php echo form_open(); ?>
			<div class="box-header with-border">
				<h3 class="box-title pull-left">Create Receipt</h3>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-4 col-xs-12">
						<div class="form-group">
							<label for="PaidAmount">Amount Received <span class="text-danger">*</span> </label>
							<input type="text" name="PaidAmount" value="<?php echo set_value('PaidAmount'); ?>" class="form-control">
							<span class="text-danger"><?php echo validation_show_error('PaidAmount'); ?></span>
						</div>
					</div>
					<div class="col-md-4 col-xs-12">
						<div class="form-group">
							<label for="ReceiptDate">Payment Date <span class="text-danger">*</span></label>
							<input type="text" name="ReceiptDate" value="<?php echo set_value('ReceiptDate'); ?>" class="form-control daterangepicker">
							<span class="text-danger"><?php echo validation_show_error('ReceiptDate'); ?></span>
						</div>
					</div>
					<div class="col-md-4 col-xs-12">
						<div class="form-group">
							<label for="PaymentModeID">Payment Mode <span class="text-danger">*</span></label>
							<select name="PaymentModeID" id="PaymentModeID" class="form-control">
								<option value="">Select Payment Mode</option>
								<?php for($i=0;$i<count($payment_modes);$i++){ ?>
									<option value="<?php echo $payment_modes[$i]['PaymentModeID']; ?>" <?php echo($payment_modes[$i]['PaymentModeID'] == set_value('PaymentModeID'))?'selected':''; ?> ><?php echo $payment_modes[$i]['PaymentMode']; ?></option>
								<?php } ?>
							</select>
							<span class="text-danger"><?php echo validation_show_error('PaymentModeID'); ?></span>
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				<button type="submit" class="btn btn-success pull-right">Save Receipt</button>
			</div>
			<?php echo form_close(); ?>
		</div>
		<?php } ?>
	</div>
</div>