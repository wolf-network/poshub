$(document).on('click','.add-tax', function(){
	
	
	var html = '<div class="row"><div class="col-md-5 col-xs-4"><div class="form-group"> <label> Tax Name <span class="text-danger">*</span> </label><input type="text" name="Tax[]" placeholder="E.g: GST" class="form-control"></div></div><div class="col-md-5 col-xs-6"><div class="form-group"><label> Tax Rate(%) <span class="text-danger">*</span></label> <div class="input-group"> <input type="text" name="TaxPercentage[]" value="" class="form-control tax-percentage"> <span class="input-group-addon">%</span> </div></div></div> <div class="col-md-2 col-xs-1"> <div class="form-group remove-tax-btn-container"></div> </div> </div>';
	$(this).parent().parent().find('.panel-body').append(html);

	if($(this).parent().parent().find('.remove-tax-btn-container').length > 1){
		var button_html = '<label for="">&nbsp;</label> <br> <button type="button" class="btn btn-danger btn-xs remove-tax-btn"><i class="fa fa-minus"></i></button>';
		$(this).parent().parent().find('.remove-tax-btn-container').html(button_html);
	}
});

$(document).on('click','.remove-tax-btn', function(){

	if($('.remove-tax-btn').length > 1){
		$(this).parent().parent().parent().remove();
	}

	if($('.remove-tax-btn').length <= 1){
		$('.remove-tax-btn').parent().empty();
	}	
});

$("#save-item-form").validate({
    ignore:[],
    rules: {
        'Item':{
          required: true
        },
        'Price':{
          required: true
        },
        'HSN':{
          required: true
        },
        'ItemType':{
          required: true
        },
        'Tax[]':{
          required:true
        },
        'TaxPercentage[]':{
          required:true,
          // digits:true
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

$(document).on('click','.save-item-category', function(){
  var here = $(this);
  var item_category = $('#ItemCategory').val();

  $('#itemCategoryModal .modal-body .text-danger').remove();

  $.ajax({
        url: base_url('api/item/save_item_category'),
        data: {'ItemCategory':item_category},
        type: "POST",
        dataType: "json",
        beforeSend: function(){
          here.html('Saving...').removeClass('btn-success save-item-category').addClass('btn-default');
        },
        error: function(response){
          var err = response.responseJSON.err;

          if(err != undefined && err != ''){
            $.each(err, function(index, item){
              var error_html = '<span class="text-danger">'+item+'</span>';
              $('#'+index).after(error_html);
            });

          }else{
            swal(response.responseJSON.msg,'', "error");
          }

          here.html('Save').removeClass('btn-default').addClass('save-item btn-success');
        },
        success: function (response) {
          var item_category_master_id = response.data.ItemCategoryMasterID;
          var item_category_html = '<option value="'+item_category_master_id+'">'+item_category+'</option>';

          $('#ItemCategoryMasterID').append(item_category_html);

          $("#itemCategoryModal").modal("hide");
          $('#itemCategoryModal .form-control').val('');
          here.html('Save').removeClass('btn-default').addClass('save-item-category btn-success');
          swal(response.msg,'', "success");
        }
    });
});

$('#ReportDateFrom.daterangepicker').on('apply.daterangepicker', function(ev, picker) {
    var from_date = picker.startDate.format('YYYY-MM-DD');
    var to_date = moment(from_date, "YYYY-MM-DD").format('YYYY-MM-DD');

    $('#ReportDateTo.daterangepicker').attr('data-min-date',to_date);
    daterangepicker('#ReportDateTo.daterangepicker');
});

$('#InwardDateFrom.daterangepicker').on('apply.daterangepicker', function(ev, picker) {
    var from_date = picker.startDate.format('YYYY-MM-DD');
    var to_date = moment(from_date, "YYYY-MM-DD").format('YYYY-MM-DD');

    $('#InwardDateTo.daterangepicker').attr('data-min-date',to_date);
    daterangepicker('#InwardDateTo.daterangepicker');
});

$('#OutwardDateFrom.daterangepicker').on('apply.daterangepicker', function(ev, picker) {
    var from_date = picker.startDate.format('YYYY-MM-DD');
    var to_date = moment(from_date, "YYYY-MM-DD").format('YYYY-MM-DD');

    $('#OutwardDateTo.daterangepicker').attr('data-min-date',to_date);
    daterangepicker('#OutwardDateTo.daterangepicker');
});

$(document).on('click','.delete-item', function(){
  var item_id = $(this).attr('data-item_id');

  swal({
    title: "Are you sure?",
    text: "This data will be deleted permanently from the database!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {
      swal("Please wait, your item is being deleted!", {
        icon: "info",
      });

      window.location.href = base_url('delete-item/'+item_id);
    } else {
      swal("Your item's data is safe!");
    }
  });
});