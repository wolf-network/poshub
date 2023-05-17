<div class="row">
	<div class="col-md-3">&nbsp;</div>
	<div class="col-md-5">
		<div class="box box-success">
			<?php echo form_open('',['id' => 'save_role_form']); ?>
			<div class="box-header">
				<h3 class="box-title"><?php echo ($industry_id == 0)?'Add':'Edit'; ?> Industry</h3>
				<div class="pull-right">
					<a href="https://www.youtube.com/watch?v=VidboF-1ZlE" target="_blank" class="btn btn-info">Watch tutorial</a>
                	<a href="<?php echo base_url('manage-industries'); ?>" class="btn btn-primary">Manage Industries</a>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="BusinessIndustry">Business Industry</label>
							<input type="text" name="BusinessIndustry" value="<?php echo set_value('BusinessIndustry'); ?>" required id="BusinessIndustry" class="form-control">
							<span class="text-danger"><?php echo validation_show_error('BusinessIndustry'); ?></span>
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