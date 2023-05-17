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
                <h3 class="pull-left box-title">Outward History</h3>
            </div>
        </div>
    </div>
</div>

<?php 
    if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){
                ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <form action="javascript:void(0)" id="form-filter">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Outward Date From</label>
                                <input type="text" name="OutwardDateFrom" id="OutwardDateFrom" class="form-control daterangepicker" data-max-date="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Outward Date To</label>
                                <input type="text" name="OutwardDateTo" id="OutwardDateTo" class="form-control daterangepicker" data-max-date="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="row">
                        <div class="col-md-10">&nbsp;</div>
                        <div class="col-md-2">
                            <button type="button" id="form-filter-btn" class="btn btn-warning btn-block">Filter</button>    
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap table-responsive <?php echo $datatable_hide_class; ?>">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/stock/get_outward_logs') ?>" data-responsive="true">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th id="Particular">Item</th>
                                <th id="HSN">HSN</th>
                                <th id="SerialNo">Serial No</th>
                                <th id="PricePerUnit">Price Per Unit</th>
                                <th id="Quantity">Qty</th>
                                <th id="TotalTaxPercentage">Total Tax Percentage</th>
                                <th id="tax_amount">Tax Amount</th>
                                <th id="TotalAmount">Total Amount</th>
                                <th id="ClientInvoiceDate">Outward date</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>