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
        <?php if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){ ?>
        <div class="box box-success">
            <form action="javascript:void(0)" id="form-filter">
                <input type="hidden" name="amountFilter" id="amountFilter">
                <div class="box-header">
                    <h3 class="box-title">Filters</h3>
                </div>    
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="PurchaseOrderStatusID">Purchase order status</label>
                                <select name="PurchaseOrderStatusID" id="PurchaseOrderStatusID" class="form-control">
                                    <option value="">Select Status</option>
                                    <?php for ($i=0; $i <count($purchase_order_status) ; $i++) { ?>
                                    <option value="<?php echo $purchase_order_status[$i]['PurchaseOrderStatusID']; ?>"><?php echo $purchase_order_status[$i]['PurchaseOrderStatus']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="DeliveryDateFrom">Delivery Date from</label>
                                <input type="text" name="DeliveryDateFrom" id="DeliveryDateFrom" class="form-control daterangepicker">
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="DeliveryDateTo">Delivery date to</label>
                                <input type="text" name="DeliveryDateTo" id="DeliveryDateTo" class="form-control daterangepicker">
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
                <h3 class="pull-left box-title">Manage Purchase Orders</h3>
                <div class="pull-right">
                    <a href="https://www.youtube.com/watch?v=FpLhXoddUO4" target="_blank" class="btn btn-info">Watch tutorial</a>
                    <a href="<?php echo base_url('create-purchase-order'); ?>" class="btn btn-success <?php echo $btn_class; ?>">Create purchase order</a>
                </div>
            </div>
            <div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap table-responsive <?php echo $datatable_hide_class; ?>">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/finance/get_purchase_orders') ?>" data-responsive="true">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th id="PurchaseOrderNo">PO No</th>
                                <th id="VendorName">Vendor</th>
                                <th id="VendorContactNo">Contact No</th>
                                <th id="DeliveryDate">Delivery Date</th>
                                <th id="TotalAmount">Total</th>
                                <th id="PurchaseOrderStatus">PO Status</th>
                                <th data-render="true" data-render_html="<a href='<?php echo base_url('manage-purchase-order-details/'); ?>{PurchaseOrderID}' title='Manage Purchase Order Details' class='btn btn-info btn-xs'><i class='fa fa-file-text-o'></i></a>" data-sortable="false" >Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>