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
                <h3 class="pull-left box-title">Manage <?php echo $vendor_details['VendorName']; ?>'s Service Taxes</h3>
                <div class="pull-right">
                    <a href="<?php echo base_url('add-vendor-service-tax/'.$vendor_id); ?>" class="btn btn-success <?php echo $btn_class; ?>">Add Service Tax</a>
                </div>
            </div>
            <div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap table-responsive">
                    <table class="table table-bordered table-hover table-responsive dataTable shadow">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th>Label</th>
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
                            <?php for($i=0; $i <count($vendor_service_taxes) ; $i++) { ?>
                                <tr>
                                    <td><?php echo $i+1; ?></td>
                                    <td><?php echo $vendor_service_taxes[$i]['Label']; ?></td>
                                    <td><?php echo $vendor_service_taxes[$i]['ServiceTaxType']; ?></td>
                                    <td><?php echo $vendor_service_taxes[$i]['ServiceTaxNumber']; ?></td>
                                    <td><?php echo $vendor_service_taxes[$i]['BillingAddress']; ?></td>
                                    <td>
                                        <?php if(!empty($action_btn)){ ?>
                                            <a href='<?php echo base_url('edit-vendor-service-tax/'.$vendor_id.'/'.$vendor_service_taxes[$i]['VendorServiceTaxID']); ?>' class='btn btn-warning btn-xs' title='edit'>
                                                <i class='fa fa-edit'></i>
                                            </a> 
                                            <button data-vendor_service_tax_id='<?php echo $vendor_service_taxes[$i]['VendorServiceTaxID']; ?>' class='btn btn-danger btn-xs delete-vendor-service-tax' title='delete'><i class='fa fa-trash'></i></button>
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