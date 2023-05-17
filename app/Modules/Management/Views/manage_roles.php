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
                <h3 class="pull-left box-title">Manage Roles</h3>
                <div class="pull-right">
                    <a href="https://www.youtube.com/watch?v=VidboF-1ZlE" target="_blank" class="btn btn-info">Watch tutorial</a>
                    <a href="<?php echo base_url('add-role'); ?>" class="btn btn-success <?php echo $btn_class; ?>">Add Role</a>
                </div>
            </div>
            <div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap table-responsive">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/management/get_roles') ?>" data-responsive="true">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th id="Role">Role</th>
                                <th id="Name">Added By</th>

                                <?php 
                                    $action_btn = "<a href='".base_url('edit-role/')."{RoleID}' class='btn btn-warning btn-xs' title='edit'><i class='fa fa-edit'></i></a>";
                                    if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0 && session()->get('user_data')['Privilege'] == 'Admin'){
                                        $action_btn .= " <button data-role_id='{RoleID}' class='btn btn-danger btn-xs delete-role' title='delete'><i class='fa fa-trash'></i></button>";
                                ?>

                                <th data-render="true" data-render_html="<?php echo $action_btn; ?>" data-sortable="false">Action</th>
                                <?php } ?>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>