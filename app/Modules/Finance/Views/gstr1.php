<div class="row">
    <div class="col-md-12">
        <form action="javascript:void(0)" id="form-filter">
            <div class="box box-success">
            	<div class="box-header">
            		<h3 class="pull-left box-title">Filter</h3>
                    <div class="pull-right">
                        <a href="https://www.youtube.com/watch?v=Od8F9UDBtEk" target="_blank" class="btn btn-info">Watch tutorial</a>
                    </div>
            	</div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="CompanyGST">Your GST No. <span class="text-danger">*</span> 
                                    <a href="<?php echo base_url('add-company-service-tax'); ?>" target="_blank">Add Billing Address</a>
                                </label>
                                <select name="CompanyGST" id="CompanyGST" class="form-control">
                                <?php for($i=0;$i<count($company_service_tax_master);$i++){ ?><option value="<?php echo $company_service_tax_master[$i]['ServiceTaxIdentificationNumber']; ?>"><?php echo $company_service_tax_master[$i]['ServiceTaxIdentificationNumber']; ?></option><?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="InvoiceDateFrom">Invoice Date from <span class="text-danger">*</span></label>
                                <input type="text" name="InvoiceDateFrom" id="InvoiceDateFrom" data-max-date="<?php echo date('Y-m-d'); ?>" class="form-control daterangepicker">
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group">
                                <label for="InvoiceDateTo">Invoice Date to <span class="text-danger">*</span></label>
                                <input type="text" name="InvoiceDateTo" id="InvoiceDateTo" data-max-date="<?php echo date('Y-m-d'); ?>" class="form-control daterangepicker">
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-6">
                            <div class="form-group">
                                <label for="InvoiceType">Invoice Type</label>
                                <select name="InvoiceType" id="InvoiceType" class="form-control">
                                    <option value="">Show All</option>
                                    <option value="b2b">Regular B2B</option>
                                    <option value="b2c">B2C</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="button" id="form-filter-btn" class="btn btn-warning pull-right filter-gstr1">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header">
                <h3 class="pull-left box-title">GSTR-1 Reports</h3>
                <div class="pull-right">
                    <a href="<?php echo base_url('export-gstr1-excel'); ?>" id="export-gstr1" class="btn btn-success export-excel" data-loader="false">Export</a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/finance_reports/get_gstr1') ?>" data-responsive="true">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th id="HSN" data-sortable="false">HSN</th>
                                <th id="ClientInvoiceDate" data-sortable="false">Invoice Date</th>
                                <th id="TotalAmount" data-sortable="false">Invoice Value</th>
                                <th id="place_of_supply" data-sortable="false">Place Of Supply</th>
                                <th data-render="true" data-render_html="N" data-sortable="false">Reverse Charge</th>
                                <th data-render="true" data-render_html="" data-sortable="false">Applicable % of Tax Rate</th>
                                <th id="invoice_type">Invoice Type</th>
                                <th data-render="true" data-render_html="" data-sortable="false">E-commerce GSTIN</th>
                                <th id="TotalTaxPercentage" data-sortable="false">Rate</th>
                                <th id="taxable_value" data-sortable="false">Taxable Value</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>