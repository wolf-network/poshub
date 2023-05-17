<html>
    <head></head>
    <body>
        <?php if(!empty($company_banking_details['CompLogoPath'])){ ?>
        <img src="<?php echo media_server($company_banking_details['CompLogoPath']); ?>" alt="<?php echo $invoice_data['CompanyName'] ?> Logo" class="img-responsive" width="150">
        <?php } ?>
        <br>
        <table border="0" cellpadding="1" cellspacing="0" style="width: 100%;">
            <tr>
                <th align="left"><b><?php echo $invoice_data['CompanyName'].' '.$invoice_data['FirmType']; ?></b></th>
                <th align="right"><b>Tax Invoice</b></th>
            </tr>
            <tr>
                <td>
                    <p><?php echo $invoice_data['CompanyAddress']; ?></p>
                    <p><?php echo $invoice_data['CompanyContactNumber']; ?></p>
                </td>
                <td style="text-align: right;">
                    <b>Invoice No</b>: <?php echo $invoice_data['InvoiceNo']; ?>
                    <?php if(!empty($invoice_data['company_tax_type'])){ ?>
                        <br>
                    <b><?php echo $invoice_data['company_tax_type']; ?>:</b> <?php echo $invoice_data['CompanyServiceTaxIdentificationNumber']; ?>
                    <?php } ?>
                </td>
            </tr>
        </table>
        <br>
        <hr>
        <p>&nbsp;</p>
        <table border="0" cellpadding="1" cellspacing="0" style="width:100%;">
            <tr>
                <th><?php if(!empty($invoice_data['ClientName'])){ ?><b>Bill To:</b><?php } ?></th>
                <th><?php if(!empty($invoice_data['ClientShippingAddress'])){ ?><b>Ship To:</b><?php } ?></th>
                <th><?php if(!empty($invoice_data['ClientContactNo'])){ ?><b>Contact No:</b> <?php echo $invoice_data['ClientContactNo']; ?> <?php } ?></th>
            </tr>
            <tr>
                
                <td rowspan="4"><?php if(!empty($invoice_data['ClientName'])){ ?><b><?php echo str_replace('~d','',$invoice_data['ClientName']).' '.$invoice_data['client_FirmType']; ?></b>,<br><?php echo $invoice_data['ClientBillingAddress']; ?><?php } ?></td>
                <td rowspan="4"><?php if(!empty($invoice_data['ClientShippingAddress'])){ ?><b><?php echo str_replace('~d','',$invoice_data['ClientName']).' '.$invoice_data['client_FirmType']; ?></b>,<br><?php echo $invoice_data['ClientShippingAddress']; ?><?php } ?></td>
                <td><b>Invoice Date:</b> <?php echo date('d M Y',strtotime($invoice_data['ClientInvoiceDate'])); ?></td>
            </tr>
            <tr>
                 <td colspan="3"><b>Due Date:</b> <?php echo date('d M Y',strtotime($invoice_data['ClientInvoiceDueDate'])); ?></td>
            </tr>
            <tr>
                 <td colspan="3"><?php if(!empty($invoice_data['ServiceTaxType'])){ ?><b>Customer <?php echo $invoice_data['ServiceTaxType']; ?>:</b> <?php echo $invoice_data['ClientServiceTaxIdentificationNumber']; ?>
                    <?php } ?></td>
            </tr>
        </table>
        <br>
        <p>&nbsp;</p>
        <table border="1" cellpadding="1" cellspacing="0" style="width:100%; text-align:center;">
            <tr>
                <th><b>Particulars</b></th>
                <th><b>HSN/SAC</b></th>
                <th><b>Qty</b></th>
                <th><b>Price per unit</b></th>
                <th><b>Discount</b></th>
                <th><b>Pre Tax Total Amount</b></th>
            </tr>
            <?php 
                $pre_tax_total_amount = 0;
                $post_tax_total_amount = 0;
                $total_paid_amount = 0;
                for($i=0;$i<count($invoice_details_data);$i++){ 
                    $post_tax_total_amount += $invoice_details_data[$i]['TotalAmount'];
            ?>
            <tr>
                <td><?php echo $invoice_details_data[$i]['Particular']; ?></td>
                <td align="center"><?php echo $invoice_details_data[$i]['HSN']; ?></td>
                <td align="center"><?php echo(!empty($invoice_details_data[$i]['Quantity']))?$invoice_details_data[$i]['Quantity']:'NA'; ?></td>
                <td align="center">
                    <?php echo $invoice_details_data[$i]['PricePerUnit']; ?>
                </td>
                <td align="center"><?php echo $invoice_details_data[$i]['Discount']; ?>%</td>
                <td align="center">
                    <?php
                        if(!empty($invoice_details_data[$i]['Quantity'])){
                            $pre_tax_amount = $invoice_details_data[$i]['PricePerUnit'] * $invoice_details_data[$i]['Quantity'];
                        }else{
                            $pre_tax_amount = $invoice_details_data[$i]['PricePerUnit'];
                        }

                        $pre_tax_amount = $pre_tax_amount - ($pre_tax_amount * $invoice_details_data[$i]['Discount'] / 100);

                        $pre_tax_total_amount += $pre_tax_amount;
                        echo round($pre_tax_amount,2,PHP_ROUND_HALF_DOWN); 
                    ?>
                </td>
            </tr>
            <?php } ?>
        </table>
        <br>
        <hr>
        <p>&nbsp;</p>
        <table border="1" cellpadding="1" cellspacing="0" style="width:100%; text-align: center;">
            <tr>
                <th width="30%"><b>Particulars</b></th>
                <th colspan="2" style="text-align:center;"><b>Taxes</b></th>
                <th><b>Taxable Amount</b></th>
            </tr>
            <?php 
            $total_tax_percentage = 0;
            $total_taxable_amount = 0;
            for($i=0;$i<count($invoice_details_tax_data);$i++){ ?>
                <tr>
                    <td><?php echo $invoice_details_tax_data[$i]['Particular']; ?></td>
                    <td><?php echo $invoice_details_tax_data[$i]['Tax']; ?></td>
                    <td>
                        <?php echo(!empty($invoice_details_tax_data[$i]['TaxPercentage']))?$invoice_details_tax_data[$i]['TaxPercentage'].'%':''; ?>
                            
                    </td>
                    <td>
                        <?php 
                            if(!empty($invoice_details_tax_data[$i]['TaxPercentage'])){
                                $total_amount = $invoice_details_tax_data[$i]['PricePerUnit'] * $invoice_details_tax_data[$i]['Quantity'];

                                $total_amount = $total_amount - ($total_amount * $invoice_details_tax_data[$i]['Discount'] / 100);
                               
                               $taxable_amount = floatval($total_amount * $invoice_details_tax_data[$i]['TaxPercentage'] / 100);

                               echo round($taxable_amount,2,PHP_ROUND_HALF_DOWN);
                               $total_taxable_amount += $taxable_amount;
                            }
                        ?>
                    </td>
                </tr>
            <?php 
                $total_tax_percentage += $invoice_details_tax_data[$i]['TaxPercentage']; 
                } 
            ?>
            
            <tr>
                <td colspan="3" class="text-right">
                    <b>Total Taxable Amount</b>
                </td>
                <td>
                    <b>
                        <?php 
                            echo round($total_taxable_amount,2,PHP_ROUND_HALF_DOWN);
                        ?>
                    </b>
                </td>
            </tr>
        </table>
        <?php if(!empty($invoice_data['CustomerNotes'])){ ?>
            <p><b>Notes:</b> <?php echo $invoice_data['CustomerNotes']; ?></p>       
        <?php } ?>
        <br>
        <hr>
        <p>&nbsp;</p>
        
        
        <table border="0" cellpadding="5" cellspacing="0" style="width:640px; float: left;">
            <tr>
                <th rowspan="<?php echo count($invoice_additional_charges_data) + 4; ?>">
                    <?php if(!empty($invoice_data['SignatureImgPath'])){ ?>
                    <img src="<?php echo media_server($invoice_data['SignatureImgPath']); ?>" alt="" style="max-height: 100px;max-width: 200px;">
                    <?php } ?>
                    <hr>
                    Authorised Signature
                </th>
                <th border="1" colspan="2" style="text-align:center;">Total</th>
            </tr>
            <tr>
                <td border="1">Pre Tax Total</td>
                <td border="1"><?php echo round($pre_tax_total_amount,2,PHP_ROUND_HALF_DOWN); ?>/-</td>
            </tr>
            <tr>
                <td border="1">Post Tax Total</td>
                <td border="1"><?php echo round($post_tax_total_amount,2,PHP_ROUND_HALF_DOWN); ?>/-</td>
            </tr>
            <?php
             $invoice_additional_charges = 0;   
             if(!empty($invoice_additional_charges_data)){ 
                for($i=0;$i<count($invoice_additional_charges_data);$i++){
                    $invoice_additional_charges += $invoice_additional_charges_data[$i]['Additionalcharge'];
            ?>
            <tr>
                <td border="1"><?php echo $invoice_additional_charges_data[$i]['AdditionalChargeType']; ?></td>
                <td border="1"><?php echo $invoice_additional_charges_data[$i]['Additionalcharge']; ?>/-</td>
            </tr>
            <?php } } ?>
            <tr>
                <td border="1">Grand Total</td>
                <td border="1"><?php echo $post_tax_total_amount + $invoice_additional_charges; ?>/-</td>
            </tr>
        </table>
        <br>
        <hr>
        <?php for($i=0;$i<6;$i++){ ?>
        <p>&nbsp;</p>
        <?php } ?>
        <br>
        <p>&nbsp;</p>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th colspan="<?php echo(!empty($company_banking_details['QRCode']))?'3':'2'; ?>" style="text-align: center;">
                    <b>Payment Options</b>
                    <?php if(empty($company_banking_details)){ ?>
                    <a href="<?php echo base_url('edit-company-bank-details'); ?>" class="btn btn-success pull-right" targe="_blank">Add Bank Details</a>
                    <?php } ?>
                </th>
            </tr>
            <?php if(!empty($company_banking_details)){ ?>
            <tr>
                <th colspan="2" style="text-align:center;"><b>Account Details</b></th>
                <?php if(!empty($company_banking_details['QRCode'])){ ?>
                    <th style="text-align: center;"><b>QR Code</b></th>
                <?php } ?>
            </tr>
            <tr>
                <td>Bank Name</td>
                <td><?php echo $company_banking_details['BankName']; ?></td>
                <?php if(!empty($company_banking_details['QRCode'])){ ?>
                <td rowspan="4">
                    <center>
                        <img src="<?php echo media_server($company_banking_details['QRCode']); ?>" alt="" width="250">
                    </center>
                </td>
            <?php } ?>
            </tr>
            <tr>
               <td>A/c Holder Name</td>
               <td><?php echo $company_banking_details['AccountHolderName']; ?></td>
            </tr>
            <tr>
                <td>A/c No</td>
                <td><?php echo $company_banking_details['AccountNo']; ?></td>
            </tr>
            <tr>
                <td>IFSC</td>
                <td><?php echo $company_banking_details['BankIFSC']; ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="3" style="text-align:center;">This is a system generated invoice, hence signature not required.</td>
            </tr>
        </table>
    </body>
</html>