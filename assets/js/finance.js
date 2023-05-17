function sumParticular(here){
	var total = 0;
	var grand_total = 0;
	var total_deductible_percentage = 0;
	var total_particular_amt = 0;
	var total_particular_amt_post_tax = 0;
	var total_additional_charges = 0;

	$('.charge').each(function(){
		total_additional_charges += ($(this).val() != '')?parseFloat($(this).val()):0;
	});

	$('.total').each(function(){
		var item_type = $(this).parent().parent().parent().parent().parent().find('.item-type').val();
		if(item_type == 'Good'){
			var qty = $(this).parent().parent().parent().parent().parent().find('.qty').val();
				qty = (qty != '')?parseFloat(qty):0;
		}else{
			var qty = 1;
		}

		var price_per_unit = $(this).parent().parent().parent().parent().parent().find('.price-per-unit').val();
		var price_per_unit = (price_per_unit)?parseFloat(price_per_unit):0;
		var discount = $(this).parent().parent().parent().parent().parent().find('.discount').val();
		var discount = (discount)?parseFloat(discount):0;
		var amount = qty * price_per_unit;

		var discounted_price = 0;
		if(discount != 0){
			var discounted_price = amount * discount / 100;
			amount = amount - discounted_price;
		}

		var tax = 0;

		$(this).parent().parent().parent().parent().parent().find('.tax-percentage').each(function(){
		if($(this).val() != ''){
				tax += parseFloat($(this).val());
			}
		});

		var tax_percentage_amount = amount * tax / 100;
		var post_tax_total = amount + tax_percentage_amount;

		$(this).parent().parent().parent().parent().parent().find('.amount').val(amount.toFixed(2));

		$(this).val(post_tax_total.toFixed(2));

		var total_amount = ($(this).val() != '')?parseFloat($(this).val()):0;
		total_particular_amt += amount;
		total_particular_amt_post_tax += total_amount;
		grand_total += total_amount;
	});

	$('.deductible-percentage').each(function(){
		if($(this).val() != ''){
			total_deductible_percentage += parseFloat($(this).val());
		}
	});

	grand_total += total_additional_charges;

	if(total_deductible_percentage != '' && total_deductible_percentage > 0){
		var amount_deductible_at_source = total_particular_amt *total_deductible_percentage/100;
	}else{
		amount_deductible_at_source = 0;
	}

	var receivable_amt = (grand_total - amount_deductible_at_source);

	$('#grandTotal').val(grand_total.toFixed(2));
	$('.total-particular-amt-post-tax').html(total_particular_amt_post_tax.toFixed(2));
	$('.total-bill-amt').html(grand_total.toFixed(2));
	$('.total-deductible-amt').html(amount_deductible_at_source.toFixed(2));
	$('.total-receivable-amt').html(receivable_amt.toFixed(2));
}

$(document).on('keyup','.qty,.price-per-unit,.tax-percentage,.discount', function(){
	if($(this).hasClass('tax-percentage')){
		sumParticular($(this).parent().parent().parent().parent().parent().parent().parent().parent().parent());
	}else{
		sumParticular($(this).parent().parent().parent().parent());
	}
});

$(document).on('change', '.qty,.price-per-unit,.tax-percentage,.discount', function(){
	$(this).trigger('keyup');
});

$(document).on('click','.add-particular', function(){
	var particular_count = parseInt($('.remove-particular-btn-container').length);
	var billing_detail_count = particular_count + 1;
	var particular_tax_key = Math.floor(1000 + Math.random() * 9000);
	var particulars = $('.particular-container .particular-selector:eq(0)').html();
	var particulars_html = '<select name="Particular[]" id="particular-'+particular_tax_key+'" class="form-control particular-selector bs_multiselect">'+particulars+'</select>';

	var html = '<div class="panel panel-default"> <div class="panel-heading"> <h4 class="pull-left">Detail '+billing_detail_count+'</h4> <div class="pull-right"><button type="button" class="btn btn-success pull-left add-particular">Add Particular</button><div class="remove-particular-btn-container pull-left"></div></div> <div class="clearfix"></div> </div> <div class="panel-body"> <div class="row"> <div class="col-md-4"> <input type="hidden" name="ParticularTaxKey[]" value="'+particular_tax_key+'" class="particular-tax-key"> <div class="form-group"> <label for="">Particular <span class="text-danger">*</span> <a href="javascript:void(0)" data-toggle="modal" data-target="#saveItemModal">Add Item</a> </label></label> '+particulars_html+'<input type="hidden" name="ItemType[]" class="item-type"><input type="hidden" name="ItemCategory[]" class="item-category"></div> </div>  <div class="col-md-2"> <div class="form-group"> <label for="">HSN/SAC <span class="text-danger">*</span></label> <input type="text" name="HSN[]" value="" id="hsn-'+particular_tax_key+'" class="form-control hsn-code"> </div> </div> <div class="col-md-2"> <div class="form-group"> <label for="">Quantity <span class="text-danger">*</span></label> <input type="text" name="Quantity[]" value="" id="qty-'+particular_tax_key+'" min="1" class="form-control qty"> </div> </div> <div class="col-md-2"> <div class="form-group"> <label for="">Price per unit <span class="text-danger">*</span></label> <input type="text" name="PricePerUnit[]" value="" id="price-per-unit-'+particular_tax_key+'" min="1" class="form-control price-per-unit"> </div> </div> <div class="col-md-2"> <div class="form-group"> <label for="">Amount</label> <input type="text" name="Amount[]" readonly value="" class="form-control amount"> </div> </div> <div class="clearfix"></div> <div class="col-md-4"><div class="form-group"><label for="Discount">Discount (%)</label><input type="text" class="form-control discount" name="Discount[]" placeholder="Discount"></div></div> </div> <br> <div class="row"><div class="col-md-6"> <div class="panel panel-default"> <div class="panel-heading"> <h4 class="pull-left">Taxes (Detail '+billing_detail_count+') </h4> <button type="button" class="btn bg-olive pull-right add-tax">Add</button> <div class="clearfix"></div> </div> <div class="panel-body"> <div class="row"> <div class="col-md-5 col-xs-4"> <div class="form-group"> <label for="">Tax Name <span class="text-danger">*</span></label> <input type="text" name="Tax['+particular_tax_key+'][]" value="" id="tax-'+particular_tax_key+'" required class="tax form-control"> </div> </div> <div class="col-md-5 col-xs-6"> <label for="">Tax Rate (%) <span class="text-danger">*</span></label> <div class="form-group"> <div class="input-group"> <input type="text" name="TaxPercentage['+particular_tax_key+'][]" value="" id="tax-percentage-'+particular_tax_key+'" required class="form-control tax-percentage"> <span class="input-group-addon">%</span> </div> </div> </div> <div class="col-md-2 col-xs-1"><div class="form-group remove-tax-btn-container"></div></div> </div> </div> </div> </div><div class="col-md-4"> <div class="form-group"><label for="SerialNo">Serial No</label><input type="text" name="SerialNo[]" value="" class="form-control"></div> <div class="form-group"><div class="input-group"> <span class="input-group-addon">Total</span> <input type="text" name="Total" value="" readonly id="Total" class="form-control total"> </div></div> </div> </div> </div> </div>';
	
	$('.particular-container').append(html);
	$("particular-"+particular_tax_key).val('');
	bs_multiselect_init();
	if($('.expense-heading-container .remove-particular-btn').length == 0 || $('.expense-heading-container .remove-particular-btn-container').length > 1){
		var button_html = '<button type="button" class="btn btn-danger pull-right remove-particular-btn">Remove</button>';
		$('.remove-particular-btn-container').html(button_html);
	}
});

