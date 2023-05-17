<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Stock Expiry Reminder</title>
		<style>
			body {
				font-family: Arial, sans-serif;
			}
			table {
				border-collapse: collapse;
				width: 100%;
			}
			th, td {
				padding: 8px;
				text-align: left;
				border-bottom: 1px solid #ddd;
			}
			th {
				background-color: #f2f2f2;
			}
			.container {
				padding: 20px;
				border-radius: 8px;
				box-shadow: 0 0 10px rgba(0,0,0,0.1);
				background-color: #fff;
			}
			.logo {
				display: block;
				margin: 0 auto;
				max-width: 150px;
			}
			.footer {
				padding: 20px;
				font-size: 12px;
				text-align: center;
				background-color: #f0f0f0;
				border-bottom-left-radius: 8px;
				border-bottom-right-radius: 8px;
			}
			.footer p {
				margin: 8px 0;
			}
		</style>
	</head>
	<body>
		<table>
			<tr>
				<td>
					<div class="container">
						<img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="<?php echo COMPANY_NAME; ?> Logo" class="logo">
						<p>Dear <?php echo $comp_name; ?>,</p>
						<p>We hope this email finds you well. We are writing to bring to your attention an urgent matter regarding your inventory management.</p>
						<p>As you are aware, you have a stock of products in your inventory, and we have noticed that some of them are about to expire in the coming days and weeks. We understand that this can result in significant losses for your business and that immediate action is necessary.</p>
						<p>To help you identify the products that are at risk of expiration, we have included a table below with the relevant information:</p>
						<table>
							<thead>
								<tr>
									<th>Item Name</th>
									<th>Returnable Qty</th>
									<th>HSN</th>
									<th>Vendor</th>
									<th>Batch No.</th>
									<th>Returnable Qty</th>
									<th>Manufacturing Date</th>
									<th>Expiry Date</th>
								</tr>
							</thead>
							<tbody>
								<?php for ($i=0; $i <count($stock_details) ; $i++) { ?>
								<tr>
									<td><?php echo $stock_details[$i]['Item']; ?></td>
									<td><?php echo $stock_details[$i]['RemainingQty']; ?></td>
									<td><?php echo $stock_details[$i]['HSN']; ?></td>
									<td><?php echo $stock_details[$i]['VendorName']; ?></td>
									<td><?php echo $stock_details[$i]['BatchNo']; ?></td>
									<td><?php echo $stock_details[$i]['RemainingQty']; ?></td>
									<td><?php echo $stock_details[$i]['ManufacturingDate']; ?></td>
									<td><?php echo $stock_details[$i]['ExpiryDate']; ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<p>We strongly recommend that you take immediate action to address this issue. You may want to consider offering discounts or special promotions to encourage sales, donating the products to a local charity or non-profit organization, or returning the products to your vendor.</p>
						<p>If you choose to return the products, we suggest reaching out to your vendor as soon as possible to arrange for the return. This can help you avoid any unnecessary losses and ensure that your inventory is up-to-date.</p>
						<p>Thank you for using <?php echo APP_NAME; ?> for your inventory management needs.</p>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="footer">
						<p>This email was sent to <?php echo $comp_email; ?> as a reminder of your stock expiry date. If you have received this email in error, please contact us at <?php echo COMPANY_EMAIL; ?>.</p>
						<p>You are receiving this email because you have subscribed to our inventory management service.</p>
						<p><?php echo COMPANY_NAME; ?> | <?php echo COMP_ADDRESS ?> | <?php echo COMPANY_CONTACT_NO; ?> | <?php echo COMPANY_EMAIL; ?></p>
					</div>
				</td>
			</tr>
		</table>
	</body>
</html>