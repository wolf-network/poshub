<div class="row">
	<div class="col-md-3">&nbsp;</div>
	<div class="col-md-5">
		<div class="box box-success">
			<?php echo form_open('',['id' => 'save_service_form']); ?>
			<div class="box-header">
				<h3 class="box-title"><?php echo ($service_id == 0)?'Add':'Edit'; ?> Service</h3>
				<div class="pull-right">
					<a href="https://www.youtube.com/watch?v=VidboF-1ZlE" target="_blank" class="btn btn-info">Watch tutorial</a>
                	<a href="<?php echo base_url('manage-services'); ?>" class="btn btn-primary">Manage Services</a>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="ServiceType">Service</label>
							<input type="text" name="ServiceType" value="<?php echo set_value('ServiceType'); ?>" required id="ServiceType" class="form-control">
							<span class="text-danger"><?php echo validation_show_error('ServiceType'); ?></span>
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				<button class="btn btn-success pull-right">Save</button>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>