$(document).on('click','.remove-particular-btn', function(){
	$(this).parent().parent().parent().parent().remove();
	if($('.remove-particular-btn').length == 1){
		$('.remove-particular-btn').remove();
	}

	sumParticular($(this).parent().parent().parent().parent());
});

$(document).on('click','.add-tax', function(){
	var random_id = Math.floor(1000 + Math.random() * 9000);

	var particular_tax_key = $(this).parent().parent().parent().parent().parent().find('.particular-tax-key').val();
	var tax_name_html = (particular_tax_key != undefined)?'['+particular_tax_key+']':'';
	
	var html = '<div class="row tax-container"><div class="col-md-5 col-sm-5 col-xs-12"><div class="form-group"> <label> Tax <span class="text-danger">*</span></label><input type="text" name="Tax'+tax_name_html+'[]" id="tax-'+random_id+'" required placeholder="E.g: GST/VAT" class="tax form-control"></div></div><div class="col-md-5 col-sm-5 col-xs-12"><div class="form-group"><label> Tax Rate(%) <span class="text-danger">*</span></label> <div class="input-group"> <input type="text" name="TaxPercentage'+tax_name_html+'[]" value="" placeholder="E.g: 18" id="tax-percentage-'+random_id+'" required class="form-control tax-percentage"> <span class="input-group-addon">%</span> </div></div></div> <div class="col-md-2 col-sm-2 col-xs-12"> <div class="form-group remove-tax-btn-container"></div> </div> </div>';
	$(this).parent().parent().find('.panel-body').append(html);

	if($(this).parent().parent().find('.remove-tax-btn-container').length > 1){
		var button_html = '<label for="">&nbsp;</label> <br> <button type="button" class="btn btn-danger btn-xs btn-block remove-tax-btn" data-particular_tax_key="'+particular_tax_key+'"><i class="fa fa-minus hidden-xs"></i><span class="hidden-lg hidden-md hidden-sm">Remove</span></button>';
		$(this).parent().parent().find('.remove-tax-btn-container').html(button_html);
	}
});

$(document).on('click','.remove-tax-btn', function(){
	var particular_tax_key = $(this).attr('data-particular_tax_key');
	$(this).parent().parent().parent().remove();

	sumParticular($('.remove-tax-btn[data-particular_tax_key="'+particular_tax_key+'"]').parent().parent().parent().parent().parent().parent().parent().parent());

	if($('.remove-tax-btn[data-particular_tax_key="'+particular_tax_key+'"]').length <= 1){
		$('.remove-tax-btn[data-particular_tax_key="'+particular_tax_key+'"]').parent().empty();
	}	
});

$(document).on('click','.delete-invoice', function(){
    var invoice_id = $(this).attr('data-invoice_id');

 	swal({
	    title: "Are you sure?",
	    text: "This will be permanently deleted from our system.!",
	    icon: "warning",
	    buttons: true,
	    dangerMode: true,
	  }).then((willDelete) => {
	    if (willDelete) {
	      swal("Please wait, your invoice is being deleted!", {
	        icon: "info",
	      });

	      window.location.href = base_url('delete-invoice/'+invoice_id);
	    } else {
	      swal("Your invoice is safe!");
	    }
  	});
});

$(document).on('click','.print', function(){
	var convertMeToImg = $('#invoiceDiv')[0];

	html2canvas(convertMeToImg).then(function(canvas) {
	    // $('#resultsDiv').append(canvas);
	    saveAs(canvas.toDataURL(), 'invoice.png');
	});
});

function saveAs(uri, filename) {

    var link = document.createElement('a');

    if (typeof link.download === 'string') {

        link.href = uri;
        link.download = filename;

        //Firefox requires the link to be in the body
        document.body.appendChild(link);

        //simulate click
        link.click();

        //remove the link when done
        document.body.removeChild(link);

    } else {

        window.open(uri);

    }
}

$('#ClientInvoiceDate.daterangepicker,#InvoiceDateFrom').on('apply.daterangepicker', function(ev, picker) {
    var invoice_date_from = picker.startDate.format('YYYY-MM-DD');
    var invoice_date_to = moment(invoice_date_from, "YYYY-MM-DD").format('YYYY-MM-DD');
    
    $('#ClientInvoiceDueDate.daterangepicker,#InvoiceDateTo.daterangepicker').val(invoice_date_to).attr('data-min-date',invoice_date_to);
    daterangepicker('#ClientInvoiceDueDate.daterangepicker,#InvoiceDateTo.daterangepicker');
});

$(document).on('click','.amount-filter-box', function(){
	var filter = $(this).attr('data-filter');
	$('#amountFilter').val(filter);
	$('#form-filter-btn').trigger('click');
});

$(document).on('click','.add-item-tax', function(){
	var html = '<div class="row"><div class="col-md-5 col-xs-4"><div class="form-group"> <label> Tax Name <span class="text-danger">*</span></label><input type="text" name="Tax[]" placeholder="E.g: GST" class="form-control tax-field"></div></div><div class="col-md-5 col-xs-6"><div class="form-group"><label> Tax Rate(%) <span class="text-danger">*</span></label> <div class="input-group"> <input type="text" name="TaxPercentage[]" value="" class="form-control tax-percentage tax-percentage-field"> <span class="input-group-addon">%</span> </div></div></div> <div class="col-md-2 col-xs-1"> <div class="form-group remove-item-tax-btn-container"></div> </div> </div>';
	$(this).parent().parent().find('.panel-body').append(html);

	if($(this).parent().parent().find('.remove-item-tax-btn-container').length > 1){
		var button_html = '<label for="">&nbsp;</label> <br> <button type="button" class="btn btn-danger btn-xs remove-item-tax-btn"><i class="fa fa-minus"></i></button>';
		$(this).parent().parent().find('.remove-item-tax-btn-container').html(button_html);
	}
});

$(document).on('click','.remove-item-tax-btn', function(){
	if($('.remove-item-tax-btn').length > 1){
		$(this).parent().parent().parent().remove();
	}

	if($('.remove-item-tax-btn').length <= 1){
		$('.remove-item-tax-btn').parent().empty();
	}	
});

