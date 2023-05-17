$(document).on('click','.manage-client-details', function(){
    var client_id = $(this).attr('data-client_id');
    $('#manage-client-documents').attr('href',base_url('manage-client-documents/'+client_id));
    $('#manage-client-users').attr('href',base_url('client/manage-users/'+client_id));
 });

 $(document).on('click','.view-documents', function(){
    var client_geography_id = $(this).attr('data-client_geography_id'); 
    $.ajax({
       url: base_url('api/clients/get_client_document_data'),
       data:{
           'client_geography_id':client_geography_id
       },
       type:"GET",
       dataType:"json",
       success:function(response){
           $('.client-documents-table tbody').empty();
           var html = '';
           var i = 0;
           $.each(response.data, function(index,item){
               var sr_no = i + 1;
               html += '<tr><td>'+sr_no+'</td><td>'+item.DocumentName+'</td><td><a href="'+media_server(item.ClientDocumentMediaPath)+'" download class="btn btn-primary btn-xs"><i class="fa fa-download"></i></a></td></tr>';
               i++;
           });
           
           $('.client-documents-table tbody').html(html);
       },
       error: function(response){
           alert(response.responseJSON.msg);
       }
    });
 });

$(document).on('click','.delete-client', function(){
  var client_id = $(this).attr('data-client_id');

  swal({
    title: "Are you sure?",
    text: "Once deleted, you will have to contact us to restore the client.!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {
      swal("Please wait, your client is being deleted!", {
        icon: "info",
      });

      window.location.href = base_url('delete-client/'+client_id);
    } else {
      swal("Your client's data is safe!");
    }
  });
});

$("#save_client_form").validate({
  ignore:[],
  rules: {
      "ClientName": {
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
      'ClientUserFirstName':{
        required: true,
      },
      'ClientUserLastName':{
        required: true,
      },
      'ClientUserContactNo':{
        required: true,
        digits:true,
        minlength: 10,
        maxlength: 12
      },
      'LogoPath':{
        extension: "jpg|jpeg|png|jfif",
        filesize : 2, // here we are working with MB
      }
  },
  messages: {
      "ClientUserEmailID": {
          required: "Please, enter an email",
          email: "Email is invalid"
      },
      'ClientUserContactNo':{
        minlength: "Please enter at least 10 numbers",
        maxlength: "Please do not enter more than 12 numbers"
      },
      "LogoPath":{
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
      }else if (element.hasClass('bs_multiselect')) {

            error.insertAfter(element.next('.btn-group'))
      }else if(element.parent().hasClass('input-group')){
        error.insertAfter(element.parent())
      } else {
      error.insertAfter(element);
    }
  }
});

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

$(document).on('click','.manage-details', function(){
  var client_id = $(this).attr('data-client_id');
  $('.manage-client-service-taxes-url').attr('href',base_url('manage-client-service-taxes/'+client_id));
  $('.manage-invoices-url').attr('href',base_url('manage-invoices?client_id='+client_id));
});

$(document).on('click','.delete-client-service-tax', function(){
  var client_service_tax_id = $(this).attr('data-client_service_tax_id');

  swal({
    title: "Are you sure?",
    text: "This will be permanently deleted from our system.!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {
      swal("Please wait, your client's service tax data is being deleted!", {
        icon: "info",
      });

      window.location.href = base_url('delete-client-service-tax/'+client_service_tax_id);
    } else {
      swal("Your data is safe!");
    }
  });
});

$("#client-service-tax-form").validate({
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
        "BillingCountryID":{
          required: true
        },
        "BillingStateID":{
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

$(document).on('click','.contact-client', function(){
  var contact_no = $(this).attr('data-contact_no');
  var whatsapp_url = 'https://api.whatsapp.com/send?phone='+contact_no;
  var call_url = 'tel:'+contact_no;
  $('.whatsapp-msg').attr({
    'href':whatsapp_url,
    'target':'_blank'
  });
  
  $('.call-client').attr({
    'href':call_url,
  });

});

$('.rating').starRating({
    starIconEmpty: 'fa fa-star',
    starIconFull: 'fa fa-star',
    starColorEmpty: 'lightgray',
    starColorFull: '#FFC107',
    starsSize: 2, // em
    stars: 5,
    showInfo: true,
    titles: ["Very Bad", "Bad", "Medium", "Good", "Excellent!"],
    inputName: 'ClientRating',
});

if($('.ClientRating').length > 0){

  if($('.ClientRating').val() != ''){
    var client_rating_val = parseInt($('.ClientRating').val()) - 1;
    $('.rating i[data-index="'+client_rating_val+'"]').trigger('click');
  }

}