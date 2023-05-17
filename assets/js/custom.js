 

$(document).ready(function(){
    var modal_id = $('.show-modal').attr('id');
    $('#'+modal_id).modal('show');
});

$('#excelErrorModal').modal('show');

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

function base_url(page = ''){
    var base_url = $('body').attr('data-base_url');
    return base_url+page;
}

function media_server(page = ''){
    var media_server = $('body').attr('data-media_server');
    return media_server+page;
}

function nFormatter(number) {
    var SI_POSTFIXES = ["", "k", "M", "G", "T", "P", "E"];

// what tier? (determines SI prefix)
var tier = Math.log10(Math.abs(number)) / 3 | 0;

// if zero, we don't need a prefix
if(tier == 0) return number;

// get postfix and determine scale
var postfix = SI_POSTFIXES[tier];
var scale = Math.pow(10, tier * 3);

// scale the number
var scaled = number / scale;

// format number and add postfix as suffix
var formatted = scaled.toFixed(1) + '';

// remove '.0' case
if (/\.0$/.test(formatted))
    formatted = formatted.substr(0, formatted.length - 2);

return formatted + postfix;
}

function onlyUnique(value, index, self) {
    return self.indexOf(value) === index;
}

Object.defineProperty(Array.prototype, 'chunk_inefficient', {
    value: function(chunkSize) {
        var array = this;
        return [].concat.apply([],
            array.map(function(elem, i) {
                return i % chunkSize ? [] : [array.slice(i, i + chunkSize)];
            })
            );
    }
});


bs_multiselect_init();


pieChart();

function pieChart(selector = '', destroy = false){
    if(selector == ''){
        selector = '.pieChart';
    }else{
        selector = selector+'.pieChart';
    }

    $(selector).each(function(){
    //    //-------------
    //    //- PIE CHART -
    //    //-------------
    //    // Get context with jQuery - using jQuery's .get() method.

    var chart_data = JSON.parse($(this).attr('data-chart_data')); 
    var chart_background_color = (chart_data.datasets[0].backgroundColor != undefined)?chart_data.datasets[0].backgroundColor:[];

    for(var i=0; i<chart_data.datasets[0].data.length;i++){
        if(chart_data.datasets[0].backgroundColor == undefined){
            var randomColor = '#'+ ('000000' + Math.floor(Math.random()*16777215).toString(16)).slice(-6);
            chart_background_color.push(randomColor);
        }
    }

    chart_data.datasets[0]['backgroundColor'] = chart_background_color;
    //    
    //    //Create pie or douhnut chart
    //    // You can switch between pie and douhnut using the method below.
    //    pieChart.Pie(PieData, pieOptions);

    var ctx = $(this).get(0).getContext('2d');
    var myPieChart = new Chart(ctx, {
        type: ($(this).attr('data-pie') == 'true')?'pie':'doughnut',
        data: {
            datasets: chart_data.datasets,
    // These labels appear in the legend and in the tooltips when hovering different arcs
    labels: chart_data.labels
    },
    options: {
        legend: {
            display: true
        },
        plugins: {
            datalabels: {
                formatter: (value, ctx) => {
                    return Math.round(value) + '%';
                },
                color: '#ffffff',
                font: {
                    weight: 'bold'
                }
            }
        }
    },
    });

    if(destroy == true){
        myPieChart.destroy();
        pieChart(selector);
    }

    });
}

Chart.plugins.register({
    afterDraw: function(chartInstance) {
        if (chartInstance.config.options.showDatapoints) {
            var helpers = Chart.helpers;
            var ctx = chartInstance.chart.ctx;
            var fontColor = helpers.getValueOrDefault(chartInstance.config.options.showDatapoints.fontColor, chartInstance.config.options.defaultFontColor);

// render the value of the chart above the bar
ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, 'normal', Chart.defaults.global.defaultFontFamily);
ctx.textAlign = 'center';
ctx.textBaseline = 'bottom';
ctx.fillStyle = fontColor;

chartInstance.data.datasets.forEach(function (dataset) {
    for (var i = 0; i < dataset.data.length; i++) {
        var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model;
        var scaleMax = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._yScale.maxHeight;
        var yPos = (scaleMax - model.y) / scaleMax >= 0.93 ? model.y + 20 : model.y - 5;
        ctx.fillText(dataset.data[i], model.x, yPos);
    }
});
}
}
});


