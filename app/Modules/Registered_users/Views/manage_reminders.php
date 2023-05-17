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
                <h3 class="box-title">View Reminders</h3>
            </div>
            <div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap <?php echo $datatable_hide_class; ?>">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/registered_users/get_all_reminders') ?>" data-responsive="true">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th id="Task">Task</th>
                                <th id="ReminderDate">Reminder Date</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>