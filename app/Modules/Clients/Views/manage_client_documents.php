<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="box box-success">
            <div class="box-header">
                <h3 class="box-title">Manage Client Documents</h3>
                <a href="<?php echo base_url('add-client-document/'.$client_id); ?>" class="btn btn-success pull-right">Add Client Document</a>
            </div>
            <div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/clients/get_client_documents?ClientID='.$client_id) ?>">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th id="CountryName">Country</th>
                                <th id="StateName">State</th>
                                <th data-render="true" data-render_html="<button class='btn btn-info btn-xs view-documents' title='View Documents' data-toggle='modal' data-target='#clientDocumentModal' data-client_geography_id='{ClientGeographyID}'><i class='fa fa-eye'></i></button>">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>




<!-- Modal -->
<div id="clientDocumentModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Client Document</h4>
      </div>
      <div class="modal-body">
        <table class="table table-bordered client-documents-table">
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