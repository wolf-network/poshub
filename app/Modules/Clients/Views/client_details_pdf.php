<h1 style="text-align:center;">Client Details</h1>


<table border="1" cellspacing="1" cellpadding="5" style="text-align:left;">
    <tr>
        <th colspan="2" bgcolor="#d4d3d3"><b>Client details</b></th>
    </tr>
    <tr>
        <th>Client Name</th>
        <td><?php echo $client_details['ClientName']; ?></td>
    </tr>
    <tr>
        <td>Firm Type</td>
        <td><?php echo $client_details['FirmType']; ?></td>
    </tr>
    <tr>
        <td>Address</td>
        <td><?php echo $client_details['Address']; ?></td>
    </tr>
    <tr>
        <td>Industry Exposure</td>
        <td><?php echo $client_details['BusinessIndustry']; ?></td>
    </tr>
    <tr>
        <th colspan="2" bgcolor="#d4d3d3"><b>Contact details</b></th>
    </tr>
    <tr>
        <td>Contact Person</td>
        <td><?php echo $client_details['ClientUserFirstName'].' '.$client_details['ClientUserLastName']; ?></td>
    </tr>
    <tr>
        <td>Email ID</td>
        <td><?php echo $client_details['ClientUserEmailID']; ?></td>
    </tr>
    <tr>
        <td>Contact No</td>
        <td><?php echo $client_details['ClientUserContactNo']; ?></td>
    </tr>
    <tr>
        <td>Designation</td>
        <td><?php echo $client_details['Roles']; ?></td>
    </tr>
    
    <?php 
        for($i=0;$i<count($client_documents);$i++){ 
            $client_documents_data[$client_documents[$i]['DocumentName']] = $client_documents[$i]['DocumentDescription'];
        }
    ?>
    
    <tr>
        <td>GST No:</td>
        <td><?php echo(!empty($client_documents_data['GST']))?$client_documents_data['GST']:''; ?></td>
    </tr>
    <tr>
        <td>PAN No:</td>
        <td><?php echo(!empty($client_documents_data['PAN Card']))?$client_documents_data['PAN Card']:''; ?></td>
    </tr>
    <tr>
        <td>TAN No:</td>
        <td><?php echo(!empty($client_documents_data['TAN']))?$client_documents_data['TAN']:''; ?></td>
    </tr>
    <tr>
        <th colspan="2" bgcolor="#d4d3d3"><b>Bank Details</b></th>
    </tr>
    <tr>
        <td>Bank Name</td>
        <td><?php echo(!empty($client_details['BankName']))?$client_details['BankName']:''; ?></td>
    </tr>
    <tr>
        <td>Account Holder Name</td>
        <td><?php echo(!empty($client_details['AccountHolderName']))?$client_details['AccountHolderName']:''; ?></td>
    </tr>
    <tr>
        <td>Account No</td>
        <td><?php echo(!empty($client_details['AccountNo']))?$client_details['AccountNo']:''; ?></td>
    </tr>
    <tr>
        <td>Bank Branch / City</td>
        <td><?php echo(!empty($client_details['BankBranch']))?$client_details['BankBranch']:''; ?></td>
    </tr>
    <tr>
        <td>IFSC code</td>
        <td><?php echo(!empty($client_details['BankIFSC']))?$client_details['BankIFSC']:''; ?></td>
    </tr>
    <tr>
        <td>MICR CODE</td>
        <td><?php echo(!empty($client_details['BankMICR']))?$client_details['BankMICR']:''; ?></td>
    </tr>
</table>
<br>
<?php if(!empty($client_details['ChequeImgPath'])){ ?>
<hr>
<h1>Cancelled Cheque</h1>
<img src="<?php echo media_server($client_details['ChequeImgPath']); ?>" alt="">
<?php } ?>
