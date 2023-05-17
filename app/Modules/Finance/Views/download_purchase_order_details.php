<html>
	<head></head>
	<body>
		<table border="0" cellpadding="1" cellspacing="0" style="width: 100%;">
			<tr>
				<th align="left"><b><?php echo $purchase_order_data['CompanyName'].' '.$purchase_order_data['CompFirmType']; ?></b></th>
				<th align="right"><b>Purchase Order</b></th>
			</tr>
			<tr>
				<td>
					<p><?php echo $purchase_order_data['CompanyAddress']; ?></p>
					<p><?php echo $purchase_order_data['CompanyContactNumber']; ?></p>
				</td>
				<td style="text-align: right;">
					<b>PO No</b>: <?php echo $purchase_order_data['PurchaseOrderNo']; ?>
					<?php if(!empty($purchase_order_data['CompanyServiceTaxType'])){ ?>
						<br>
                    <b><?php echo $purchase_order_data['CompanyServiceTaxType']; ?>:</b> <?php echo $purchase_order_data['CompanyServiceTaxIdentificationNumber']; ?>
                    <?php } ?><br>
                    <b>Delivery Date:</b> <?php echo date('d M Y',strtotime($purchase_order_data['DeliveryDate'])); ?>
				</td>
			</tr>
		</table>
		<br>
		<hr>
		<p>&nbsp;</p>
        <table border="0" cellpadding="1" cellspacing="0" style="width:100%;">
            <tr>
                <th><b>Vendor:</b></th>
                <th style="text-align: right;"><?php if(!empty($purchase_order_data['ShippingAddress'])){ ?><b>Ship To:</b><?php } ?></th>
            </tr>
            <tr>
                <td rowspan="3"><b><?php echo $purchase_order_data['VendorName']; ?></b>,<br><?php echo $purchase_order_data['VendorBillingAddress']; ?><br><?php echo $purchase_order_data['VendorContactNo']; ?></td>
                <td rowspan="3" style="text-align: right;"><?php if(!empty($purchase_order_data['ShippingAddress'])){ ?><b><?php echo $purchase_order_data['CompanyName'].' '.$purchase_order_data['CompFirmType']; ?></b>,<br><?php echo $purchase_order_data['ShippingAddress']; ?><br><?php echo $purchase_order_data['CompanyContactNumber']; ?><?php } ?></td>
            </tr>
        </table>

		<p>&nbsp;</p>
        <table border="1" cellpadding="1" cellspacing="0" style="width:100%; text-align:center;">
            <tr>
                <th>PO Status</th>
                <th>Shipping Terms & Conditions</th>
                <th>Payment Terms</th>
            </tr>
            <tr>
                <td>
                    <?php switch ($purchase_order_data['PurchaseOrderStatus']) {
                        case 'Canceled':
                            echo '<span style="color:red;">'.$purchase_order_data['PurchaseOrderStatus'].'</span>';
                            break;
                        case 'Received':
                            echo '<span style="color:green;">'.$purchase_order_data['PurchaseOrderStatus'].'</span>';
                            break;
                        default:
                            echo $purchase_order_data['PurchaseOrderStatus'];
                            break;
                    } ?>
                </td>
                <td><?php echo $purchase_order_data['ShippingTermsAndConditions']; ?></td>
                <td><?php echo $purchase_order_data['PaymentTerms']; ?></td>
            </tr>
        </table>

        <?php if(!empty($purchase_order_data['CancelationRemark'])){ ?>
            <p>&nbsp;</p>
            <table border="1" cellpadding="1" cellspacing="0" style="width:33%; text-align:center;">
                <tr>
                    <th>Cancelation Remark</th>
                </tr>
                <tr>
                    <td><?php echo $purchase_order_data['CancelationRemark']; ?></td>
                </tr>
            </table>
        <?php } ?>
        <p>&nbsp;</p>
		<table border="1" cellpadding="1" cellspacing="0" style="width:100%; text-align:center;">
			<tr>
				<th width="30%"><b>Particulars</b></th>
				<th width="15%"><b>HSN/SAC</b></th>
				<th width="25%"><b>Price Per Unit</b></th>
				<th width="8%"><b>Qty</b></th>
				<th><b>Total Amount</b></th>
			</tr>
			<?php 
                for($i=0;$i<count($purchase_order_details);$i++){ 
            ?>
            <tr>
                <td><?php echo $purchase_order_details[$i]['Particular']; ?></td>
                <td align="center"><?php echo $purchase_order_details[$i]['HSN']; ?></td>
                <td align="center">
                    <?php 
                        echo round($purchase_order_details[$i]['PricePerUnit'],2,PHP_ROUND_HALF_DOWN); 
                    ?>
                </td>
                <td align="center"><?php echo $purchase_order_details[$i]['Quantity']; ?></td>
                <td align="center">
                    <?php 
                        $total_amount = $purchase_order_details[$i]['PricePerUnit'] * $purchase_order_details[$i]['Quantity'];
                        echo round($total_amount,2,PHP_ROUND_HALF_DOWN); 
                    ?>
                </td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="4" style="text-align:right;"><b>Sub Total</b></td>
                <td><b><?php echo $purchase_order_data['TotalAmount']; ?></b></td>
            </tr>
		</table>
		<br>
		<hr>
</html>