$('.bar-chart').each(function(){
    var chart_data = JSON.parse($(this).attr('data-chart_data'));
    var chart_type = ($(this).attr('data-chart_type'))?'horizontalBar':'bar';
    var suggested_max = Math.max.apply(Math, chart_data.datasets[0].data) + 2;
    var ctx = $(this).get(0).getContext('2d');
    var datasets_arr = [];
    for(var i=0;i<chart_data.datasets.length;i++){
        var background_color = "#c3a73e";

        if(chart_data.datasets[i].backgroundColor != undefined && chart_data.datasets[i].backgroundColor.length > 0){
            background_color = chart_data.datasets[i].backgroundColor;

        }
        var dataset_obj = {
            backgroundColor: background_color,
            borderColor: background_color,
            borderWidth: 2,
            hoverBackgroundColor: background_color,
            hoverBorderColor: background_color,
            data: chart_data.datasets[i].data,
            datalabels: {
                align: 'end',
                anchor: 'start'
            }
        };

        datasets_arr.push(dataset_obj);
    }

    var chart_data_label = chart_data.labels.map(function(x){return x.replace(/~/g, "'");});

    var myBarChart = new Chart(ctx, {
        type: chart_type,
        data: {
            labels: chart_data_label,
            datasets: datasets_arr
        },
        options: {
            animation: {
                duration:5000,
            },
            legend: {
                display: false
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        drawOnChartArea: false
                    }
                }],
                yAxes: [{
                    ticks: {
                        display: true,
                        beginAtZero: true,
                        precision:0,
                        suggestedMax:suggested_max
                    },
                    gridLines: {
                        drawOnChartArea: false
                    }
                }]
            },
            plugins: {
                datalabels: {
                    formatter: (value, ctx) => {
                        return nFormatter(Math.round(value));
                    },
                    color: '#000000',
                    font: {
                        weight: 'bold'
                    }
                }
            }
        }
    });
});

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

icheck_box();
function icheck_box(){
    var triggeredByChild = false;

    $('.icheck').iCheck({
        checkboxClass: 'icheckbox_square-comp-primary',
        radioClass: 'iradio_minimal-comp-primary',
increaseArea: '20%' // optional
});

    $('#check-all').on('ifChecked', function (event) {
        $('.icheck').iCheck('check');
        triggeredByChild = false;
    });

    $('#check-all').on('ifUnchecked', function (event) {
        if (!triggeredByChild) {
            $('.icheck').iCheck('uncheck');
        }
        triggeredByChild = false;
    });
    /* Removed the checked state from "All" if any checkbox is unchecked */
    $('.icheck').on('ifUnchecked', function (event) {
        triggeredByChild = true;
        $('#check-all').iCheck('uncheck');
    });

    $('.icheck').on('ifChecked', function (event) {
        if ($('.icheck').filter(':checked').length == $('.icheck').length) {
            $('#check-all').iCheck('check');
        }
    });  
}


var imageReplace = function(input, placeToReplaceSrc) {
    if (input.files) {
        var filesAmount = input.files.length;

        for (i = 0; i < filesAmount; i++) {
            var reader = new FileReader();

            reader.onload = function(event) {
                $(placeToReplaceSrc).attr('src',event.target.result);
            }
            reader.readAsDataURL(input.files[i]);
        }
    }

};

$(document).on('change','.img_replace', function(){
    var selector = $(this).attr('data-img_prev_selector');
    imageReplace(this, selector);
});

var imagesVideosPreview = function(input, placeToInsertMediaPreview, extension = 'img' , width='100', height='100') {
    if (input.files) {
        var filesAmount = input.files.length;

        for (i = 0; i < filesAmount; i++) {
            var reader = new FileReader();

            reader.onload = function(event) {

                if (extension == 'video') {
                    var uploaded_media_html = '<div class="media-wrap remove_media"><video width="'+width+'" height="'+height+'" controls><source src="'+event.target.result+'" type="video/mp4"></video></div>';
                }else{
                    var uploaded_media_html = '<div class="media-wrap remove_media"><img src="'+event.target.result+'" width="'+width+'" height="'+height+'"></div>';
                }


                $(placeToInsertMediaPreview).html(uploaded_media_html);
            }
            reader.readAsDataURL(input.files[i]);
        }
    }

};

$(document).on('change', '.media', function(){
    var extension = $(this).val().replace(/^.*\./, '');
    var media_selector = $(this).attr('data-media_prev_selector');
    var width = $(this).attr('data-width');
    var height = $(this).attr('data-height');

    var file_ext = 'img';
    if (extension == 'mp4' || extension == 'avi' || extension == 'ogg' || extension == 'flv' || extension == 'wmv' || extension == 'wmv' || extension == 'mov' || extension == 'mpg' || extension == 'mpeg' || extension == 'm4v') {
        file_ext = 'video'
    }
    imagesVideosPreview(this, media_selector, file_ext,width,height);
    $('.media_space').show();
});

$(document).on('click','.multiselect.dropdown-toggle', function(){
    $('.multiselect-search').focus(); 
});

$(document).on('change','#Region', function(){
    $('.country').trigger('change'); 
});



$(document).ready(function(){
    var current_url = $(location).attr('href');

    $('.sidebar-menu li a[href="'+current_url+'"]').parent().addClass('active');
    $('.sidebar-menu li a[href="'+current_url+'"]').parents('li').addClass('active menu-open');
});

$('#menus-container').jstree({
    "plugins" : ["checkbox"]
});

var assigned_menus = $('#assigned_menus').val();
if(assigned_menus != '' && assigned_menus != undefined){
    assigned_menus = assigned_menus.split(',');

    $.each(assigned_menus, function(index,item){
        if(typeof item != 'object'){
            $('#menus-container').jstree(true).select_node(item);
        }else{
            $.each(item, function(key, value){
                $('#menus-container').jstree(true).select_node(value);
            });
        }
    });
}

$(document).on('change','#role', function(){
    var assigned_menus =  $('option:selected', this).attr('data-assigned_menus');
    $("#menus-container").jstree().deselect_all(true)
    if(assigned_menus != ''){
        if(assigned_menus != 'all'){
            assigned_menus = assigned_menus.split(',');
            $('#menus-container').jstree(true).select_node(assigned_menus);
        }else{
            $('#menus-container').jstree("check_all");
        }
    }
});

