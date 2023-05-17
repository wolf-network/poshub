$(document).on('change','#ClientID', function(){
    var here = $(this);
    var client_id = $(this).val();
    var invoice_id = $(this).attr('data-invoice_id');
    $('#InvoiceID.bs_multiselect').multiselect('destroy');

    var invoice_data_html = '<option value="">Select Invoice No</option>';
    $('#InvoiceID').html(invoice_data_html);

    $.ajax({
        url: base_url('api/finance/get_client_invoices'),
        data: {
            'ClientID':client_id
        },
        type: "GET",
        dataType: "json",
        beforeSend: function(){
            $('.overlay-wrapper').removeClass('hide');
            $('.loader').addClass('breathing-animation');
        },
        success: function(response){
            var invoice_data = response.data;

            $.each(invoice_data, function(index,item){
                invoice_data_html += '<option value="'+item.InvoiceID+'">'+item.InvoiceNo+'</option>';
            });

            $('#InvoiceID').html(invoice_data_html).val(invoice_id);
        },
        error: function(response){
            alert(response.responseJSON.msg);
        },
        complete: function(){
            $('.overlay-wrapper').addClass('hide');
            $('.loader').removeClass('breathing-animation');
            if(invoice_id != ''){
              $('#InvoiceID').trigger('change');
            }
            
            here.removeAttr('data-invoice_id');
            bs_multiselect_init('#InvoiceID.bs_multiselect');
        }
    });
});

if($('#ClientID').val() != ''){
    $('#ClientID').trigger('change');
}

$(document).on('change','#InvoiceID', function(){
    var invoice_id = $(this).val();

    $('#ClientServiceTaxID,#BillingAddress,#ShippingAddress').val('');
    $('.particular-return-container:gt(0),.particular-return-container tr:gt(1)').remove();
    $('.particular-return-container .hsn-code,.particular-return-container .qty, .particular-return-container .price-per-unit,.particular-return-container .amount,.particular-return-container .total-taxable-amount,.particular-return-container .total').val('');

    $('.detail-count').html('1');

    $('.particular-return-selector option:gt(0)').remove();

    $('.particular-return-selector').multiselect('destroy');
    var particular_return_selector = $('.particular-return-selector').parent().html();

    $('.particular-selector-container').html(particular_return_selector);

    if(invoice_id == ''){
        return false;
    }

    $.ajax({
        url: base_url('api/finance/get_invoice_data'),
        data: {
            'InvoiceID':invoice_id
        },
        type: "GET",
        dataType: "json",
        beforeSend: function(){
            $('.overlay-wrapper').removeClass('hide');
            $('.loader').addClass('breathing-animation');
        },
        success: function(response){
            $('#ClientServiceTaxID').val(response.data.invoice_data.ClientServiceTaxIdentificationNumber);
            $('#BillingAddress').val(response.data.invoice_data.ClientBillingAddress);
            $('#ShippingAddress').val(response.data.invoice_data.ClientShippingAddress);

            var invoice_details = response.data.invoice_details;
            var invoice_details_html = '<option value="">Select Item</option>';

            if(invoice_details.length > 0){
                $.each(invoice_details, function(index, item){

                    if(parseFloat(item.Discount) > 0){
                        var pre_discount_item_price = parseFloat(item.PricePerUnit);
                        var discount_amt = pre_discount_item_price * parseFloat(item.Discount) / 100;
                        var item_price = pre_discount_item_price - discount_amt;
                    }else{
                        var item_price = parseFloat(item.PricePerUnit);
                    }

                    var barcode_no = (item.BarcodeNo != null)?item.BarcodeNo:'';

                    var returnable_qty = item.Quantity - item.returned_qty;

                    invoice_details_html += '<option value="'+item.Particular+'" data-particular_type="'+item.ParticularType+'" data-barcode_no="'+barcode_no+'" data-item_qty="'+returnable_qty+'" data-item_price="'+item_price+'" data-hsn="'+item.HSN+'" data-taxes="'+item.service_taxes+'">'+item.Particular+' ('+returnable_qty+' units returnable) </option>';
                });
            }

            $('.particular-return-selector').html(invoice_details_html);
            
        },
        error: function(response){
            alert(response.responseJSON.msg);
        },
        complete: function(){
            $('.overlay-wrapper').addClass('hide');
            $('.loader').removeClass('breathing-animation');
            bs_multiselect_init('.particular-return-selector.bs_multiselect');
        }
    });
});

