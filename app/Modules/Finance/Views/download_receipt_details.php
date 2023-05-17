<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<style>
		th{
			font-weight: bold;
		}
	</style>
</head>
<body>
	<h3><?php echo $invoice_data['CompanyName'].' '.$invoice_data['FirmType']; ?>,</h3>
	<table border="1" cellspacing="1" cellpadding="5">
		<tr>
			<th colspan="2" style="text-align:center;"><b>Payment Receipt</b></th>
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
	<p>&nbsp;</p>

	<table border="0" cellpadding="1" cellspacing="1" width="300">
		<tr>
			<th align="left">Bill To:</th>
		</tr>
		<tr>
			<td>
				<?php echo $receipt_data['ClientName']; ?>,
				<?php echo $receipt_data['ClientBillingAddress']; ?>
			</td>
		</tr>
	</table>
</body>
</html>