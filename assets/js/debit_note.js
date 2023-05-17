$("#debit-note-form").validate({
    ignore:[],
    rules: {
        "VendorID":{
            required: true,
        },
        "InvoiceNo":{
            required: true,
        },
        "DebitNoteDate":{
            required: true,
        },
        "PaymentStatus":{
            required: true,
        },
        'Particular[]':{
            required: true
        },
        'Quantity[]':{
            decimal:true,
            required: function(e){
                if(!e.hasAttribute('readonly')){
                    return true;
                }else{
                    return e.value;
                }
            }
        },
        'HSN[]':{
            required: true
        },
        'PricePerUnit[]':{
            required: true
        },
        'PaidAmount[]':{
            number: true
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

$(document).on('click','.delete-debit-note', function(){
    var debit_note_id = $(this).attr('data-debit_note_id');

    swal({
        title: "Are you sure?",
        text: "This will be permanently deleted from our system.!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          swal("Please wait, your debit note is being deleted!", {
            icon: "info",
          });

          window.location.href = base_url('delete-debit-note/'+debit_note_id);
        } else {
          swal("Your debit note is safe!");
        }
    });
});