daterangepicker();

function daterangepicker(selector = '.daterangepicker'){
    $(selector).each(function(){
        $(this).attr('autocomplete','off');
        var min_date = ($(this).attr('data-min-date') != '' && $(this).attr('data-min-date') != undefined)?$(this).attr('data-min-date'):'1901-01-01';

        var max_date = ($(this).attr('data-max-date') != '' && $(this).attr('data-max-date') != undefined)?$(this).attr('data-max-date'):false;

        var time_picker = ($(this).attr('data-time-picker') != undefined && $(this).attr('data-time-picker') == 'true')?true:false;

        var date_blank = true;
        if($(this).val() != ''){
            date_blank = false;
        }

        var picker_format = (time_picker == true)?'Y-MM-DD HH:mm':'Y-MM-DD';

        $(this).daterangepicker({
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            minDate: min_date,
            maxDate: max_date,
            timePicker24Hour: time_picker,
            locale: {
                format: picker_format
            }
        });

        if(date_blank == true){

$(this).val('');
}
});

    $('.daterangepicker').on('apply.daterangepicker', function(ev, picker) {
        var time_picker = ($(this).attr('data-time-picker') != undefined && $(this).attr('data-time-picker') == 'true')?true:false;
        var picker_format = (time_picker == true)?'Y-MM-DD HH:mm':'Y-MM-DD';

        $(this).val(picker.startDate.format(picker_format));
    });
}

function split( val ) {
    return val.split( /,\s*/ );
}

function extractLast( term ) {
    return split( term ).pop();
}

$('input[type=file]').on('change', function() {
    var max_size = $(this).attr('data-max_size');
    if(max_size != undefined){
        var size = this.files[0].size;

        if (size > max_size || size < 1) {
            alert("File must be greater then 0 KB & less then 5 MB");
            $(this).val('');
        }   
    }
});

$(document).on('change','.bank_id', function(){
    var bankID = $(this).val();
    var selected_bank_details = ($('.bank_details').attr('data-selected_bank_details_id') != '')?$('.bank_details').attr('data-selected_bank_details_id'):$('.bank_details').val();
    var html = ($('.bank_details').attr('multiple') == undefined)?'<option value="">Select IFSC Code</option>':'';

    $.ajax({
        url: base_url('api/finance/get_ifsc'),
        data:{
            'BankID':bankID,
            'BankDetailsID': selected_bank_details
        },
        type:"GET",
        dataType:"json",
        beforeSend: function(){
            $('.overlay-wrapper').removeClass('hide');
            $('.loader').addClass('breathing-animation');
        },
        success:function(response){

            $.each(response.data, function(index, item){
                html += '<option value="'+item.BankDetailsID+'">'+item.BankIFSC+'('+item.BankBranch+')</option>'; 
            });

            $('.bank_details').html(html);

            if(selected_bank_details != '' && selected_bank_details != null){
                selected_bank_details = (!Array.isArray(selected_bank_details))?selected_bank_details.split(','):selected_bank_details;
                $('.bank_details_container .multiselect-search').trigger('keyup');
                $('.bank_details').val(selected_bank_details).attr('data-selected_bank_details_id','');
            }

            $('.bank_details.bs_multiselect').multiselect('destroy').trigger('change');
            bs_multiselect_init('.bank_details.bs_multiselect');
        },
        error: function(){
            $('.bank_details').html(html);
            $('.bank_details.bs_multiselect').multiselect('destroy').trigger('change');
            bs_multiselect_init('.bank_details.bs_multiselect');
        },
        complete: function(){
            $('.overlay-wrapper').addClass('hide');
            $('.loader').removeClass('breathing-animation');
        },
    });
});

$('.bank_id').trigger('change');

ifsc_load_more();
function ifsc_load_more(){
    $('.bank_details_container .multiselect-container').on('scroll',function(){
//    alert($(this)[0].scrollHeight);
if(($(this).scrollTop() + $(this).innerHeight() + 10) >= $(this)[0].scrollHeight) {
//        alert('2');
var limit = 50;
var offset = $('.bank_details').attr('data-offset');
offset = (offset != undefined)?parseInt(offset):50;

$.ajax({
    url: base_url('api/finance/get_ifsc'),
    data:{
        'BankID':$('.bank_id').val(),
        'limit':limit,
        'offset':offset,
        'search_txt':$('.bank_details_container .multiselect-search').val()
    },
    type:"GET",
    dataType:"json",
    success:function(response)
    {
        var bank_details_html = '';
        var container_html = '';
        var existing_bank_details = [];

        $('.bank_details_container .multiselect-container li a label input').each(function(){
            existing_bank_details.push($(this).val());
        });

        $.each(response.data, function(index,item){
            if(!existing_bank_details.includes(item.BankDetailsID)){
                bank_details_html += '<option value="'+item.BankDetailsID+'">'+item.BankIFSC+'('+item.BankBranch+')</option>';
                container_html += '<li><a tabinde="0"><label class="radio" title="'+item.BankIFSC+'"><input type="radio" value="'+item.BankDetailsID+'" >'+item.BankIFSC+'('+item.BankBranch+')</label></a></li>';   
            }
        });
        $('.bank_details').append(bank_details_html);
        $('.bank_details_container .multiselect-container').append(container_html);
        $('.bank_details').attr({
            'data-offset':offset+50,
        });
    }
});
}
});
}

