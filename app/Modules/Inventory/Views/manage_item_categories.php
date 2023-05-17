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
                <h3 class="pull-left box-title">Manage Item Categories</h3>
                <div class="pull-right">
                    <a href="<?php echo base_url('add-item-category'); ?>" class="btn btn-success <?php echo $btn_class; ?>">Add Item Category</a>
                </div>
            </div>
            <div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap table-responsive <?php echo $datatable_hide_class; ?>">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/item/get_item_categories') ?>" data-responsive="true">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th id="ItemCategory">Category</th>
                                <?php 
                                    if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0 && $user_data['Privilege'] == 'Admin'){
                                ?>
                                <th data-render="true" data-render_html="<a href='<?php echo base_url('edit-item-category/'); ?>{ItemCategoryMasterID}' class='btn btn-warning btn-xs' title='edit'><i class='fa fa-edit'></i></a>" data-sortable="false">Action</th>
                                <?php } ?>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>