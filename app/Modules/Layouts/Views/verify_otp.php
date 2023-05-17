<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo COMPANY_NAME; ?> | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>css/font-awesome.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url('assets/'); ?>css/AdminLTE.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="<?php echo base_url(); ?>">
      <img src="<?php echo base_url('assets/img/logo.svg'); ?>" width="250" alt="Wolf Network Logo">
      <br>
      <b>Lead</b> Management
    </a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <?php 
        $flashdata = $this->session->flashdata('flashmsg'); 
        if(!empty($flashdata)){
            
    ?>
    <div class="alert alert-<?php echo($flashdata['status'] == false)?'danger':'success'; ?> text-center"><?php echo $flashdata['msg']; ?></div>
    <?php } ?>
    <?php echo form_open(); ?>
      <div class="form-group has-feedback">
        <input type="text" name="OTP" value="" class="form-control" placeholder="Enter OTP">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        <span class="text-danger"><?php echo form_error('OTP'); ?></span>
      </div>
      <div class="row">
        <div class="col-xs-4">
          <a href="<?php echo base_url(); ?>" class="btn btn-warning btn-block btn-flat">Edit Email</a>
        </div>
        <div class="col-xs-4">&nbsp;</div>
        <div class="col-xs-4">
          <button type="submit" class="btn btn-success btn-block btn-flat">Login</button>
        </div>
        <!-- /.col -->
      </div>
    <?php echo form_close(); ?>
    
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
</body>
</html>
