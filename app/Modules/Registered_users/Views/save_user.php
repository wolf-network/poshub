<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <?php echo form_open_multipart('',['id' => 'save_employee_form']); ?>
                <div class="box-header">
                    <h3 class="box-title"><?php echo ($registered_user_id == 0)?'Add':'Edit'; ?> User</h3>
                    <div class="pull-right">
                        <a href="https://www.youtube.com/watch?v=jj6CUqN2MVk" class="btn btn-info" target="_blank">Watch tutorial</a>
                        <a href="<?php echo base_url('manage-users'); ?>" class="btn btn-primary">Manage Users</a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="Name">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="Name" value="<?php echo set_value('Name'); ?>" id="Name" class="form-control">
                                <span class="text-danger"><?php echo validation_show_error('Name'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="Gender">Gender <span class="text-danger">*</span></label>
                                <select name="Gender" id="Gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo(set_value('Gender') == 'Male')?'selected':''; ?> >Male</option>
                                    <option value="Female" <?php echo(set_value('Gender') == 'Female')?'selected':''; ?>>Female</option>
                                    <option value="Transgender" <?php echo(set_value('Gender') == 'Transgender')?'selected':''; ?>>Transgender</option>
                                </select>
                                <span class="text-danger"><?php echo validation_show_error('Gender'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="RoleID">Roles <span class="text-danger">*</span> <a href="javascript:void(0)"  data-toggle="modal" data-target="#saveRoleModal">Add Role</a> </label>
                                <select name="RoleID[]" multiple id="RoleID" class="form-control bs_multiselect" data-non-selected-text="Select Role">
                                <?php 
                                    for($i=0;$i<count($roles);$i++){ 
                                        $selected_role = (!empty($_POST['RoleID']) && in_array($roles[$i]['RoleID'], $_POST['RoleID']))?'selected':'';
                                ?>
                                <option value="<?php echo $roles[$i]['RoleID']; ?>" <?php echo $selected_role; ?> ><?php echo $roles[$i]['Role']; ?></option>
                                <?php } ?>
                                </select>
                                <span class="text-danger"><?php echo validation_show_error('RoleID.*'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="EmailID">Email ID <span class="text-danger">*</span></label>
                                <input type="text" name="EmailID" value="<?php echo set_value('EmailID'); ?>" id="EmailID" class="form-control">
                                <span class="text-danger"><?php echo validation_show_error('EmailID'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="PrivilegeID">Permission <span class="text-danger">*</span></label>
                                <select name="PrivilegeID" id="PrivilegeID" class="form-control">
                                    <option value="">Select Permission</option>
                                    <?php for($i=0;$i<count($privileges);$i++){ ?>
                                        <option value="<?php echo $privileges[$i]['PrivilegeID']; ?>" <?php echo($privileges[$i]['PrivilegeID'] == set_value('PrivilegeID'))?'selected':''; ?> >
                                            <?php echo $privileges[$i]['Privilege'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger"><?php echo validation_show_error('PrivilegeID'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-success pull-right">Save User</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>