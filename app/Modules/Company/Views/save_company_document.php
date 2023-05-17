<div class="row">
	<div class="col-md-12">
		<div class="box box-success">
			<?php echo form_open_multipart('',['id' => 'save-company-document-form']); ?>
			<div class="box-header with-border">
				<h3 class="box-title pull-left">Add Company Document</h3>
				<div class="pull-right">
					<a href="<?php echo base_url('manage-company-documents'); ?>" class="btn btn-info">
						Manage Company Documents
					</a>
				</div>
			</div>
			<div class="box-body">
				<span class="pull-right">
                    <b>Note: MAX File size allowed is 2MB <br> Extensions allowed are: [jpg|png|pdf|jfif]</b>
                </span>
                <div class="clearfix"></div>
				<div class="row">
					<div class="col-md-4">
                        <div class="form-group">
                            <label for="CountryID">Country <span class="text-danger">*</span></label>
                            <select name="CountryID" required class="form-control bs_multiselect country" data-non-selected-text="Select Country">
                                <option value="">Select Country</option>
                                <?php for($i=0;$i<count($countries);$i++){ ?>
                                <option value="<?php echo $countries[$i]['CountryID']; ?>" <?php echo(set_value('CountryID') == $countries[$i]['CountryID'])?'selected':''; ?> ><?php echo $countries[$i]['CountryName'] ?></option>
                                <?php } ?>
                            </select>
                            <span class="text-danger"><?php echo validation_show_error('CountryID'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="StateID">State <span class="text-danger">*</span></label>
                            <select name="StateID" required class="form-control bs_multiselect state" data-non-selected-text="Select State" data-selected_state="<?php echo set_value('StateID'); ?>">
                                <option value="">Select State</option>
                            </select>
                            <span class="text-danger"><?php echo validation_show_error('StateID'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="CityID">City</label>
                            <select name="CityID" class="form-control bs_multiselect city" data-non-selected-text="Select City" data-selected_city="<?php echo set_value('CityID'); ?>">
                                <option value="">Select City</option>
                            </select>
                            <span class="text-danger"><?php echo validation_show_error('CityID'); ?></span>
                        </div>
                    </div>
				</div>
				<div class="documents-container">
                    <?php 
                        $documents_count = (isset($_POST['DocumentName']))?count($_POST['DocumentName']):1;
                        for($i=0;$i<$documents_count;$i++){
                    ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Document Name <span class="text-danger">*</span></label>
                                <input type="text" name="DocumentName[]" required class="form-control" value="<?php echo set_value('DocumentName['.$i.']'); ?>" placeholder="E.g: GST">
                                <span class="text-danger"><?php echo validation_show_error('DocumentName.'.$i); ?></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Document File</label>
                                <input type="file" name="DocumentFilePath[]">
                                <span class="text-danger"><?php echo validation_show_error('DocumentFilePath.'.$i); ?></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Document Description <span class="text-danger">*</span></label>
                                <input type="text" name="DocumentDescription[]" placeholder="E.g: GST No" class="form-control" required>
                                <span class="text-danger"><?php echo validation_show_error('DocumentDescription.'.$i); ?></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">&nbsp;</label>
                                <br />
                                <?php if($documents_count > 1){ ?>
                                    <button type="button" class="btn btn-danger remove-document">-</button>
                                <?php } ?>
                                
                                <?php if($i == ($documents_count - 1)){ ?>
                                    <button type="button" class="btn btn-success add-document">+</button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
			</div>
			<div class="box-footer">
                <div class="pull-right">
                    <button type="submit" name="save" value="save" class="btn btn-warning">Save documents</button> 
                    <button type="submit" name="save" value="save_add" class="btn btn-success">Save &amp; add more documents</button>
                </div>
            </div>
            <?php echo form_close(); ?>
		</div>
	</div>
</div>