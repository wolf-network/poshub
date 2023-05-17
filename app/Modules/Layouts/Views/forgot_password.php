<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo APP_NAME; ?> | Forgot Password</title>
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
  <style>
    .overlay-wrapper {
      position: fixed;
      top: 0;
      bottom: 0;
      right: 0;
      left: 0;
      background-color: rgba(23, 23, 23, 0.77);
      z-index: 1050;
      width: 100%;
      height: 100%;
    }

    .overlay-wrapper .loader-wrapper {
      /*width: 90px;*/
      /*height: 91px;*/
      /*border: 8px solid #026873;*/
      margin: auto;
      margin-top: 265px;
      /*border-radius: 29%;*/
    }

    .breathing-animation {
      width: 180px;
      padding: 12px;
      margin: 50px auto;
      border: 1px solid #048e6c;
      -webkit-animation: breathing 2s ease-out infinite normal;
      animation: breathing 2s ease-out infinite normal;
      font-family:'Lantinghei SC';
      font-size: 16px;
      background: #048e6c;
      color: #fff;
      -webkit-font-smoothing: antialiased;
      
      text-align: center;    
    }


    @-webkit-keyframes breathing {
      0% {
        -webkit-transform: scale(0.9);
        transform: scale(0.9);
      }

      50% {
        -webkit-transform: scale(0.9);
        transform: scale(0.9);
      }

      100% {
        -webkit-transform: scale(0.9);
        transform: scale(0.9);
      }
    }

    @keyframes breathing {
      0% {
        -webkit-transform: scale(0.9);
        -ms-transform: scale(0.9);
        transform: scale(0.9);
      }

      50% {
        -webkit-transform: scale(1);
        -ms-transform: scale(1);
        transform: scale(1);
      }

      100% {
        -webkit-transform: scale(0.9);
        -ms-transform: scale(0.9);
        transform: scale(0.9);
      }
    }

    .hide{display: none;}

    .text-danger .help-block{
      color: #a94442 !important;
    }
  </style>
</head>
<body class="hold-transition login-page">
  <div class="overlay-wrapper hide">
       <div class="loader-wrapper">
          <img src="<?php echo base_url('assets/img/wolf-symbol-mini.jpg') ?>" alt="" class="img-responsive img-circle loader">
        </div>
       <!-- <p>Please, do not refresh or close the page</p> -->
  </div>
<div class="login-box">
  <div class="login-logo">
    <a href="<?php echo base_url('login'); ?>">
      <img src="<?php echo base_url('assets/img/logo.png'); ?>" width="250" alt="Wolf Network Logo">
    </a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <?php 
        $flashdata = session()->getFlashdata('flashmsg');
        if(!empty($flashdata)){
    ?>
        <div class="alert alert-<?php echo ($flashdata['status'] == true)?'success':'danger'; ?>">
            <?php echo $flashdata['msg']; ?>
        </div>
    <?php } ?>
    <p class="login-box-msg">Get New Password</p>

    <?php echo form_open(); ?>
      <div class="form-group has-feedback">
        <input type="text" name="CompName" value="<?php echo set_value('CompName'); ?>" class="form-control" placeholder="Organization Name">
        <span class="glyphicon glyphicon-briefcase form-control-feedback"></span>
        <span class="text-danger"><?php echo validation_show_error('CompName'); ?></span>
      </div>

      <div class="form-group has-feedback">
        <input type="text" name="Username" value="<?php echo set_value('Username'); ?>" class="form-control" placeholder="Username">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        <span class="text-danger"><?php echo validation_show_error('Username'); ?></span>
      </div>

      <div class="row">
        <div class="col-xs-12">
          <button type="submit" class="text-center btn btn-warning btn-block">Reset</button>
        </div>
        <!-- /.col -->
      </div>
    <?php echo form_close(); ?>
    <br>
    <a href="<?php echo base_url('login'); ?>">Login</a>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
  <!-- jQuery 3 -->
  <script src="<?php echo base_url('assets/') ?>js/jquery.min.js"></script>
  <script>
    $(document).on('click', 'a' ,function(e){
      var href = $(this).attr('href');
      var loader = $(this).attr('data-loader');
      
      if(href != 'javascript:void(0)' && href != '#' && loader != 'false' && !e.ctrlKey){
          $('.overlay-wrapper').removeClass('hide');
          $('.loader').addClass('breathing-animation');
      }
    });

    $("form").submit(function(e){
      var loader = $(this).attr('data-loader');
      
      if(loader != 'false' && !e.ctrlKey){
          $('.overlay-wrapper').removeClass('hide');
          $('.loader').addClass('breathing-animation');
      }
    });
  </script>
</body>
</html>
