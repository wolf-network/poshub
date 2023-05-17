<div class="row">
	<div class="col-md-12">
		<div class="box box-success">
			<div class="box-header">
				<h3 class="box-title pull-left">View Receipt Details</h3>
				<div class="pull-right">
					<a href="<?php echo base_url('manage-receipts/'.$receipt_data['InvoiceID']); ?>" class="btn btn-primary">Manage Receipts</a>
					<a href="<?php echo base_url('download-receipt/'.$receipt_id); ?>" download class="btn btn-warning">Download Receipt</a>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-12">
						<table class="table table-bordered table-striped">
							<tr>
								<th colspan="2" class="text-center">Payment Receipt</th>
							</tr>
							<tr>
								<th>Receipt No</th>
								<td><?php echo $receipt_data['ReceiptNo']; ?></td>
							</tr>
							<tr>
								<th>Invoice No</th>
								<td><?php echo $receipt_data['InvoiceNo']; ?></td>
							</tr>
							<tr>
								<th>Receipt Date</th>
								<td><?php echo $receipt_data['ReceiptDate']; ?></td>
							</tr>
							<tr>
								<th>Payment Mode</th>
								<td><?php echo $receipt_data['PaymentMode']; ?></td>
							</tr>
							<tr>
								<th>Amount Received</th>
								<td><?php echo $receipt_data['PaidAmount']; ?></td>
							</tr>
							<tr>
								<th>Amount Received in words</th>
								<td><?php echo ucwords(numberToWords($receipt_data['PaidAmount'])); ?> Only</td>
							</tr>
						</table>
					</div>
				</div>
				<?php if(!empty($receipt_data['ClientName'])){ ?>
				<div class="row">
					<div class="col-md-3 col-xs-6">
                        <p><b>Bill To:</b></p>
                        <p>
                            <?php echo $receipt_data['ClientName']; ?>,
                            <br>
                            <?php echo $receipt_data['ClientBillingAddress']; ?>
                        </p>
                    </div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>