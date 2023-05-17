<div class="row">
    <div class="col-md-12 col-xs-12">
        <?php if(!empty(validation_errors())){ ?>
            <div class="alert alert-danger">Kindly fix the errors below</div>
        <?php } ?>
        <div class="box box-success">
            <?php echo form_open(); ?>
            <div class="box-header">
                <h3 class="box-title">Add Client User</h3>
                <a href="<?php echo base_url('client/manage-users/'.$client_id); ?>" class="btn btn-success pull-right">Manage client Users</a>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ClientUserFirstName">First Name</label>
                            <input type="text" name="ClientUserFirstName" value="<?php echo set_value('ClientUserFirstName'); ?>" id="ClientUserFirstName" class="form-control">
                            <span class="text-danger"><?php echo form_error('ClientUserFirstName'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ClientUserLastName">Last Name</label>
                            <input type="text" name="ClientUserLastName" value="<?php echo set_value('ClientUserLastName'); ?>" id="ClientUserLastName" class="form-control">
                            <span class="text-danger"><?php echo form_error('ClientUserLastName'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Role <span class="text-danger">*</span></label>
                            <select name="RoleID[]" multiple id="RoleID" class="form-control bs_multiselect" data-non-selected-text="Select Roles">
                                <?php for($i=0;$i<count($roles);$i++){
                                    $selected = (!empty($_POST['RoleID']) && in_array($roles[$i]['RoleID'],$_POST['RoleID']))?'selected':'';
                                ?>
                                <option value="<?php echo $roles[$i]['RoleID']; ?>" <?php echo $selected; ?>><?php echo $roles[$i]['Role']; ?></option>
                                <?php } ?>
                            </select>
                            <span class="text-danger"><?php echo form_error('RoleID'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ClientUserEmailID">Official Email ID</label>
                            <input type="text" name="ClientUserEmailID" value="<?php echo set_value('ClientUserEmailID'); ?>" id="ClientUserEmailID" class="form-control">
                            <span class="text-danger"><?php echo form_error('ClientUserEmailID'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ClientUserContactNo">Official Contact No</label>
                            <input type="text" name="ClientUserContactNo" value="<?php echo set_value('ClientUserContactNo'); ?>" id="ClientUserContactNo" class="form-control">
                            <span class="text-danger"><?php echo form_error('ClientUserContactNo'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-success">
                            <div class="box-header"><h3 class="box-title">User Geographic Details</h3></div>
                            <div class="box-body">
                                <div class="row">
                                   <div class="col-md-4">
                                       <div class="form-group">
                                            <label for="ClientUserCountryID">Country</label>
                                            <select name="ClientUserCountryID[]" id="ClientUserCountryID" class="form-control bs_multiselect country" data-non-selected-text="Select country" multiple>
                                                <?php 
                                                    for($i=0;$i<count($countries);$i++){ 
                                                    $selected_countries = (in_array($countries[$i]['CountryID'],$_POST['ClientUserCountryID']))?'selected':'';
                                                ?>
                                                    <option value="<?php echo $countries[$i]['CountryID']; ?>" <?php echo $selected_countries; ?> ><?php echo $countries[$i]['CountryName']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('ClientUserCountryID[]'); ?></span>
                                        </div>
                                   </div>
                                   <div class="col-md-4">
                                       <div class="form-group">
                                            <label for="ClientUserStateID">State</label>
                                            <?php
                                                $selected_state = (!empty($_POST['ClientUserStateID']))?implode(',',$_POST['ClientUserStateID']):'';
                                            ?>
                                            <select name="ClientUserStateID[]" id="ClientUserStateID" class="form-control state bs_multiselect" data-non-selected-text="Select state" data-selected_state="<?php echo $selected_state; ?>" multiple>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('ClientUserStateID[]'); ?></span>
                                        </div>
                                   </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success pull-right">Save User</button>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>