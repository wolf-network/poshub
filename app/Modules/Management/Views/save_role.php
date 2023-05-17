<div class="row">
	<div class="col-md-4">&nbsp;</div>
	<div class="col-md-4">
		<div class="box box-success">
			<?php echo form_open('',['id' => 'save_role_form']); ?>
			<div class="box-header">
				<h3 class="box-title"><?php echo ($role_id == 0)?'Add':'Edit'; ?> Role</h3>
				<div class="pull-right">
					<a href="https://www.youtube.com/watch?v=VidboF-1ZlE" target="_blank" class="btn btn-info">Watch tutorial</a>
                	<a href="<?php echo base_url('manage-roles'); ?>" class="btn btn-primary">Manage Roles</a>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="Role">Role</label>
							<input type="text" name="Role" value="<?php echo set_value('Role'); ?>" required id="Role" class="form-control">
							<span class="text-danger"><?php echo validation_show_error('Role'); ?></span>
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