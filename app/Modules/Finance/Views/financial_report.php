<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
        	<div class="box-header">
        		<h3 class="pull-left box-title">Financial Reports</h3>
        		<div class="pull-right">
        			<a href="https://www.youtube.com/watch?v=Od8F9UDBtEk" target="_blank" class="btn btn-info">Watch tutorial</a>
        		</div>
        	</div>
        	<div class="box-body">
        		<div id="finance-report-filter" class="row">
        			<div class="col-md-4">
        				<div class="form-group">
        					<label for="FromDate">From Date</label>
        					<input type="text" name="FromDate" id="FromDate" class="form-control daterangepicker" data-max-date="<?php echo date('Y-m-d'); ?>">
        				</div>
        			</div>
        			<div class="col-md-4">
        				<div class="form-group">
        					<label for="ToDate">To Date</label>
        					<input type="text" name="ToDate" id="ToDate" class="form-control daterangepicker" data-max-date="<?php echo date('Y-m-d'); ?>">
        				</div>
        			</div>
        			<div class="col-md-4">
        				<label for="">&nbsp;</label> <br>
        				<button type="button" class="btn btn-success fetch-reports">Get Report</button>
        			</div>
        		</div>
        	</div>
        </div>
    </div>
</div>

<div class="row">
	<div class="col-md-4 col-sm-6 col-xs-12">
			<div class="info-box">
			<span class="info-box-icon bg-blue"><i class="fa fa-line-chart"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Total Sales</span>
				<span class="info-box-number"><span class="total-sales-num">0</span>/-</span>
			</div>
		</div>
	</div>
	<div class="col-md-4 col-sm-6 col-xs-12">
			<div class="info-box">
			<span class="info-box-icon bg-aqua"><i class="fa fa-hourglass-end" aria-hidden="true"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Total Received</span>
				<span class="info-box-number"><span class="total-received-num">0</span>/-</span>
			</div>
		</div>
	</div>
	<div class="col-md-4 col-sm-6 col-xs-12">
			<div class="info-box">
			<span class="info-box-icon bg-red"><i class="fa fa-file-text-o"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Goods & Service Tax</span>
				<span class="info-box-number"><span class="total-service-tax-num">0</span>/-</span>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-4 col-sm-6 col-xs-12">
			<div class="info-box">
			<span class="info-box-icon bg-maroon"><i class="fa fa-credit-card"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Expenses</span>
				<span class="info-box-number"><span class="total-expense-num">0</span>/-</span>
			</div>
		</div>
	</div>
	<div class="col-md-4 col-sm-6 col-xs-12">
			<div class="info-box">
			<span class="info-box-icon bg-green"><i class="fa fa-money" aria-hidden="true"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Profit</span>
				<span class="info-box-number"><span class="total-profit-num">0</span>/-</span>
			</div>
		</div>
	</div>
</div>