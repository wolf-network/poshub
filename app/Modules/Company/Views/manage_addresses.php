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
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title">Manage Addresses</h3>
				<div class="pull-right">
					<a href="https://www.youtube.com/watch?v=VidboF-1ZlE" target="_blank" class="btn btn-info">Watch tutorial</a>
					<a href="<?php echo base_url('add-address'); ?>" class="btn btn-success <?php echo $btn_class; ?> ">Add Address</a>
				</div>
			</div>
			<div class="box-body">
				<div class="table-responsive <?php echo $datatable_hide_class; ?>">
					<table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/company/get_addresses'); ?>" data-responsive="true">
	                    <thead>
	                        <tr>
	                            <th data-sortable="false">Sr.</th>
	                            <th id="CountryName">Country Name</th>
	                            <th id="StateName">State Name</th>
	                            <th id="CityName">City Name</th>
	                            <th id="Address">Address</th>
                             	<?php if($subscription_time_left['years'] >= 0 && $subscription_time_left['months'] >= 0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0 && $user_data['Privilege'] == 'Admin'){ ?>
	                            <th data-render="true" data-render_html='<a href="<?php echo base_url('edit-address/') ?>{CompanyAddressID}" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a> <button type="button" data-company_address_id="{CompanyAddressID}" class="btn btn-danger btn-xs delete-company-address"><i class="fa fa-trash"></i></button> '>Action</th>
	                        	<?php } ?>
	                        </tr>
	                    </thead>
	                </table>
				</div>
			</div>
		</div>
	</div>
</div>