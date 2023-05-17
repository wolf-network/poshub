<!DOCTYPE html>
<html lang="en">
<head>
	<title>POS Hub | Login - A wolf network billing and inventory management product</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="icon" type="image/png" href="<?php echo base_url(); ?>favicon.ico" />

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/frontend/') ?>css/bootstrap-4.css">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/') ?>css/font-awesome.min.css">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/frontend/') ?>css/util.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/frontend/') ?>css/main.css">

	<meta name="robots" content="noindex, follow">

	<style>
		.text-danger p{
			color: #dc3545 !important;
		}
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

    .login100-pic {
		  margin-top: 18% !important;
		}
	</style>
</head>
<body>
	<div class="overlay-wrapper hide">
       	<div class="loader-wrapper">
       		<center>
          		<img src="<?php echo base_url('assets/img/wolf-symbol-mini.jpg') ?>" alt="" class="img-responsive rounded-circle loader breathing-animation">
       		</center>
        </div>
  	</div>
	<div class="limiter">
		<div class="container-login100">
			<?php 
	          	$flashdata = session()->getFlashdata('flashmsg');
	          	if(!empty($flashdata)){
	      	?>
		        <div class="alert alert-<?php echo ($flashdata['status'] == true)?'success':'danger'; ?>">
		            <?php echo $flashdata['msg']; ?>
		        </div>
	      	<?php } ?>
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="Wolf Network Logo">
				</div>
				<?php echo form_open('',['class' => 'login100-form validate-form']); ?>
					<span class="login100-form-title">
						User Login
					</span>
					<div class="wrap-input100 validate-input">
						<input class="input100" type="text" name="CompName" value="<?php echo set_value('CompName'); ?>" placeholder="Organization Name">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-briefcase" aria-hidden="true"></i>
						</span>
					</div>
					<span class="text-danger"><?php echo validation_show_error('CompName'); ?></span>
					<div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
						<input class="input100" type="text" name="Username" value="<?php echo set_value('Username'); ?>" placeholder="Email ID">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>
					<span class="text-danger"><?php echo validation_show_error('Username'); ?></span>
					<div class="wrap-input100 validate-input" data-validate="Password is required">
						<input class="input100" type="password" name="Password" value="<?php echo set_value('Password'); ?>" placeholder="Password">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					<span class="text-danger"><?php echo validation_show_error('Password'); ?></span>
					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Login
						</button>
					</div>
					<div class="text-center p-t-12">
						<span class="txt1">
							Forgot
						</span>
						<a class="txt2" href="<?php echo base_url('forgot-password'); ?>">
							Password?
						</a>
					</div>
					<div class="text-center p-t-20 p-b-20">
						<a class="txt2" href="<?php echo base_url('register'); ?>">
							Create your Account
							<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
						</a>
					</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>

	<script src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/frontend/'); ?>js/tilt.js"></script>
	<script>
		$(document).ready(function(){
			$("form").submit(function(e){
		      var loader = $(this).attr('data-loader');
		      
		      if(loader != 'false' && !e.ctrlKey){
		          $('.overlay-wrapper').removeClass('hide');
		          $('.loader').addClass('breathing-animation');
		      }
		    });
		    
		    $(document).on('click', 'a' ,function(e){
    		    var href = $(this).attr('href');
    		    var loader = $(this).attr('data-loader');
    		    var target = $(this).attr('target');
    		    var download = $(this).attr('download');
    
    		    if(href.toLowerCase() != 'javascript:void(0)' && href != '#' && href != undefined && loader != 'false' && !e.ctrlKey && target != '_blank' && download == undefined && !$(this).hasClass('cke_button') && !$(this).hasClass('cke_path_item')){
    		        $('.overlay-wrapper').removeClass('hide');
    		        $('.loader').addClass('breathing-animation');
    		    }
			});
		});
	</script>
</body>
</html>