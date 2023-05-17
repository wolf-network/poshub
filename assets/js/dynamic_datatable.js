String.prototype.replaceArray = function(find, replace) {
  var replaceString = this;
  var regex; 
  for (var i = 0; i < find.length; i++) {
    regex = new RegExp(find[i], "g");
    replaceString = replaceString.replace(regex, replace[i]);
  }
  return replaceString;
};

 var filter_data = $('#form-filter').serializeArray();

 $(document).on('click', '#form-filter-btn', function(){
    filter_data = $('#form-filter').serializeArray();
    $('.commonDataTable').DataTable().ajax.reload();
 });

 $(document).on('change', '#form-filter-btn', function(){
    filter_data = $('#form-filter').serializeArray();
    $('.commonDataTable').DataTable().ajax.reload();
 });

$('.commonDataTable').each(function(){
   var dataTable_url = $(this).attr('data-url');
   var responsive = $(this).attr('data-responsive');
   var obj = [
		{ mData: null,
		  "bSortable":false,
          render: function(data, type, full, meta){
          	return meta.row + meta.settings._iDisplayStart + 1;
          }
        }
	];
   $(this).find('th').each(function(){
		var sortable = ($(this).attr('data-sortable') == 'false')?false:true;
		if ($(this).attr('id') != false && $(this).attr('id') != undefined && ($(this).attr('data-manipulate_json') == undefined || $(this).attr('data-manipulate_json') == '')) {
			obj.push({mData:$(this).attr('id'),"bSortable":sortable});
		}

		if ($(this).attr('data-render') == "true") {
			var render_html = $(this).attr('data-render_html');
			var replacer = render_html.match(/[^{\}]+(?=})/g);
			var element = render_html.match(/{([^}]+)}/g);
			obj.push({mData:null,
						"bSortable":sortable,
						render: function(data, type, full, meta){
						// console.log(data);
							if (replacer != null) {
								var replacer_value = Object.keys(data).map(function (key, value) { 
									return data[replacer[value]]; 
								});
								replacer_value = replacer_value.filter(function( element ) {
								   return element !== undefined;
								});
								return render_html.replaceArray(element,replacer_value).replace(/[{}]/g, "");
							} else{
								return render_html;
							}							
						}
					});
		}

		if ($(this).attr('data-condition_render') != undefined) {
			var condition_render = $(this).attr('data-condition_render');
			var condition_render_json = $.parseJSON(condition_render);
			var str = '';
			var conditional_cases = Array();

			var condition_key = Object.keys(condition_render_json).map(function (key, value) {
								return key; 
							});

			obj.push({mData:null,
					  "bSortable":sortable,
					  render: function(data, type, full, meta){
                        var condition_html = '';
					  	for (var i = 0; i < condition_key.length; i++) {
							conditional_cases = Object.keys(condition_render_json[condition_key[i]]).map(function (key, value) {
                                            key = (key == 'null')?null:key;
											return key; 
										});
                            
                            if (conditional_cases.includes(data[condition_key[i]]) == true) {
                                condition_html += condition_render_json[condition_key[i]][data[condition_key[i]]]['html'];
                            } else{
                                condition_html += condition_render_json[condition_key[i]]['default']['html'];
                            }
						}
                        
                        var replacer = condition_html.match(/[^{\}]+(?=})/g);
                        var element = condition_html.match(/{([^}]+)}/g);

                        if (replacer != null) {
                            var replacer_value = Object.keys(data).map(function (key, value) { 
                                return data[replacer[value]]; 
                            });
                            replacer_value = replacer_value.filter(function( element ) {
                               return element !== undefined;
                            });

                            return condition_html.replaceArray(element,replacer_value).replace(/[{}]/g, "");
                        } else{
                            return condition_html;
                        }
							
					  }
					});
			

		}
	});
    
    if (obj.length > 1) {
		var table = $(this).DataTable({
        mark: true,
        "responsive":(responsive != undefined && responsive != '' && responsive != 'false')?{
	        details: {
	            type: 'column',
	            target: 'tr'
	        }
	    }:false,
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": dataTable_url,
        "sServerMethod": "GET",
        "aoColumns": obj,
        "aaSorting": [[ 0, "desc" ]],
        "fnServerParams": function ( aoData ) {
          if (filter_data.length > 0) {
            for (var i = 0; i < filter_data.length; i++) {
              aoData.push(filter_data[i]);
            }
          }
        }
    });
	}
});


$(document).on('click','#form-reset-btn', function(){
	$('#form-filter')[0].reset();
	$('#form-filter-btn').trigger('click');
});