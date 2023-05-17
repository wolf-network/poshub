<div class="row">
    <input type="hidden" id="ClientID" value="<?php echo $client_id; ?>">
    <div class="col-md-12">
        <div class="box box-success">
           <?php echo form_open_multipart('',['id' => 'save_client_form']); ?>
            <div class="box-header">
              <h3 class="box-title"><?php echo ($client_id == 0)?'Add':'Edit'; ?> Client</h3>
              <div class="pull-right">
                <a href="https://www.youtube.com/watch?v=y9_1HEEpEw8" target="_blank" class="btn btn-info">Watch tutorial</a>
                <a href="<?php echo base_url('manage-clients'); ?>" class="btn btn-primary">Manage Clients</a>
              </div>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="LogoPath">Client Image [max 2mb] [jpg | jpeg | png | jfif] </label>
                    <div class="clearfix"></div>
                    <label for="LogoPath" class="pointer">
                      <img src="<?php echo media_server(set_value('LogoPath')) ?>" alt="" onerror="this.src='<?php echo base_url('assets/img/upload-photo.png') ?>'" width="100" height="100" id="client-logo">
                    </label>
                    <input type="file" name="LogoPath" id="LogoPath" data-img_prev_selector="#client-logo" class="hide img_replace">
                    <span class="text-danger"><?php echo validation_show_error('LogoPath'); ?></span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="BusinessName">Business Name <span class="text-danger">*</span></label>
                        <input type="text" name="ClientName" id="ClientName" placeholder="Client Name" value="<?php echo set_value('ClientName'); ?>" class="form-control">
                        <span class="text-danger"><?php echo validation_show_error('ClientName'); ?></span>
                    </div> 
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="FirmTypeID">Firm Type</label> 
                       <select name="FirmTypeID" id="FirmTypeID" class="form-control">
                           <option value="">Select Firm Type</option>
                           <?php for($i=0;$i<count($firm_types);$i++){ ?>
                           <option value="<?php echo $firm_types[$i]['FirmTypeID']; ?>" <?php echo($firm_types[$i]['FirmTypeID'] == set_value('FirmTypeID'))?'selected':''; ?> >
                               <?php echo $firm_types[$i]['FirmType']; ?>
                           </option>
                           <?php } ?>
                       </select>
                       <span class="text-danger"><?php echo validation_show_error('FirmTypeID'); ?></span>
                    </div> 
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                      <label for="CIN">Company Identification Number (CIN/EIN, etc) </label>
                      <input type="text" name="CIN" value="<?php echo set_value('CIN'); ?>" id="CIN" class="form-control">
                      <span class="text-danger"><?php echo validation_show_error('CIN'); ?></span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="BusinessIndustryID">Industry <a href="javascript:void(0)" data-toggle="modal" data-target="#saveIndustryModal">Add Industry</a></label>
                    <select name="BusinessIndustryID[]" multiple id="BusinessIndustryID" class="form-control bs_multiselect" data-non-selected-text="Select Industry">
                        <?php for($i=0;$i<count($business_industries);$i++){ 
                          $selected_business_industries = (!empty($_POST['BusinessIndustryID']) && in_array($business_industries[$i]['BusinessIndustryID'],$_POST['BusinessIndustryID']))?'selected':'';
                        ?>
                        <option value="<?php echo $business_industries[$i]['BusinessIndustryID']; ?>" <?php echo $selected_business_industries; ?>>
                            <?php echo $business_industries[$i]['BusinessIndustry']; ?>
                        </option>
                        <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo validation_show_error('BusinessIndustryID.*'); ?></span>
                  </div> 
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="TaxIdentificationTypeID">Tax Identification Type</label>
                    <select name="TaxIdentificationTypeID" id="TaxIdentificationTypeID" class="form-control">
                      <option value="">Select Tax Identification Type</option>
                      <?php for($i=0;$i<count($tax_identification_types);$i++){ ?>
                        <option value="<?php echo $tax_identification_types[$i]['TaxIdentificationTypeID']; ?>" <?php echo($tax_identification_types[$i]['TaxIdentificationTypeID'] == set_value('TaxIdentificationTypeID'))?'selected':''; ?> ><?php echo $tax_identification_types[$i]['TaxIdentificationType']; ?></option>
                      <?php } ?>
                    </select>
                    <span class="text-danger"><?php echo validation_show_error('TaxIdentificationTypeID'); ?></span>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="TaxIdentificationNumber">Tax Identification Number</label>
                    <input type="text" name="TaxIdentificationNumber" value="<?php echo set_value('TaxIdentificationNumber'); ?>" placeholder="" id="TaxIdentificationNumber" class="form-control">
                    <span class="text-danger"><?php echo validation_show_error('TaxIdentificationNumber'); ?></span>
                  </div>
                </div>
              </div>
              <hr>
              <fieldset>
                  <legend>
                      <h4>Contact details</h4>
                  </legend>
                  <div class="row">
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="ClientUserFirstName">First Name <span class="text-danger">*</span></label>
                              <input type="text" name="ClientUserFirstName" value="<?php echo set_value('ClientUserFirstName'); ?>" id="ClientUserFirstName" class="form-control">
                              <span class="text-danger"><?php echo validation_show_error('ClientUserFirstName'); ?></span>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="ClientUserLastName">Last Name <span class="text-danger">*</span></label>
                              <input type="text" name="ClientUserLastName" value="<?php echo set_value('ClientUserLastName'); ?>" id="ClientUserLastName" class="form-control">
                              <span class="text-danger"><?php echo validation_show_error('ClientUserLastName'); ?></span>
                          </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="">Select Role <a href="javascript:void(0)"  data-toggle="modal" data-target="#saveRoleModal">Add Role</a></label>
                          <select name="RoleID[]" multiple id="RoleID" class="form-control bs_multiselect">
                            <option value="">Select Role</option>
                            <?php 
                              for($i=0;$i<count($roles);$i++){ 
                                $selected = (!empty($_POST['RoleID']) && in_array($roles[$i]['RoleID'], $_POST['RoleID']))?'selected':'';
                            ?>
                              <option value="<?php echo $roles[$i]['RoleID']; ?>" <?php echo $selected; ?> >
                                <?php echo $roles[$i]['Role'] ?>
                              </option>
                            <?php } ?>
                          </select>
                          <span class="text-danger"><?php echo validation_show_error('RoleID'); ?></span>
                        </div>
                      </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                          <label for="ClientUserEmailID">Email ID </label>
                          <input type="text" name="ClientUserEmailID" value="<?php echo set_value('ClientUserEmailID'); ?>" id="ClientUserEmailID" class="form-control">
                          <span class="text-danger"><?php echo validation_show_error('ClientUserEmailID'); ?></span>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                          <label for="ClientUserContactNo">Contact No <span class="text-danger">*</span></label>
                          <input type="text" name="ClientUserContactNo" value="<?php echo set_value('ClientUserContactNo'); ?>" id="ClientUserContactNo" class="form-control">
                          <span class="text-danger"><?php echo validation_show_error('ClientUserContactNo'); ?></span>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="CountryID">Select country <span class="text-danger">*</span></label>
                              <select name="CountryID" id="CountryID" required class="form-control bs_multiselect country">
                                <option value="">Select country</option>
                                <?php for($i=0;$i<count($countries);$i++){ ?>
                                <option value="<?php echo $countries[$i]['CountryID']; ?>" <?php echo($countries[$i]['CountryID'] == set_value('CountryID'))?'selected':''; ?> ><?php echo $countries[$i]['CountryName']; ?></option>
                                <?php } ?>
                              </select>
                              <span class="text-danger"><?php echo validation_show_error('CountryID'); ?></span>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="StateID">Select state <span class="text-danger">*</span></label>
                              <select name="StateID" id="StateID" required class="form-control bs_multiselect state" data-selected_state="<?php echo set_value('StateID'); ?>">
                              </select>
                              <span class="text-danger"><?php echo validation_show_error('StateID'); ?></span>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="CityID">Select city <span class="text-danger">*</span></label>
                              <select name="CityID" id="CityID" required class="form-control bs_multiselect city" data-selected_city="<?php echo set_value('CityID'); ?>"></select>
                              <span class="text-danger"><?php echo validation_show_error('CityID'); ?></span>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                          <label for="Address">Full Address</label>
                          <textarea name="Address" id="Address" cols="30" rows="4" class="form-control"><?php echo set_value('Address'); ?></textarea>
                          <span class="text-danger"><?php echo validation_show_error('Address'); ?></span>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="ClientRating">Client Rating</label> <br>
                        <div class="rating"></div>
                        <input type="hidden" value="<?php echo set_value('ClientRating'); ?>" name="" class="ClientRating">
                      </div>
                    </div>
                  </div>
              </fieldset>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-success pull-right">Save Client</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>