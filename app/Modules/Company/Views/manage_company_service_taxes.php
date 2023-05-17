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
                <h3 class="pull-left box-title">Manage Service Taxes</h3>
                <div class="pull-right">
                    <a href="https://www.youtube.com/watch?v=X_gLahYe_S0" target="_blank" class="btn btn-info">Watch tutorial</a>
                    <a href="<?php echo base_url('add-company-service-tax'); ?>" class="btn btn-success <?php echo $btn_class; ?>">Add Service Tax</a>
                </div>
            </div>
            <div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap table-responsive">
                    <table class="table table-bordered table-striped table-hover table-responsive dataTable shadow">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th>Service Tax Type</th>
                                <th>Service Tax Number</th>
                                <th>Billing Address</th>
                                <?php 
                                    $action_btn = FALSE;

                                    if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0 && session()->get('user_data')['Privilege'] == 'Admin'){
                                        $action_btn = true;
                                ?>

                                <th>Action</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for($i=0; $i <count($company_service_taxes) ; $i++) { ?>
                                <tr>
                                    <td><?php echo $i+1; ?></td>
                                    <td><?php echo $company_service_taxes[$i]['ServiceTaxType']; ?></td>
                                    <td><?php echo $company_service_taxes[$i]['ServiceTaxIdentificationNumber']; ?></td>
                                    <td><?php echo $company_service_taxes[$i]['RegisteredAddress']; ?></td>
                                    <td>
                                        <?php if(!empty($action_btn)){ ?>
                                            <a href='<?php echo base_url('edit-company-service-tax/'.$company_service_taxes[$i]['CompanyServiceTaxMasterID']); ?>' class='btn btn-warning btn-xs' title='edit'>
                                                <i class='fa fa-edit'></i>
                                            </a> 
                                            <button data-company_service_tax_id='<?php echo $company_service_taxes[$i]['CompanyServiceTaxMasterID']; ?>' class='btn btn-danger btn-xs delete-company-service-tax' title='delete'><i class='fa fa-trash'></i></button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>