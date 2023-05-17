<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header">
                <h3 class="pull-left box-title">All Notifications</h3>
            </div>
            <div class="box-body">
                <form action="javascript:void(0)" id="form-filter">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Read/Unread Notifications</label>
                                <select name="NotificationReadDate" id="NotificationReadDate" class="form-control">
                                    <option value="">Show All</option>
                                    <option value="null">View Unread</option>
                                    <option value="read">View Read</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">&nbsp;</label>
                                <button type="button" id="form-filter-btn" class="btn btn-warning btn-block">Apply Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="dataTables_wrapper form-inline dt-bootstrap table-responsive">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/registered_users/get_notifications') ?>" data-responsive="true">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th data-render="true" data-render_html="{Name} {Notification}">Notification</th>
                                <th id="NotificationDate">Notification Date</th>
                                <th data-render="true" data-render_html="<span class='notififcation-read-date'>{NotificationReadDate}</span>">Notification Read Date</th>
                                <th data-render="true" data-render_html="<a href='<?php echo base_url('read-notification/{RegisteredUserNotificationID}'); ?>' class='btn btn-success'>View</a>">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>