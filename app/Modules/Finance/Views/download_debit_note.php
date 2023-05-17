<html>
    <head></head>
    <body>
        <?php if(!empty($debit_note_data['CompLogoPath'])){ ?>
        <img src="<?php echo media_server($debit_note_data['CompLogoPath']); ?>" alt="<?php echo $debit_note_data['CompName'] ?> Logo" class="img-responsive" width="150">
        <?php } ?>
        <br>
        <table border="0" cellpadding="1" cellspacing="0" style="width: 100%;">
            <tr>
                <th align="left"><b><?php echo $debit_note_data['CompName'].' '.$debit_note_data['FirmType']; ?></b></th>
                <th align="right"><b>Debit Note</b></th>
            </tr>
            <tr>
                <td><p><?php echo $debit_note_data['ContactNo']; ?></p></td>
                <td style="text-align: right;">
                    <b>Invoice No</b>: <?php echo $debit_note_data['InvoiceNo']; ?>
                </td>
            </tr>
        </table>
        <br>
        <hr>
        <p>&nbsp;</p>
        <table border="0" cellpadding="1" cellspacing="0">
            <tr>
                <th><b>Vendor:</b></th>
                <th style="text-align: right;"><b>Debit Note Date:</b> <?php echo date('d M Y',strtotime($debit_note_data['DebitNoteDate'])); ?></th>
            </tr>
            <tr>
                <td><b><?php echo str_replace('~d','',$debit_note_data['VendorName']).' '.$debit_note_data['vendor_FirmType']; ?></b><br></td>
                <td style="text-align: right;"><b>Debit Note No:</b> <?php echo $debit_note_data['DebitNoteNo']; ?><br><b>Debt Remaining:</b> <?php echo($debit_note_data['PaymentStatus'] == 'Pending')?$debit_note_data['ReceivableAmount']:'0'; ?></td>
            </tr>
        </table>
        <p>&nbsp;</p>
        <table border="1" cellpadding="1" cellspacing="0" style="width:100%; text-align:center;">
            <tr>
                <th><b>Sr</b></th>
                <th><b>Particulars</b></th>
                <th><b>HSN/SAC</b></th>
                <th><b>Qty</b></th>
                <th><b>Price per unit</b></th>
                <th><b>Taxes</b></th>
                <th><b>Tax amount</b></th>
                <th><b>Taxable Amount</b></th>
                <th><b>Total Amount</b></th>
            </tr>
            <?php
                $post_tax_total_amount = 0;
                $total_paid_amount = 0;
                for($i=0;$i<count($debit_note_details);$i++){
                    $qty = $debit_note_details[$i]['Quantity'];
                    $price_per_unit = $debit_note_details[$i]['PricePerUnit'];
                    $total_units_price = (!empty($qty))?$price_per_unit * $qty:$price_per_unit;
            ?>
            <tr>
                <th><?php echo $i+1; ?></th>
                <td><?php echo $debit_note_details[$i]['Particular']; ?></td>
                <td align="center"><?php echo $debit_note_details[$i]['HSN']; ?></td>
                <td align="center"><?php echo(!empty($qty))?$qty:'NA'; ?></td>
                <td align="center"><?php echo $price_per_unit; ?></td>
                <td><?php $cn_taxes = explode(',', $debit_note_details[$i]['taxes']);
                $tax_amount = 0;
                for($j=0;$j<count($cn_taxes);$j++){
                    $cn_taxes_split = explode('|',$cn_taxes[$j]);
                    $tax_amount += (!empty($cn_taxes_split[1]))?$cn_taxes_split[1]:0;
                    echo str_replace('|',' : ',$cn_taxes[$j]).'%<br>';
                }?></td>
                <td><?php $tax_amount = $total_units_price * $tax_amount / 100; 
                    echo $tax_amount;
                ?></td>
                <td><?php echo $total_units_price; ?></td>
                <td align="center"><?php echo $debit_note_data['ReceivableAmount']; ?></td>
            </tr>
            <?php } ?>
            <tr><td colspan="5"><b style="text-align:right;">Total</b></td><td colspan="4"><b><?php echo $debit_note_data['ReceivableAmount']; ?>/-</b></td></tr>
            <tr><td colspan="5"><b style="text-align:right;">Total</b></td><td colspan="4"><b><?php echo numberToWords($debit_note_data['ReceivableAmount']); ?> only</b></td></tr>
            <tr><td colspan="5"><b style="text-align:right;">Credits Remaining</b></td><td colspan="4"><b><?php echo($debit_note_data['PaymentStatus'] == 'Unpaid')?$debit_note_data['ReceivableAmount']:'0'; ?></b></td></tr>
        </table>
        <br>
        <hr>
        <p>&nbsp;</p>
        
        <table border="0" cellpadding="5" cellspacing="0" width="200">
            <tr>
                <th>
                    <img src="<?php echo media_server($debit_note_data['SignatureImgPath']); ?>" alt="" style="max-height: 100px;max-width: 200px;">
                    <br>
                    <hr />
                    Authorised Signature
                </th>
            </tr>
        </table>
        <br>
        <hr>
        
        <br>
        <p>&nbsp;</p>
    </body>
</html>