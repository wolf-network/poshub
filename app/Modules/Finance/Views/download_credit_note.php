<html>
    <head></head>
    <body>
        <?php if(!empty($credit_note_data['CompLogoPath'])){ ?>
        <img src="<?php echo media_server($credit_note_data['CompLogoPath']); ?>" alt="<?php echo $credit_note_data['CompanyName'] ?> Logo" class="img-responsive" width="150">
        <?php } ?>
        <br>
        <table border="0" cellpadding="1" cellspacing="0" style="width: 100%;">
            <tr>
                <th align="left"><b><?php echo $credit_note_data['CompanyName'].' '.$credit_note_data['FirmType']; ?></b></th>
                <th align="right"><b>Credit Note</b></th>
            </tr>
            <tr>
                <td>
                    <p><?php echo $credit_note_data['CompanyAddress']; ?></p>
                    <p><?php echo $credit_note_data['CompanyContactNumber']; ?></p>
                </td>
                <td style="text-align: right;">
                    <b>Original Invoice No</b>: <?php echo $credit_note_data['InvoiceNo']; ?>
                    <?php if(!empty($credit_note_data['company_tax_type'])){ ?>
                        <br>
                    <b><?php echo $credit_note_data['company_tax_type']; ?>:</b> <?php echo $credit_note_data['CompanyServiceTaxIdentificationNumber']; ?>
                    <?php } ?>
                </td>
            </tr>
        </table>
        <br>
        <hr>
        <p>&nbsp;</p>
        <table border="0" cellpadding="1" cellspacing="0">
            <tr>
                <th><b>Customer:</b></th>
                <th style="text-align: right;"><b>Credit Note Date:</b> <?php echo date('d M Y',strtotime($credit_note_data['CreditNoteDate'])); ?></th>
            </tr>
            <tr>
                <td><b><?php echo str_replace('~d','',$credit_note_data['ClientName']).' '.$credit_note_data['client_FirmType']; ?></b>,<br><?php echo $credit_note_data['ClientBillingAddress']; ?><br><br><?php echo $credit_note_data['ClientContactNo']; ?></td>
                <td style="text-align: right;"><b>Credit Note No:</b> <?php echo $credit_note_data['CreditNoteNo']; ?><br><b>Credits Remaining:</b> <?php echo($credit_note_data['PaymentStatus'] == 'Unpaid')?$credit_note_data['PayableAmount']:'0'; ?></td>
            </tr>
            <tr>
                <td colspan="3"><?php if(!empty($credit_note_data['ServiceTaxType'])){ ?><b>Customer <?php echo $credit_note_data['ServiceTaxType']; ?>:</b> <?php echo $credit_note_data['ClientServiceTaxIdentificationNumber']; ?>
                    <?php } ?></td>
                <td>&nbsp;</td>
            </tr>
        </table>
        <br>
    
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
                for($i=0;$i<count($credit_note_details);$i++){
                    $qty = $credit_note_details[$i]['Qty'];
                    $price_per_unit = $credit_note_details[$i]['PricePerUnit'];
                    $total_units_price = (!empty($qty))?$price_per_unit * $qty:$price_per_unit;
            ?>
            <tr>
                <th><?php echo $i+1; ?></th>
                <td><?php echo $credit_note_details[$i]['Particular']; ?></td>
                <td align="center"><?php echo $credit_note_details[$i]['HSN']; ?></td>
                <td align="center"><?php echo(!empty($qty))?$qty:'NA'; ?></td>
                <td align="center"><?php echo $price_per_unit; ?></td>
                <td><?php $cn_taxes = explode(',', $credit_note_details[$i]['taxes']);
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
                <td align="center"><?php echo $credit_note_data['PayableAmount']; ?></td>
            </tr>
            <?php } ?>
            <tr><td colspan="5"><b style="text-align:right;">Total</b></td><td colspan="4"><b><?php echo $credit_note_data['PayableAmount']; ?>/-</b></td></tr>
            <tr><td colspan="5"><b style="text-align:right;">Total</b></td><td colspan="4"><b><?php echo numberToWords($credit_note_data['PayableAmount']); ?> only</b></td></tr>
            <tr><td colspan="5"><b style="text-align:right;">Credits Remaining</b></td><td colspan="4"><b><?php echo($credit_note_data['PaymentStatus'] == 'Unpaid')?$credit_note_data['PayableAmount']:'0'; ?></b></td></tr>
        </table>
        <br>
        <hr>
        <p>&nbsp;</p>
        
        <table border="0" cellpadding="5" cellspacing="0" width="200">
            <tr>
                <th>
                    <img src="<?php echo media_server($credit_note_data['SignatureImgPath']); ?>" alt="" style="max-height: 100px;max-width: 200px;">
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