$(document).on('click','.save-item', function(){
 	var form_data = $('#saveItemModal #itemForm').serializeArray();
 	var here = $(this);

 	$('#saveItemModal .modal-body .text-danger').empty();

 	$.ajax({
        url: base_url('api/item/save_item'),
        data: form_data,
        type: "POST",
        dataType: "json",
        beforeSend: function(){
        	here.html('Saving...').removeClass('btn-success save-item').addClass('btn-default');
        },
        error: function(response){
        	var err = response.responseJSON.err;

        	if(err != undefined && err != ''){
        		$.each(err, function(index, item){
        			var error_html = '<span class="text-danger">'+item+'</span>';
        			$('#'+index+'-field').after(error_html);
        		});

        		var tax_error_html = '<span class="text-danger">Tax field is required</span>';
        		$('#saveItemModal .modal-body .panel-body input[name="Tax[]"]').each(function(index){
        			if(err['Tax.'+index]){
        				$(this).after(tax_error_html);
        			}
        		});

        		var tax_percentage_error_html = '<span class="text-danger">Tax Rate field is required</span>';
        		$('#saveItemModal .modal-body .panel-body input[name="TaxPercentage[]"]').each(function(index){
        			if(err['TaxPercentage.'+index]){
        				$(this).parent().after(tax_percentage_error_html);
        			}
        		});

        	}else{
        		swal(response.responseJSON.msg,'', "error");
        	}

        	here.html('Save').removeClass('btn-default').addClass('save-item btn-success');
        },
        success: function (response) {
        	var item_name = $('#saveItemModal .modal-body #Item-field').val();
        	var item_price = $('#saveItemModal .modal-body #Price-field').val();
        	var item_buying_price = $('#saveItemModal .modal-body #BuyingPrice-field').val();
        	var item_hsn = $('#saveItemModal .modal-body #HSN-field').val();
        	var taxes = $('#saveItemModal .tax-field').map(function(){
		 		var tax_percentage = $(this).parent().parent().parent().find('.tax-percentage-field').val()
		 		return $(this).val()+':'+tax_percentage; 
		 	}).get().join('|');
		 	var item_barcode_no = $('#saveItemModal .modal-body #BarcodeNo-field').val();
		 	
		 	var item_name_html = '<option value="'+item_name+'" data-price="'+item_price+'" data-buying_price="'+item_buying_price+'" data-hsn="'+item_hsn+'" data-taxes="'+taxes+'" data-barcode_no="'+item_barcode_no+'">'+item_name+'</option>';

		 	$('.particular-container .particular-selector,.po-particular-container .po-particular-selector').append(item_name_html);

		 	var container_html = '<li><a tabinde="0"><label class="radio" title="'+item_name+'"><input type="radio" value="'+item_name+'" data-price="'+item_price+'" data-buying_price="'+item_buying_price+'" data-hsn="'+item_hsn+'" data-taxes="'+taxes+'" data-barcode_no="'+item_barcode_no+'" data-item_qty="0">'+item_name+'</label></a></li>';

		 	$('.particular-container .multiselect-container,.po-particular-container .multiselect-container').append(container_html);

        	$("#saveItemModal").modal("hide");
        	$('#saveItemModal .modal-body .panel-body .row:gt(0)').remove();
 			$('#saveItemModal .form-control').val('');
 			here.html('Save').removeClass('btn-default').addClass('save-item btn-success');
        	swal(response.msg,'', "success");
        }
    });
});

$(document).on('change','.particular-selector', function(){
	var here = $(this);
	var particular = $(this).val();
	var particular_id = $(this).attr('id');

	if($(this).val() != ''){
		$('.particular-selector').each(function(){
			if($(this).val() == particular && $(this).attr('id') != particular_id){
				here.val('');
				
				$('#'+particular_id+'.bs_multiselect').multiselect('destroy');
				bs_multiselect_init('#'+particular_id+'.bs_multiselect');

				swal('Duplicate particular selected','This item has already been selected. Please update the qty if you need to.', "error");
				return false;
			}	
		});
	}

	var item = $(this).find(':selected').html();
	var item_qty = $(this).find('option:selected').attr('data-item_qty');
	var item_type = $(this).find(':selected').attr('data-item_type');
	var item_category = $(this).find(':selected').attr('data-item_category');
	var barcode_no = $(this).find(':selected').attr('data-barcode_no');
	var buying_price = $(this).find(':selected').attr('data-buying_price');
	var price = $(this).find(':selected').attr('data-price');
	var hsn = $(this).find(':selected').attr('data-hsn');
	var taxes = $(this).find(':selected').attr('data-taxes');
	var taxes_arr = (taxes != undefined)?taxes.split('|'):[];
	var active_detail_section = $(this).parent().parent().parent().parent().parent();

	$(active_detail_section).find('.tax-container:gt(0)').remove();

	if(taxes_arr.length > 0){
		for(var i=0;i<taxes_arr.length;i++){
			var tax_details_array = (taxes_arr[i].split(':'));
			
			if(i>0){
				$(active_detail_section).find('.add-tax').trigger('click');
			}
			
			active_detail_section.find('.tax:eq('+i+')').val(tax_details_array[0]);
			active_detail_section.find('.tax-percentage:eq('+i+')').val(tax_details_array[1]);
		}	
	}

	if(item_type != 'Good'){
		active_detail_section.find('.qty').val('').removeAttr('max').attr('readonly',true).trigger('change');	
	}else{
		active_detail_section.find('.qty').attr({'max':item_qty,'readonly':false});
	}

	active_detail_section.find('.item').val(item);
	active_detail_section.find('.item-type').val(item_type);
	active_detail_section.find('.item-category').val(item_category);
	active_detail_section.find('.barcode-no').val(barcode_no);
	active_detail_section.find('.buying-price').val(buying_price);
	active_detail_section.find('.price-per-unit').val(price);
	active_detail_section.find('.hsn-code').val(hsn);
	active_detail_section.find('.price-per-unit').trigger('change');
});

$(document).on('change','#ClientID', function(){
	var client_id = $(this).val();
	var add_client_service_tax_url = base_url('add-client-service-tax/'+client_id);
	var client_service_tax_identification_number = $(this).attr('data-client_service_tax_identification_number');

	$('#ClientServiceTaxIdentificationNumber.bs_multiselect').empty().multiselect('destroy');
	$('.add-client-service-tax-no').attr('href',add_client_service_tax_url);

	$.ajax({
        url: base_url('api/clients/get_client_data'),
        data: {
        	'ClientID':client_id
        },
        type: "GET",
        dataType: "json",
        beforeSend: function(){
        	
        },
        success: function(response){
        	var client_data = response.data.client_data;
        	var client_service_tax_data = response.data.client_service_taxes;

        	$('#ClientContactNo').val(client_data.ClientUserContactNo);
        	
        	var client_service_tax_html = '<option value="">Select Service Tax No</option>';
        	$.each(client_service_tax_data, function(index,item){
        		client_service_tax_html += '<option value="'+item.ServiceTaxNumber+'" data-service_tax_type_id="'+item.ServiceTaxTypeID+'" data-service_tax_type="'+item.ServiceTaxType+'" data-billing_address="'+item.BillingAddress+'">'+item.ServiceTaxNumber+'('+item.Label+')</option>';
        	});

        	$('#ClientServiceTaxIdentificationNumber').html(client_service_tax_html).val(client_service_tax_identification_number);
        	$('#ClientServiceTaxIdentificationNumber').trigger('change');
        	bs_multiselect_init('#ClientServiceTaxIdentificationNumber.bs_multiselect');
        }
    });
});

$('#ClientID').trigger('change');

