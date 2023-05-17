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
                <h3 class="box-title">Manage Vendor Documents</h3>
                <a href="<?php echo base_url('add-vendor-document/'.$vendor_id); ?>" class="btn btn-success pull-right <?php echo $btn_class; ?>">Add Vendor Document</a>
            </div>
            <div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap <?php echo $datatable_hide_class; ?>">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/vendors/get_vendor_documents?VendorID='.$vendor_id) ?>">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th id="CountryName">Country</th>
                                <th id="StateName">State</th>
                                <th data-render="true" data-render_html="<button class='btn btn-info btn-xs view-documents' title='View Documents' data-toggle='modal' data-target='#vendorDocumentModal' data-vendor_geography_id='{VendorGeographyID}'><i class='fa fa-eye'></i></button>">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>




<!-- Modal -->
<div id="vendorDocumentModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Vendor Document</h4>
      </div>
      <div class="modal-body">
        <table class="table table-bordered vendor-documents-table">
            <thead>
                <tr>
                    <th>Sr</th>
                    <th>Document Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>