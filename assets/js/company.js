$(document).on('click','.remove-document', function(){
   $(this).parent().parent().parent().remove();
   if($('.add-document').length == 0){
       $('.remove-document:last').after(' <button type="button" class="btn btn-success add-document">+</button>');   
   }
    
   if($('.remove-document').length == 1){
       $('.remove-document').remove();
   }
});


$(document).on('click','.add-document', function(){
   var html = '<div class="row"><div class="col-md-3"><div class="form-group"><label for="">Document Name <span class="text-danger">*</span></label><input type="text" name="DocumentName[]" class="form-control" placeholder="E.g: GST" ></div></div><div class="col-md-3"><div class="form-group"><label for="">Document File</label><input type="file" name="DocumentFilePath[]"></div></div><div class="col-md-4"><div class="form-group"><label for="">Document Description</label><input type="text" name="DocumentDescription[]" placeholder="E.g: GST No" class="form-control"></div></div><div class="col-md-2"><div class="form-group"><label for="">&nbsp;</label><br /> <button type="button" class="btn btn-danger remove-document">-</button> <button type="button" class="btn btn-success add-document">+</button></div></div></div>'; 
   
   $(this).parent().parent().parent().find('.remove-document').remove()
   $(this).after(' <button type="button" class="btn btn-danger remove-document">-</button>').remove();
   $('.documents-container').append(html);
});

$(document).on('click','.delete-document', function(){
    var company_document_id = $(this).attr('data-company_document_id');

     swal({
    title: "Are you sure?",
    text: "This document will be deleted permanently.!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {
      swal("Please wait, your document is being deleted!", {
        icon: "info",
      });

      window.location.href = base_url('delete-company-document/'+company_document_id);
    } else {
      swal("Your document is safe!");
    }
  });
});

$("#company_bank_details_form").validate({
  ignore:[],
  rules: {
      "BankID": {
          required: true,
      },
      "BankDetailsID": {
          required: true,
      },
      "AccountHolderName": {
          required: true,
      },
      "AccountNo": {
          required: true
      },
      "ConfirmAccountNo":{
        required: true,
        equalTo: "#AccountNo"
      },
      'QRCode':{
        accept: "image/jpg,image/jpeg,image/png,application/jfif"
      }
  },
  messages: {
      "ConfirmAccountNo":{
        equalTo: "Confirm Account No should be the same as Account No.",
      },
      'QRCode':{
        accept: "QRCode should be an valid image."
      }
  },
  submitHandler: function (form) {
      $('.overlay-wrapper').removeClass('hide');
      $('.loader').addClass('breathing-animation');
      form.submit();
  }
});

$("#edit-comp-form").validate({
    ignore:[],
    rules: {
        'CompName':{
          required: true
        },
        'EmailID':{
          required: true
        },
        'ContactNo':{
          required: true,
          digits:true,
          minlength: 10,
          maxlength: 12
        },
        'FirmTypeID':{
          required: true
        },
        'CompLogoPath':{
          extension: "jpg|jpeg|png|jfif",
          filesize : 2, // here we are working with MB
        }
    },
    messages: {
        'ContactNo':{
          minlength: "Please enter at least 10 numbers",
          maxlength: "Please do not enter more than 12 numbers"
        },
        "CompLogoPath":{
          extension: "Image should be either jpg, jpeg, png, jfif"
        }
    },
    submitHandler: function (form) {
        $('.overlay-wrapper').removeClass('hide');
        $('.loader').addClass('breathing-animation');
        form.submit();
    },
    errorPlacement: function(error, element) {
        // Add the `help-block` class to the error element
      error.addClass("help-block");
      if (element.prop("type") === "checkbox") {
            error.insertAfter(element.parent("label"));
      }else if(element.prop("type") === "radio"){
        error.appendTo(element.parent());
      }
      else if (element.hasClass('bs_multiselect')) {

            error.insertAfter(element.next('.btn-group'))
      }else if(element.parent().hasClass('input-group')){
        error.insertAfter(element.parent())
      } else {
        error.insertAfter(element);
      }
    }
});

