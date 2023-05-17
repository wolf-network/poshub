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
				<h3 class="box-title pull-left">Manage Company Documents</h3>
				<div class="pull-right">
                    <a href="https://www.youtube.com/watch?v=VidboF-1ZlE" target="_blank" class="btn btn-info">Watch tutorial</a>
					<a href="<?php echo base_url('add-company-document'); ?>" class="btn btn-success <?php echo(session()->get('user_data')['Privilege'] != 'Admin')?'disabled':''; ?> <?php echo $btn_class; ?> ">
						Add Document
					</a>
				</div>
			</div>
			<div class="box-body">
                <div class="dataTables_wrapper form-inline dt-bootstrap <?php echo $datatable_hide_class; ?> ">
                    <table class="table table-bordered table-hover dataTable shadow commonDataTable" data-url="<?php echo base_url('api/company/get_company_documents') ?>">
                        <thead>
                            <tr>
                                <th data-sortable="false">Sr.</th>
                                <th id="CountryName">Country</th>
                                <th id="StateName">State</th>
                                <th id="CityName">City</th>
                                <?php
                                	$document_cond['DocumentFilePath'] = [
                                		'null' => [
                                			'html' => '{DocumentName}'
                                		],
                                		'default' => [
                                			'html' => '<span class="pull-left">{DocumentName}</span> <a href="'.media_server('{DocumentFilePath}').'" download class="btn btn-primary btn-xs download-vendor-document pull-right" data-vendor_id="1" data-loader="false"><i class="fa fa-download"></i></a>'
                                		]
                                	];
                                ?>
                                <th data-condition_render='<?php echo json_encode($document_cond); ?>'>Document</th>
                                <th id="DocumentDescription">Description</th>
                                <?php if($subscription_time_left['years'] >= 0 && $subscription_time_left['months'] >= 0 && $subscription_time_left['days'] >= 0 && $subscription_time_left['hours'] >= 0 && $subscription_time_left['minutes'] >= 0 && $subscription_time_left['seconds'] > 0 && $user_data['Privilege'] == 'Admin'){ ?>
                                    <th data-render="true" data-render_html="<button class='btn btn-danger btn-xs delete-document' title='Delete Document' data-company_document_id='{CompanyDocumentID}'><i class='fa fa-trash'></i></button>">Action</th>
                                <?php } ?>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
		</div>
	</div>
</div>