$(document).on('keyup','.bank_details_container .multiselect-search', function(){
    var limit = 50;
    var offset = 0;

    var selected_bank_details = $('.bank_details').attr('data-selected_bank_details_id');

    if(selected_bank_details != '' && selected_bank_details != null){
        selected_bank_details = (!Array.isArray(selected_bank_details))?selected_bank_details.split(','):selected_bank_details;
    }

    $.ajax({
        url: base_url('api/finance/get_ifsc'),
        data:{
            'BankID':$('.bank_id').val(),
            'limit':limit,
            'offset':offset,
            'search_txt':$(this).val(),
        },
        type:"GET",
        dataType:"json",
        success:function(response)
        {
            var bank_details_html = '';
            var container_html = '';
            var existing_bank_details_id = [];

            $('.bank_details_container .multiselect-container li a label input').each(function(){
                existing_bank_details_id.push($(this).val());
            });

            $.each(response.data, function(index,item){
                if(!existing_bank_details_id.includes(item.BankDetailsID)){
                    bank_details_html += '<option value="'+item.BankDetailsID+'">'+item.BankIFSC+'('+item.BankBranch+')</option>';
                    container_html += '<li><a tabinde="0"><label class="radio" title="'+item.BankIFSC+'"><input type="radio" value="'+item.BankDetailsID+'" >'+item.BankIFSC+'('+item.BankBranch+')</label></a></li>';
                }
            });
            $('.bank_details').append(bank_details_html);
            $('.bank_details_container .multiselect-container').append(container_html);
            if(selected_bank_details != ''){
                var previous_selected = $('.bank_details').val();
                $(".bank_details").multiselect('deselect', previous_selected);
                $('.bank_details').multiselect('select', selected_bank_details, true);
                $('.bank_details').attr('data-selected_bank_details_id','');
            }


        }
    });
});

$(document).on('change','.bank_details', function(){
    var bank_details_id = $(this).val();
    $.ajax({
        url: base_url('api/finance/get_micr'),
        data:{
            'BankDetailsID':bank_details_id
        },
        type:"GET",
        dataType:"json",
        success:function(response)
        {
            $('#BankMICR').val(response.data.BankMICR);
            if(response.data.BankMICR != null && response.data.BankMICR != ''){
                $('#BankMICR').attr('readonly',true);
            }
        }
    });
});

$(document).on('change','.client',function(){
    var client_id = $(this).val();
    var brand_id = $('.brand').attr('data-brand_id');
    var brands_html = '<option value="">Select Brand</option>';
    $.ajax({
        url: base_url('api/clients/get_brands'),
        data:{
            'ClientID':client_id
        },
        type:"GET",
        dataType:"json",
        success:function(response)
        {
            if(response.data.length > 0){
                $.each(response.data, function(index,item){
                    brands_html += '<option value="'+item.BrandID+'">'+item.Brand+'</option>';
                });
            }

            $('.brand').html(brands_html);
            if(brand_id != '' && brand_id != undefined){
                $('.brand').val(brand_id).attr('data-brand_id','');
            }
        }
    });
});

$('.client').trigger('change');

$(document).on('keyup','.client_name_container .multiselect-search', function(){
    var limit = 10;
    var offset = 0;
    var selected_client = $('.client_name').attr('data-selected_client');

    $.ajax({
        url: base_url('api/clients/get_clients'),
        data:{
            'limit':limit,
            'offset':offset,
            'sSearch':$(this).val()
        },
        type:"GET",
        dataType:"json",
        success:function(response)
        {
            var client_html = '';
            var container_html = '';
            var existing_clients = [];

            $('.client_name_container .multiselect-container li a label input').each(function(){
                existing_clients.push($(this).val());
            });

            $.each(response.data, function(index,item){
                if(!existing_clients.includes(item.ClientID)){
                    client_html += '<option value="'+item.ClientName+'" data-client_id="'+item.ClientID+'">'+item.ClientName+'</option>';
                    container_html += '<li><a tabinde="0"><label class="radio" title="'+item.ClientName+'"><input type="radio" value="'+item.ClientName+'" data-client_id="'+item.ClientID+'">'+item.ClientName+'</label></a></li>';
                }
            });

            $('.client').append(client_html);
            $('.client_name_container .multiselect-container').append(container_html);
            if(selected_client != ''){
                var previous_selected = $('.client').val();
                $(".client").multiselect('deselect', previous_selected);
                $('.client').multiselect('select', selected_client, true);
                $('.client').attr('data-selected_client','');
            }


        }
    });
});

