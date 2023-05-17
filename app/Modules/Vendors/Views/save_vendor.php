<div class="row">
    <div class="col-md-12 col-xs-12">
        <?php if(!empty(validation_errors())){ ?>
            <div class="alert alert-danger">Kindly fix the errors below</div>
        <?php } ?>
        
        <div class="box box-success">
           <?php echo form_open_multipart('',['id' => 'save_vendor_form']); ?>
            <div class="box-header">
                <h3 class="box-title"><?php echo ($vendor_id == 0)?'Add':'Edit'; ?> Vendor</h3>
                <div class="pull-right">
                  <a href="https://www.youtube.com/watch?v=hQENH21lojE" target="_blank" class="btn btn-info">Watch tutorial</a>
                  <a href="<?php echo base_url('manage-vendors'); ?>" class="btn btn-primary">Manage Vendors</a>
                </div>
            </div>
            <div class="box-body">
              <div class="row">
                  <div class="col-md-4">
                      <div class="form-group">
                          <label for="VendorName">Vendor Name <span class="text-danger">*</span></label>
                          <input type="text" name="VendorName" id="VendorName" placeholder="Vendor Name" value="<?php echo set_value('VendorName'); ?>" required class="form-control">
                          <span class="text-danger"><?php echo validation_show_error('VendorName'); ?></span>
                      </div> 
                  </div>
                  <div class="col-md-4">
                      <div class="form-group">
                          <label for="ServiceID">Vendor Services <span class="text-danger">*</span> <a href="javascript:void(0)" data-toggle="modal" data-target="#saveServiceModal">Add Service</a> </label>
                          <select name="ServiceID[]" id="ServiceID" required class="form-control bs_multiselect" multiple data-non-selected-text="Select Vendor Services">
                              <?php 
                                for($i=0;$i<count($services);$i++){ 
                                    $selected_services = (!empty($_POST['ServiceID']) && in_array($services[$i]['ServiceID'],$_POST['ServiceID']))?'selected':'';
                              ?>
                              <option value="<?php echo $services[$i]['ServiceID']; ?>" <?php echo $selected_services; ?> ><?php echo $services[$i]['ServiceType']; ?></option>
                              <?php } ?>
                          </select>
                          <span class="text-danger"><?php echo validation_show_error('ServiceID.*'); ?></span>
                      </div>
                  </div>
                  <div class="col-md-4">
                      <div class="form-group">
                         <label for="FirmTypeID">Firm Type <span class="text-danger">*</span></label> 
                         <select name="FirmTypeID" id="FirmType" required class="form-control">
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
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                      <label for="CIN">Company Identification Number (CIN/EIN, etc) </label>
                      <input type="text" name="CIN" value="<?php echo set_value('CIN'); ?>" id="CIN" class="form-control">
                      <span class="text-danger"><?php echo validation_show_error('CIN'); ?></span>
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
                    <label for="TaxIdentificationNumber">Tax Identification Number (PAN/SSN/ITIN) </label>
                    <input type="text" name="TaxIdentificationNumber" value="<?php echo set_value('TaxIdentificationNumber'); ?>" placeholder="" id="TaxIdentificationNumber" class="form-control">
                    <span class="text-danger"><?php echo validation_show_error('TaxIdentificationNumber'); ?></span>
                  </div>
                </div>
              </div>
              <hr>
              <h4 class="box-title">Vendor head office details</h4>
              <br>
              <fieldset>
                  <legend><h4>Geography details</h4></legend>
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
                              <label for="StateID">Select State <span class="text-danger">*</span></label>
                              <select name="StateID" id="StateID" required class="form-control bs_multiselect state" data-selected_state="<?php echo set_value('StateID'); ?>">
                              </select>
                              <span class="text-danger"><?php echo validation_show_error('StateID'); ?></span>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="CityID">Select City <span class="text-danger">*</span></label>
                              <select name="CityID" id="CityID" required class="form-control bs_multiselect city" data-selected_city="<?php echo set_value('CityID'); ?>"></select>
                              <span class="text-danger"><?php echo validation_show_error('CityID'); ?></span>
                          </div>
                      </div>
                  </div>
              </fieldset>
              <br>
              <fieldset>
                  <legend><h4>Contact details</h4></legend>
                  <div class="row">
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="VendorUserFirstName ">First Name <span class="text-danger">*</span></label>
                              <input type="text" name="VendorUserFirstName" value="<?php echo set_value('VendorUserFirstName'); ?>" id="VendorUserFirstName" required class="form-control">
                              <span class="text-danger"><?php echo validation_show_error('VendorUserFirstName'); ?></span>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="VendorUserLastName ">Last Name <span class="text-danger">*</span></label>
                              <input type="text" name="VendorUserLastName" value="<?php echo set_value('VendorUserLastName'); ?>" id="VendorUserLastName" required class="form-control">
                              <span class="text-danger"><?php echo validation_show_error('VendorUserLastName'); ?></span>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="RoleID">Role <span class="text-danger">*</span> <a href="javascript:void(0)"  data-toggle="modal" data-target="#saveRoleModal">Add Role</a> </label>
                              <select name="RoleID[]" id="RoleID" required data-non-selected-text="Select Role" data-include-select-all-option="false" class="form-control bs_multiselect" multiple>
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
                              <label for="VendorUserContactNo">Contact No <span class="text-danger">*</span></label>
                              <input type="text" name="VendorUserContactNo" value="<?php echo set_value('VendorUserContactNo'); ?>" id="VendorUserContactNo" class="form-control">
                             
                              <span class="text-danger"><?php echo validation_show_error('VendorUserContactNo'); ?></span>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="VendorUserEmailID ">Official Email ID<span class="text-danger">*</span></label>
                              <input type="email" name="VendorUserEmailID" value="<?php echo set_value('VendorUserEmailID'); ?>" id="VendorUserEmailID" required class="form-control">
                              <span class="text-danger"><?php echo validation_show_error('VendorUserEmailID'); ?></span>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="Address">Full Address <span class="text-danger">*</span></label>
                              <textarea name="Address" id="Address" required cols="30" rows="4" class="form-control"><?php echo set_value('Address'); ?></textarea>
                              <span class="text-danger"><?php echo validation_show_error('Address'); ?></span>
                          </div>
                      </div>
                  </div>
              </fieldset>
              
              <fieldset>
                  <legend><h4>Vendor Bank Details</h4></legend>
                  <div class="row">
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="BankID">Bank Name <span class="text-danger">*</span></label>
                              <select name="BankID" id="BankID" class="bank_id form-control bs_multiselect">
                                  <option value="">Select Bank Name</option>
                                  <?php for($i=0;$i<count($bank_details);$i++){ ?>
                                  <option value="<?php echo $bank_details[$i]['BankID']; ?>" <?php echo($bank_details[$i]['BankID'] == set_value('BankID'))?'selected':''; ?> >
                                      <?php echo $bank_details[$i]['BankName']; ?>
                                  </option>
                                  <?php } ?>
                              </select>
                              <span class="text-danger"><?php echo validation_show_error('BankID'); ?></span>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group bank_details_container">
                              <label for="BankDetailsID">IFSC Code <span class="text-danger">*</span></label>
                              
                                  <select name="BankDetailsID" id="BankDetailsID" class="bank_details form-control bs_multiselect" data-selected_bank_details_id="<?php echo set_value('BankDetailsID'); ?>" data-offset="0">
                                      <option value="">Select IFSC Code</option>
                                  </select>
                              
                              <span class="text-danger"><?php echo validation_show_error('BankDetailsID'); ?></span>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="Cheque">Cancelled Cheque [max 2mb] [jpg | jpeg | png | jfif] </label>
                              <br>
                              <?php if(!empty(set_value('ChequeImgPath')) && !empty($vendor_id)){ ?>
                              <a href="<?php echo media_server(set_value('ChequeImgPath')); ?>" download class="btn btn-primary btn-xs pull-left"><i class="fa fa-download"></i></a>
                              <span class="pull-left">&nbsp;</span>
                              <?php } ?>
                              <input type="file" id="ChequeImgPath" name="ChequeImgPath">
                              <span class="text-danger"><?php echo validation_show_error('ChequeImgPath'); ?></span>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="AccountHolderName">Account Holder Name <span class="text-danger">*</span></label>
                              <input type="text" name="AccountHolderName" value="<?php echo set_value('AccountHolderName'); ?>" id="AccountHolderName" class="form-control">
                              <span class="text-danger"><?php echo validation_show_error('AccountHolderName'); ?></span>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="AccountNo">Account No <span class="text-danger">*</span></label>
                              <input type="password" name="AccountNo" value="<?php echo set_value('AccountNo'); ?>" id="AccountNo" class="form-control">
                              <span class="text-danger"><?php echo validation_show_error('AccountNo'); ?></span>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="form-group">
                              <label for="ConfirmAccountNo">Confirm Account No <span class="text-danger">*</span></label>
                              <input type="text" name="ConfirmAccountNo" value="<?php echo set_value('ConfirmAccountNo'); ?>" id="ConfirmAccountNo" class="form-control">
                              <span class="text-danger"><?php echo validation_show_error('ConfirmAccountNo'); ?></span>
                          </div>
                      </div>
                  </div>
              </fieldset>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-success pull-right">Save Vendor</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Service Modal -->
<div id="saveServiceModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Service</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="ServiceType">Service</label>
              <input type="text" name="ServiceType" id="ServiceType" class="form-control">
              <span class="text-danger ServiceType-error"></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success save-service">Save</button>
      </div>
    </div>

  </div>
</div>