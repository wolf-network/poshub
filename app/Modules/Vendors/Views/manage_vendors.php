<?php 
    $btn_class = '';
    $datatable_hide_class = '';
    if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
        $btn_class .= 'disabled';
        $datatable_hide_class = 'datatable-hide-search';
    }
?>

<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="box box-success">
            <div class="box-header">
                <h3 class="box-title">Manage Vendors</h3>
                <div class="pull-right">
                    <a href="https://www.youtube.com/watch?v=hQENH21lojE" target="_blank" class="btn btn-info">Watch tutorial</a>
                    <a href="<?php echo base_url('add-vendor'); ?>" class="btn btn-success <?php echo $btn_class; ?>">Add Vendor</a>
                </div>
            </div>
            <div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap <?php echo $datatable_hide_class; ?>">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/vendors/get_vendors') ?>">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th id="VendorName">Vendor Name</th>
                                <th data-render="true" data-render_html="{VendorUserFirstName} {VendorUserLastName}">Contact Person</th>
                                <th id="VendorUserEmailID">Email ID</th>
                                <th id="VendorUserContactNo">Contact No</th>
                                <th id="Name">Added By</th>

                                <?php
                                    $action_btn = '';
                                    if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){
                                        $action_btn .= "";    
                                        if(session()->get('user_data')['Privilege'] == 'Admin'){ 
                                            $action_btn .= "<a href='".base_url('edit-vendor/')."{VendorID}' class='btn btn-warning btn-xs' title='edit'><i class='fa fa-edit'></i></a> <button data-vendor_id='{VendorID}' class='btn btn-danger btn-xs delete-vendor' title='delete'><i class='fa fa-trash'></i></button> ";
                                        }
                                    }
                                ?>

                                <th data-render="true" data-render_html="<a href='javascript:void(0)' data-vendor_id='{VendorID}' data-toggle='modal' data-target='#manageVendorDetailsModal' class='btn btn-info btn-xs manage-vendor-details'><i class='fa fa-cogs'></i></a> <?php echo $action_btn; ?>" data-sortable="false" >Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manage Details Modal -->
<div id="manageVendorDetailsModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Manage Vendor Details</h4>
      </div>
      <div class="modal-body">
        <a href="javascript:void(0)" id="manage-vendor-documents" class="btn btn-info">Manage Documents</a>
        <a href="javascript:void(0)" id="manage-vendor-service-taxes" class="btn btn-info">Manage Service Taxes</a>
        <a href="javascript:void(0)" id="view-vendor-expenses" class="btn btn-info">View Expenses</a>
        <a href="javascript:void(0)" id="view-vendor-inwards" class="btn btn-info">View Vendor Inwards</a>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>