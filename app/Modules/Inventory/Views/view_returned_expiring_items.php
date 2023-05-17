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
        <?php if(empty($datatable_hide_class)){ ?>
            
        <div class="box box-success">
            <form action="javascript:void(0)" id="form-filter">
                <div class="box-header">
                    <h3 class="box-title">Filters</h3>
                </div>    
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="Vendor">Select Vendor</label>
                                <select name="Vendor" class="form-control">
                                    <option value="">Select Vendor</option>
                                    <?php for($i=0;$i<count($vendors);$i++){ ?>
                                    <option value="<?php echo $vendors[$i]['Vendor']; ?>"><?php echo $vendors[$i]['Vendor']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="ReturnDateFrom">Return date from</label>
                                <input type="text" name="ReturnDateFrom" class="form-control daterangepicker" data-min-date="<?php echo date('Y-m-d', strtotime($min_max_return_dates['min_return_date'])); ?>" data-max-date="<?php echo date('Y-m-d', strtotime($min_max_return_dates['max_return_date'])); ?>">
                            </div>
                        </div>

                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="ReturnDateTo">Return date to</label>
                                <input type="text" name="ReturnDateTo" class="form-control daterangepicker" data-min-date="<?php echo date('Y-m-d', strtotime($min_max_return_dates['min_return_date'])); ?>" data-max-date="<?php echo date('Y-m-d', strtotime($min_max_return_dates['max_return_date'])); ?>">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="button" id="form-reset-btn" class="btn btn-danger">Reset</button>
                        <button type="button" id="form-filter-btn" class="btn btn-warning">Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <?php } ?>

        <div class="box box-success">
            <div class="box-header">
                <h3 class="pull-left box-title">View Returned Expiring Stocks</h3>
            </div>
            <div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap table-responsive datatable-hide-search">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/stock/get_returned_expiring_items') ?>" data-responsive="true">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th id="Item" data-sortable="false">Item</th>
                                <th id="Vendor" data-sortable="false">Vendor</th>
                                <th id="BatchNo" data-sortable="false">Batch No</th>
                                <th id="UnitsReturned" data-sortable="false">Units Returned </th>
                                <th id="ReturnDate" data-sortable="false">Return Date</th>
                                <th id="VendorRepresentativeName" data-sortable="false">Vendor Representative Name</th>
                                <th id="VendorRepresentativeEmail" data-sortable="false">Vendor Representative Email</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="itemReturnModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Return Item</h4>
      </div>
      <div class="modal-body">
        <form action="javascript:void(0)" id="return-expiring-stock-form">
            <div class="form-group">
                <label for="">Item</label>
                <input type="text" value="Parle G" disabled class="form-control">
                <input type="hidden" name="StockInwardHistoryID" id="StockInwardHistoryID">
            </div>
            <div class="form-group">
                <label for="">Vendor</label>
                <input type="text" value="Content Fluence" disabled class="form-control">
            </div>
            <div class="form-group">
                <label for="">Batch No.</label>
                <input type="text" value="001" disabled class="form-control">
            </div>
            <div class="form-group">
                <label for="UnitsReturned">Units Returned <span class="text-red">*</span></label>
                <input type="text" name="UnitsReturned" value="4" id="UnitsReturned" class="form-control">
            </div>
            <div class="form-group">
                <label for="ReturnDate">Return date <span class="text-red">*</span></label>
                <input type="text" name="ReturnDate" value="" id="ReturnDate" class="form-control daterangepicker" data-max-date="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label for="VendorRepresentativeName">Vendor representative name <span class="text-red">*</span></label>
                <input type="text" name="VendorRepresentativeName" value="" id="VendorRepresentativeName" class="form-control">
            </div>
            <div class="form-group">
                <label for="VendorRepresentativeEmail">Vendor representative email <span class="text-red">*</span></label>
                <input type="text" name="VendorRepresentativeEmail" value="" id="VendorRepresentativeEmail" class="form-control">
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="return-expiring-stock-btn" class="btn btn-success">Save</button>
      </div>
    </div>

  </div>
</div>