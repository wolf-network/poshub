<?php 
    $btn_class = '';
    $datatable_hide_class = '';
    if($subscription_time_left['years'] <=0 && $subscription_time_left['months'] <=0 && $subscription_time_left['days'] <= 0 && $subscription_time_left['hours'] <= 0 && $subscription_time_left['minutes'] <= 0 && $subscription_time_left['seconds'] <= 0){
        $btn_class .= 'disabled';
        $datatable_hide_class = 'datatable-hide-search';
    }
?>

<div class="row">
	<div class="col-md-8">
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-success">
					Your Referral Code: <b><?php echo $user_data['ReferralCode']; ?></b> <br>
					Your Referral link: <b><?php echo base_url('register?ReferralCode='.$user_data['ReferralCode']); ?></b>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-danger">
					<b>Note:</b> Referral rewards will be credited to the referrer's bank account within 10 business days after the end of the month.
				</div>
			</div>
		</div>
		<div class="row">
		    <div class="col-md-6 col-sm-6 col-xs-12">
		        <div class="info-box pointer amount-filter-box" data-filter="sales">
		            <span class="info-box-icon bg-yellow"><i class="fa fa-money" aria-hidden="true"></i></span>
		            <div class="info-box-content">
		                <span class="info-box-text">This Month Earning</span>
		                <span class="info-box-number monthly-earnings"><i class="fa fa-rupee"></i> <?php echo(!empty($user_earnings['monthly_earning']))?$user_earnings['monthly_earning']:0; ?> </span>
		            </div>
		        </div>
		    </div>

		    <div class="col-md-6 col-sm-6 col-xs-12">
		        <div class="info-box pointer amount-filter-box" data-filter="sales">
		            <span class="info-box-icon bg-green"><i class="fa fa-suitcase" aria-hidden="true"></i></span>
		            <div class="info-box-content">
		                <span class="info-box-text">Total Earnings</span>
		                <span class="info-box-number total-received">
		                	<i class="fa fa-rupee"></i>  <?php echo(!empty($user_earnings['total_earnings']))?$user_earnings['total_earnings']:0; ?>
		                </span>
		            </div>
		        </div>
		    </div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="box box-success">
					<div class="box-header">
						<h3 class="box-title pull-left">This Month referrals</h3>
						<div class="pull-right">
							<a href="https://www.youtube.com/watch?v=tWjT3XIdaVo" target="_blank" class="btn btn-info">Watch tutorial</a>
							
							<a href="https://www.wolfnetwork.in/referral-policy-terms-and-conditions" target="_blank">Referral policy terms & conditions.</a>
						</div>
					</div>
					<div class="box-body">
						<div class="dataTables_wrapper form-inline dt-bootstrap table-responsive datatable-hide-search">
							<table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/registered_users/get_referral_details') ?>" data-responsive="true">
								<thead>
									<tr>
										<th>Sr.</th>
										<th id="Name">Referral Name</th>
										<th id="InsertedDate">Registered Date</th>
										<th id="PaymentReceivedDate">Last Renewal Date</th>
										<th data-render="true" data-render_html="{PlanAmount} + 18% GST">Plan Amount</th>
										<th id="earned_amount">Your share</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>
	<div class="col-md-4">
	    <div class="box box-success">
			<div class="box-header">
				<h3 class="box-title">Your Bank Account Details</h3>
			</div>
			<div class="box-body">
				<?php if(empty($user_bank_details)){ ?>
				<a href="<?php echo base_url('save-registered-user-bank-details'); ?>" class="btn btn-success btn-block save-bank-account-details">Add Bank Account Details</a>
				<?php }else{ ?>
				<ul class="list-group">
				  <li class="list-group-item">
				  	<div class="media">
					    <div class="media-left">
					      <i class="fa fa-id-card-o fa-3x media-object"></i>
					    </div>
					    <div class="media-body">
					      <h5 class="media-heading">Account Number</h5>
					      <p><b><?php echo $user_bank_details['AccountNumber']; ?></b></p>
					    </div>
				  	</div>
				  </li>
				  <li class="list-group-item">
				  	<div class="media">
					    <div class="media-left">
					      <i class="fa fa-bank fa-3x media-object"></i>
					    </div>
					    <div class="media-body">
					      <h5 class="media-heading">Bank Name</h5>
					      <p><b><?php echo $user_bank_details['BankName']; ?></b></p>
					    </div>
				  	</div>
				  </li>
				  <li class="list-group-item">
				  	<div class="media">
					    <div class="media-left">
					      <i class="fa fa-code-fork fa-3x media-object"></i>
					    </div>
					    <div class="media-body">
					      <h5 class="media-heading">IFSC</h5>
					      <p><b><?php echo $user_bank_details['BankIFSC']; ?></b></p>
					    </div>
				  	</div>
				  </li>
				  <li class="list-group-item">
				  	<div class="media">
					    <div class="media-left">
					      <i class="fa fa-credit-card fa-3x media-object"></i>
					    </div>
					    <div class="media-body">
					      <h5 class="media-heading">Account Type</h5>
					      <p><b><?php echo $user_bank_details['AccountType']; ?></b></p>
					    </div>
				  	</div>
				  </li>
				  <li class="list-group-item">
				  	<div class="media">
					    <div class="media-left">
					      <i class="fa fa-address-book-o fa-3x media-object"></i>
					    </div>
					    <div class="media-body">
					      <h5 class="media-heading">Account Holder's Name</h5>
					      <p><b><?php echo $user_bank_details['AccountHolderName']; ?></b></p>
					    </div>
				  	</div>
				  </li>
				</ul> 
				<?php } ?>
			</div>
		</div>
	</div>
</div>