function SumParticularReturns(here){
    var hsn = here.find('.particular-return-selector').find(':selected').attr('data-hsn');
    var tax_details = here.find('.particular-return-selector').find(':selected').attr('data-taxes');
    var qty = (here.find('.qty').val() != '')?parseFloat(here.find('.qty').val()):0;

    if(here.find('.particular-return-selector').val() != ''){
        var price_per_unit = (here.find('.price-per-unit').val() != '')?parseFloat(here.find('.price-per-unit').val()):'';
    }else{
        var price_per_unit = 0;
    }

    if(here.find('.qty').attr('readonly') == 'readonly'){
        var amount = price_per_unit;
    }else{
        var amount = price_per_unit * qty;
    }


    here.find('.hsn-code').val(hsn);
    here.find('.price-per-unit').val(price_per_unit);
    here.find('.amount').val(amount);
    here.parent().find('.tax-table tr:gt(1)').remove();

    var total_taxable_amount = 0;

    if(tax_details != '' && tax_details != undefined){
        var tax_details_arr = tax_details.split(',');
        for(var i=0;i<tax_details_arr.length;i++){
            var tax_data_split = tax_details_arr[i].split('-');
            var taxable_value = amount * parseFloat(tax_data_split[1]) / 100;
                // taxable_value.toFixed(2);

                total_taxable_amount += taxable_value;

            var tax_table_html = '<tr><td>'+tax_data_split[0]+'</td><td>'+tax_data_split[1]+'</td><td>'+taxable_value.toFixed(2)+'</td></tr>';
            here.parent().find('.tax-table').append(tax_table_html);
        }
    }

    var total_payable_amount = amount + total_taxable_amount;
    
    here.parent().find('.total-taxable-amount').val(roundDown(total_taxable_amount,2));
    here.parent().find('.total').val(total_payable_amount.toFixed(2));
}

$(document).on('change','.particular-return-selector', function(){
    var particular_type = $(this).find('option:selected').attr('data-particular_type');
    var item_qty = $(this).find('option:selected').attr('data-item_qty');
    var price_per_unit = $(this).find('option:selected').attr('data-item_price');
    var error = 0;

    if($(this).val() != ''){
        var here = $(this);
        var particular = $(this).val();
        var particular_id = $(this).attr('id');

        $('.particular-return-selector').each(function(){
            if($(this).val() == particular && $(this).attr('id') != particular_id){
                here.val('');
                
                $('#'+particular_id+'.bs_multiselect').multiselect('destroy');
                bs_multiselect_init('#'+particular_id+'.bs_multiselect');

                swal('Duplicate particular selected','This item has already been selected. Please update the qty if you need to.', "error");

                error = 1;
                return false;
            }   
        });
    }

    var here = $(this).parent().parent().parent().parent().parent();

    if(error == 1 || $(this).val() == ''){
        here.find('.qty').val('0');
    }else{
        here.find('.qty').val('1');
    }

    if(particular_type == 'Service'){
        here.find('.qty').attr({'readonly':true}).removeAttr('max').val('');
    }else{
        here.find('.qty').attr({'max':item_qty,'readonly':false});
    }
    
    here.find('.ParticularType').val(particular_type);
    here.find('.price-per-unit').val(price_per_unit).attr('max',price_per_unit);

    SumParticularReturns(here)
});

$(document).on('keyup','.qty,.price-per-unit', function(){
    var here = $(this).parent().parent().parent().parent();
    SumParticularReturns(here);
});

$(document).on('click','.add-particular-return', function(){
    var particular_id = Math.floor(1000 + Math.random() * 9000);

    var particular_return_container_html = $('.particular-return-container:eq(0)').clone();

    particular_return_container_html.find('.particular-return-selector').multiselect('destroy');

    var particular_return_selector = particular_return_container_html.find('.particular-return-selector').parent().html();
        
        particular_return_container_html.find('.multiselect-native-select').remove();

        particular_return_container_html.find('.particular-selector-container').html(particular_return_selector);
        particular_return_container_html.find('.qty').removeAttr('max').attr('id','qty-'+particular_id);
        particular_return_container_html.find('.price-per-unit').removeAttr('max').attr('id','ppu-'+particular_id);

        particular_return_container_html.find('input').val('');
        particular_return_container_html.find('.tax-table tr:gt(1)').remove();
        particular_return_container_html.find('.error.help-block').remove();

    var particular_return_container_count = $('.particular-return-container').length + 1;

        particular_return_container_html.find('.detail-count').html(particular_return_container_count);

    $('.particular-return-container').parent().append(particular_return_container_html);

    $('.particular-return-selector:last').attr('id',particular_id);

    if($('.remove-particular-return-btn').length <= 0){
        var remove_particular_return_btn_html = '<button type="button" class="btn btn-danger remove-particular-return-btn">Remove</button>';
        $('.remove-particular-return-btn-container').html(remove_particular_return_btn_html);
    }

    bs_multiselect_init('.particular-return-selector.bs_multiselect:last');
});

