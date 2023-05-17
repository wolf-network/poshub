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
   var html = '<div class="row"><div class="col-md-3"><div class="form-group"><label for="">Document Name <span class="text-danger">*</span></label><input type="text" name="DocumentName[]" class="form-control"></div></div><div class="col-md-3"><div class="form-group"><label for="">Document File</label><input type="file" name="DocumentFile[]"></div></div><div class="col-md-4"><div class="form-group"><label for="">Document Description</label><input type="text" name="DocumentDescription[]" placeholder="E.g: Pan Card No" class="form-control"></div></div><div class="col-md-2"><div class="form-group"><label for="">&nbsp;</label><br /> <button type="button" class="btn btn-danger remove-document">-</button> <button type="button" class="btn btn-success add-document">+</button></div></div></div>'; 
   
   $(this).parent().parent().parent().find('.remove-document').remove()
   $(this).after(' <button type="button" class="btn btn-danger remove-document">-</button>').remove();
   $('.documents-container').append(html);
});

$(document).on('click','.manage-vendor-details', function(){
   var vendor_id = $(this).attr('data-vendor_id');
   $('#manage-vendor-documents').attr('href',base_url('manage-vendor-documents/'+vendor_id));
   $('#manage-vendor-service-taxes').attr('href',base_url('manage-vendor-service-taxes/'+vendor_id));
   $('#view-vendor-inwards').attr('href',base_url('stock-inward-history?vendor_id='+vendor_id));
   $('#view-vendor-expenses').attr('href',base_url('view-expenses?vendor_id='+vendor_id));
});

$(document).on('click','.view-documents', function(){
   var vendor_geography_id = $(this).attr('data-vendor_geography_id'); 
   $.ajax({
      url: base_url('api/vendors/get_vendor_document_data'),
      data:{
          'vendor_geography_id':vendor_geography_id
      },
      type:"GET",
      dataType:"json",
      success:function(response){
          $('.vendor-documents-table tbody').empty();
          var html = '';
          var i = 0;
          $.each(response.data, function(index,item){
              var sr_no = i + 1;
              html += '<tr><td>'+sr_no+'</td><td>'+item.DocumentName+'</td><td><a href="'+media_server(item.VendorDocumentMediaPath)+'" download class="btn btn-primary btn-xs"><i class="fa fa-download"></i></a></td></tr>';
              i++;
          });
          
          $('.vendor-documents-table tbody').html(html);
      },
      error: function(response){
          alert(response.responseJSON.msg);
      }
   });
});

$(document).on('click','.delete-vendor', function(){
    var vendor_id = $(this).attr('data-vendor_id');

     swal({
    title: "Are you sure?",
    text: "Once deleted, you will have to contact your development team to restore the vendor.!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {
      swal("Please wait, your vendor is being deleted!", {
        icon: "info",
      });

      window.location.href = base_url('delete-vendor/'+vendor_id);
    } else {
      swal("Your vendor's data is safe!");
    }
  });
});



$("#save_vendor_form").validate({
    ignore:[],
    rules: {
        "VendorName": {
            required: true,
        },
        "VendorUserEmailID": {
            required: true,
            email: true
        },
        "ServiceID[]":{
          required: true,
        },
        "FirmTypeID":{
          required: true,
        },
        "TaxIdentificationTypeID": {
          required: function(){
            return $('#TaxIdentificationNumber').val().length > 0
          }
        },
        "TaxIdentificationNumber": {
          required: function(){
            return $('#TaxIdentificationTypeID').val().length > 0
          }
        },
        "StateID":{
          required: true,
        },
        "CityID":{
          required: true,
        },
        'VendorUserFirstName':{
          required: true,
        },
        'VendorUserLastName':{
          required: true,
        },
        'Address':{
          required: true,
        },
        'RoleID[]':{
          required: true,
        },
        'VendorUserContactNo':{
          required: true,
          // digits:true,
          minlength: 10,
          maxlength: 12
        },
        'GST':{
          accept: "image/jpg,image/jpeg,image/png,application/pdf"
        },
        'PANCard':{
          accept: "image/jpg,image/jpeg,image/png,application/pdf"
        },
        'MSME':{
          accept: "image/jpg,image/jpeg,image/png,application/pdf"
        },
        'TAN':{
          accept: "image/jpg,image/jpeg,image/png,application/pdf"
        },
        'VRF':{
          accept: "image/jpg,image/jpeg,image/png,application/pdf"
        },
        'ChequeImgPath':{ 
          extension: "jpg|jpeg|png|jfif",
          filesize : 2, // here we are working with MB
        },
        'BankID':{
          required: true
        },
        'BankDetailsID':{
          required: true
        },
        'AccountHolderName':{
          required: true
        },
        'AccountNo':{
          required: true,
        },
        'ConfirmAccountNo':{
          required: true,
          equalTo: "#AccountNo"
        }
    },
    messages: {
        "VendorUserEmailID": {
            required: "Please, enter an email",
            email: "Email is invalid"
        },
        "ServiceID[]":{
          required: "Please, select at least 1 Service",
        },
        "RoleID":{
          required: "Please, select  at least 1 Role",
        },
        "VendorUserContactNo":{
          minlength: "Please enter at least 10 numbers",
          maxlength: "Please do not enter more than 12 numbers"
        },
        'ConfirmAccountNo':{
          equalTo: "Confirm account no should be the same as Account No"
        },
        'GST':{
          accept: "GST should be an image or PDF."
        },
        'MSME':{
          accept: "MSME should be an image or PDF."
        },
        'TAN':{
          accept: "TAN should be an image or PDF."
        },
        'VRF':{
          accept: "VRF should be an image or PDF."
        },
        'ChequeImgPath':{
          accept:"Cancelled Cheque should be an image.",
          extension: "Cancelled Cheque should be either jpg, jpeg, png, jfif"
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
      }else if (element.hasClass('bs_multiselect')) {

            error.insertAfter(element.next('.btn-group'))
      }else if(element.parent().hasClass('input-group')){
        error.insertAfter(element.parent())
      } else {
        error.insertAfter(element);
      }
    }
});

$("#vendor-service-tax-form").validate({
    ignore:[],
    rules: {
        "Label": {
            required: true
        },
        "ServiceTaxTypeID": {
            required: true
        },
        "ServiceTaxNumber":{
          required: true
        },
        "BillingAddress":{
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
      }else if (element.hasClass('bs_multiselect')) {

            error.insertAfter(element.next('.btn-group'))
      }else if(element.parent().hasClass('input-group')){
        error.insertAfter(element.parent())
      } else {
        error.insertAfter(element);
      }
    }
});

$(document).on('click','.delete-vendor-service-tax', function(){
  var vendor_service_tax_id = $(this).attr('data-vendor_service_tax_id');

  swal({
    title: "Are you sure?",
    text: "This will be permanently deleted from our system.!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {
      swal("Please wait, your vendor's service tax data is being deleted!", {
        icon: "info",
      });

      window.location.href = base_url('delete-vendor-service-tax/'+vendor_service_tax_id);
    } else {
      swal("Your data is safe!");
    }
  });
});