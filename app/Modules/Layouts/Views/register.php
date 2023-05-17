<!DOCTYPE html>
<html lang="en">
<head>
	<title>POS Hub | Register - A wolf network billing and inventory management product</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="icon" type="image/png" href="<?php echo base_url(); ?>favicon.ico" />

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/frontend/') ?>css/bootstrap-4.css">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/') ?>css/font-awesome.min.css">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/frontend/') ?>css/material-design-iconic-font.min.css">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/frontend/') ?>css/animate.css">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/frontend/') ?>css/animsition.min.css">

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/frontend/') ?>css/util.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/frontend/') ?>css/main.css">

	<script src="https://www.google.com/recaptcha/api.js" async defer></script>

	<meta name="robots" content="noindex, follow">

	<style>

	  @media (max-width: 768px){
			.login100-pic {
			  width: 100%;
			  margin-left: 0%;
			}
		}

  	@media (max-width: 576px){
			.wrap-login100 {
			  padding: 16px 15px 33px !important;
			}
		}

		@media (min-width: 1024px){
			.login100-pic {
			  margin-top: 25%;
			}
		}

		.wrap-login100{
			padding: 34px 48px 33px 95px;
		}

		@media (min-width: 800px) and (max-width: 1020px){
			.login100-pic {
			  margin-top: 25% !important;
			}

			.wrap-login100 {
			  padding: 34px 48px 33px 13px;
			}
		}

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
			<div class="wrap-login100">
				<div class="login100-pic js-tilt" data-tilt>
					<img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="Wolf Network Logo" class="img-fluid">
					<center>
						<h3 class="mt-4" style="color:#026874;font-weight: bold;">21 Days free trial</h3>
						<h6 class="mt-4 mb-4">Credit card not required</h6>
					</center>
				</div>
				<?php echo form_open('',['class' => 'login100-form register-form']); ?>
					<?php 
	          	$flashdata = session()->getFlashdata('flashmsg');
	          	if(!empty($flashdata)){
	      	?>
		        <div class="alert alert-<?php echo ($flashdata['status'] == true)?'success':'danger'; ?>">
		            <?php echo $flashdata['msg']; ?>
		        </div>
	      	<?php } ?>
					<span class="login100-form-title">
						Register
					</span>
					<div class="wrap-input100 validate-input">
						<input class="input100" type="text" name="CompName" value="<?php echo set_value('CompName'); ?>" placeholder="Organization Name *">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-briefcase" aria-hidden="true"></i>
						</span>
					</div>
					<span class="text-danger"><?php echo validation_show_error('CompName'); ?></span>

					<div class="wrap-input100 validate-input">
						<select name="FirmTypeID" id="FirmTypeID" class="input100">
		          <option value="">Select Firm Type *</option>
		          <?php for($i=0;$i<count($firm_types);$i++){ ?>
		            <option value="<?php echo $firm_types[$i]['FirmTypeID']; ?>" <?php echo($firm_types[$i]['FirmTypeID'] == set_value('FirmTypeID'))?'selected':''; ?> >
		              <?php echo $firm_types[$i]['FirmType'] ?>
		            </option>
		          <?php } ?>
		        </select>
						<span class="focus-input100"></span>
					</div>
					<span class="text-danger"><?php echo validation_show_error('FirmTypeID'); ?></span>

					<div class="wrap-input100">
						<input type="text" name="Name" value="<?php echo set_value('Name'); ?>" class="input100" placeholder="Full Name *">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-user" aria-hidden="true"></i>
						</span>
					</div>
					<span class="text-danger"><?php echo validation_show_error('Name'); ?></span>

					<div class="wrap-input100 validate-input">
						<input class="input100" type="text" name="EmailID" value="<?php echo set_value('EmailID'); ?>" placeholder="Email ID *">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>
					<span class="text-danger"><?php echo validation_show_error('EmailID'); ?></span>

					<div class="wrap-input100 validate-input">
						<input class="input100" type="password" name="Password" value="<?php echo set_value('Password'); ?>" placeholder="Password *">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					<span class="text-danger"><?php echo validation_show_error('Password'); ?></span>

					<div class="wrap-input100 validate-input">
						<input type="password" name="ConfirmPassword" value="<?php echo set_value('ConfirmPassword'); ?>" class="input100" placeholder="Confirm Password *">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>
					<span class="text-danger"><?php echo validation_show_error('ConfirmPassword'); ?></span>

					<div class="wrap-input100 validate-input">
						<?php 
							$referral_code = set_value('ReferralCode');
							if(empty($referral_code) && !empty($_GET['ReferralCode'])){
								$referral_code = $_GET['ReferralCode'];
							}

						?>
						<input type="text" name="ReferralCode" value="<?php echo $referral_code; ?>" class="input100" placeholder="Referral Code (Optional)">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-ticket" aria-hidden="true"></i>
						</span>
					</div>

					<div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>

					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Register
						</button>
					</div>
					<div class="text-center p-t-12">
						<a class="txt2" href="<?php echo base_url('login'); ?>">I already have an account</a>
					</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>

	<script src="<?php echo base_url('assets/js/jquery.min.js'); ?>"></script>

	<script src="<?php echo base_url('assets/frontend/'); ?>js/animsition.min.js"></script>

	<script src="<?php echo base_url('assets/frontend/'); ?>js/popper.js"></script>
	<script src="<?php echo base_url('assets/frontend/'); ?>js/bootstrap-4.js"></script>
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