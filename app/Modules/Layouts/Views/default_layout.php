<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>POS hub - Billing software</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/') ?>css/bootstrap.min.css">
  
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="<?php echo base_url('assets/') ?>plugins/icheck/skins/all.css">
  <!-- Bootstrap Multiselect -->
  <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>css/bootstrap-multiselect.css">
  <!-- Bootstrap Daterangepicker -->
  <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>css/daterangepicker.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url('assets/') ?>css/font-awesome.min.css">

  <!-- Datatables -->
  <link rel="stylesheet" href="<?php echo base_url('assets/') ?>css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>css/responsive.dataTables.min.css">
  
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url('assets/') ?>css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo base_url('assets/') ?>css/skins/_all-skins.min.css">

  <link rel="stylesheet" href="<?php echo base_url('assets/css/brain.css'); ?>">
  <!-- Custom CSS Below common CSS Starts -->
  <?php if(isset($add_bel_global_css)){
    if(is_array($add_bel_global_css)) {
      for ($i=0; $i <count($add_bel_global_css) ; $i++) { 
  ?>
    <link rel="stylesheet" href="<?php echo $add_bel_global_css[$i]; ?>">
  <?php 
    } 
  }
  else { 
  ?>
    <link rel="stylesheet" href="<?php echo $add_bel_global_css; ?>">
  <?php 
      }
    } 
  ?>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini" data-base_url="<?php echo base_url(); ?>" data-media_server="<?php echo media_server(); ?>" data-privilege="<?php echo session()->get('user_data')['Privilege']; ?>">

<input type="hidden" id="push-token" value="<?php echo(!empty($user_data['push_token']))?$user_data['push_token']:''; ?>">

