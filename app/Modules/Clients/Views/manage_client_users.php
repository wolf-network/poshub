<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="box box-success">
            <div class="box-header">
                <h3 class="box-title">Manage Client Users</h3>
                <a href="<?php echo base_url('client/add-user/'.$client_id); ?>" class="btn btn-success pull-right">Add Client User</a>
            </div>
            <div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/clients/get_client_users?ClientID='.$client_id) ?>">
                        <thead>
                            <tr>
                                <th>Sr</th>
                                <th data-render="true" data-render_html="{ClientUserFirstName} {ClientUserLastName}">Full Name</th>
                                <th id="ClientUserEmailID">Email ID</th>
                                <th id="ClientUserContactNo">Contact No</th>
                                <th id="ClientUserPassword">Password</th>
                                <th data-render="true" data-render_html="<a href='<?php echo base_url('client/edit-user/'.$client_id.'/{ClientUserID}'); ?>' class='btn btn-warning btn-xs'><i class='fa fa-edit'></i></a> <a href='<?php echo base_url('client/delete-user/{ClientUserID}') ?>' class='btn btn-danger btn-xs'><i class='fa fa-trash'></i></a> <button type='button' class='btn btn-info btn-xs generate-password' data-client_user_id='{ClientUserID}'>Generate Password</button>">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>