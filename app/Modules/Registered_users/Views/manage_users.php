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
                <h3 class="pull-left box-title">Manage Users</h3>
                <div class="pull-right">
                    <a href="https://www.youtube.com/watch?v=jj6CUqN2MVk" class="btn btn-info" target="_blank">Watch tutorial</a>
                    <button type="button" data-toggle="modal" data-target="#importUsersModal" class="btn bg-maroon <?php echo $btn_class; ?>">Import Users</button>
                    <a href="<?php echo base_url('add-user'); ?>" class="btn btn-success <?php echo $btn_class; ?> ">Add User</a>
                </div>
            </div>
            <div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap table-responsive <?php echo $datatable_hide_class; ?>">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/registered_users/get_registered_users') ?>" data-responsive="true">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th data-render="true" data-render_html="<span class='user-name'>{Name}</span>">Name</th>
                                <th id="Gender">Gender</th>
                                <th id="EmailID">Email ID</th>
                                <?php
                                    if($subscription_time_left['years'] >= 0 && $subscription_time_left['months'] >= 0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0 && $user_data['Privilege'] == 'Admin'){
                                        $subscription_cond['SubscriptionEndDate'] = [
                                            'null' => [
                                                'html' => ''
                                            ],
                                            'Re-new' => [
                                                'html' => '<a href="javascript:void(0)" data-toggle="modal" data-target="#subscriptionRenewModal" data-registered_user_id="{ID}" class="subscription-btn btn btn-success btn-xs" >{SubscriptionEndDate}</a>'
                                            ],
                                            'default' => [
                                                'html' => '<a href="javascript:void(0)" data-toggle="modal" data-target="#subscriptionRenewModal" data-registered_user_id="{ID}" class="subscription-btn" >{SubscriptionEndDate}</a>'
                                            ]
                                        ];
                                    }else{
                                        $subscription_cond['SubscriptionEndDate'] = [
                                            'null' => [
                                                'html' => ''
                                            ],
                                            'default' => [
                                                'html' => '{SubscriptionEndDate}'
                                            ]
                                        ];
                                    }
                                ?>
                                <th data-condition_render='<?php echo json_encode($subscription_cond); ?>'>Subscription Ends on</th>
                                <?php 
                                    $status_cond['Status'] = [
                                        'Disabled' => [
                                            'html' => '{Status} <br> <a href="'.base_url('change-user-status/').'{ID}" class="btn btn-success btn-xs">Activate</a>'
                                        ],
                                        'default' => [
                                            'html' => '{Status} <br> <a href="'.base_url('change-user-status/').'{ID}" class="btn btn-danger btn-xs">Disable</a>'
                                        ]
                                    ];
                                ?>
                                <th data-condition_render='<?php echo json_encode($status_cond); ?>'>Status</th>
                                <?php 
                                    if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0 && $user_data['Privilege'] == 'Admin'){
                                ?>
                                <th data-render="true" data-render_html="<a href='<?php echo base_url('edit-user/'); ?>{ID}' class='btn btn-warning btn-xs' title='edit'><i class='fa fa-edit'></i></a>" data-sortable="false" >Action</th>
                                <?php } ?>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    if($subscription_time_left['years'] >= 0 && $subscription_time_left['months'] >= 0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0 && $user_data['Privilege'] == 'Admin'){
?>
<!-- Subscription Renewal Modal -->
<div id="subscriptionRenewModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Get Subscription for <span id="subscription-for"></span></h4>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-12">

                <div class="box box-widget widget-user-2">
                    <div class="widget-user-header bg-green text-center">
                        <h3>Pricing Plans</h3>
                        <p><b>Enjoy unlimited usage of Wolf Network CRM</b></p>
                        <p><b>Prices are in INR</b></p>
                    </div>
                    <div class="box-footer">
                        <ul class="nav nav-stacked">
                            <li>
                                <a href="#">
                                    Receive continous updates 
                                    <span class="pull-right text-green">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Manage Clients & Vendors
                                    <span class="pull-right text-green">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Create and store Unlimited invoices 
                                    <span class="pull-right text-green">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Imventory Management
                                    <span class="pull-right text-green">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Track your sales & Expense
                                    <span class="pull-right text-green">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Receive Inventory expiry alert
                                    <span class="pull-right text-green">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Seamless billing with barcode scanning
                                    <span class="pull-right text-green">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </span>
                                </a>
                            </li>
                        </ul>
                        <div class="row">

                            <?php for($i=0;$i<count($subscription_plans);$i++){ ?>
                                <div class="col-sm-6 col-xs-6 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header"> <i class="fa fa-inr"></i> <?php echo round($subscription_plans[$i]['TotalAmount']+(($subscription_plans[$i]['TaxPercentage']/100)*$subscription_plans[$i]['TotalAmount']),2); ?> Per User</h5>
                                        <span class="description-text"><?php echo $subscription_plans[$i]['PlanName']; ?></span>
                                        <br><br>
                                        <button href="javascript:void(0)" data-subscription_plan_id="<?php echo $subscription_plans[$i]['SubscriptionPlanID']; ?>" class="btn bg-olive btn-block buy-subscription">Buy now</button>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
      </div>
    </div>

  </div>
</div>

<div id="importUsersModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
    <?php echo form_open_multipart(); ?>
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Import Users</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label for="">User Excel [csv, xls, xlsx] [max 2 mb]</label>
            <input type="file" name="UserExcel">
            <input type="hidden" name="NA" value="NA">
            <span class="text-danger"><?php echo validation_show_error('UserExcel'); ?></span>
        </div>
      </div>
      <div class="modal-footer">
        <a href="<?php echo base_url('assets/samples/user-import-sample.xlsx'); ?>" download class="btn btn-info pull-left">Download format</a>
        <button type="submit" class="btn btn-success">Import</button>
      </div>
    </div>
    <?php echo form_close(); ?>
  </div>
</div>
<?php } ?>