<div class="wrapper">
  <div class="overlay-wrapper hide">
       <div class="loader-wrapper">
          <img src="<?php echo base_url('assets/img/wolf-symbol-mini.jpg') ?>" alt="" class="img-responsive img-circle loader">
        </div>
       <!-- <p>Please, do not refresh or close the page</p> -->
  </div>

  <header class="main-header">

    <!-- Logo -->
    <a href="<?php echo base_url('dashboard'); ?>" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini">
        <img src="<?php echo base_url('assets/img/wolf-symbol-mini.jpg') ?>" alt="">
      </span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg">
        <img src="<?php echo base_url('assets/img/logo-light.png') ?>" alt="">
      </span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu Starts -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="">
            <a href="javascript:void(0)"  data-toggle="modal" data-target="#createReminderModal">
              <i class="fa fa-plus-circle"></i> Reminder
            </a>
          </li>
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-th-large"></i>
            </a>
            <ul class="dropdown-menu">
              <li class="user-body">
                <div class="row">
                  <?php
                    for($i=0;$i<count($apps);$i++){ 
                  ?>
                    <div class="col-xs-4 text-center">
                      <a href="<?php echo $apps[$i]['UserURL']; ?>" title="<?php echo $apps[$i]['App']; ?>">
                        <?php $app_img = (!empty($apps[$i]['IconPath']))?$apps[$i]['IconPath']:base_url('assets/img/logo-light.png'); ?>
                        <img src="<?php echo $app_img; ?>" alt="<?php echo $apps[$i]['App']; ?>" class="app-icon">
                      </a>
                    </div>
                    <?php } ?>
                  </div>
                </li>
              </ul>
            </li>
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <span><?php echo $user_data['Name']; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <p><?php echo $user_data['Name'] ?>
                  <small>Member since <?php echo $user_data['InsertedDate'] ?></small>
                </p>
                <p>Referral Code: <a href="<?php echo base_url('view-referral-details'); ?>" class="text-muted"><?php echo $user_data['ReferralCode']; ?></a></p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo base_url('reset-password') ?>" class="btn btn-default btn-flat">Reset Password</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo base_url('logout') ?>" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
      <!-- Navbar Right Menu Ends -->
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li>
          <a href="<?php echo base_url('dashboard'); ?>">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-users"></i>
            <span>Relationships</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if(session()->get('user_data')['Privilege'] == 'Admin'){ ?>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-circle-o"></i> 
                <span>Users</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?php echo base_url('add-user'); ?>"><i class="fa fa-circle-o"></i> Add User</a></li>
                <li><a href="<?php echo base_url('manage-users'); ?>"><i class="fa fa-circle-o"></i> Manage Users</a></li>
              </ul>
            </li>
            <?php } ?>

            <li class="treeview">
              <a href="#">
                <i class="fa fa-circle-o"></i>
                <span>Clients</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?php echo base_url('add-client'); ?>"><i class="fa fa-circle-o"></i> Add Client</a></li>
                <li><a href="<?php echo base_url('manage-clients'); ?>"><i class="fa fa-circle-o"></i> Manage Clients</a></li>
              </ul>
            </li>

            <li class="treeview">
              <a href="#">
                <i class="fa fa-circle-o"></i>
                <span>Vendors</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?php echo base_url('add-vendor') ?>"><i class="fa fa-circle-o"></i> Add Vendor</a></li>
                <li><a href="<?php echo base_url('manage-vendors'); ?>"><i class="fa fa-circle-o"></i> Manage Vendors</a></li>
              </ul>
            </li>

          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-tag"></i>
            <span>Items</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo base_url('add-item') ?>"><i class="fa fa-circle-o"></i> Add Item</a></li>
            <li><a href="<?php echo base_url('manage-items'); ?>"><i class="fa fa-circle-o"></i> Manage Items</a></li>
            <li class="treeview">
              <a href="javascript:void(0)">
                <i class="fa fa-circle-o"></i> Item Categories
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li>
                  <a href="<?php echo base_url('manage-item-categories'); ?>">
                    <i class="fa fa-circle-o"></i> 
                    <span>Manage Categories</span>
                  </a>
                </li>
                <li>
                  <a href="<?php echo base_url('add-item-category'); ?>">
                    <i class="fa fa-circle-o"></i> 
                    <span>Add Category</span>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-cubes"></i>
            <span>Inventory</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo base_url('add-stock') ?>"><i class="fa fa-circle-o"></i> Add Stock</a></li>
            <li><a href="<?php echo base_url('stock-inward-history') ?>"><i class="fa fa-circle-o"></i> Inward History</a></li>
            <li><a href="<?php echo base_url('stock-outward-history') ?>"><i class="fa fa-circle-o"></i> Outward History</a></li>
            <li><a href="<?php echo base_url('stock-inward-outward-report') ?>"><i class="fa fa-circle-o"></i> Inward/Outward Reports</a></li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-circle-o"></i> Expiring Stocks
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li>
                  <a href="<?php echo base_url('view-expiring-items') ?>"><i class="fa fa-circle-o"></i>View expiring Stocks</a>
                </li>
                <li>
                  <a href="<?php echo base_url('view-returned-expiring-items') ?>"><i class="fa fa-circle-o"></i>Returned expiring Stocks</a>
                </li>
              </ul>
            </li>
          </ul>
        </li>
        <li>
          <a href="<?php echo base_url('pos'); ?>">
            <i class="fa fa-print" aria-hidden="true"></i>
            <span>Point of Sale</span>
          </a>
        </li>
        <li class="treeview">
          <a href="javascript:void(0)"> 
            <i class="fa fa-university"></i> 
            <span>Finance </span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="treeview">
              <a href="javascript:void(0)">
                <i class="fa fa-circle-o"></i> Purchase Order
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
              </a>
              <ul class="treeview-menu">
                <li class="">
                  <a href="<?php echo base_url('create-purchase-order'); ?>">
                    <i class="fa fa-circle-o"></i> Create
                  </a>
                </li>
                <li class="">
                  <a href="<?php echo base_url('manage-purchase-orders'); ?>">
                    <i class="fa fa-circle-o"></i> Manage
                  </a>
                </li>
                <li class="">
                  <a href="<?php echo base_url('edit-purchase-order-settings'); ?>">
                    <i class="fa fa-circle-o"></i> Settings <span class="text-danger"></span>
                  </a>
                </li>
              </ul>
            </li>
            <li class="treeview">
              <a href="javascript:void(0)">
                <i class="fa fa-circle-o"></i> Invoice
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
              </a>
              <ul class="treeview-menu">
                <li class="">
                  <a href="<?php echo base_url('create-invoice'); ?>">
                    <i class="fa fa-circle-o"></i> Create
                  </a>
                </li>
                <li class="">
                  <a href="<?php echo base_url('manage-invoices'); ?>">
                    <i class="fa fa-circle-o"></i> Manage
                  </a>
                </li>
                <li class="">
                  <a href="<?php echo base_url('invoice-settings'); ?>">
                    <i class="fa fa-circle-o"></i> Settings
                  </a>
                </li>
              </ul>
            </li>
            <li class="treeview">
              <a href="javascript:void(0)">
                <i class="fa fa-circle-o"></i> Sales Return
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview">
                  <a href="javascript:void(0)">
                    <i class="fa fa-circle-o"></i> Credit Note
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                  </a>
                  <ul class="treeview-menu">
                    <li>
                      <a href="<?php echo base_url('create-credit-note'); ?>">
                        <i class="fa fa-circle-o"></i> Create
                      </a>
                    </li>
                    <li>
                      <a href="<?php echo base_url('manage-credit-notes'); ?>">
                        <i class="fa fa-circle-o"></i> Manage
                      </a>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
            <!-- <li class="treeview">
              <a href="javascript:void(0)">
                <i class="fa fa-circle-o"></i> Purchase Return
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview">
                  <a href="javascript:void(0)">
                    <i class="fa fa-circle-o"></i> Debit Note
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                  </a>
                  <ul class="treeview-menu">
                    <li>
                      <a href="<?php echo base_url('create-debit-note'); ?>">
                        <i class="fa fa-circle-o"></i> Create
                      </a>
                    </li>
                    <li>
                      <a href="<?php echo base_url('manage-debit-notes'); ?>">
                        <i class="fa fa-circle-o"></i> Manage
                      </a>
                    </li>
                  </ul>
                </li>
              </ul>
            </li> -->
            <li class="treeview">
              <a href="javascript:void(0)">
                <i class="fa fa-circle-o"></i> Expense
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
              </a>
              <ul class="treeview-menu">
                <li class="">
                  <a href="<?php echo base_url('add-expense'); ?>">
                    <i class="fa fa-circle-o"></i> Add Expense
                  </a>
                </li>
                <?php if(session()->get('user_data')['Privilege'] == 'Admin'){ ?>
                <li class="">
                  <a href="<?php echo base_url('view-expenses'); ?>">
                    <i class="fa fa-circle-o"></i> View Expenses
                  </a>
                </li>
                <?php } ?>
              </ul>
            </li>
            <li class="treeview">
              <a href="javascript:void(0)">
                <i class="fa fa-circle-o"></i> Purchase Return
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview">
                  <a href="javascript:void(0)">
                    <i class="fa fa-circle-o"></i> Debit Note
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                  </a>
                  <ul class="treeview-menu">
                    <li>
                      <a href="<?php echo base_url('create-debit-note'); ?>">
                        <i class="fa fa-circle-o"></i> Create
                      </a>
                    </li>
                    <li>
                      <a href="<?php echo base_url('manage-debit-notes'); ?>">
                        <i class="fa fa-circle-o"></i> Manage
                      </a>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
            <li class="treeview">
              <a href="javascript:void(0)">
                <i class="fa fa-circle-o"></i>Your GST/VAT <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
              </a>
              <ul class="treeview-menu">
                <li class="">
                  <a href="<?php echo base_url('add-company-service-tax'); ?>">
                    <i class="fa fa-circle-o"></i> Add GST/VAT
                  </a>
                </li>
                <li class="">
                  <a href="<?php echo base_url('manage-company-service-taxes'); ?>">
                    <i class="fa fa-circle-o"></i> Manage GST/VAT
                  </a>
                </li>
              </ul>
            </li>
            <li class="treeview">
              <a href="javascript:void(0)">
                <i class="fa fa-circle-o"></i>Reports <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
              </a>
              <ul class="treeview-menu">
                <li>
                  <a href="<?php echo base_url('financial-report'); ?>">
                    <i class="fa fa-circle-o"></i> Financial Report <span class="text-danger"><b></b></span>
                  </a>
                </li>
                <li>
                  <a href="<?php echo base_url('gstr-1'); ?>">
                    <i class="fa fa-circle-o"></i> GSTR-1 <span class="text-danger"><b></b></span>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </li>
        <li>
          <a href="<?php echo base_url('manage-reminders'); ?>">
            <i class="fa fa-clock-o"></i> 
            <span>Reminders</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url('view-referral-details'); ?>">
            <i class="fa fa-money"></i> 
            <span>Refer & Earn</span>
          </a>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-cogs"></i>
            <span>Settings</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if(session()->get('user_data')['Privilege'] == 'Admin'){ ?>
            <li><a href="<?php echo base_url('edit-comp-details') ?>"><i class="fa fa-circle-o"></i> Company</a></li>
            <li class="treeview">
              <a href="javascript:void(0)">
                <i class="fa fa-circle-o"></i> 
                <span>Masters</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="treeview">
                  <a href="javascript:void(0)">
                    <i class="fa fa-circle-o"></i> 
                    <span>Roles</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>  
                  </a>
                  <ul class="treeview-menu">
                    <li>
                      <a href="<?php echo base_url('manage-roles'); ?>">
                        <i class="fa fa-circle-o"></i> 
                        <span>Manage Roles</span>
                      </a>
                    </li>
                    <li>
                      <a href="<?php echo base_url('add-role'); ?>">
                        <i class="fa fa-circle-o"></i> 
                        <span>Add Role</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="javascript:void(0)">
                    <i class="fa fa-circle-o"></i> 
                    <span>Industries</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>  
                  </a>
                  <ul class="treeview-menu">
                    <li>
                      <a href="<?php echo base_url('manage-industries'); ?>">
                        <i class="fa fa-circle-o"></i> 
                        <span>Manage Industries</span>
                      </a>
                    </li>
                    <li>
                      <a href="<?php echo base_url('add-industry'); ?>">
                        <i class="fa fa-circle-o"></i> 
                        <span>Add Industry</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="treeview">
                  <a href="javascript:void(0)">
                    <i class="fa fa-circle-o"></i> 
                    <span>Services</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>  
                  </a>
                  <ul class="treeview-menu">
                    <li>
                      <a href="<?php echo base_url('manage-services'); ?>">
                        <i class="fa fa-circle-o"></i> 
                        <span>Manage Services</span>
                      </a>
                    </li>
                    <li>
                      <a href="<?php echo base_url('add-service'); ?>">
                        <i class="fa fa-circle-o"></i> 
                        <span>Add Service</span>
                      </a>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
            <?php } ?>
            <li><a href="<?php echo base_url('reset-password') ?>"><i class="fa fa-circle-o"></i> Reset Password</a></li>
          </ul>
        </li>
        <li>
          <a href="<?php echo base_url('view-pricing-plans/'.$app_id); ?>">
            <i class="fa fa-credit-card"></i> <span>Pricing Plans</span>
          </a>
        </li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
      <?php if(session()->getFlashdata('flashmsg')){
        $response = session()->getFlashdata('flashmsg');
        $msg = $response['msg'];
      ?>
      <div class="panel <?php echo($response['status'] == true)?'panel-success':'panel-danger'; ?>">
        <div class="panel-heading"><?php echo $msg; ?></div>
      </div>
      <?php } ?>

      <?php 
        $error_flashdata = session()->getFlashdata('excel_err');
        if(!empty($error_flashdata)){ 
      ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo $error_flashdata['msg']; ?></h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-striped bg-danger">
                            <tr>
                                <th>Sr</th>
                                <th>Error</th>
                            </tr>
                            <?php for ($i=0; $i <count($error_flashdata['data']) ; $i++) { ?>
                            <tr>
                                <td><?php echo $i+1; ?></td>
                                <td><?php echo $error_flashdata['data'][$i]['msg']; ?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
      <?php } ?>

      <?php 
        if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 15 && $subscription_time_left['seconds'] >= 0){
      ?>
        <div class="alert alert-danger">
          <?php if($plan_name != 'Trial'){ ?>
          <div class="row">
            <div class="col-md-9 col-xs-12">
              <span class="pull-left">Your subscription is about to end in <?php echo $subscription_time_left['days'].' Days, '.$subscription_time_left['hours'].' Hours, '.$subscription_time_left['minutes'].' Minutes and '.$subscription_time_left['seconds'].' Seconds.'; ?></span>
            </div>
            <div class="col-md-3 div.col-xs-12">
              <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#selfSubscriptionRenewModal">Renew Now</button>
            </div>
          </div>
          <?php }else{ ?>
            <div class="row">
            <div class="col-md-9 col-xs-12">
              <span class="pull-left">Your trial will end in <?php echo $subscription_time_left['days'].' Days, '.$subscription_time_left['hours'].' Hours, '.$subscription_time_left['minutes'].' Minutes and '.$subscription_time_left['seconds'].' Seconds.'; ?></span>
            </div>
            <div class="col-md-3 div.col-xs-12">
              <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#selfSubscriptionRenewModal">Buy Now</button>
            </div>
          </div>
          <?php } ?>
          <div class="clearfix"></div>
        </div>
        <?php } ?>

      <?php if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){ ?>
        <div class="alert alert-danger">
          Your subscription has ended, You won't be able to perform any action in the CRM apart from viewing the data. any new data entered in the system by your team mates won't be visible to you.
          <br>
          <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#selfSubscriptionRenewModal">Renew Now</button>
          <div class="clearfix"></div>
        </div>
      <?php } ?>

      <?php 
        echo view($view); 
      ?>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php if(!empty($roles)){ ?>
  <!-- Role Modal -->
  <div id="saveRoleModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Role</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="Role">Role</label>
                <input type="text" name="Role" id="Role" class="form-control">
                <span class="text-danger Role-error"></span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success save-role">Save</button>
        </div>
      </div>

    </div>
  </div>
  <?php } ?>

  <?php if(!empty($business_industries)){ ?>
  <!-- Role Modal -->
  <div id="saveIndustryModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Industry</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="BusinessIndustry">Business Industry</label>
                <input type="text" name="BusinessIndustry" id="BusinessIndustry" class="form-control">
                <span class="text-danger BusinessIndustry-error"></span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success save-industry">Save</button>
        </div>
      </div>

    </div>
  </div>
  <?php } ?>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.7
    </div>
    <strong>Developed by <a href="https://www.wolfnetwork.in">Wolf Network (OPC) PVT LTD.</a></strong>
  </footer>

  <!-- Control Sidebar -->
  
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->

