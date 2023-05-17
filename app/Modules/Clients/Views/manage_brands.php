<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header">
                <h3 class="pull-left box-title">Manage Brands</h3>
                <a href="<?php echo base_url('add-brand/'.$client_id); ?>" class="btn btn-success pull-right">Add Brand</a>
            </div>
            <div class="box-body">
            <div class="dataTables_wrapper form-inline dt-bootstrap">
                <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/clients/get_brands?ClientID='.$client_id) ?>">
                    <thead>
                        <tr>
                            <th data-sortable="false">Sr.</th>
                            <th id="Brand">Brand</th>
                            <th id="Name">Added By</th>
                            <th data-render="true" data-render_html="<a href='<?php echo base_url('edit-brand/{ClientID}/{BrandID}'); ?>' class='btn btn-warning btn-xs' title='edit'><i class='fa fa-edit'></i></a> <a href='<?php echo base_url('delete-brand/{ClientID}/{BrandID}'); ?>' class='btn btn-danger btn-xs' title='delete'><i class='fa fa-trash'></i></a>">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        </div>
    </div>
</div>