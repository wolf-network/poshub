<?php 
    $btn_class = '';
    $datatable_hide_class = '';
    if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
        $btn_class .= 'disabled';
        $datatable_hide_class = 'datatable-hide-search';
    }
?>

<div class="row">
    <div class="col-md-4 col-lg-3 col-sm-6 col-xs-12">
        <div class="info-box pointer amount-filter-box" data-filter="sales">
            <span class="info-box-icon bg-blue"><i class="fa fa-bar-chart" aria-hidden="true"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Sales</span>
                <span class="info-box-number total-sales"></span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-3 col-xs-12">
        <div class="info-box pointer amount-filter-box" data-filter="received">
            <span class="info-box-icon bg-green"><i class="fa fa-hourglass-end"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Received</span>
                <span class="info-box-number received-amount"></span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-3 col-sm-6 col-xs-12">
        <div class="info-box pointer amount-filter-box" data-filter="receivables">
            <span class="info-box-icon bg-red"><i class="fa fa-hourglass-start"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Outstandings</span>
                <span class="info-box-number outstanding"></span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){ ?>
        <div class="box box-success">
            <form action="javascript:void(0)" id="form-filter">
                <input type="hidden" name="amountFilter" id="amountFilter">
                <input type="hidden" name="ClientID" value="<?php echo $client_id ?>" id="ClientID">
                <div class="box-header">
                    <h3 class="box-title">Filters</h3>
                </div>    
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="InvoiceDateFrom">Invoice Date from</label>
                                <input type="text" name="InvoiceDateFrom" id="InvoiceDateFrom" class="form-control daterangepicker">
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="InvoiceDateTo">Invoice Date to</label>
                                <input type="text" name="InvoiceDateTo" id="InvoiceDateTo" class="form-control daterangepicker">
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="DueDateFrom">Due from</label>
                                <input type="text" name="DueDateFrom" id="DueDateFrom" class="form-control daterangepicker">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="button" id="form-reset-btn" class="btn btn-danger">Reset</button>
                        <button type="button" id="form-filter-btn" class="btn btn-warning filter-invoice">Filter</button>
                    </div>
                </div>
            </form>
        </div>
        <?php } ?>

        <div class="box box-success">
            <div class="box-header">
                <h3 class="pull-left box-title">Manage Invoices</h3>
                <div class="pull-right">
                    <a href="https://www.youtube.com/watch?v=TBsrcrizovg" target="_blank" class="btn btn-info">Watch tutorial</a>
                    <a href="<?php echo base_url('create-invoice'); ?>" class="btn btn-success <?php echo $btn_class; ?>">Add Invoice</a>
                    <a href="<?php echo base_url('export-excel-invoices?ClientID='.$client_id); ?>" download class="btn btn-warning export-excel <?php echo $btn_class; ?>">Export Excel</a>
                </div>
            </div>
            <div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap table-responsive <?php echo $datatable_hide_class; ?>">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/finance/get_invoices') ?>">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th id="InvoiceNo">Invoice No</th>
                                <th id="ClientName">Client</th>
                                <th id="ClientContactNo">Contact No</th>
                                <th id="ClientInvoiceDate">Invoice Date</th>
                                <th id="ClientInvoiceDueDate">Due Date</th>
                                <th id="TotalAmount">Total</th>
                                <th id="PaidAmount">Received</th>
                                <th id="OutstandingAmount">Outstanding</th>
                                <?php 
                                    $action_cond['ReceiptID'] = [
                                        'null' => [
                                            'html' => '<a href="'.base_url('download-invoice/').'{InvoiceID}" data-loader="false" title="Manage Invoice Details" class="btn btn-primary btn-xs"><i class="fa fa-download"></i></a> <a href="'.base_url('manage-invoice-details/').'{InvoiceID}" title="Manage Invoice Details" class="btn btn-info btn-xs"><i class="fa fa-file-text-o"></i></a> <a href="'.base_url('manage-receipts/').'{InvoiceID}" title="Manage Receipts" class="btn bg-maroon btn-xs"><i class="fa fa-book"></i></a> <button type="button" data-invoice_id="{InvoiceID}" class="btn btn-danger btn-xs delete-invoice"><i class="fa fa-trash"></i></button>'
                                        ],
                                        'default' => [
                                            'html' => '<a href="'.base_url('download-invoice/').'{InvoiceID}" data-loader="false" title="Manage Invoice Details" class="btn btn-primary btn-xs"><i class="fa fa-download"></i></a> <a href="'.base_url('manage-invoice-details/').'{InvoiceID}" title="Manage Invoice Details" class="btn btn-info btn-xs"><i class="fa fa-file-text-o"></i></a> <a href="'.base_url('manage-receipts/').'{InvoiceID}" title="Manage Receipts" class="btn bg-maroon btn-xs"><i class="fa fa-book"></i></a>'
                                        ]
                                    ];
                                ?>
                                <th data-condition_render='<?php echo json_encode($action_cond); ?>' data-sortable="false" >Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>