</div>
<!-- ./wrapper -->

<!-- Modal -->
<div id="createReminderModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Create Reminder</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="">Reminder Date & time</label>
              <input type="text" name="ReminderDate" id="ReminderDate" class="form-control daterangepicker" data-time-picker="true" data-min-date="<?php echo date('Y-m-d H:i'); ?>">
              <span class="ReminderDate-error text-danger"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="Task">Task</label>
              <textarea name="Task" id="Task" class="form-control" cols="30" rows="5"></textarea>
              <span class="Task-error text-danger"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success save-reminder">Set Reminder</button>
      </div>
    </div>

  </div>
</div>

<?php if(($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0) || ($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 15 && $subscription_time_left['seconds'] >= 0) ){ ?>
<!-- Subscription Renewal Modal -->
<div id="selfSubscriptionRenewModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Kindly Re-new to enjoy all your services</h4>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-12">

                <div class="box box-widget widget-user-2">
                    <div class="widget-user-header bg-green text-center">
                        <h3>Pricing Plans</h3>
                        <p><b>Enjoy unlimited usage of Wolf Network CRM</b></p>
                        <p><b>Prices are in INR</b></p>
                    </div>
                    <div class="box-footer">
                        <ul class="nav nav-stacked">
                          <li>
                              <a href="#">
                                  Receive continous updates 
                                  <span class="pull-right text-green">
                                      <i class="fa fa-check" aria-hidden="true"></i>
                                  </span>
                              </a>
                          </li>
                          <li>
                              <a href="#">
                                  Manage Clients & Vendors
                                  <span class="pull-right text-green">
                                      <i class="fa fa-check" aria-hidden="true"></i>
                                  </span>
                              </a>
                          </li>
                          <li>
                              <a href="#">
                                  Create and store Unlimited invoices 
                                  <span class="pull-right text-green">
                                      <i class="fa fa-check" aria-hidden="true"></i>
                                  </span>
                              </a>
                          </li>
                          <li>
                              <a href="#">
                                  Imventory Management
                                  <span class="pull-right text-green">
                                      <i class="fa fa-check" aria-hidden="true"></i>
                                  </span>
                              </a>
                          </li>
                          <li>
                              <a href="#">
                                  Track your sales & Expense
                                  <span class="pull-right text-green">
                                      <i class="fa fa-check" aria-hidden="true"></i>
                                  </span>
                              </a>
                          </li>
                          <li>
                              <a href="#">
                                  Receive Inventory expiry alert
                                  <span class="pull-right text-green">
                                      <i class="fa fa-check" aria-hidden="true"></i>
                                  </span>
                              </a>
                          </li>
                          <li>
                              <a href="#">
                                  Seamless billing with barcode scanning
                                  <span class="pull-right text-green">
                                      <i class="fa fa-check" aria-hidden="true"></i>
                                  </span>
                              </a>
                          </li>
                        </ul>
                        <div class="row">
                            <?php 
                              if($user_data['Privilege'] == 'Admin'){
                                for($i=0;$i<count($subscription_plans);$i++){ 
                            ?>
                                <div class="col-sm-6 col-xs-6 border-right">
                                    <div class="description-block">
                                        <h5 class="description-header"> <i class="fa fa-inr"></i> <?php echo $subscription_plans[$i]['TotalAmount']; ?> Per User</h5>
                                        <span class="description-text"><?php echo $subscription_plans[$i]['PlanName']; ?></span>
                                        <br><br>
                                        <a href="<?php echo base_url('user-subscription/'.$user_data['ID']); ?>?plan=<?php echo $subscription_plans[$i]['SubscriptionPlanID']; ?>" class="btn bg-olive btn-block buy-subscription">Buy now</a>
                                    </div>
                                </div>
                            <?php } } else{ ?>
                                <div class="col-xs-12">
                                    <div class="description-block">
                                        <h5 class="description-header">Kindly ask your admin to Re-new your plan.</h5>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<!-- jQuery 3 -->
<script src="<?php echo base_url('assets/') ?>js/jquery.min.js"></script>
<!-- Jquery Mark -->
<script src="<?php echo base_url('assets/'); ?>js/jquery.mark.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url('assets/') ?>js/bootstrap.min.js"></script>
<!-- Bootstrap Multiselect -->
<script src="<?php echo base_url('assets/'); ?>js/bootstrap-multiselect.js"></script>
<!-- iCheck 1.0.1 -->
<script src="<?php echo base_url('assets/plugins/icheck/icheck.min.js'); ?>"></script>
<!-- Datatables -->
<script src="<?php echo base_url('assets/'); ?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url('assets/'); ?>js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo base_url('assets/'); ?>js/datatables.mark.min.js"></script>
<script src="<?php echo base_url('assets/'); ?>js/dataTables.responsive.min.js"></script>
<!-- Dynamic datatable -->
<script src="<?php echo base_url('assets/'); ?>js/dynamic_datatable.js"></script>
<!-- Table Sorter -->
<script src="<?php echo base_url('assets/'); ?>js/jquery.tablesorter.min.js"></script>
<!-- Vakata JS Tree -->
<script src="<?php echo base_url('assets/plugins/vakata/jstree.min.js'); ?>"></script>
<!-- Chart JS -->
<script src="<?php echo base_url('assets/js/chart.js'); ?>"></script>
<!-- Seeet Alert -->
<script src="<?php echo base_url('assets/js/sweetalert.min.js'); ?>"></script>
<!-- moment JS -->
<script src="<?php echo base_url('assets/') ?>js/moment.min.js"></script>
<!-- Daterange picker -->
<script src="<?php echo base_url('assets/') ?>js/daterangepicker.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url('assets/') ?>js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url('assets/') ?>js/demo.js"></script>
<!-- Star Rating JS -->
<script src="<?php echo base_url('assets/') ?>js/jquery.star-rating.js"></script>
<!-- Validate JS -->
<script src="<?php echo base_url('assets/js/jquery.validate.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/additional-methods.min.js'); ?>"></script>
<!-- Device UUID JS -->
<script src="<?php echo base_url('assets/js/device-uuid.min.js'); ?>"></script>
<!-- CK Editor -->
<script src="<?php echo base_url('assets/plugins/ckeditor/ckeditor.js'); ?>"></script>
<!-- Custom Javascript Above common JS Starts -->
<?php if(!empty($add_sup_cust_js)){
  if(is_array($add_sup_cust_js)) {
    for ($i=0; $i <count($add_sup_cust_js) ; $i++) { 
?>
  <script src="<?php echo $add_sup_cust_js[$i]; ?>"></script>
<?php 
  } 
}
else { 
?>
  <script src="<?php echo $add_sup_cust_js; ?>"></script>
<?php 
    }
  } 
?>
<!-- Custom Javascript Above common JS Ends -->

<!-- Global JS -->
<script src="<?php echo base_url('assets/js/custom.js'); ?>"></script>
<!-- Custom Javascript Below common JS Starts -->
<?php if(!empty($add_bel_global_js)){
  if(is_array($add_bel_global_js)) {
    for ($i=0; $i <count($add_bel_global_js) ; $i++) { 
?>
  <script src="<?php echo $add_bel_global_js[$i]; ?>"></script>
<?php 
  } 
}
else { 
?>
  <script src="<?php echo $add_bel_global_js; ?>"></script>
<?php 
    }
  } 
?>
<!-- Custom Javascript Below common JS Ends -->
</body>
</html>
