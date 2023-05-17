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
                                <label for="ExpiryDays">Items expiring in</label>
                                <select name="ExpiryDays" id="ExpiryDays" class="form-control pull-right">
                                  <option value="">Select items expiring in</option>
                                  <?php
                                    $expiring_duration = (!empty($_GET['duration']))?$_GET['duration']:'';
                                    $prev_days_arr = ['7 days','14 days','28 days','30 days','3 months','6 months','1 year'];
                                    for($i=0;$i<count($prev_days_arr);$i++){
                                  ?>
                                  <option value="<?php echo $prev_days_arr[$i]; ?>" <?php echo($expiring_duration == $prev_days_arr[$i])?'selected':''; ?> > <?php echo $prev_days_arr[$i]; ?></option>
                                  <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="VendorID">Select Vendor</label>
                                <select name="VendorID" id="VendorID" class="form-control">
                                    <option value="">Select Vendor</option>
                                    <?php for($i=0;$i<count($vendors);$i++){ ?>
                                    <option value="<?php echo $vendors[$i]['VendorID']; ?>"><?php echo $vendors[$i]['VendorName']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="button" id="form-reset-btn" class="btn btn-danger">Reset</button>
                        <button type="button" id="form-filter-btn" class="btn btn-warning filter-expiring-items">Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <?php } ?>

        <div class="box box-success">
            <div class="box-header">
                <h3 class="pull-left box-title">View Expiring Items</h3>
                <a href="<?php echo base_url('export-expiring-items'); ?>" data-loader="false" class="btn btn-success pull-right export-excel">Export Expiring Items</a>
            </div>
            <div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap table-responsive datatable-hide-search">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/stock/get_expiring_items') ?>" data-responsive="true">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th id="Item">Item</th>
                                <th id="VendorName">Vendor</th>
                                <th id="BatchNo">Batch No</th>
                                <th id="RemainingQty">Returnable Units</th>
                                <th id="ManufacturingDate">Manufacturing Date</th>
                                <th data-render="true" data-render_html="{ExpiryDate} ({expiry_days_left} Days Left)">Expires on</th>
                                <th data-render="true" data-sortable="false" data-render_html="<button type='button' id='stock-return-btn' class='btn btn-danger' data-stock_inward_history_id='{StockInwardHistoryID}' data-inward_date='{InwardDate}' data-toggle='modal' data-target='#itemReturnModal'>Returned</button>">Action</th>
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