$(document).on('click','.delete-user', function(){
    var registered_user_id = $(this).attr('data-registered_user_id');

     swal({
    title: "Are you sure?",
    text: "Once deleted, you will have to contact your development team to restore the user.!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {
      swal("Please wait, your user is being deleted!", {
        icon: "info",
      });

      window.location.href = base_url('delete-user/'+registered_user_id);
    } else {
      swal("Your user's data is safe!");
    }
  });
});

$(document).on('click','.subscription-btn', function(){
  var registered_user_id = $(this).attr('data-registered_user_id');
  var user_name = $(this).closest('tr').find('.user-name').html();
  $('#subscriptionRenewModal #subscription-for').html(user_name);
  $('.buy-subscription').attr('data-registered_user_id',registered_user_id);
  

  // $('#subscriptionRenewModal .buy-subscription').attr('href','user-subscription/'+registered_user_id+'?plan=30');
});

$(document).on('click', '.buy-subscription', function(){
  var subscription_plan_id = $(this).attr('data-subscription_plan_id');
  var registered_user_id = $(this).attr('data-registered_user_id');
  window.location.href = 'user-subscription/'+registered_user_id+'?plan='+subscription_plan_id;
});

$("#reset_password_form").validate({
  ignore:[],
  rules: {
      "CurrentPassword": {
          required: true,
      },
      "Password": {
          required: true
      },
      "ConfirmPassword":{
        required: true,
        equalTo: "#Password"
      }
  },
  messages: {
      "ConfirmPassword":{
        equalTo: "Confirm password should be the same as New Password.",
      }
  },
  submitHandler: function (form) {
      $('.overlay-wrapper').removeClass('hide'); 
      $('.loader').addClass('breathing-animation');  
      form.submit();
  }
});

$("#save_employee_form").validate({
    ignore:[],
    rules: {
        'Name':{
          required: true
        },
        'Gender':{
          required: true
        },
        'RoleID[]':{
          required: true
        },
        'EmailID':{
          required: true
        },
        'PrivilegeID':{
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