$("#save-company-document-form").validate({
    ignore:[],
    rules: {
        'CountryID':{
          required: true
        },
        'StateID':{
          required: true
        },
        'DocumentName[]':{
          required: true
        },
        'DocumentFilePath[]':{
          extension: "jpg|jpeg|png|jfif|pdf",
          filesize : 2, // here we are working with MB
        },
        'DocumentDescription[]':{
          required: true
        }
    },
    messages: {
        'ContactNo':{
          minlength: "Please enter at least 10 numbers",
          maxlength: "Please do not enter more than 12 numbers"
        },
        "DocumentFilePath[]":{
          extension: "Document file should be either jpg, jpeg, png, jfif or pdf"
        }
    },
    submitHandler: function (form) {
        $('.overlay-wrapper').removeClass('hide');
        $('.loader').addClass('breathing-animation');
        form.submit();
    },
    errorPlacement: function(error, element) {
        // Add the `help-block` class to the error element
      error.addClass("help-block");
      if (element.prop("type") === "checkbox") {
            error.insertAfter(element.parent("label"));
      }else if(element.prop("type") === "radio"){
        error.appendTo(element.parent());
      }
      else if (element.hasClass('bs_multiselect')) {

            error.insertAfter(element.next('.btn-group'))
      }else if(element.parent().hasClass('input-group')){
        error.insertAfter(element.parent())
      } else {
        error.insertAfter(element);
      }
    }
});

$("#save-company-address").validate({
    ignore:[],
    rules: {
        'CountryID':{
          required: true
        },
        'StateID':{
          required: true
        },
        'CityID':{
          required: true
        },
        'Address':{
          required: true
        }
    },
    messages: {
    },
    submitHandler: function (form) {
        $('.overlay-wrapper').removeClass('hide');
        $('.loader').addClass('breathing-animation');
        form.submit();
    },
    errorPlacement: function(error, element) {
        // Add the `help-block` class to the error element
      error.addClass("help-block");
      if (element.prop("type") === "checkbox") {
            error.insertAfter(element.parent("label"));
      }else if(element.prop("type") === "radio"){
        error.appendTo(element.parent());
      }
      else if (element.hasClass('bs_multiselect')) {

            error.insertAfter(element.next('.btn-group'))
      }else if(element.parent().hasClass('input-group')){
        error.insertAfter(element.parent())
      } else {
        error.insertAfter(element);
      }
    }
});

$("#save-social-media").validate({
    ignore:[],
    rules: {
        'SocialPlatformID':{
          required: true
        },
        'SocialPlatformLink':{
          required: true
        }
    },
    messages: {
    },
    submitHandler: function (form) {
        $('.overlay-wrapper').removeClass('hide');
        $('.loader').addClass('breathing-animation');
        form.submit();
    },
    errorPlacement: function(error, element) {
        // Add the `help-block` class to the error element
      error.addClass("help-block");
      if (element.prop("type") === "checkbox") {
            error.insertAfter(element.parent("label"));
      }else if(element.prop("type") === "radio"){
        error.appendTo(element.parent());
      }
      else if (element.hasClass('bs_multiselect')) {

            error.insertAfter(element.next('.btn-group'))
      }else if(element.parent().hasClass('input-group')){
        error.insertAfter(element.parent())
      } else {
        error.insertAfter(element);
      }
    }
});

$(document).on('click','.delete-company-service-tax', function(){
    var company_service_tax_id = $(this).attr('data-company_service_tax_id');

     swal({
    title: "Are you sure?",
    text: "This will be permanently deleted from our system.!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {
      swal("Please wait, your service tax data is being deleted!", {
        icon: "info",
      });

      window.location.href = base_url('delete-company-service-tax/'+company_service_tax_id);
    } else {
      swal("Your data is safe!");
    }
  });
});