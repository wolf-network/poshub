<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <?php echo form_open(); ?>
                <div class="box-header with-border">
    				<h3 class="box-title pull-left">Invoice Settings</h3>
    			</div>
    			<div class="box-body">
    			    <div class="row">
    			        <div class="col-md-4">
    			            <div class="form-group">
    			                <label for="TermsAndConditions">Invoice Terms And Conditions <span class="text-danger">*</span></label>
    			                <textarea name="TermsAndConditions" id="TermsAndConditions" cols="30" rows="5" required class="form-control"><?php echo set_value('TermsAndConditions'); ?></textarea>
    			                <span class="text-danger"><?php echo validation_show_error('TermsAndConditions'); ?></span>
    		                </div>
    		            </div>
    			    </div>
    		    </div>
    		    <div class="box-footer">
    		        <button type="submit" class="btn btn-success pull-right">Save</button>
    		    </div>
		    <?php echo form_close(); ?>
        </div>
    </div>
</div>