$('.client_name_container .multiselect-container').on('scroll',function(){
    if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
        var limit = 10;
        var offset = $('.client_name').attr('data-offset');
        offset = (offset != undefined)?parseInt(offset):10;

        $.ajax({
            url: base_url('api/clients/get_clients'),
            data:{
                'iDisplayLength':limit,
                'iDisplayStart':offset
            },
            type:"GET",
            dataType:"json",
            success:function(response)
            {
                var client_html = '';
                var container_html = '';
                var existing_clients = [];

                $('.client_name_container .multiselect-container li a label input').each(function(){
                    existing_clients.push($(this).val());
                });

                console.log(existing_clients);

                $.each(response.data, function(index,item){
                    if(!existing_clients.includes(item.ClientName)){
                        client_html += '<option value="'+item.ClientName+'" data-client_id="'+item.ClientID+'">'+item.ClientName+'</option>';
                        container_html += '<li><a tabinde="0"><label class="radio" title="'+item.ClientName+'"><input type="radio" value="'+item.ClientName+'" data-client_id="'+item.ClientID+'">'+item.ClientName+'</label></a></li>';   
                    }
                });
                $('.client').append(client_html);
                $('.client_name_container .multiselect-container').append(container_html);
                $('.client').attr({
                    'data-offset':offset+10,
                });
            }
        });
    }
});

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else { 
        alert("Geolocation is not supported by this browser.");
    }
}

function showPosition(position) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;

    $('.lat').val(latitude);
    $('.long').val(longitude);
}

function showError(error) {
    var error_msg = '';
    switch(error.code) {
        case error.PERMISSION_DENIED:
// error_msg = "User denied the request for Geolocation."
$('body').html('Cannot proceed further without location access!, Reload the page and allow location.');
break;
case error.POSITION_UNAVAILABLE:
error_msg = "Location information is unavailable."
break;
case error.TIMEOUT:
error_msg = "The request to get user location timed out."
break;
case error.UNKNOWN_ERROR:
error_msg = "An unknown error occurred."
break;
}

if(error_msg != ''){
    alert(error_msg);
}
}

$(document).ready(function(){
    if($('.notififcation-read-date').length > 0){
        $('.notififcation-read-date').each(function(){
            if($(this).html() == 'null'){
                $(this).parent().closest('tr').css('font-weight','bold')
            }
        });
    }
});

$(document).on('click', 'a' ,function(e){
    var href = $(this).attr('href');
    var loader = $(this).attr('data-loader');
    var target = $(this).attr('target');
    var download = $(this).attr('download');

    if(href != undefined && href.toLowerCase() != 'javascript:void(0)' && href != '#' && loader != 'false' && !e.ctrlKey && target != '_blank' && download == undefined && !$(this).hasClass('cke_button') && !$(this).hasClass('cke_path_item')){
        $('.overlay-wrapper').removeClass('hide');
        $('.loader').addClass('breathing-animation');
    }
});

$(document).on('click','.save-reminder', function(){
    var here = $(this);
    var reminder_date = $('#createReminderModal #ReminderDate').val();
    var task = $('#createReminderModal #Task').val();

    $.ajax({
        url: base_url('api/registered_users/save_reminder'),
        data:{
            'ReminderDate':reminder_date,
            'Task':task
        },
        type:"POST",
        dataType:"json",
        beforeSend: function(){
            here.html('Saving Reminder...').removeClass('btn-success save-reminder').addClass('btn-default disabled');
        },
        statusCode:{
            403: function(response){
                swal(response.responseJSON.msg,'','error');
            },
            501: function(response){
                var validation_err = response.responseJSON.error;
                console.log(validation_err);
                $.each(validation_err, function(index, item){
                    $('.'+index+'-error').html(item);
                });
            }
        },
        success:function(response){
            var date_format = moment(reminder_date).format('DD MMM YYYY HH:mm');
            var reminder_html = '<li class="list-group-item"><a href="javascript:void(0)" data-reminder_id="'+response.data.ReminderID+'" class="view-reminder"><b>'+task+'</b></a><br><i class="fa fa-clock-o"></i> <strong class="text-danger">'+date_format+'</strong></li>';

            $('.reminder-container .list-group').append(reminder_html);
            $("#createReminderModal").modal("hide");

            swal('Task Created successfully!','','success');

            sessionStorage.removeItem("reminders");
            fetchReminders();

        },
        complete: function(response){
            here.html('Set Reminder').removeClass('btn-default disabled').addClass('btn-success save-reminder');
        }
    });


});

function fetchReminders(){
    $.ajax({
        url: base_url('api/registered_users/get_reminders'),
        type:"GET",
        dataType:"json",
        success:function(response){

            if(response.data.length > 0){
                setReminder(response.data[0]);
            
                sessionStorage.setItem("reminders", JSON.stringify(response.data));
                
                var reminder_html = '';
                $.each(response.data, function(index, item){
                    var date_format = moment(item.ReminderDate).format('DD MMM YYYY HH:mm');
                    reminder_html += '<li class="list-group-item"><a href="javascript:void(0)" data-reminder_id="'+item.ReminderID+'" class="view-reminder"><b>'+item.Task+'</b></a><br><i class="fa fa-clock-o"></i> <strong class="text-danger">'+date_format+'</strong></li>';

                });

                $('.reminder-container').removeClass('hide');
                $('.reminder-container .list-group').html(reminder_html);
            }
        }
    });
}

