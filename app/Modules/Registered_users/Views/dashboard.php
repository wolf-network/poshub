<div class="row">

  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
      <a href="<?php echo base_url('manage-items'); ?>">
        <span class="info-box-icon bg-green"><i class="fa fa-tags"></i></span>
      </a>

      <div class="info-box-content">
        <span class="info-box-add pull-right">
          <a href="<?php echo base_url('add-item'); ?>" title="Add Item" class="text-green">
            <i class="fa fa-2x fa-plus-square" aria-hidden="true"></i>
          </a>
        </span>
        <a href="<?php echo base_url('manage-items'); ?>" class="text-black">
          <span class="info-box-text">Items</span>
          <span class="info-box-number"><?php echo(!empty($items_count))?$items_count:0; ?></span>
        </a>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div> 
   <!-- /.col -->

  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
      <a href="<?php echo base_url('manage-invoices'); ?>">
        <span class="info-box-icon bg-red"><i class="fa fa-money"></i></span>
      </a>

      <div class="info-box-content">
        <a href="<?php echo base_url('manage-invoices'); ?>" class="text-black">
          <span class="info-box-text">Today's Revenue</span>
          <span class="info-box-number"><?php echo round($revenue, PHP_ROUND_HALF_DOWN); ?></span>
        </a>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div> 
   <!-- /.col -->

  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
      <a href="<?php echo base_url('manage-users'); ?>">
        <span class="info-box-icon bg-blue"><i class="fa fa-user"></i></span>
      </a>

      <div class="info-box-content">
        <span class="info-box-add pull-right">
          <a href="<?php echo base_url('add-user'); ?>" title="Add User" class="text-blue">
            <i class="fa fa-2x fa-plus-square" aria-hidden="true"></i>
          </a>
        </span>
        <a href="<?php echo base_url('manage-users'); ?>" class="text-black">
          <span class="info-box-text">Users</span>
          <span class="info-box-number"><?php echo $basic_statistics['total_users']; ?></span>
        </a>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div> 
   <!-- /.col -->

  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
      <a href="<?php echo base_url('manage-vendors'); ?>">
        <span class="info-box-icon bg-yellow"><i class="fa fa-user-secret"></i></span>
      </a>

      <div class="info-box-content">
        <span class="info-box-add pull-right">
          <a href="<?php echo base_url('add-vendor'); ?>" title="Add Vendor" class="text-yellow">
            <i class="fa fa-2x fa-plus-square" aria-hidden="true"></i>
          </a>
        </span>
        <a href="<?php echo base_url('manage-vendors'); ?>" class="text-black">
          <span class="info-box-text">Vendors</span>
          <span class="info-box-number"><?php echo $basic_statistics['total_vendors']; ?></span>
        </a>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div> 
   <!-- /.col -->
</div>

