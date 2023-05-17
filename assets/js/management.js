$(document).on('click','.delete-role', function(){
    var role_id = $(this).attr('data-role_id');

     swal({
    title: "Are you sure?",
    text: "This role will be deleted permanently.",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {
      swal("Please wait, role is being deleted!", {
        icon: "info",
      });

      window.location.href = base_url('delete-role/'+role_id);
    } else {
      swal("Your role's data is safe!");
    }
  });
});

$(document).on('click','.delete-industry', function(){
    var industry_id = $(this).attr('data-industry_id');

     swal({
    title: "Are you sure?",
    text: "This industry will be deleted permanently.",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {
      swal("Please wait, industry is being deleted!", {
        icon: "info",
      });

      window.location.href = base_url('delete-industry/'+industry_id);
    } else {
      swal("Your industry's data is safe!");
    }
  });
});

$(document).on('click','.delete-service', function(){
    var service_id = $(this).attr('data-service_id');

     swal({
    title: "Are you sure?",
    text: "This service will be deleted permanently.",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {
      swal("Please wait, service is being deleted!", {
        icon: "info",
      });

      window.location.href = base_url('delete-service/'+service_id);
    } else {
      swal("Your service data is safe!");
    }
  });
});