function loadReminders(){
    sessionStorage.removeItem("reminders");
    if (sessionStorage.getItem('reminders') == null) {
        fetchReminders();
    }else{
        var reminder = JSON.parse(sessionStorage.getItem('reminders'));
        if($('.reminder-container').length > 0){
            var reminder_html = '';
            var reminder_arr = [];
            $.each(reminder, function(index, item){

                var current_date = moment(moment().format('YYYY-MM-DD HH:mm:ss'));
                var reminder_date = moment(item.ReminderDate);

                if(reminder_date > current_date){

                    reminder_arr.push({
                        'ReminderID':item.ReminderID,
                        'Task':item.Task,
                        'ReminderDate':item.ReminderDate
                    });

                    var date_format = moment(item.ReminderDate).format('DD MMM YYYY HH:mm');
                        reminder_html += '<li class="list-group-item reminder-'+item.ReminderID+'"><a href="javascript:void(0)" data-reminder_id="'+item.ReminderID+'" class="view-reminder"><b>'+item.Task+'</b></a><br><i class="fa fa-clock-o"></i> <strong class="text-danger">'+date_format+'</strong></li>';
                }
            });

            sessionStorage.removeItem("reminders");
            if(reminder_arr.length > 0){
                sessionStorage.setItem("reminders", JSON.stringify(reminder_arr));
                setReminder(reminder_arr[0]);
                $('.reminder-container').removeClass('hide');
                $('.reminder-container .list-group').html(reminder_html);
            }else{
                $('.reminder-container').addClass('hide');
                $('.reminder-container .list-group').empty();
            } 
        }else{
            $('.reminder-container').addClass('hide');
            $('.reminder-container .list-group').empty();
        }
    }
}

function setReminder(reminder) {
    var current_date = moment(moment().format('YYYY-MM-DD HH:mm:ss'));
    var reminder_date = moment(reminder.ReminderDate);
    var reminder_diff = reminder_date.diff(current_date,'milliseconds');

    setTimeout(function () {

        var audplay = new Audio(base_url('assets/sounds/whistle.mp3'))
            audplay.play();

        swal({
            title: "Reminder Alert",
            text: reminder.Task,
            dangerMode: true,
        });

        $('.reminder-'+reminder.ReminderID).remove();
        loadReminders();
    }, reminder_diff );
}

loadReminders();

function fetchClientsWithScroll(){
    $('.clients-container .multiselect-container').on('scroll', function () {
        if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
        
            
            var client_id = $('#ClientID').attr('data-client_name');
                client_id = (client_id != undefined && client_id != '')?client_id:0;
            var input_type = ($('#ClientID').attr('multiple') != undefined)?'checkbox':'radio';
            
            var limit = 30;
            var offset = $('#allClientsOffset').val();

            offset = (offset != undefined) ? parseInt(offset) : 30;

            var here = $(this);
            $.ajax({
                url: base_url('api/clients/get_all_clients'),
                data: {
                    'offset': offset,
                    'client_id': client_id
                },
                type: "GET",
                dataType: "json",
                success: function (response) {
                    var clients_html = '';
                    var container_html = '';
                    var existing_clients = [];

                    here.find('li>a>label>input').each(function () {
                        existing_clients.push($(this).val());
                    });

                    $.each(response.data, function (index, item) {
                        if (!existing_clients.includes(item.ClientID)) {
                            clients_html += '<option value="'+item.ClientName+'">'+item.ClientName+'</option>';
                            container_html += '<li><a tabinde="0"><label class="'+input_type+'" title="'+item.ClientName+'"><input type="'+input_type+'" value="'+item.ClientName+'">'+item.ClientName+'</label></a></li>';   
                        }
                    });

                    var updated_offset = offset;
                    if(response.data.length > 0){
                        updated_offset = offset + 30;
                    }

                    here.parent().parent().find('#ClientID').append(clients_html);
                    here.append(container_html);
                    $('#allClientsOffset').val(offset);
                    
                }
            });
        }
    });
}

fetchClientsWithScroll();

$(document).on('keyup', '.clients-container .multiselect-search', function () {
    var offset = 0;

    var here = $(this);

    var client_id = $("#ClientID").attr("data-client_id");
    var input_type = ($('#ClientID').attr('multiple') != undefined)?'checkbox':'radio';
    $.ajax({
        url: base_url('api/clients/get_all_clients'),
        data: {
            'offset': offset,
            'client_id': client_id,
            'search_txt': $(this).val()
        },
        type: "GET",
        dataType: "json",

        success: function (response) {
            var clients_html = '';
            var container_html = '';


            var existing_clients = [];


            here.parent().parent().parent().find('li>a>label>input').each(function () {
                existing_clients.push($(this).val());
            });

            $.each(response.data, function (index, item) {
                if (!existing_clients.includes(item.ClientID)) {
                    clients_html += '<option value="' + item.ClientID + '" data-activity_name="' + item.ClientName + '" >' + item.ClientName + '</option>';
                    container_html += '<li><a tabinde="0"><label class="'+input_type+'" title="' + item.ClientName + '"><input type="'+input_type+'" value="' + item.ClientID + '" data-activity_name="' + item.ClientName + '" >' + item.ClientName + '</label></a></li>';
                }
            });

            here.parent().parent().parent().parent().parent().find('#ClientID').append(clients_html);
            here.parent().parent().parent().append(container_html);

        }
    });
});

