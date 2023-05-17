<div class="row">
    <div class="col-md-6 col-xs-12 col-md-offset-3">
        <div class="box box-success">
           <?php echo form_open(); ?>
            <div class="box-header">
                <h3 class="box-title"><?php echo ($brand_id == 0)?'Add':'Edit'; ?> Brand</h3>
                <a href="<?php echo base_url('manage-brands/'.$client_data['ClientID']); ?>" class="btn btn-primary pull-right">Manage Brands</a>
            </div>
            <div class="box-body">
              <div class="form-group">
                  <label for="ClientName">Client Name</label>
                  <input type="text" class="form-control" value="<?php echo $client_data['ClientName']; ?>" disabled="">
              </div>
              <div class="form-group">
                  <label for="Brand">Brand Name</label>
                  <input type="text" name="Brand" id="Brand" placeholder="Brand Name" value="<?php echo set_value('Brand'); ?>" class="form-control">
                  <span class="text-danger"><?php echo form_error('Brand'); ?></span>
              </div> 
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-success pull-right">Save Brand</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>