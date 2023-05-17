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
        		<input type="hidden" name="ClientID" value="<?php echo $client_id ?>" id="ClientID">
        		<div class="box-header">
                    <h3 class="box-title">Filters</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="CreditNoteDateFrom">Credit Note Date from</label>
                                <input type="text" name="CreditNoteDateFrom" id="CreditNoteDateFrom" class="form-control daterangepicker">
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="CreditNoteDateTo">Credit Note Date to</label>
                                <input type="text" name="CreditNoteDateTo" id="CreditNoteDateTo" class="form-control daterangepicker">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="button" id="form-reset-btn" class="btn btn-danger">Reset</button>
                        <button type="button" id="form-filter-btn" class="btn btn-warning filter-credit-note">Filter</button>
                    </div>
                </div>
        	</form>
        </div>
        <?php } ?>

		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title pull-left">Manage Credit Notes</h3>
				<div class="pull-right">
                    <a href="https://www.youtube.com/watch?v=iTKIGeIRX88" target="_blank" class="btn btn-info">Watch tutorial</a>
					<a href="<?php echo base_url('create-credit-note'); ?>" class="btn btn-success <?php echo $btn_class; ?>">Create Credit Note</a>
					<a href="<?php echo base_url('export-credit-notes'); ?>" data-loader="false" class="btn btn-warning export-credit-note <?php echo $btn_class; ?>">Export Credit Notes</a>
				</div>
			</div>
			<div class="box-body">
				<div class="dataTables_wrapper form-inline dt-bootstrap table-responsive <?php echo $datatable_hide_class; ?>">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/finance/get_credit_notes') ?>">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th id="CreditNoteNo">Credit Note No</th>
                                <th id="InvoiceNo">Original Invoice No</th>
                                <th id="ClientName">Client</th>
                                <th id="CreditNoteDate">Credit Note Date</th>
                                <th id="Reason">Reason</th>
                                <th id="PayableAmount">Payable Amount</th>
                                <th id="PaymentStatus">Payment Status</th>
                                <?php
                                	$action_cond['PaymentStatus'] = [
                                		'Unpaid' => [
                                			'html' => '<a href="'.base_url('manage-credit-note-details/').'{CreditNoteID}" title="View Credit Note Details" class="btn btn-info btn-xs"><i class="fa fa-file-text-o"></i></a> <button type="button" title="Delete Credit Note ID" data-credit_note_id="{CreditNoteID}" class="btn btn-danger btn-xs delete-credit-note"><i class="fa fa-trash"></i></button>'
                                		],
                                		'default' => [
                                			'html' => '<a href="'.base_url('manage-credit-note-details/').'{CreditNoteID}" title="View Credit Note Details" class="btn btn-info btn-xs"><i class="fa fa-file-text-o"></i></a>'
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
</div>