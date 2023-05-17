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
                <input type="hidden" name="VendorID" value="<?php echo(!empty($_GET['vendor_id']))?$_GET['vendor_id']:''; ?>">
                <div class="box-header">
                    <h3 class="box-title">Filters</h3>
                </div>    
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="ExpenseDateFrom">Expense Date from</label>
                                <input type="text" name="ExpenseDateFrom" id="ExpenseDateFrom" class="form-control daterangepicker">
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="ExpenseDateTo">Expense Date to</label>
                                <input type="text" name="ExpenseDateTo" id="ExpenseDateTo" class="form-control daterangepicker">
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="FilterTaxAmount">Filter Taxable Expense</label>
                                <select name="TaxAmount" id="FilterTaxAmount" class="form-control">
                                    <option value="">Show All</option>
                                    <option value="Taxable">Show Taxable Only</option>
                                    <option value="Non-Taxable">Show Non-Taxable Only</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="button" id="form-reset-btn" class="btn btn-danger">Reset</button>
                        <button type="button" id="form-filter-btn" class="btn btn-warning filter-expenses">Filter</button>
                    </div>
                </div>
            </form>
        </div>
        <?php } ?>

        <div class="box box-success">
            <div class="box-header">
                <h3 class="pull-left box-title">View Expenses</h3>
                <div class="pull-right">
                    <a href="https://www.youtube.com/watch?v=Cw7YuTfkqd4" target="_blank" class="btn btn-info">Watch tutorial</a>
                    <a href="<?php echo base_url('add-expense'); ?>" class="btn btn-success <?php echo $btn_class; ?>">Add Expense</a>
                    <a href="<?php echo base_url('export-expenses'); ?>" class="btn btn-warning export-excel <?php echo $btn_class; ?>" data-loader="false">Export Excel</a>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/finance/get_expenses') ?>" data-responsive="true">
                    <thead>
                        <tr>
                            <th data-sortable="false">Sr.</th>
                            <th id="ExpenseHead">Head</th>
                            <th id="VendorName">Vendor</th>
                            <th id="ExpenseDate">Expense Date</th>
                            <th id="ExpenseAmount">Expense Amount</th>
                            <th id="TaxAmount">Tax Amount</th>
                            <th id="Remarks">Remarks</th>
                            <th id="Name">Added By</th>
                            <?php
                                $action_btn = '';
                                if($subscription_time_left['years'] >=0 && $subscription_time_left['months'] >=0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0 && $user_data['Privilege'] == 'Admin'){

                                    $action_btn .= '<button title="Delete Expense" class="btn btn-danger btn-xs delete-expense" data-expense_id="{ExpenseID}"><i class="fa fa-trash"></i></button>';
                                }

                                $action_cond['AttachedDocumentPath'] = [
                                    'null' => [
                                        'html' => $action_btn
                                    ],
                                    'default' => [
                                        'html' => '<a href="'.media_server('{AttachedDocumentPath}').'" class="btn btn-info btn-xs" download><i class="fa fa-download"></i></a> '.$action_btn
                                    ]
                                ];
                            ?>
                            <th data-condition_render='<?php echo json_encode($action_cond); ?>' data-sortable="false">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>