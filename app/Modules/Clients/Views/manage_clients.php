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
                <h3 class="pull-left box-title">Manage Clients</h3>
                <div class="pull-right">
                    <a href="https://www.youtube.com/watch?v=y9_1HEEpEw8" target="_blank" class="btn btn-info">Watch tutorial</a>
                    <a href="<?php echo base_url('add-client'); ?>" class="btn btn-success <?php echo $btn_class; ?>">Add Client</a>
                    <a href="<?php echo base_url('export-clients'); ?>" download class="btn btn-info <?php echo $btn_class; ?>">Export Clients</a>
                </div>
            </div>
            <div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap table-responsive">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/clients/get_clients') ?>" data-responsive="true">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th id="ClientName">Business Name</th>
                                <th id="BusinessIndustry">Industry</th>
                                <th id="StateName">State</th>
                                <th id="CityName">City</th>
                                <th data-render="true" data-render_html="{ClientUserFirstName} {ClientUserLastName}">Contact Person</th>
                                <th id="Role">Role</th>
                                <th data-render="true" data-render_html="<a href='JavaScript:void(0)' data-toggle='modal' data-target='#contactModal' data-contact_no='{ClientUserContactNo}' class='contact-client' >{ClientUserContactNo}</a>">Contact No</th>
                                <th data-render="true" data-render_html="<a href='mailto:{ClientUserEmailID}' data-download='false' data-loader='false'>{ClientUserEmailID}</a>">Email ID</th>
                                <?php 
                                    $rating_cond['ClientRating'] = [
                                        '1' => [
                                            'html' => '<i class="fa fa-star text-yellow"></i> <br> Very Bad'
                                        ],
                                        '2' => [
                                            'html' => '<i class="fa fa-star text-yellow"></i> <i class="fa fa-star text-yellow"></i> <br> Bad'
                                        ],
                                        '3' => [
                                            'html' => '<i class="fa fa-star text-yellow"></i> <i class="fa fa-star text-yellow"></i> <i class="fa fa-star text-yellow"></i> <br> Medium'
                                        ],
                                        '4' => [
                                            'html' => '<i class="fa fa-star text-yellow"></i> <i class="fa fa-star text-yellow"></i> <i class="fa fa-star text-yellow"></i> <i class="fa fa-star text-yellow"></i> <br> Good'
                                        ],
                                        '5' => [
                                            'html' => '<i class="fa fa-star text-yellow"></i> <i class="fa fa-star text-yellow"></i> <i class="fa fa-star text-yellow"></i> <i class="fa fa-star text-yellow"></i> <i class="fa fa-star text-yellow"></i> <br> Excellent!'
                                        ],
                                        'default' => [
                                            'html' => 'NA'
                                        ]
                                    ];
                                ?>
                                <th data-condition_render='<?php echo json_encode($rating_cond); ?>'>Ratings</th>
                                <th id="Name">Added By</th>

                                <?php 
                                    $action_btn = "<button data-client_id='{ClientID}' data-toggle='modal' data-target='#manageDetailsModal' class='btn btn-info btn-xs manage-details' title='Manage Details'><i class='fa fa-cogs'></i></button>";
                                    if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0 && session()->get('user_data')['Privilege'] == 'Admin'){
                                        $action_btn .= " <a href='".base_url('edit-client/')."{ClientID}' class='btn btn-warning btn-xs' title='edit'><i class='fa fa-edit'></i></a> <button data-client_id='{ClientID}' class='btn btn-danger btn-xs delete-client' title='delete'><i class='fa fa-trash'></i></button>";
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

<!-- Modal -->
<div id="contactModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Contact Client Via</h4>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-6 col-xs-6">
                <a href="#" class="btn btn-success btn-block whatsapp-msg"><i class="fa fa-whatsapp"></i> Whatsapp</a>
            </div>
            <div class="col-md-6 col-xs-6">
                <a href="#" class="btn btn-primary btn-block call-client" data-loader="false"><i class="fa fa-phone"></i> Call</a>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<!-- Manage Details Modal -->
<div id="manageDetailsModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Manage Details</h4>
      </div>
      <div class="modal-body">
        <a href="javascript:void(0)" class="btn btn-info manage-client-service-taxes-url">Manage Service Taxes</a>
        <a href="javascript:void(0)" class="btn btn-info manage-invoices-url">View Invoices</a>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>