$(document).on('click','.remove-particular-return-btn', function(){
    $(this).parent().parent().parent().parent().parent().remove();
    
    var i = 0;
    $('.panel-heading .detail-count').each(function(){
        $(this).parent().parent().parent().find('.detail-count').html(i+1);
        i++;
    });

    if($('.remove-particular-return-btn').length <= 1){
        $('.remove-particular-return-btn').remove();
    }
});

creditNoteValidation();
function creditNoteValidation(){
    $("#credit-note-form").validate({
        ignore:[],
        rules: {
            'ClientID':{
                required: true
            },
            'CreditNoteDate':{
                required: true
            },
            'InvoiceID':{
                required: true
            },
            'PaymentStatus':{
                required: true
            },
            'Particular[]':{
                required: true,
            },
            'Quantity[]':{
                decimal: true,
                required: function(e){
                    if(!e.hasAttribute('readonly')){
                        return true;
                    }else{
                        return e.value;
                    }
                },
                max: function(e){
                    if(e.hasAttribute('readonly')){
                        return true;
                    }else{
                        return parseInt(e.getAttribute('max'));
                    }
                }
            },
            'HSN[]':{
                required: true
            },
            'PricePerUnit[]':{
                required: true,
                decimal: true,
                max: function(e){
                    return parseInt(e.getAttribute('max'));
                }
            }
        },
        messages: {
           'Quantity[]':{
                max: "Return qty should be less then or equal to sold qty."
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
}

$(document).on('keypress','#BarcodeNo', function(e){
    if(e.keyCode === 13){
        e.preventDefault(); // Ensure it is only this code that runs

        var barcode_no = $(this).val();

        /* Particular selector code for invoice starts */

        if($(this).val() != ''){
            var added_particular = 0;
            var out_of_stock = 0;
            var error = 0;
            var returnable_qty = 
            $('.particular-return-selector').each(function(){
                var selected_particular = $(this).find("option[data-barcode_no='" + barcode_no + "']").val();

                if(selected_particular === undefined){
                    swal('Item not found','Not item was found against the provided barcode','error');
                    error = 1;
                    return false;
                }

                var selected_particular_qty = parseInt($(this).find("option[data-barcode_no='" + barcode_no + "']").attr('data-item_qty'));

                
                if($(this).find('option:selected').val() == selected_particular){
                    var qty = $(this).parent().parent().parent().parent().parent().find('.qty').val();
                        qty = (qty != '')?parseInt(qty):0;

                    if(selected_particular_qty <= qty){
                        swal('Return qty should be less then or equal to sold qty.','Only '+qty+' quantity of '+selected_particular+' is returnable','error');
                        added_particular = 1;

                        return false;
                    }

                    $(this).parent().parent().parent().parent().parent().find('.qty').val(qty + 1).trigger('keyup');

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

                var selected_particular = $('.particular-return-selector:last').find("option[data-barcode_no='" + barcode_no + "']").val();

                $(this).parent().parent().parent().find('.qty:last').val('1');

                $('.particular-return-selector:last').val(selected_particular).attr('selected',true).trigger('change');

                
                $(".bs_multiselect").multiselect("refresh");
            }
        }

        /* Particular selector code for invoice ends */

        $(this).val('');
    }
});

$(document).on('click','.delete-credit-note', function(){
    var credit_note_id = $(this).attr('data-credit_note_id');

    swal({
        title: "Are you sure?",
        text: "This will be permanently deleted from our system.!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          swal("Please wait, your credit is being deleted!", {
            icon: "info",
          });

          window.location.href = base_url('delete-credit-note/'+credit_note_id);
        } else {
          swal("Your credit note is safe!");
        }
    });
});

$(document).on('click','.filter-credit-note', function(){
    var params = $('#form-filter').serialize();
    var export_credit_note_url = $('.export-credit-note').attr('href');
        export_credit_note_url = export_credit_note_url.replace(/\?.*/g,"$'");
    $('.export-credit-note').attr('href',export_credit_note_url+'?'+params);
});