<div class="row">
  <div class="col-md-7">
    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title">Inward/Outward reports (Recent 10 records)</h3>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <tr>
              <th>Sr</th>
              <th>Item</th>
              <th>HSN</th>
              <th>Opening Stock</th>
              <th>Inward Stocks</th>
              <th>Outward Stocks</th>
              <th>Closing Stocks</th>
              <th>Report Date</th>
            </tr>
            <?php for($i=0;$i<count($inward_outward_reports);$i++){ ?>
              <tr>
                <td><?php echo $i+1; ?></td>
                <td><?php echo $inward_outward_reports[$i]['Item'] ?></td>
                <td><?php echo $inward_outward_reports[$i]['HSN'] ?></td>
                <td><?php echo $inward_outward_reports[$i]['OpeningStockQty'] ?></td>
                <td><?php echo $inward_outward_reports[$i]['InwardStockQty'] ?></td>
                <td><?php echo $inward_outward_reports[$i]['OutwardStockQty'] ?></td>
                <td><?php echo $inward_outward_reports[$i]['ClosingStockQty'] ?></td>
                <td><?php echo $inward_outward_reports[$i]['ReportDate'] ?></td>
              </tr>
            <?php } ?>
          </table>
        </div>
      </div>
      <div class="box-footer">
        <a href="<?php echo base_url('stock-inward-outward-report'); ?>" class="btn btn-info btn-sm pull-right">View All</a>
      </div>
    </div>

    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title text-center"> Category Wise Sale (Jan - Dec <?php echo date('Y')?>)</h3>
        <div class="box-body">
          <?php 
            for($i=0;$i<count($category_wise_sales);$i++){
              $category_labels[] = ($category_wise_sales[$i]['ItemCategory'] != null)?$category_wise_sales[$i]['ItemCategory']:'NA';
              $category_datasets[] = $category_wise_sales[$i]['total_sales'];
            }

            if(!empty($category_labels)){
              $category_wise_arr = [
                'labels' => $category_labels,
                'datasets' => [
                  [
                    'data' => $category_datasets,
                    'backgroundColor' => '#026972'
                  ],
                ]
              ];
          ?>
          <canvas class="bar-chart" data-chart_data='<?php echo json_encode($category_wise_arr); ?>' data-chart_type="horizontalBar"></canvas>
          <?php } ?>
        </div>
      </div>
    </div>

        <div class="box box-success received-outstanding-container">
      <div class="box-header with-border">
        <div class="row">
          <div class="col-md-6">
            <h3 class="box-title text-center pull-left"> Received v/s Outstanding</h3>
          </div>
          <div class="col-md-6">
            <select name="" id="financial-date-filter" class="form-control pull-right">
              <?php 
                $prev_days_arr = ['7 days','14 days','28 days','30 days','3 months','6 months','1 year'];
                for($i=0;$i<count($prev_days_arr);$i++){
              ?>
              <option value="<?php echo date('Y-m-d', strtotime('-'.$prev_days_arr[$i])); ?>"> Since Last <?php echo $prev_days_arr[$i]; ?></option>
              <?php } ?>
              <option value="<?php echo date('Y-m-d'); ?>">Today</option>
            </select>
          </div>
        </div>
      </div>
      <div class="box-body text-center">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw div-loader"></i>
        <div id="financials-pie-chart-container">
          
        </div>
        <br>
        <div class="row">
          <div class="col-md-4">
            <div class="panel panel-default">
              <div class="panel-heading text-center">Total Sales</div>
              <div class="panel-body text-center">
                <b class="total-sales">0</b>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="panel panel-default">
              <div class="panel-heading text-center">Received</div>
              <div class="panel-body text-center">
                <b class="total-received">0</b>
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="panel panel-default">
              <div class="panel-heading text-center">Outstanding</div>
              <div class="panel-body text-center">
                <b class="total-outstanding">0</b>
              </div>
            </div>
          </div>

        </div>
      </div>
      <div class="box-footer">
        <a href="<?php echo base_url('manage-invoices'); ?>" class="btn btn-info btn-sm pull-right">View complete reports</a>
      </div>
    </div>

  </div>

  <div class="col-md-5">

    <div class="reminder-container">
      <div class="box box-danger">
        <div class="box-header with-border">
          <h3 class="box-title">Reminders</h3>
          <button type="button" class="btn btn-comp-primary pull-right btn-sm" data-toggle="modal" data-target="#createReminderModal">Add Reminder</button>
        </div>
        <div class="box-body">
          <ul class="list-group">
          </ul> 
        </div>
        <div class="box-body">
          <a href="<?php echo base_url('manage-reminders'); ?>" class="btn btn-info btn-flat btn-sm pull-right">View All</a>
        </div>
      </div>
    </div>

    <div class="box box-danger">
      <div class="box-header with-border">
        <div class="row">
          <div class="col-md-6 col-xs-7">
            <h3 class="box-title pull-left">Items expiring in next</h3>
          </div>
          <div class="col-md-6 col-xs-5">
            <select name="" id="item-expiry-filter" class="form-control pull-right">
              <?php 
                $prev_days_arr = ['7 days','14 days','28 days','30 days','3 months','6 months','1 year'];
                for($i=0;$i<count($prev_days_arr);$i++){
              ?>
              <option value="<?php echo $prev_days_arr[$i]; ?>"> <?php echo $prev_days_arr[$i]; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>
      <div class="box-body text-center expiring-items-container">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw div-loader hide"></i>
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-danger expiring-items-details-box hide">
              <div class="panel-heading bg-red">Expiring Items</div>
              <div class="panel-body">
                <b id="expiring-items-count">0</b>
              </div>
              <div class="panel-footer">
                <a href="javascript:void(0)" id="expiring-items-url" class="text-red">Return</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title text-center"> M.O.M Sales Growth (Jan - Dec <?php echo date('Y')?>)</h3>
        <div class="box-body">

          <?php

            for($i=0;$i<count($mom_growth);$i++){
              $labels[] = $mom_growth[$i]['month'];
              $datasets_data[] = $mom_growth[$i]['total_payable_amount'];
            }

            if(!empty($labels)){
              $mom_growth_arr = [
                'labels' => $labels,
                'datasets' => [
                  [
                    'data' => $datasets_data,
                    'backgroundColor' => '#00a65a'
                  ],
                ]
              ];
            }else{
              $mom_growth_arr = [
                'labels' => ['Jan','Feb','Mar','Apr','May','June','Jul','Aug','Sep','Oct','Nov','Dec'],
                'datasets' => [
                  [
                    'data' => ['0','0','0','0','0','0','0','0','0','0','0','0']
                  ],
                ]
              ];
            }
          ?>
          <canvas class="bar-chart" data-chart_data='<?php echo json_encode($mom_growth_arr); ?>'></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if($subscription_time_left['years'] >= 0 && $subscription_time_left['months'] >= 0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0){ ?>
<!-- Save meeting Modal -->
<div id="changeAssigneeModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <div class="row search-employees-container">
          <div class="col-md-6">&nbsp;</div>
          <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <div class="input-group">
                  <input type="text" id="employees-search-bar" class="form-control" placeholder="Search employees">
                  <div class="input-group-btn">
                    <button class="btn btn-default search-employees" type="button">
                      <i class="glyphicon glyphicon-search"></i>
                    </button>
                  </div>
                </div>
              </div>
          </div>
        </div>
        <div class="row employee-person-container"></div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php } ?>