$(document).on('click','.save-role', function(){
    var form_data = $('#saveRoleModal .form-control').serializeArray();
    var here = $(this);
    $('#saveRoleModal .modal-body .text-danger').empty();

    $.ajax({
        url: base_url('api/management/save_role'),
        data: form_data,
        type: "POST",
        dataType: "json",
        beforeSend: function(){
            here.html('Saving...').removeClass('btn-success save-role').addClass('btn-default');
        },
        error: function(response){
            var err = response.responseJSON.err;

            if(err != undefined && err != ''){
                $.each(err, function(index, item){
                    var error_html = '<span class="text-danger">'+item+'</span>';
                    $('.'+index+'-error').after(error_html);
                });

            }else{
                swal(response.responseJSON.msg,'', "error");
            }

            here.html('Save').removeClass('btn-default').addClass('save-role btn-success');
        },
        success: function (response) {
            var role = $('#saveRoleModal #Role').val();
            var role_id = response.data.RoleID;
            
            var role_html = '<option value="'+role_id+'">'+role+'</option>';

            $('#RoleID').append(role_html);

            var select_type = ($('#RoleID').attr('multiple') != undefined)?'checkbox':'radio';

            var multiselect_role_html = '<li><a tabinde="0"><label class="'+select_type+'" title="'+role+'"><input type="'+select_type+'" value="'+role_id+'">'+role+'</label></a></li>';

            $('#RoleID').parent().find('.multiselect-container').append(multiselect_role_html);

            $("#saveRoleModal").modal("hide");
            $('#saveRoleModal .modal-body .panel-body .row:gt(0)').remove();
            $('#saveRoleModal .form-control').val('');
            here.html('Save').removeClass('btn-default').addClass('save-role btn-success');
            
            swal(response.msg,'', "success");
        }
    });
});

$(document).on('click','.save-industry', function(){
    var form_data = $('#saveIndustryModal .form-control').serializeArray();
    var here = $(this);

    $('#saveIndustryModal .modal-body .text-danger').empty();

    $.ajax({
        url: base_url('api/management/save_industry'),
        data: form_data,
        type: "POST",
        dataType: "json",
        beforeSend: function(){
            here.html('Saving...').removeClass('btn-success save-industry').addClass('btn-default');
        },
        error: function(response){
            var err = response.responseJSON.err;

            if(err != undefined && err != ''){
                $.each(err, function(index, item){
                    var error_html = '<span class="text-danger">'+item+'</span>';
                    $('.'+index+'-error').after(error_html);
                });

            }else{
                swal(response.responseJSON.msg,'', "error");
            }

            here.html('Save').removeClass('btn-default').addClass('save-industry btn-success');
        },
        success: function (response) {
            var industry = $('#saveIndustryModal #BusinessIndustry').val();
            var business_industry_id = response.data.BusinessIndustryID;
            
            var industry_html = '<option value="'+business_industry_id+'">'+industry+'</option>';

            $('#BusinessIndustryID').append(industry_html);

            var select_type = ($('#BusinessIndustryID').attr('multiple') != undefined)?'checkbox':'radio';

            var multiselect_industry_html = '<li><a tabinde="0"><label class="'+select_type+'" title="'+industry+'"><input type="'+select_type+'" value="'+business_industry_id+'">'+industry+'</label></a></li>';

            $('#BusinessIndustryID').parent().find('.multiselect-container').append(multiselect_industry_html);

            $("#saveIndustryModal").modal("hide");
            $('#saveIndustryModal .modal-body .panel-body .row:gt(0)').remove();
            $('#saveIndustryModal .form-control').val('');
            here.html('Save').removeClass('btn-default').addClass('save-industry btn-success');
            swal(response.msg,'', "success");
        }
    });
});

$(document).on('click','.save-service', function(){
    var form_data = $('#saveServiceModal .form-control').serializeArray();
    var here = $(this);

    $('#saveServiceModal .modal-body .text-danger').empty();

    $.ajax({
        url: base_url('api/management/save_service'),
        data: form_data,
        type: "POST",
        dataType: "json",
        beforeSend: function(){
            here.html('Saving...').removeClass('btn-success save-service').addClass('btn-default');
        },
        error: function(response){
            var err = response.responseJSON.err;

            if(err != undefined && err != ''){
                $.each(err, function(index, item){
                    var error_html = '<span class="text-danger">'+item+'</span>';
                    $('.'+index+'-error').after(error_html);
                });

            }else{
                swal(response.responseJSON.msg,'', "error");
            }

            here.html('Save').removeClass('btn-default').addClass('save-service btn-success');
        },
        success: function (response) {
            var service = $('#saveServiceModal #ServiceType').val();
            var service_id = response.data.ServiceID;
            
            var service_html = '<option value="'+service_id+'">'+service+'</option>';

            $('#ServiceID').append(service_html);

            var select_type = ($('#ServiceID').attr('multiple') != undefined)?'checkbox':'radio';

            var multiselect_service_html = '<li><a tabinde="0"><label class="'+select_type+'" title="'+service+'"><input type="'+select_type+'" value="'+service_id+'">'+service+'</label></a></li>';

            $('#ServiceID').parent().find('.multiselect-container').append(multiselect_service_html);

            $("#saveServiceModal").modal("hide");
            $('#saveServiceModal .modal-body .panel-body .row:gt(0)').remove();
            $('#saveServiceModal .form-control').val('');
            here.html('Save').removeClass('btn-default').addClass('save-service btn-success');
            swal(response.msg,'', "success");
        }
    });
});

$.validator.addMethod('filesize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param * 1000000)
}, 'File size must be less than or equal to {0} MB');