$("#invoice-form").validate({
  	ignore:[],
  	rules: {
      	"CompanyAddress": {
          	required: true,
      	},
      	"ClientID":{
        	required: true,
      	},
      	"ClientContactNo":{
	        required: true,
	        digits:true
      	},
      	'ClientInvoiceDate':{
	        required: true,
      	},
      	'ClientInvoiceDueDate':{
	        required: true,
      	},
      	'ClientBillingAddress':{
	        required: true
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
      	"ClientContactNo": {
          	digits: "Contact number should be numeric"
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

$("#lead-quotation-form").validate({
  	ignore:[],
  	rules: {
  		'CompBillingAddress':{
  			required: true
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

$(document).on('click','.add-charges', function(){
	var random_id = Math.floor(1000 + Math.random() * 9000);
	
	var html = '<div class="row charge-container"><div class="col-md-5 col-sm-5 col-xs-12"><div class="form-group"> <label> Charge Type</label><input type="text" name="AdditionalChargeType[]" id="AdditionalChargeType-'+random_id+'" required placeholder="E.g: Shipping" class="charge-type form-control"></div></div><div class="col-md-5 col-sm-5 col-xs-12"><div class="form-group"><label>Rate</label>  <input type="text" name="AdditionalCharge[]" value="" id="charge-'+random_id+'" required class="form-control charge"></div></div> <div class="col-md-2 col-sm-2 col-xs-12"> <div class="form-group remove-charge-btn-container"></div> </div> </div>';
	$(this).parent().parent().find('.panel-body').append(html);

	if($(this).parent().parent().find('.remove-charge-btn-container').length > 1){
		var button_html = '<label for="">&nbsp;</label> <br> <button type="button" class="btn btn-danger btn-xs btn-block remove-charge-btn"><i class="fa fa-minus hidden-xs"></i><span class="hidden-lg hidden-md hidden-sm">Remove</span></button>';
		$(this).parent().parent().find('.remove-charge-btn-container').html(button_html);
	}
});

$(document).on('click','.remove-charge-btn', function(){
	$(this).parent().parent().parent().remove();

	sumParticular($('.remove-charge-btn').parent().parent().parent().parent().parent().parent().parent().parent());

	if($('.remove-charge-btn').length <= 1){
		$('.remove-charge-btn').parent().empty();
	}	
});

$(document).on('keyup','.charge', function(){
	sumParticular($(this).parent().parent().parent().parent().parent().parent().parent().parent().parent());
});

$(document).on('click','.add-deductible', function(){
	var random_id = Math.floor(1000 + Math.random() * 9000);
	
	var html = '<div class="row deductible-container"><div class="col-md-5 col-sm-5 col-xs-12"><div class="form-group"> <label> Deductible type</label><input type="text" name="DeductibleType[]" id="deductible-'+random_id+'" required placeholder="E.g: TDS" class="form-control"></div></div><div class="col-md-5 col-sm-5 col-xs-12"><div class="form-group"><label> Deductible Percentage</label> <div class="input-group"> <input type="text" name="DeductiblePercentage[]" value="" placeholder="E.g: 10" id="deductible-percentage-'+random_id+'" required class="form-control deductible-percentage"> <span class="input-group-addon">%</span> </div></div></div> <div class="col-md-2 col-sm-2 col-xs-12"> <div class="form-group remove-deductible-btn-container"></div> </div> </div>';
	$(this).parent().parent().find('.panel-body').append(html);


	if($(this).parent().parent().find('.remove-deductible-btn-container').length > 1){
		var button_html = '<label for="">&nbsp;</label> <br> <button type="button" class="btn btn-danger btn-xs btn-block remove-deductible-btn"><i class="fa fa-minus hidden-xs"></i><span class="hidden-lg hidden-md hidden-sm">Remove</span></button>';
		$(this).parent().parent().find('.remove-deductible-btn-container').html(button_html);
	}
});

$(document).on('click','.remove-deductible-btn', function(){
	$(this).parent().parent().parent().remove();

	sumParticular($(this).parent().parent().parent().parent().parent().parent().parent().parent());

	if($('.remove-deductible-btn').length <= 1){
		$('.remove-deductible-btn').parent().empty();
	}	
});

$(document).on('keyup','.deductible-percentage', function(){
	var total_deductible_percentage = 0;
	var total_particular_amt = 0;
	var total_bill_amount = 0;
	var total_additional_charges = 0;

	$('.charge').each(function(){
		total_additional_charges += ($(this).val() != '')?parseFloat($(this).val()):0;
	});

	$('.deductible-percentage').each(function(){
		if($(this).val() != ''){
			total_deductible_percentage += parseFloat($(this).val());
		}
	});

	$('.amount').each(function(){
		total_particular_amt += parseFloat($(this).val());
	});

	$('.total').each(function(){
		total_bill_amount += parseFloat($(this).val());
	});

	if(total_deductible_percentage > 0){
		var amount_deductible_at_source = total_particular_amt*total_deductible_percentage/100;
	}else{
		var amount_deductible_at_source = 0;
	}

	var receivable_amt = (total_bill_amount - amount_deductible_at_source) + total_additional_charges;

	$('.total-bill-amt').html(total_bill_amount.toFixed(2));
	$('.total-deductible-amt').html(amount_deductible_at_source.toFixed(2));
	$('.total-receivable-amt').html(receivable_amt.toFixed(2));

});

$('.deductible-percentage:eq(0)').trigger('keyup');


try{
	new Clipboard('.copy-quotation');
}catch(e){
	
}



$(document).on('click','.delete-expense', function(){
    var expense_id = $(this).attr('data-expense_id');

 	swal({
	    title: "Are you sure?",
	    text: "This will be permanently deleted from our system.!",
	    icon: "warning",
	    buttons: true,
	    dangerMode: true,
	  }).then((willDelete) => {
	    if (willDelete) {
	      swal("Please wait, your expense data is being deleted!", {
	        icon: "info",
	      });

	      window.location.href = base_url('delete-expense/'+expense_id);
	    } else {
	      swal("Your expense data is safe!");
	    }
  	});
});

$(document).on('click','.save-expense-heading', function(){
 	var form_data = $('#saveExpenseHeadingModal .form-control').serializeArray();
 	var here = $(this);

 	$('#saveExpenseHeadingModal .modal-body .text-danger').empty();

 	$.ajax({
        url: base_url('api/finance/save_expense_heading'),
        data: form_data,
        type: "POST",
        dataType: "json",
        beforeSend: function(){
        	here.html('Saving...').removeClass('btn-success save-expense-heading').addClass('btn-default');
        },
        error: function(response){
        	var err = response.responseJSON.err;

        	if(err != undefined && err != ''){
        		$.each(err, function(index, item){
        			var error_html = '<span class="text-danger">'+item+'</span>';
        			$('#'+index+'-field').after(error_html);
        		});
        	}else{
        		swal(response.responseJSON.msg,'', "error");
        	}

        	here.html('Save').removeClass('btn-default').addClass('save-expense-heading btn-success');
        },
        success: function (response) {
        	var expense_heading = $('#saveExpenseHeadingModal .modal-body #ExpenseHead-field').val();
        	var expense_master_id = response.data.ExpenseHeadMasterID;
		 	
		 	var expense_heading_html = '<option value="'+expense_master_id+'">'+expense_heading+'</option>';

		 	$('.expense-heading-container #ExpenseHeadMasterID').append(expense_heading_html);

		 	var container_html = '<li><a tabinde="0"><label class="radio" title="'+expense_heading+'"><input type="radio" value="'+expense_master_id+'">'+expense_heading+'</label></a></li>';

		 	$('.expense-heading-container .multiselect-container').append(container_html);

        	$("#saveExpenseHeadingModal").modal("hide");
        	$('#saveExpenseHeadingModal .modal-body .panel-body .row:gt(0)').remove();
 			$('#saveExpenseHeadingModal .form-control').val('');
 			here.html('Save').removeClass('btn-default').addClass('save-expense-heading btn-success');
        	swal(response.msg,'', "success");
        }
    });
});

$('#FromDate.daterangepicker').on('apply.daterangepicker', function(ev, picker) {
    var from_date = picker.startDate.format('YYYY-MM-DD');
    var to_date = moment(from_date, "YYYY-MM-DD").format('YYYY-MM-DD');

    $('#ToDate.daterangepicker').attr('data-min-date',to_date);
    daterangepicker('#ToDate.daterangepicker');
});

$(document).on('click','.fetch-reports', function(){
	if($('#FromDate').val() == '' && $('#ToDate').val() == ''){
		swal('Validation error','Please enter from date or to date.', "error");
		return false;
	}

	var form_data = $('#finance-report-filter .form-control').serializeArray();

	$.ajax({
        url: base_url('api/finance/get_reports'),
        data:form_data,
        type:"GET",
        dataType:"json",
        beforeSend: function(){
            $('.overlay-wrapper').removeClass('hide');
            $('.loader').addClass('breathing-animation');
        },
        success:function(response){
            var total_sales = (response.data.total_sales != '' && response.data.total_sales != null)?parseFloat(response.data.total_sales):0;
            var total_received = (response.data.total_received != '' && response.data.total_received != null)?parseFloat(response.data.total_received):0;
            var total_service_tax = (response.data.total_service_tax != '' && response.data.total_service_tax != null)?parseFloat(response.data.total_service_tax):0;
            var total_expenses = (response.data.expenses != '' && response.data.expenses != null)?parseFloat(response.data.expenses):0;

            if(total_received > 0){
            	var profit = total_received - total_service_tax - total_expenses;
            }else{
            	var profit = total_service_tax - total_sales - total_expenses;
            }

            $('.total-sales-num').html(numberWithCommas(total_sales.toFixed(2)));
            $('.total-received-num').html(numberWithCommas(total_received.toFixed(2)));
            $('.total-service-tax-num').html(numberWithCommas(total_service_tax.toFixed(2)));
            $('.total-expense-num').html(numberWithCommas(total_expenses.toFixed(2)));
            $('.total-profit-num').html(numberWithCommas(profit.toFixed(2)));
        },
        error:function(response){
        	swal({
		        title: response.responseJSON.msg, 
		        html: true,
		        content: {
		          element: 'p',
		          attributes: {
		            innerHTML: response.responseJSON.error,
		          },
		        },
		        dangerMode: true,  
	      	});
        },
        complete: function(){
            $('.overlay-wrapper').addClass('hide');
            $('.loader').removeClass('breathing-animation');
        }
    });
});

// $("#expense-form").validate({
//   	ignore:[],
//   	rules: {
//       	'ExpenseHeadMasterID':{
// 	        required: true
//       	},
//       	'ExpenseDate':{
// 	        required: true
//       	},
//       	'ExpenseAmount':{
// 	        required: true,
// 	        decimal: true
//       	},
//       	'TaxableAmount':{
// 	        decimal: true
//       	},
//       	'AttachedDocumentPath':{
//       		filesize : 2, // here we are working with MB
//       	}
//   	},
//   	messages: {
      	
//   	},
//   	submitHandler: function (form) {
//       	$('.overlay-wrapper').removeClass('hide');
//       	$('.loader').addClass('breathing-animation');
//       	form.submit();
//   	},
//   	errorPlacement: function(error, element) {
//         // Add the `help-block` class to the error element
//     	error.addClass("help-block");
//     	if (element.prop("type") === "checkbox") {
//             error.insertAfter(element.parent("label"));
//     	}else if (element.hasClass('bs_multiselect')) {

//             error.insertAfter(element.next('.btn-group'))
//     	}else if(element.parent().hasClass('input-group')){
//     		error.insertAfter(element.parent())
//     	} else {
// 			error.insertAfter(element);
// 		}
//     }
// });

$("#tax-identification-form").validate({
  	ignore:[],
  	rules: {
      	'ServiceTaxTypeID':{
	        required: true
      	},
      	'TaxIdentificationNumber':{
	        required: true
      	},
      	'RegisteredAddress':{
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

$(document).on('click','.delete-tax-identification', function(){
    var company_service_tax_master_id = $(this).attr('data-company_service_tax_master_id');

 	swal({
	    title: "Are you sure?",
	    text: "This will be permanently deleted from our system.!",
	    icon: "warning",
	    buttons: true,
	    dangerMode: true,
	  }).then((willDelete) => {
	    if (willDelete) {
	      swal("Please wait, your GST/VAT data is being deleted!", {
	        icon: "info",
	      });

	      window.location.href = base_url('delete-tax-identification/'+company_service_tax_master_id);
	    } else {
	      swal("Your GST/VAT data is safe!");
	    }
  	});
});

$(document).on('change','#CompanyAddress', function(){
	var service_tax_type = $(this).find(':selected').attr('data-service_tax_type');
	var service_tax_type_id = $(this).find(':selected').attr('data-service_tax_type_id');
	var tax_identification_number = $(this).find(':selected').attr('data-tax_identification');

	$('#CompanyServiceTaxType').val(service_tax_type);
	$('#CompanyServiceTaxTypeID').val(service_tax_type_id);
	$('.CompanyServiceTaxIdentificationNumber').val(tax_identification_number);
});

$('#CompanyAddress').trigger('change');

$(document).on('change','#ClientServiceTaxIdentificationNumber',function(){
	var service_tax_type = $(this).find(':selected').attr('data-service_tax_type');
	var service_tax_type_id = $(this).find(':selected').attr('data-service_tax_type_id');
	var billing_address = $(this).find(':selected').attr('data-billing_address');

	$('#ServiceTaxTypeID').val(service_tax_type_id);
	$('#ServiceTaxType').val(service_tax_type);
	$('#ClientBillingAddress').val(billing_address);
});

$(document).on('change','#VendorID', function(){
	var vendor_id = $(this).val();
	var selected_vendor_service_tax_no = $('#VendorServiceTaxIdentificationNumber').attr('data-selected_vendor_service_tax_identification_number');
   	
   	$('#VendorServiceTaxIdentificationNumber.bs_multiselect').empty().multiselect('destroy');

   	$('.add-vendor-service-tax-no').attr({
   		'href':base_url('add-vendor-service-tax/'+vendor_id),
   		'target':'_blank'
   	});

	$.ajax({
        url: base_url('api/vendors/get_vendor_data'),
        data: {
        	'VendorID':vendor_id
        },
        type: "GET",
        dataType: "json",
        beforeSend: function(){
        	
        },
        success: function(response){
        	var vendor_data = response.data.vendor_data;
        	var vendor_service_tax_data = response.data.vendor_service_taxes;

        	$('#VendorContactNo').val(vendor_data.VendorUserContactNo);
        	
        	var vendor_service_tax_html = '<option value="">Select Service Tax No</option>';
        	$.each(vendor_service_tax_data, function(index,item){
        		vendor_service_tax_html += '<option value="'+item.ServiceTaxNumber+'" data-service_tax_type_id="'+item.ServiceTaxTypeID+'" data-service_tax_type="'+item.ServiceTaxType+'" data-billing_address="'+item.BillingAddress+'" >'+item.ServiceTaxNumber+'('+item.Label+')</option>';
        	});

        	$('#VendorServiceTaxIdentificationNumber').html(vendor_service_tax_html);

        	if(selected_vendor_service_tax_no != undefined && selected_vendor_service_tax_no != ''){
        		$('#VendorServiceTaxIdentificationNumber').val(selected_vendor_service_tax_no);	
        	}

        	bs_multiselect_init('#VendorServiceTaxIdentificationNumber.bs_multiselect');
        }
    })
});

if($('#VendorID').val() != ''){
	$('#VendorID').trigger('change');
}

$(document).on('change','#VendorServiceTaxIdentificationNumber',function(){
	var service_tax_type = $(this).find(':selected').attr('data-service_tax_type');
	var service_tax_type_id = $(this).find(':selected').attr('data-service_tax_type_id');
	var billing_address = $(this).find(':selected').attr('data-billing_address');

	$('#ServiceTaxTypeID').val(service_tax_type_id);
	$('#ServiceTaxType').val(service_tax_type);
	$('#VendorBillingAddress').val(billing_address);
});

$(document).on('click','.add-po-particular', function(){
	var particular_count = parseInt($('.remove-po-particular-btn-container').length);
	var billing_detail_count = particular_count + 1;
	var particular_tax_key = Math.floor(1000 + Math.random() * 9000);
	var particulars = $('.po-particular-container .po-particular-selector:eq(0)').html();
	var particulars_html = '<select name="Particular[]" id="particular-'+particular_tax_key+'" class="form-control po-particular-selector bs_multiselect">'+particulars+'</select>';

	var html = '<div class="panel panel-default"> <div class="panel-heading"> <h4 class="pull-left">Detail '+billing_detail_count+'</h4> <div class="pull-right"><button type="button" class="btn bg-olive add-po-particular pull-left">Add Particular</button> <div class="remove-po-particular-btn-container pull-left"></div></div> <div class="clearfix"></div> </div> <div class="panel-body"> <div class="row"> <div class="col-md-4"> <input type="hidden" name="ParticularTaxKey[]" value="'+particular_tax_key+'" class="particular-tax-key"> <div class="form-group"> <label for="">Particular <span class="text-danger">*</span> <a href="javascript:void(0)" data-toggle="modal" data-target="#saveItemModal">Add Item</a> </label></label> '+particulars_html+' <input type="hidden" name="ItemType[]" class="item-type"></div> </div>  <div class="col-md-2"> <div class="form-group"> <label for="">HSN/SAC <span class="text-danger">*</span></label> <input type="text" name="HSN[]" value="" id="hsn-'+particular_tax_key+'" class="form-control hsn-code"> </div> </div> <div class="col-md-2"> <div class="form-group"> <label for="">Quantity <span class="text-danger">*</span></label> <input type="text" name="Quantity[]" value="" id="qty-'+particular_tax_key+'" min="1" class="form-control qty"> </div> </div> <div class="col-md-2"> <div class="form-group"> <label for="">Price per unit <span class="text-danger">*</span></label> <input type="text" name="PricePerUnit[]" value="" id="price-per-unit-'+particular_tax_key+'" min="1" class="form-control price-per-unit"> </div> </div> <div class="col-md-2"> <div class="form-group"> <label for="">Amount</label> <input type="text" name="Amount[]" readonly value="" class="form-control amount total"> </div> </div> </div> </div> </div>';
	
	$('.po-particular-container').append(html);
	bs_multiselect_init();
	if($('.expense-heading-container .remove-po-particular-btn').length == 0 || $('.expense-heading-container .remove-po-particular-btn-container').length > 1){
		var button_html = '<button type="button" class="btn btn-danger remove-po-particular-btn">Remove</button>';
		$('.remove-po-particular-btn-container').html(button_html);
	}
});

$(document).on('click','.remove-po-particular-btn', function(){
	$(this).parent().parent().parent().parent().remove();
	if($('.remove-po-particular-btn').length == 1){
		$('.remove-po-particular-btn').remove();
	}

	sumParticular($(this).parent().parent().parent().parent());
});

$(document).on('change','.po-particular-selector', function(){
	var particular = $(this).val();
	var price = $(this).find(':selected').attr('data-buying_price');
	var hsn = $(this).find(':selected').attr('data-hsn');
	var taxes = $(this).find(':selected').attr('data-taxes');
	var taxes_arr = taxes.split('|');
	var item_type = $(this).find(':selected').attr('data-item_type');

	var active_detail_section = $(this).parent().parent().parent().parent().parent();
	$(active_detail_section).find('.tax-container:gt(0)').remove();

	if(item_type != 'Good'){
		active_detail_section.find('.qty').val('').attr('readonly',true).trigger('change');	
	}else{
		active_detail_section.find('.qty').attr('readonly',false);
	}

	for(var i=0;i<taxes_arr.length;i++){
		var tax_details_array = (taxes_arr[i].split(':'));
		
		if(i>0){
			$(active_detail_section).find('.add-tax').trigger('click');
		}
		
		active_detail_section.find('.tax:eq('+i+')').val(tax_details_array[0]);
		active_detail_section.find('.tax-percentage:eq('+i+')').val(tax_details_array[1]);
	}	

	active_detail_section.find('.price-per-unit').val(price);
	active_detail_section.find('.hsn-code').val(hsn);
	active_detail_section.find('.price-per-unit').trigger('change');
	active_detail_section.find('.item-type').val(item_type);
});

$(document).on('change','#ChangePurchaseOrderStatus', function(){
	var purchase_order_status = $(this).find(':selected').html();
	if(purchase_order_status != 'Canceled'){
		changePurchaseOrderStatus();
	}else{
		$("#purchaseOrderStatusModal").modal("show");
	}
});

function changePurchaseOrderStatus(){
	var purchase_order_status_id = $('#ChangePurchaseOrderStatus').val();
	var here = $(this);

	$.ajax({
        url: base_url('api/finance/update_purchase_order_status'),
        data: {
        	'PurchaseOrderID':$('#PurchaseOrderID').val(),
        	'PurchaseOrderStatusID':purchase_order_status_id,
        	'CancelationRemark': $('#CancelationRemark').val()
        },
        type: "POST",
        dataType: "json",
        beforeSend: function(){
        	$('.overlay-wrapper').removeClass('hide');
      		$('.loader').addClass('breathing-animation');
        },
        success: function(response){
        	swal(response.msg,'', "success");
        	$("#purchaseOrderStatusModal").modal("hide");
        },
        error: function(response){
        	$('#ChangePurchaseOrderStatus').val(response.responseJSON.data);
        	swal({
		        title: response.responseJSON.msg, 
		        html: true,
		        content: {
		          element: 'p',
		          attributes: {
		            innerHTML: response.responseJSON.error,
		          },
		        },
		        dangerMode: true,  
	      	});
        },
        complete: function(response){
        	$('.overlay-wrapper').addClass('hide');
      		$('.loader').removeClass('breathing-animation');
        }
    });
}

$("#po-setting-form").validate({
  	ignore:[],
  	rules: {
      	'PaymentTerms':{
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

$("#purchase-order-form").validate({
  	ignore:[],
  	rules: {
      	"CompanyAddress": {
          	required: true,
      	},
      	"VendorID":{
        	required: true,
      	},
      	"VendorContactNo":{
	        required: true,
	        digits:true
      	},
      	'DeliveryDate':{
	        required: true,
      	},
      	'PaymentTerms':{
	        required: true
      	},
      	'PurchaseOrderStatusID':{
	        required: true
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
      	}
  	},
  	messages: {
      	"VendorContactNo": {
          	digits: "Contact number should be numeric"
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

$(document).on('click','#save-cancelation-remark', function(){
	changePurchaseOrderStatus();
});

$(document).on('click','.filter-invoice,.amount-filter-box', function(){
	var params = $('#form-filter').serialize();
	var export_excel_url = $('.export-excel').attr('href');
		export_excel_url = export_excel_url.replace(/\?.*/g,"$'");
	$('.export-excel').attr('href',export_excel_url+'?'+params);
});

$(document).on('change','#financial-date-filter', function(){
	var invoice_date = $(this).val();
	$('#financials-pie-chart-container').empty();
	$.ajax({
        url: base_url('api/finance/get_sales_data'),
        data: {
        	'ClientInvoiceDate':invoice_date
        },
        type: "GET",
        dataType: "json",
        beforeSend: function(){
        	$('.received-outstanding-container .div-loader').removeClass('hide');
        },
        success: function(response){

        	var total_price = (response.data.total_price != null)?parseFloat(response.data.total_price):0;
        	var total_received = (response.data.total_received_amount != null)?parseFloat(response.data.total_received_amount):0;
        	var outstanding = 0;

        	$('#financials-pie-chart-container').empty();
        	if(total_received > 0 || total_price > 0){
	          	var outstanding = (total_price - total_received).toFixed(2);

	       		var json = '{"labels":["Received","Outstanding"],"datasets":[{"backgroundColor":["#00a65a","#dd4b39"],"data":["'+total_received+'",'+outstanding+']}]}';
				$('#financials-pie-chart').attr('data-chart_data',json);
				var canvas_html = "<canvas id='financials-pie-chart' class='pieChart' data-chart_data='"+json+"'></canvas>";

				$('#financials-pie-chart-container').html(canvas_html);
				pieChart('#financials-pie-chart');
        	}

        	$('.total-sales').html(numberWithCommas(total_price));
			$('.total-received').html(numberWithCommas(total_received));
			$('.total-outstanding').html(numberWithCommas(outstanding));

        },
        complete: function(response){
        	$('.received-outstanding-container .div-loader').addClass('hide');
        }
    });
});

$('#financial-date-filter').trigger('change');

$(document).on('click', '.mail-invoice-btn', function(index, item){
	var here = $(this);
	CKEDITOR.instances.Content.updateElement();
	
	var form_data = $('#mailInvoiceModal #mailForm').serializeArray();

	$('#mailInvoiceModal .text-danger').remove();

	$.ajax({
        url: base_url('api/finance/mail_invoice'),
        data: form_data,
        type: "POST",
        dataType: "json",
        beforeSend: function(){
        	here.html('Sending mail...').removeClass('btn-success mail-invoice-btn').addClass('btn-default');
        },
        error: function(response){
        	var err = response.responseJSON.err;

        	if(err != undefined && err != ''){
        		$.each(err, function(index, item){
        			var error_html = '<span class="text-danger">'+item+'</span>';
        			$('#'+index).after(error_html);
        		});
        	}else{
        		swal(response.responseJSON.msg,response.responseJSON.error, "error");
        	}

        	here.html('Send Mail').removeClass('btn-default').addClass('mail-invoice-btn btn-success');
        },
        success: function (response) {
        	$('#mailInvoiceModal').modal('hide');
    		swal(response.msg,'', "success");
    		here.html('Send Mail').removeClass('btn-default').addClass('mail-invoice-btn btn-success');
        }
    });
});

$(document).on('keypress','#BarcodeNo', function(e){
	if(e.keyCode === 13){
        e.preventDefault(); // Ensure it is only this code that runs

        var barcode_no = $(this).val();

        /* Particular selector code for invoice starts */

		if($(this).val() != ''){
			var added_particular = 0;
			var out_of_stock = 0;
			var error = 0;
			$('.particular-selector').each(function(){
				var selected_particular = $(this).find("option[data-barcode_no='" + barcode_no + "']").val();

				if(selected_particular === undefined){
					swal('Item not found','Not item was found against the provided barcode','error');
					error = 1;
					return false;
				}

				var selected_particular_qty = parseInt($(this).find("option[data-barcode_no='" + barcode_no + "']").attr('data-item_qty'));

				if(selected_particular_qty == '0'){
					out_of_stock = 1;
					swal('Item out of stock',selected_particular+' is out of stock','error');
					return false;
				}

				if($(this).find('option:selected').val() == selected_particular){
					var qty = $(this).parent().parent().parent().parent().find('.qty').val();
						qty = (qty != '')?parseInt(qty):0;

					if(selected_particular_qty <= qty){
						swal('Item out of stock',selected_particular+' is out of stock','error');
						added_particular = 1;
						return false;
					}

					$(this).parent().parent().parent().parent().find('.qty').val(qty + 1).trigger('keyup');

					added_particular = 1;
					return false;
				} else if($(this).val() == '' && selected_particular != undefined){
					$(this).parent().parent().parent().parent().find('.qty').val('1');
					$(this).val(selected_particular).attr('selected',true).trigger('change');

					$(".bs_multiselect").multiselect("refresh");
					added_particular = 1;
					return false;
				}else{}
			});

			if(out_of_stock == 1 || error == 1){
				$(this).val('');
				return false;
			}

			if(added_particular == 0){
				$('.add-particular:eq(0)').trigger('click');

				var selected_particular = $('.particular-selector:last').find("option[data-barcode_no='" + barcode_no + "']").val();

				$(this).parent().parent().parent().find('.qty:last').val('1');

				$('.particular-selector:last').val(selected_particular).attr('selected',true).trigger('change');

				
				$(".bs_multiselect").multiselect("refresh");
			}
		}

		/* Particular selector code for invoice ends */

		$(this).val('');
    }
});


$(document).on('keypress','#po-BarcodeNo', function(e){
	if(e.keyCode === 13){
        e.preventDefault(); // Ensure it is only this code that runs

        var barcode_no = $(this).val();

        /* Particular selector code for invoice starts */

		if($(this).val() != ''){
			var added_particular = 0;
			var error = 0;
			$('.po-particular-selector').each(function(){
				var selected_particular = $(this).find("option[data-barcode_no='" + barcode_no + "']").val();
				var buying_price = $(this).find("option[data-barcode_no='" + barcode_no + "']").attr('data-buying_price');

				if(selected_particular === undefined){
					swal('Item not found','Not item was found against the provided barcode','error');
					error = 1;
					return false;
				}

				var selected_particular_qty = parseInt($(this).find("option[data-barcode_no='" + barcode_no + "']").attr('data-item_qty'));


				if($(this).find('option:selected').val() == selected_particular){
					var qty = $(this).parent().parent().parent().parent().find('.qty').val();
						qty = (qty != '')?parseInt(qty):0;
					$(this).parent().parent().parent().parent().find('.qty').val(qty + 1).trigger('change');

					added_particular = 1;
					return false;
				}else if($(this).val() == '' && selected_particular != undefined){
					$(this).val(selected_particular).attr('selected',true).trigger('change');
					$(this).parent().parent().parent().parent().find('.qty').val('1');
					$(this).parent().parent().parent().parent().find('.price-per-unit').val(buying_price);

					$(".bs_multiselect").multiselect("refresh");
					added_particular = 1;
					return false;
				}else{}
			});

			if(error == 1){
				$(this).val('');
				return false;
			}

			if(added_particular == 0){
				$('.add-po-particular:eq(0)').trigger('click');

				var selected_particular = $('.po-particular-selector:last').find("option[data-barcode_no='" + barcode_no + "']").val();
				$(this).parent().parent().parent().parent().find('.qty:last').val('1');

				$('.po-particular-selector:last').val(selected_particular).attr('selected',true).trigger('change');

				
				$(".bs_multiselect").multiselect("refresh");
			}
		}

		/* Particular selector code for invoice ends */

		$(this).val('');
    }
});

if($('.amount-filter-box').length > 0){
	$(document).ajaxComplete(function(event,xhr,options){
		if(xhr.responseJSON.total_sales != undefined){
			var total_sales = (xhr.responseJSON.total_sales.total_price != null)?parseFloat(xhr.responseJSON.total_sales.total_price):0;
			
			var received_amount = (xhr.responseJSON.total_sales.total_received_amount)?parseFloat(xhr.responseJSON.total_sales.total_received_amount):0;
			var outstanding_amount = total_sales - received_amount;
				outstanding_amount = parseFloat(outstanding_amount).toFixed(2);

	  		$('.total-sales').html(numberWithCommas(total_sales.toFixed(2))+'/-');
	  		$('.received-amount').html(numberWithCommas(received_amount.toFixed(2))+'/-');
	  		$('.outstanding').html(numberWithCommas(outstanding_amount)+'/');
		}
	});
}

$(document).on('change','#item-expiry-filter', function(){
	var expiry_days = $(this).val();
	$.ajax({
        url: base_url('api/stock/get_expiring_items_count'),
        data: {
        	'ExpiryDays':expiry_days
        },
        type: "GET",
        dataType: "json",
        beforeSend: function(){
        	$('.expiring-items-container .div-loader').removeClass('hide');
        	$('.expiring-items-details-box').addClass('hide');
        },
        success: function(response){
        	var expiring_items_count = (response.data.total_expiring_items_count != null)?parseInt(response.data.total_expiring_items_count):'0';
        	if(expiring_items_count > 0){
        		var expiring_items_url = base_url('view-expiring-items?duration='+expiry_days);
        	}else{
        		var expiring_items_url = 'javascript:void(0)';
        	}

        	$('#expiring-items-url').attr('href',expiring_items_url);
        	$('#expiring-items-count').html(expiring_items_count);
        },
        error: function(response){
        	swal(response.responseJSON.msg,'', "error");
        },
        complete: function(){
        	$('.expiring-items-container .div-loader').addClass('hide');
        	$('.expiring-items-details-box').removeClass('hide');
        }
    });
});

$('#item-expiry-filter').trigger('change');

$(document).on('change','#ExpiryDays', function(){
	var expiry_days = $(this).val();
	$.ajax({
        url: base_url('api/stock/get_expiring_items_vendors'),
        data: {
        	'ExpiryDays':expiry_days
        },
        type: "GET",
        dataType: "json",
        beforeSend: function(){
        	$('.overlay-wrapper').removeClass('hide');
  			$('.loader').addClass('breathing-animation');
        },
        success: function(response){
        	var vendors_html = '<option value="">Select Vendor</option>';

        	$.each(response.data, function(index, item){
        		vendors_html += '<option value="'+item.VendorID+'">'+item.VendorName+'</option>';
        	});

        	$('#VendorID').html(vendors_html);
        },
        error: function(response){
        	swal(response.responseJSON.msg,'', "error");
        },
        complete: function(){
        	$('.overlay-wrapper').addClass('hide');
      		$('.loader').removeClass('breathing-animation');
        }
    });
});

$(document).on('click','#stock-return-btn', function(){
	var stock_inward_history_id = $(this).attr('data-stock_inward_history_id');
	var inward_date = $(this).attr('data-inward_date');

	$('#ReturnDate').attr('data-min-date',inward_date);
	$('#StockInwardHistoryID').val(stock_inward_history_id);

	daterangepicker('#ReturnDate.daterangepicker');
});

$(document).on('click','#return-expiring-stock-btn', function(){
	var here = $(this);
	var return_expiring_stock_form = $('#return-expiring-stock-form').serializeArray();

	$('#itemReturnModal .text-danger').remove();

	$.ajax({
        url: base_url('api/stock/save_returned_expiring_item'),
        data: return_expiring_stock_form,
        type: "POST",
        dataType: "json",
        beforeSend: function(){
        	here.removeAttr('id').removeClass('btn-success').addClass('btn-default').html('saving');
        },
        success: function(response){
        	$('#form-filter-btn').trigger('click');
        	$('#itemReturnModal').modal('hide');
    		swal(response.msg,'', "success");
        },
        error: function(response){
        	var err = response.responseJSON.err;
        	$.each(err, function(index, item){
    			var error_html = '<span class="text-danger">'+item+'</span>';
    			$('#'+index).after(error_html);
    		});
        },
        complete: function(response){
			here.attr('id','return-expiring-stock-btn').removeClass('btn-default').addClass('btn-success').html('save');
        }
    });
});

$('#ExpiryDate.daterangepicker').on('apply.daterangepicker', function(ev, picker) {
    var expiry_date = picker.startDate.format('YYYY-MM-DD');
    var expiry_reminder_date = moment(expiry_date, "YYYY-MM-DD").subtract(1, "days").format('YYYY-MM-DD');
    
    $('#ExpiryReminderDate.daterangepicker').val('').attr('data-max-date',expiry_reminder_date);
    daterangepicker('#ExpiryReminderDate.daterangepicker');
});

$(document).on('click','.filter-expiring-items', function(){
	var params = $('#form-filter').serialize();
	var export_excel_url = $('.export-excel').attr('href');
		export_excel_url = export_excel_url.replace(/\?.*/g,"$'");
	$('.export-excel').attr('href',export_excel_url+'?'+params);
});

$(document).on('click','.filter-expenses', function(){
	var params = $('#form-filter').serialize();
	var export_excel_url = $('.export-excel').attr('href');
		export_excel_url = export_excel_url.replace(/\?.*/g,"$'");
	$('.export-excel').attr('href',export_excel_url+'?'+params);
});

$(document).on('click','.filter-gstr1', function(){
	var params = $('#form-filter').serialize();
	var export_excel_url = $('.export-excel').attr('href');
		export_excel_url = export_excel_url.replace(/\?.*/g,"$'");
	$('.export-excel').attr('href',export_excel_url+'?'+params);
});