<?php 
    $btn_class = '';
    $datatable_hide_class = '';
    if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
        $btn_class .= 'disabled';
        $datatable_hide_class = 'datatable-hide-search';
    }
?>

<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header">
            	<h3 class="pull-left box-title">Manage GST/VAT</h3>
                <div class="pull-right">
                    <a href="<?php echo base_url('add-tax-identification'); ?>" class="btn btn-success <?php echo $btn_class; ?>">Add GST/VAT</a>
                </div>
        	</div>
        	<div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Service Tax Type</th>
                            <th>Service Tax Identification Number</th>
                            <th>Registered Address</th>
                            <th>Action</th>
                        </tr>
                        <?php for($i=0;$i<count($company_service_tax_master_data);$i++){ ?>
                        <tr>
                            <td><?php echo $company_service_tax_master_data[$i]['ServiceTaxType']; ?></td>
                            <td><?php echo $company_service_tax_master_data[$i]['ServiceTaxIdentificationNumber']; ?></td>
                            <td><?php echo $company_service_tax_master_data[$i]['RegisteredAddress']; ?></td>
                            <td>
                                <a href="<?php echo base_url('edit-tax-identification/'.$company_service_tax_master_data[$i]['CompanyServiceTaxMasterID']); ?>" class="btn btn-warning btn-xs" ><i class="fa fa-edit"></i></a>
                                <button type="button" class="btn btn-danger btn-xs delete-tax-identification" data-company_service_tax_master_id="<?php echo $company_service_tax_master_data[$i]['CompanyServiceTaxMasterID']; ?>"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
        	</div>
        </div>
    </div>
</div>