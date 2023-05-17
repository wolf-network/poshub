<h1 style="text-align:center;">Vendor Details</h1>


<table border="1" cellspacing="1" cellpadding="5" style="text-align:left;">
    <tr>
        <th colspan="2" bgcolor="#d4d3d3"><b>Vendor details</b></th>
    </tr>
    <tr>
        <th>Vendor Name</th>
        <td><?php echo $vendor_details['VendorName']; ?></td>
    </tr>
    <tr>
        <td>Firm Type</td>
        <td><?php echo $vendor_details['FirmType']; ?></td>
    </tr>
    <tr>
        <td>CIN</td>
        <td><?php echo $vendor_details['CIN']; ?></td>
    </tr>
    <?php if(!empty($vendor_details['ServiceTaxType'])){ ?>
    <tr>
        <td><?php echo $vendor_details['ServiceTaxType']; ?> No</td>
        <td><?php echo $vendor_details['ServiceTaxNumber']; ?></td>
    </tr>
    <?php } ?>
    <tr>
        <td>Address</td>
        <td><?php echo $vendor_details['Address']; ?></td>
    </tr>
    <tr>
        <td>Services provided</td>
        <td><?php echo $vendor_details['ServiceType']; ?></td>
    </tr>
    <tr>
        <th colspan="2" bgcolor="#d4d3d3"><b>Contact details</b></th>
    </tr>
    <tr>
        <td>Contact Person</td>
        <td><?php echo $vendor_details['VendorUserFirstName'].' '.$vendor_details['VendorUserLastName']; ?></td>
    </tr>
    <tr>
        <td>Email ID</td>
        <td><?php echo $vendor_details['VendorUserEmailID']; ?></td>
    </tr>
    <tr>
        <td>Contact No</td>
        <td><?php echo $vendor_details['VendorUserContactNo']; ?></td>
    </tr>
    <tr>
        <td>Designation</td>
        <td><?php echo $vendor_details['Roles']; ?></td>
    </tr>
    
    <tr>
        <th colspan="2" bgcolor="#d4d3d3"><b>Bank Details</b></th>
    </tr>
    <tr>
        <td>Bank Name</td>
        <td><?php echo(!empty($vendor_details['BankName']))?$vendor_details['BankName']:''; ?></td>
    </tr>
    <tr>
        <td>Account Holder Name</td>
        <td><?php echo(!empty($vendor_details['AccountHolderName']))?$vendor_details['AccountHolderName']:''; ?></td>
    </tr>
    <tr>
        <td>Account No</td>
        <td><?php echo(!empty($vendor_details['AccountNo']))?$vendor_details['AccountNo']:''; ?></td>
    </tr>
    <tr>
        <td>Bank Branch / City</td>
        <td><?php echo(!empty($vendor_details['BankBranch']))?$vendor_details['BankBranch']:''; ?></td>
    </tr>
    <tr>
        <td>IFSC code</td>
        <td><?php echo(!empty($vendor_details['BankIFSC']))?$vendor_details['BankIFSC']:''; ?></td>
    </tr>
    <tr>
        <td>MICR CODE</td>
        <td><?php echo(!empty($vendor_details['BankMICR']))?$vendor_details['BankMICR']:''; ?></td>
    </tr>
    <tr>
        <td><smal>Whether vendor is covered under micro
and small enterprise</smal></td>
        <td><?php echo(!empty($vendor_documents_data['MSME']))?'YES':'NO'; ?></td>
    </tr>
</table>
<br>
<?php if(!empty($vendor_details['ChequeImgPath'])){ ?>
<hr>
<h1>Cancelled Cheque</h1>
<img src="<?php echo media_server($vendor_details['ChequeImgPath']); ?>" alt="">
<?php } ?>
