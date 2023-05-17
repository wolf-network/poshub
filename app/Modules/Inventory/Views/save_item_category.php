<div class="row">
	<div class="col-md-4">&nbsp;</div>
	<div class="col-md-5">
		<div class="box box-success">
			<?php echo form_open('',['id' => 'save_item_category_form']); ?>
			<div class="box-header">
				<h3 class="box-title"><?php echo ($item_category_master_id == 0)?'Add':'Edit'; ?> Item Category</h3>
                <a href="<?php echo base_url('manage-item-categories'); ?>" class="btn btn-primary pull-right">Manage Item Categories</a>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="ItemCategory">Item Category</label>
							<input type="text" name="ItemCategory" value="<?php echo set_value('ItemCategory'); ?>" required id="ItemCategory" class="form-control">
							<span class="text-danger"><?php echo validation_show_error('ItemCategory'); ?></span>
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				<center>
					<button class="btn btn-success">Save</button>
				</center>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>