$.validator.addMethod('decimal', function(value, element) {
  return this.optional(element) || /^((\d+(\\.\d{0,2})?)|((\d*(\.\d{1,2}))))$/.test(value);
}, "Please enter a correct number, format 0.00");

function fetchVendorsWithScroll(){
    $('.vendors-container .multiselect-container').on('scroll', function () {
        if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
        
            
            var vendor_id = $('#VendorID').attr('data-vendor_id');
                vendor_id = (vendor_id != undefined && vendor_id != '')?vendor_id:0;
            var input_type = ($('#VendorID').attr('multiple') != undefined)?'checkbox':'radio';
            
            var limit = 30;
            var offset = $('#allVendorsOffset').val();

            offset = (offset != undefined) ? parseInt(offset) : 5;

            var here = $(this);
            $.ajax({
                url: base_url('api/vendors/get_all_vendors'),
                data: {
                    'offset': offset,
                    'vendor_id': vendor_id
                },
                type: "GET",
                dataType: "json",
                success: function (response) {
                    var vendors_html = '';
                    var container_html = '';
                    var existing_vendors = [];

                    here.find('li>a>label>input').each(function () {
                        existing_vendors.push($(this).val());
                    });

                    $.each(response.data, function (index, item) {
                        if (!existing_vendors.includes(item.VendorID)) {
                            vendors_html += '<option value="'+item.VendorID+'">'+item.VendorName+'</option>';
                            container_html += '<li><a tabinde="0"><label class="'+input_type+'" title="'+item.VendorName+'"><input type="'+input_type+'" value="'+item.VendorID+'">'+item.VendorName+'</label></a></li>';   
                        }
                    });

                    var updated_offset = offset;
                    if(response.data.length > 0){
                        updated_offset = offset + 30;
                    }

                    here.parent().parent().find('#VendorID').append(vendors_html);
                    here.append(container_html);
                    $('#allVendorsOffset').val(updated_offset);
                    
                }
            });
        }
    });
}

fetchVendorsWithScroll();

$(document).on('keyup', '.vendors-container .multiselect-search', function () {
    var offset = 0;

    var here = $(this);

    var vendor_id = $("#VendorID").attr("data-vendor_id");
    var input_type = ($('#VendorID').attr('multiple') != undefined)?'checkbox':'radio';
    $.ajax({
        url: base_url('api/vendors/get_all_vendors'),
        data: {
            'offset': offset,
            'vendor_id': vendor_id,
            'search_txt': $(this).val()
        },
        type: "GET",
        dataType: "json",

        success: function (response) {
            var vendors_html = '';
            var container_html = '';


            var existing_vendors = [];


            here.parent().parent().parent().find('li>a>label>input').each(function () {
                existing_vendors.push($(this).val());
            });

            $.each(response.data, function (index, item) {
                if (!existing_vendors.includes(item.VendorID)) {
                    vendors_html += '<option value="' + item.VendorID + '" data-activity_name="' + item.VendorName + '" >' + item.VendorName + '</option>';
                    container_html += '<li><a tabinde="0"><label class="'+input_type+'" title="' + item.VendorName + '"><input type="'+input_type+'" value="' + item.VendorID + '" data-activity_name="' + item.VendorName + '" >' + item.VendorName + '</label></a></li>';
                }
            });

            here.parent().parent().parent().parent().parent().find('#VendorID').append(vendors_html);
            here.parent().parent().parent().append(container_html);

        }
    });
});

$('form input').keydown(function (e) {
    if (e.keyCode == 13) {
        e.preventDefault();
        return false;
    }
});

function handleBetween(number, calc) {
    let [a, b] = calc;
    let min = Math.min(a, b), max = Math.max(a, b);
    return number >= min && number <= max;
}

let barcode = "";
let reading = false;

document.addEventListener("keydown", e => {
    
    if (e.key == 'Enter') {
        var ev = $.Event("keypress");
        ev.which = 13;
        ev.keyCode = 13;

        if (!$("input").is(":focus")) {
          // $('#BarcodeNo,#po-BarcodeNo').focus();
          $('.barcode-input').focus();
        }

        // $('#BarcodeNo,#po-BarcodeNo').trigger(ev);
        $('.barcode-input').trigger(ev);
    }
    else {
        if (e.key != 'Shift') {
            barcode += e.key;
        }
    }
    
    if (!reading) {
        reading = true;
        setTimeout( () => {
            barcode = "";
            reading = false;
        }, 200);
    }
}, true);

function roundDown(number, decimals) {
    decimals = decimals || 0;
    return ( Math.floor( number * Math.pow(10, decimals) ) / Math.pow(10, decimals) );
}

if($('#reader').length > 0){
    const html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", { fps: 10, qrbox: 250 });

    function onScanSuccess(decodedText, decodedResult) {
        // Handle on success condition with the decoded text or result.
        console.log(`Scan result: ${decodedText}`, decodedResult);
        swal('Item Scanned','','success');
        stopScanning();

        $('.barcode-input').val(decodedText);
        var ev = $.Event("keypress");
        ev.which = 13;
        ev.keyCode = 13;
        $('.barcode-input').trigger(ev);
    }

    function stopScanning(){
        html5QrcodeScanner.clear();
    }

    function onScanError(errorMessage) {
        // handle on error condition, with error message
    }
}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}