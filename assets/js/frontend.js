function base_url(page = ''){
    var base_url = $('body').attr('data-base_url');
    return base_url+page;
}

function bs_multiselect_init(selector = ''){
    var selector = (selector != '')?selector:'.bs_multiselect';
    var selector_index = $(selector).index();
    var selector_container_class = selector+'_container'+selector_index;
    selector_container_class = selector_container_class.replace(/[.#]/g,'');

    var include_select_all_option = ($(selector).attr('data-include-select-all-option') == 'false')?false:true;

    $(selector).multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: include_select_all_option,
        optionClass: function(element) {
            return selector_container_class;
        },
        onChange: function(option, checked) {
            var max_limit = $(selector).attr('data-max_limit');
            var max_limit_msg = $(selector).attr('data-max_limit_msg');
            var selectedOptions = $(selector+' option:selected');
            if(max_limit != undefined && max_limit != '' && !isNaN(max_limit)){
                if (selectedOptions.length == max_limit) {
                    alert(max_limit_msg);
                    // Disable all other checkboxes.
                    var nonSelectedOptions = $(selector+' option').filter(function() {
                        return !$(this).is(':selected');
                    });

                    nonSelectedOptions.each(function() {
                        var input = $('.'+selector_container_class+' input[value="' + $(this).val() + '"]');
                        input.prop('disabled', true);
                        input.parent('li').addClass('disabled');
                    });
                }else {
                    // Enable all checkboxes.
                    $(selector+' option').each(function() {
                        var input = $('.'+selector_container_class +' input[value="' + $(this).val() + '"]');
                        input.prop('disabled', false);
                        input.parent('li').addClass('disabled');
                    });
                }
            }
        }
    });   
}

bs_multiselect_init();

$(document).on('change','.country', function(){
    var selected_state = ($('.state').attr('data-selected_state') != '')?$('.state').attr('data-selected_state'):$('.state').val();
    var selected_city = ($('.city').attr('data-selected_city') != '')?$('.city').attr('data-selected_city'):$('.city').val();
    if(selected_city != ''){
        $('.city').attr('data-selected_city',selected_city);
    }
    $('.state.bs_multiselect, .city.bs_multiselect').empty().multiselect('destroy');
    bs_multiselect_init('.state.bs_multiselect');
    bs_multiselect_init('.city.bs_multiselect');
    $.ajax({
        url: base_url('api/basic/get_states'),
        data:{
            'country_id':$(this).val()
        },
        type:"GET",
        dataType:"json",
        beforeSend: function(){
            $('.overlay-wrapper').removeClass('hide');
            $('.loader').addClass('breathing-animation');
        },
        success:function(response){
            var html = '';
            $('.state').each(function(){
                html = ($(this).attr('multiple') == undefined)?'<option value="">Select State</option>':'';
                $.each(response.data, function(index, item){
                    html += '<option value="'+item.StateID+'" data-region="'+item.Region+'">'+item.StateName+'</option>'; 
                });

                $(this).html(html);

                if(selected_state != '' && selected_state != null){

                    selected_state = (!Array.isArray(selected_state))?selected_state.split(','):selected_state;
                    $(this).val(selected_state).attr('data-selected_state','').trigger('change');
                }
            });

            $('.state.bs_multiselect').multiselect('destroy');
            bs_multiselect_init('.state.bs_multiselect');
        },
        complete: function(){
            $('.overlay-wrapper').addClass('hide');
            $('.loader').removeClass('breathing-animation');
        }
    });
});

$('.country').change();

$(document).on('change','.state', function(){
    var selected_city = ($('.city').attr('data-selected_city') != '')?$('.city').attr('data-selected_city'):$('.city').val();
    $('.city.bs_multiselect').empty().multiselect('destroy');

    bs_multiselect_init('.city.bs_multiselect');
    $.ajax({
        url: base_url('api/basic/get_cities'),
        data:{
            'state_id':$(this).val(),
        },
        type:"GET",
        dataType:"json",
        beforeSend: function(){
            $('.overlay-wrapper').removeClass('hide');
            $('.loader').addClass('breathing-animation');
        },
        success:function(response){

            $('.city').each(function(){
                var html = ($(this).attr('multiple') == undefined)?'<option value="">Select City</option>':'';
                $.each(response.data, function(index, item){
                    html += '<option value="'+item.CityID+'">'+item.CityName+'</option>'; 
                });

                $(this).html(html);

                if(selected_city != '' && selected_city != null){
                    selected_city = (!Array.isArray(selected_city))?selected_city.split(','):selected_city;
                    $(this).val(selected_city).attr('data-selected_city','');
                }
            });

            $('.city.bs_multiselect').multiselect('destroy').trigger('change');
            bs_multiselect_init('.city.bs_multiselect');
        },
        complete: function(){
            $('.overlay-wrapper').addClass('hide');
            $('.loader').removeClass('breathing-animation');
        },
    });
});

$(document).on('click','.multiselect.dropdown-toggle', function(){
    $('.multiselect-search').focus(); 
});