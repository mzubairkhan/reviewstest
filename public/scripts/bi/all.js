$(function(){

  if(BI.Config.name == '') {
        BI.Admin.User.get(BI.Config.user_id);
    }

    $(document).on("click", ".btn-ExpiredClients", function () {
        var inputDate = $('#txt-ExpiryDate').val();
        if(inputDate == '') {
            return;
        }
        BI.Retention.listExpiredClients(inputDate);
    });
});
/*$.ajaxSetup({
    beforeSend: function(xhr) {
        xhr.setRequestHeader('X-custom-header', 'tariq');

    }
});*/

var BI = BI || {};

BI.Admin = BI.Admin || {};

BI.Common = BI.Common || {};
BI.Coupon = BI.Coupon || {};

BI.Config = BI.Config || {};
BI.Helper = BI.Helper || {};

BI.Admin.Pages = BI.Admin.Pages || {};
BI.Admin.User = BI.Admin.User || {};
BI.Admin.Providers = BI.Admin.Providers || {};
BI.Admin.CustomField = BI.Admin.CustomField || {};
BI.Admin.Websites = BI.Admin.Websites || {};
BI.Admin.Git = BI.Admin.Git || {};
BI.Admin.Media = BI.Admin.Media || {};

// how many records to show in data table
BI.Common.display_length = 10;

// unblock when ajax activity stops
$(document).ajaxStop($.unblockUI);

$.ajaxPrefilter(function( options ) {

    //1. adding token
    var url = options.url;
    if(url.indexOf('=') > -1) {
        url += '&token=' + BI.Config.token;

    } else {
        url += '?token=' + BI.Config.token;
    }
    options.url = url;

    //2. adding user_id
    var url = options.url;
    if(url.indexOf('=') > -1) {
        url += '&u_id=' + BI.Config.user_id;
    } else {
        url += '?u_id=' + BI.Config.user_id;
    }
    options.url = url;

    //3. adding mode
    if(BI.Config.mode == 'production') {
        var url = options.url;
        if(url.indexOf('=') > -1) {
            url += '&m=1';
        } else {
            url += '?m=1';
        }
        options.url = url;
    }

});

BI.Common.PrepareDataTableFromDataSource = function(dataSet , options) {

    var tmpl = $('#tmp_SimpleTable');
    var header_columns = '';
    var rows = '';
    $.each(dataSet[0], function(k,v) {
        //if(k == 'id') { return;}
        header_columns += '<th>' + k +  '</th>';
    });
    $.each(dataSet, function(k,v) {
       // console.log(v);
        if(v.hasOwnProperty('id')){
            rows += '<tr id="' + v['id'] + '">';
        } else {
            rows += '<tr>';
        }
        $.each(v, function(k1,v1) {

            //if(k1 == 'id') { return;}
            if(k1 == 'options') {
                rows += '<td>';
                $.each(v1 , function(k2, v2){

                    if(v2['js']) {
                        if(v2['extraAttrs']) {
                            rows += '<a class="' + v2['class']+ '" ' + v2['extraAttrs'] + ' >' + v2['label'] + '</a>';
                        } else {
                            rows += '<a  href="javascript:void(0);" class="' + v2['class']+ '">' + v2['label'] + '</a>';
                        }
                    }else if(v2['modal']){
                        rows += '<a data-id="'+v.id+'" data-modaltype="'+v2['modal_type']+'" href="javascript:void(0)"' + v2+ ' class="modal_trigger ' + v2['class']+ '">' + v2['label'] + '</a>';
                    } else {
                        rows += '<a href="' + BI.Config.base_path + v2['label_link'] + '">' + v2['label'] + '</a>';
                    }



                    if(k2 != v1.length-1)
                        rows += ' | ';
                });
                rows +='</td>';

            } else {
                rows += '<td>' + v1 +  '</td>';
            }

        });
        rows + '</tr>';
    });
    var html = $('#tmp_SimpleTable').html();
    html = html.replace('((header_columns))', header_columns);
    html = html.replace('((rows))', rows);
    return html;
};

BI.Common.PrepareDataTableFromDataSourceForMultiple = function(dataSet , selector) {
    var tmpl = $('#tmp_' + selector);
    var header_columns = '';
    var rows = '';
    $.each(dataSet[0], function(k,v) {
        header_columns += '<th>' + k +  '</th>';
    });
    $.each(dataSet, function(k,v) {

        if(v.hasOwnProperty('id')){
            rows += '<tr id="' + v['id'] + '">';
        } else {
            rows += '<tr>';
        }
        $.each(v, function(k1,v1) {

            if(k1 == 'options') {
                rows += '<td>';
                $.each(v1 , function(k2, v2){

                    if(v2['js']) {
                        if(v2['extraAttrs']) {
                            rows += '<a class="' + v2['class']+ '" ' + v2['extraAttrs'] + ' >' + v2['label'] + '</a>';
                        } else {
                            rows += '<a href="javascript:void(0);" class="' + v2['class']+ '">' + v2['label'] + '</a>';
                        }
                    } else {
                        rows += '<a href="' + BI.Config.base_path + v2['label_link'] + '">' + v2['label'] + '</a>';
                    }
                    if(k2 != v1.length-1)
                        rows += ' | ';
                });
                rows +='</td>';

            } else {
                rows += '<td>' + v1 +  '</td>';
            }

        });
        rows + '</tr>';
    });
    var html = $('#tmp_' + selector).html();
    html = html.replace('((header_columns))', header_columns);
    html = html.replace('((rows))', rows);
    return html;
};


BI.Common.PrepareDataTableFromDataSourceForNiShortSessionGeneral = function(dataSet , options) {

    var html = $('#nishortsession_general').html();
    var rows = '';
	var pairs_types = ['5_mins_data','10_mins_data','15_mins_data'];


    $.each(dataSet['unique_dates'], function(k,date) {

        rows += '<tr>';
        rows += '<td>'+ date +'</td>';
        //console.log(dataSet['data']);

        $.each(pairs_types, function(k,pair_type) {

            var p1 = 0;
            var p2 = 0;
            if(typeof dataSet['data'] !== "undefined" && dataSet['data'] !== null){
                var p1 = dataSet['data'][date][pair_type];
                var p2 = dataSet['data_distinct_final'][date][pair_type];
            }
            rows += '<td>'+ p1 +' ('+ p2 +') </td>';


        });


        rows += '</tr>';
    });

    rows = rows.replace(/undefined/g, '0');
    rows = rows.replace(/null/g, '0');
    html = html.replace('((rows))', rows);
    return html;
};

BI.Common.PrepareDataTableFromDataSourceForNiShortSessionPairWise = function(dataSet , options) {

    var final_html = '';
    var iteration = 0;

    //console.log(options.iso2country);
    var iso2country = options.iso2country;
    var pairs_types = ['5_mins_data','10_mins_data','15_mins_data'];
    BI.Network.NiShortSession.global_pairs_total = {};

    $.each(dataSet['unique_pairs'], function(k,pair)
    {
        iteration++;
        var pair_total = 0;
        var html = $('#nishortsession_pairwise').html();

        var rows = '';


        $.each(dataSet['unique_dates'], function(k,date) {

            rows += '<tr>';
            rows += '<td>'+ date +'</td>';

            $.each(pairs_types, function(k,pair_type) {

                if(dataSet['data_pairs'][date][pair_type]){
                    var p1 =  dataSet['data_pairs'][date][pair_type][pair];
                }
                if(dataSet['data_pairs_distinct_final'][date][pair_type]){
                    var p2 =  dataSet['data_pairs_distinct_final'][date][pair_type][pair];
                }


                //console.log(dataSet['data_pairs_distinct_servers'][date][pair_type][pair]);
                var tmp = '';
                tmp += '<span class="sb">';
                if(dataSet['data_pairs_distinct_final'][date][pair_type]){
                    if(dataSet['data_pairs_distinct_final'][date][pair_type][pair]){
                        $.each(dataSet['data_pairs_distinct_servers'][date][pair_type][pair], function(server,users) {
                            var tu = $.map(users, function(n, i) { return i; }).length;

                            tmp += '<h4 class="hx">'+  server  +' ('+ tu +') </h4>';
                            tmp += '<ul class="ux" style="display:none;">';
                            $.each(users, function(uk,uv) {
                                tmp += '<li>'+  uk  +'</li>';
                            });
                            tmp += '</ul>';


                        });
                    }
                }
                tmp += '</span>';
                if(p2 > 0){
                    rows += '<td class="cx">'+ p1 +' <a href="#" class="ctooltip">('+ p2 +')'+  tmp +' </a></td>';
                }else{
                    rows += '<td>'+ p1 +' ('+ p2 +')</td>';
                }

                // calculate total of each pair, non unique
                if(isNaN(p1)) {
                    p1 = 0;
                }
                pair_total +=  p1;
                BI.Network.NiShortSession.global_pairs_total[pair] = pair_total;
            });

            rows += '</tr>';

        });

        // show title only once
        if(iteration == '1'){
            //html = html.replace('{{title}}', options['title']);
        }else{
            //html = html.replace('{{title}}', '');
        }
        html = html.replace('{{title}}', '');
        rows = rows.replace(/undefined/g, '0');
        rows = rows.replace(/null/g, '0');
        //html = html.replace('{{style}}', options['style']);
        var pair_orig = pair;
        html = html.replace(/{{pair-orig}}/g, pair_orig);
        pair = pair.replace('-', ' To ');
        //html = html.replace('{{pair}}', pair);
        html = html.replace(/{{pair}}/g, pair);
        html = html.replace('{{btn-display}}', 'hide');
        //html = html.replace('{{tbl-display}}', 'show');

        // Format Iso2Country
        var arr = pair_orig.split('-');
        var source = arr[0];
        var destination = arr[1];
        source = iso2country[source];
        destination = iso2country[destination];
        var text = source+' To '+destination;
        html = html.replace('{{caption}}', text + '  (' + pair_total + ')');

        html = html.replace('((rows))', rows);
        final_html += html;

    });


    return final_html;
};

BI.Common.PrepareMultiDataTableFromDataSource = function(dataSet , options) {
    var tmpl = $('.tmp_SimpleTable');
    var header_columns = '';
    var rows = '';
    $.each(dataSet[0], function(k,v) {

	    if(k == 'subscription'){
		k = 'subscription (GB)';
		}
		if(k == 'threshold'){
		k = 'threshold (%)';
		}
		if(k == 'value'){
		k = 'value in Bytes (GB)';
		}

        header_columns += '<th>' + k +  '</th>';
    });

    $.each(dataSet, function(k,v) {

        if(v.hasOwnProperty('id')){
            rows += '<tr id="' + v['id'] + '">';
        } else {
            rows += '<tr>';
        }
        $.each(v, function(k1,v1) {

            if(k1 == 'options') {
                rows += '<td>';
                $.each(v1 , function(k2, v2){

                    if(v2['js']) {
                        rows += '<a href="javascript:void(0);" class="' + v2['class']+ '">' + v2['label'] + '</a>';
                    } else {
                        rows += '<a href="' + BI.Config.base_path + v2['label_link'] + '">' + v2['label'] + '</a>';
                    }
                    if(k2 != v1.length-1)
                        rows += ' | ';
                });
                rows +='</td>';

            } else {
                rows += '<td>' + v1 +  '</td>';
            }

        });
        rows + '</tr>';
    });
    var html = $('.tmp_SimpleTable').html();
	html = html.replace('{{title}}', options.title);
    html = html.replace('((header_columns))', header_columns);
    html = html.replace('((rows))', rows);
    return html;
};

BI.Common.PrepareBlackListedTableFromDataSource = function(dataSet , options) {
    var tmpl = $('.tmp_SimpleTable');
    var header_columns = '';
    var rows = '';
    $.each(dataSet[0], function(k,v) {
        header_columns += '<th>' + k +  '</th>';
    });

	header_columns += '<th> Actions </th>';
    $.each(dataSet, function(k,v) {

        if(v.hasOwnProperty('id')){
            rows += '<tr id="' + v['id'] + '">';
        } else {
            rows += '<tr>';
        }
        $.each(v, function(k1,v1) {

			v1 = v1 == null ? '' : v1;
			if(k1 == 'is_email_sent' || k1 == 'is_exceptional_case' || k1 == 'sfa_disabled'){
			   var checked  = v1 == '1' ? "checked=true" : '';
			   rows += '<td> <input type="checkbox" id="inlineCheckbox1" class="chk" rel="'+v['id']+'-'+k1+'" value="' + v1 +  '" ' + checked +  '>  </td>';
  		       //var val = v1 == '1' ? 'Yes' : 'No';
               //rows += '<td>' + val +  '</td>';
			}

			else if(k1 == 'is_sfa'){
			     var val = v1 == '1' ? 'Yes' : 'No';
                rows += '<td>' + val +  '</td>';
			}

            else {
                rows += '<td>' + v1 +  '</td>';
            }

        });
    	 rows += '<td style="text-align:center;"> <a href="javascript:void(0);" role="button" class="btn btn-xs btn-primary edit-user" rel="' + v['id'] + '"> Edit </a></td>';
        rows + '</tr>';
    });
    var html = $('.tmp_SimpleTable').html();
	html = html.replace('{{title}}', options.title);
    html = html.replace('((header_columns))', header_columns);
    html = html.replace('((rows))', rows);
    return html;
};

BI.Common.PrepareCheckListForDataSource = function(dataSet, dataSetSelected) {

    var html = '';
    $.each(dataSet, function(k1, v1) {
        var row = '';
        var checked = false;
        var html_checked = '';
        if(dataSetSelected != undefined && dataSetSelected.length > 0) {

            $.each(dataSetSelected, function(k2,v2) {
                if(v2.id == v1.id) {
                    checked = true;
                    html_checked = 'checked=""';
                }
            });
        }

        row += '<input type="checkbox" class="chk-Generic" name="chk-Generic" value="' + v1.id+ '" ' + html_checked + '/>' + v1.url +'<br/>';
        html += row;
    });
    return html;
};

BI.Common.AttachDataTable = function (selector) {

    /*
     * Initialize DataTables, with no sorting on the 'details' column
     */

	 if(typeof BI.Common.AttachDataTable.defaultcol != 'undefined'){
	 var defaultcol = BI.Common.AttachDataTable.defaultcol;
	 }else{
 	 var defaultcol = 0;
	 }
	 //BI.Common.AttachDataTable.defaultcol = 0;//BI.Common.AttachDataTable.defaultcol != '' ? BI.Common.AttachDataTable.defaultcol : '0';
    if ( $.fn.DataTable.fnIsDataTable( selector ) ) {
        var oTable = $(selector).dataTable();
        oTable.fnDestroy();
        oTable = undefined;
    }
    var oTable = $(selector).DataTable( {
        /*"aoColumnDefs": [
         {"bSortable": false, "aTargets": [ 0 ] }
         ],*/
        //"aaSorting": [[0, 'desc']],
		"aaSorting": [[defaultcol, 'desc']],
        "aLengthMenu": [
            [10, 15, 20, -1],
            [10, 15, 20, "All"] // change per page values here
        ],
        "aoColumnDefs": [
        {
          "bSortable": false,
          "aTargets": [ -1 ] // <-- gets last column and turns off sorting
         }
         ],

        // set the initial value
        "iDisplayLength": BI.Common.display_length

    });


    jQuery('.dataTables_filter input').addClass("form-control input-small input-inline"); // modify table search input
    jQuery('.dataTables_length select').addClass("form-control input-small"); // modify table per page dropdown
    jQuery('.dataTables_length select').select2(); // initialize select2 dropdown

    return oTable;
};

BI.Common.PrepareDropDownOptions = function(data) {
    var html = '';
    $.each(data, function(k,v) {
        html +='<option value="'+ v.id +'">' + v.title + '</option>';
    });
    return html;
};

BI.Common.clearAlert = function(){
    //$('.message').html('').removeClass(".alert-danger");
    $('.alert').show().css('background-color','#F2DEDE').css('color','#A94442');
	$('.alert').css('display','none');
};

BI.Common.showPageInfo = function(message){
    $('.alert').show().css('background-color','#BDE5F8').css('color','#00529B');
    $('.alert .message').html(message).focus();
};

BI.Common.showPageError = function(message){
    $('.alert-danger').show();
    $('.alert-danger .message').html(message).focus();
};

BI.Common.showPageSuccess = function(message){
    $('.alert-success').show();
    $('.alert-success .message').html(message).focus();
};

BI.Common.showModal = function(arg){

        var output = '';
        var modal_container = ".modal_container";

        $(modal_container).html("");
        output += '<a class="btn default hidden modal_link" data-toggle="modal" href="#'+arg.type+'" ></a>';
        output += '<div class="modal fade" id="'+arg.type+'" tabindex="-1" role="basic" aria-hidden="true">';
        output += '<div class="modal-dialog"> <div class="modal-content"> <div class="modal-header"> ';
        output += '<div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button><h4 class="modal-title">'+arg.title+'</h4></div>';
        if(arg.body) {
            output += '<div class="modal-body"> '+arg.body+' </div>';
            if(arg.button !== 0 ) {
                output += '<div class="modal-footer"> <button type="button" class="btn default" data-dismiss="modal">Close</button><button type="button" class="confirm btn blue">'+arg.button+'</button></div>';
            }
        }else {
            if(arg.button !== 0 ) {
            output += '<div class="modal-footer" style="border: 0"> <button type="button" class="btn default" data-dismiss="modal">Close</button><button type="button" class="confirm btn blue">'+arg.button+'</button></div>';
            }
        }
        output += '</div> </div> </div>';

        $(modal_container).html(output);

        $('#'+arg.type).modal('show');


};

BI.Common.showGritterNotificationForFailure = function(response){
    var msg = '';
    if(response != undefined) {
        if(response.statusMessage != undefined) {
            msg = response.statusMessage;
        } else {
            msg = response.error.message;
        }

        $.gritter.add({
            // (string | mandatory) the heading of the notification
            title: 'There is some error !',
            // (string | mandatory) the text inside the notification
            text: msg
        });
    }
};

BI.Common.checkDateTimeRangeFilter = function() {

    var args ={};

    args.from = $('.from').val();
    args.to = $('.to').val();
    args.set = false;

    if(args.from ==  ""){
        BI.Common.showPageError('Please select a From DateTime');
        args.set = false;
    } else {
        args.set = true;
    }

    if(args.to ==  ""){
        BI.Common.showPageError('Please select a To DateTime');
        args.set = false;
    } else {
        args.set = true;
    }

    var dateDifference	= 86400000 * 31;

   // var oDate1	= new Date(args.from);
   // var oDate2	= new Date(args.to);

	var oDate1 = new Date(args.from.substr(0, 4), args.from.substr(5, 2) - 1, args.from.substr(8, 2), args.from.substr(11, 2), args.from.substr(14, 2), args.from.substr(17, 2));
	var oDate2 = new Date(args.to.substr(0, 4), args.to.substr(5, 2) - 1, args.to.substr(8, 2), args.to.substr(11, 2), args.to.substr(14, 2), args.to.substr(17, 2));

    if(oDate1.getTime() > oDate2.getTime())
    {
        BI.Common.showPageError('ToDate must be greater than FromDate');
        args.set = false;
    }
    if(oDate2.getTime() > (oDate1.getTime()+dateDifference))
    {
        BI.Common.showPageError('Please select date range within 30 days');
        args.set = false;
    }

    return args;
};

BI.Common.generate_pdf_xls = function(args) {

    var html = '';
    html += '<div class="logo-cnt"><img src="http://bi.purevpn.com/assets/img/logo_purevpn.png" alt="logo"></div>';

    if($('#div-protocols-data-processed .dataTable').length > 0) {
        html += '<h3 class="title">Summary</h3>';
        html += '<table class="table table-bordered table-striped table-condensed flip-content tbl-SimpleTable dataTable">';
        html += $('#div-protocols-data-processed .dataTable').clone().html();
        html += "</table>";
    }
    if($('#div-connected-data-processed .dataTable').length > 0) {
        html += '<h3 class="title">Current sessions</h3>';
        html += '<table class="table table-bordered table-striped table-condensed flip-content tbl-SimpleTable dataTable">';
        html += $('#div-connected-data-processed .dataTable').clone().html();
        html += "</table>";
    }
    if($('#div-logs-data-processed .dataTable').length > 0) {
        html += '<h3 class="title">Previous sessions</h3>';
        html += '<table class="table table-bordered table-striped table-condensed flip-content tbl-SimpleTable dataTable">';
        html += $('#div-logs-data-processed .dataTable').clone().html();
        html += "</table>";
    }
    $('.content-pdf').html(html);
    var content = $('.content-pdf').text();

    var url = BI.Config.api_path + '/network/generatePDF';

    $.ajax({
        url: url,
        type: "POST",
        dataType: 'json',
        data: {
            content: content,
            type : args.type
        }
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        var url= BI.Config.api_path + "/files/purevpn-invoice."+args.type;
        window.open(url, '_blank');
    })
    .fail(function( jqXHR, textStatus, errorThrown) {
        var response = jqXHR.responseJSON;
        BI.Common.showGritterNotificationForFailure(response);
    });

};

BI.Common.convertToSlug = function(Text){
    return Text
        .toLowerCase()
        .replace(/ /g,'_')
        .replace(/[^\w-]+/g,'')
        ;
};

BI.Common.collapsible_row = function (id, title, key, input_type, default_value, module_type, row, update_type ) {

    var output = '';
    var fields = '';
    var collapse_type = 'in';

    if(update_type == 1) {
        var collapse_type = 'collapse';
    }

    fields += '<div class="form-group col-md-5"> <label class="control-label col-md-3">Title</label><div class="col-md-9"> <input name="cf_title" type="text" class="form-control cf_title" value="'+title+'">  </div> </div><div class="clearfix"></div>';

    if(input_type == 'select' || input_type == 'checkbox') {
        fields += '<div class="form-group col-md-5"> <label class="control-label col-md-3">Options</label><div class="col-md-9"><input name="cf_options" type="hidden" id="cf_options_'+row+'" class="form-control select2 cf_options" value="'+default_value+'"></div> </div><div class="clearfix"></div>';
    }
    if(input_type == 'boolean') {
        fields += '<input name="cf_options" type="hidden" id="cf_options_'+row+'" class="form-control cf_options" value="No,Yes">';
    }
    output += '<div data-id="' + row + '" class="accor_row panel panel-default" id="cf_' + row + '"> <div class="panel-heading">';
    output += '<h4 class="panel-title"> <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion' + row + '" href="#collapse_' + row + '">' + title + '</a>';
    output += '<a data-remove="'+id+'" href="javascript:void(0)" class="remove_cf btn default btn-xs black pull-right"> <i class="fa fa-trash-o"></i> Delete </a> <span class="label label-sm label-success pull-right" style="  margin-top: 5px;margin-right: 8px;">'+input_type+' </span> </h4></div> ';
    output += '<div id="collapse_' + row + '" class="panel-collapse '+collapse_type+'"> <div class="panel-body">' + fields;
    if(update_type == 1) {
        output += ' <a style="display:none;margin-left: 109px;" data-type="'+input_type+'" data-update="'+id+'" class="update_cf btn green">Update</a> ';
    }else {
        output += ' <a style="display:none;margin-left: 109px;" data-type="'+input_type+'" class="update_cf btn green">Update</a> ';
    }

    output += '</div></div></div>';

    return output;
};

BI.Common.moduleFieldsRows = function (obj1,obj2, relation_id) {

    //console.log(o)
    var fields = '';
    var output = '';

    output += '<form class="cf_form" id="cf_form_'+obj2.id+'" action="javascript:void(0)" method="post">';
    fields += '<input name="relation_id" value="'+relation_id+'" type="hidden">';
    fields += '<div class="col-md-6">';
    $.each(obj2.group_fields, function(k,v) {

        var field_data = v.value !== null ? v.value : '';
        fields += '<div class="form-group">';
        fields += '<input name="custom_fields['+k+'][id]" value="'+v.id+'" type="hidden">';
        fields += '<input name="custom_fields['+k+'][key]" value="'+v.key+'" type="hidden">';
                if(v.input_type == "select") {
                    fields += '<label class="control-label col-md-4">' + v.title + "</label>";
                    if(v.default_value) {
                        var select_v = v.default_value.split(',');
                        fields += '<div class="col-md-8"><select class="form-control" name="custom_fields['+k+'][value]">';
                            $.each(select_v, function(kw,vl) {
                                var selected = vl == field_data ? 'selected' : '';
                                fields += '<option '+selected+'>'+vl+ '</option>';
                            });
                        fields += '</select></div><br /> <br />';
                    }else {
                        fields += '<div class="col-md-5"><input class="form-control" type"text" value="options not defined !" disabled></div>';
                    }
                }

                if(v.input_type == "boolean") {
                    fields += '<label class="control-label col-md-4">' + v.title + "</label>";
                    if(v.default_value) {
                        var select_v = v.default_value.split(',');
                        fields += '<div class="col-md-8"><select class="form-control" name="custom_fields['+k+'][value]">';
                        $.each(select_v, function(kw,vl) {
                            var selected = vl == field_data ? 'selected' : '';
                            fields += '<option '+selected+'>'+vl+ '</option>';
                        });
                        fields += '</select></div><br /> <br />';
                    }else {
                        fields += '<div class="col-md-5"><input class="form-control" type"text" value="options not defined !" disabled></div>';
                    }
                }

                if(v.input_type == "input") {
                    fields += '<label class="control-label col-md-4">' + v.title + "</label>";
                    fields += '<div class="col-md-8"><input name="custom_fields['+k+'][value]" class="form-control" type="text" value="'+field_data+'"></div>';
                }

                if(v.input_type == "tags") {

                    fields += '<label class="control-label col-md-4">' + v.title + "</label>";
                    fields += '<div class="col-md-8"><input name="custom_fields['+k+'][value]" class="form-control" id="'+v.key+'" class="form-control select2" type="text" value="'+field_data+'"></div>';
                }

                if(v.input_type == "textarea") {
                    fields += '<label class="control-label col-md-4">' + v.title + "</label>";
                    fields += '<div class="col-md-8"><textarea name="custom_fields['+k+'][value]" class="form-control">'+field_data+'</textarea></div>';
                }

                if(v.input_type == "checkbox") {

                    fields += '<label class="control-label col-md-4">' + v.title + "</label>";

                    if(v.default_value) {
                        var checkboxes = v.default_value.split(',');
                        fields += '<div class="col-md-5 checkbox-list">';
                        $.each(checkboxes, function(kw,vl) {
                            if(field_data) {
                                var checked = v.value.indexOf(vl) != -1 ? 'checked' : '';
                            }
                            fields += '<label><input name="custom_fields['+k+'][value][]" value="'+vl+'" type="checkbox" '+checked+'>'+vl+ '</label>';

                        });
                        fields += '</div>'
                    }else {
                        fields += '<div class="col-md-5"><input class="form-control" type"text" value="options not defined !" disabled></div>';
                    }
                }

            fields += '</div><div class="clearfix"><br></div>';
        });
    fields += '</div>';

    output += '<div id="panel_'+obj2.id+'" class="panel panel-default"> <div class="panel-heading">';
    output += '<h4 class="panel-title"> <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion'+obj2.id+'" href="#collapse_'+obj2.id+'">'+obj2.title+'</a> </h4> </div>';
    output += '<div id="collapse_'+obj2.id+'" class="panel-collapse collapse" style="height: 0px;">';
    output += '<div class="panel-body"> '+fields;
    output += '<div class="clearfix"></div>';
    output += '<div class="col-md-6"><label class="control-label col-md-4"></label> <div class="form-group"><a style=" display:none; margin: 8px 0px 0px 14px" data-type="checkbox" data-formid="cf_form_'+obj2.id+'" data-update="cf_group_'+obj2.id+'" class="cf_group_update btn green">Update</a></div></div>';
    output += '</div></div> </div>';
    output += '</form>';
    return output;
};

BI.Common.initUniform = function () {

    if (!jQuery().uniform) {
        return;
    }
    var test = $("input[type=checkbox]:not(.toggle, .make-switch), input[type=radio]:not(.toggle, .star, .make-switch)");
    if (test.size() > 0) {
        test.each(function () {
            if ($(this).parents(".checker").size() == 0) {
                $(this).show();
                $(this).uniform();
            }
        });
    }
};

BI.Common.CKupdate = function (){
    for ( instance in CKEDITOR.instances )
        CKEDITOR.instances[instance].updateElement();
};

BI.Helper.isReseller = function() {

    var is_reseller = false;

    if(BI.Config.roles != undefined) {
        $.each(BI.Config.roles, function(k,v) {
            if(v.title == 'Reseller') {
                is_reseller = true;
            }
        });
    }

    return is_reseller;

};


BI.Admin.createRole = function(title) {
    var data_to_send = 'title=' + encodeURIComponent(title);
    var url = BI.Config.api_path + '/acl/createRole';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            var id = data.resultSet.id;
            window.location = 'roles?message=' + 'Role created successfully!' + '&id=' + id;
        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            console.log( "error" );
    });
};

BI.Admin.createModule = function(title) {
    var data_to_send = 'title=' + encodeURIComponent(title);
    var url = BI.Config.api_path + '/acl/createModule';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
})
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            var id = data.resultSet.id;
            window.location = 'modules?message=' + 'Module created successfully!' + '&id=' + id;
        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            console.log( "error" );
    });
};

BI.Admin.createPermission = function(title , isBackend, moduleId, permissionId) {

    var data_to_send = 'title=' + encodeURIComponent(title) + '&is_backend=' + encodeURIComponent(isBackend)  + '&module_id=' + encodeURIComponent(moduleId);

    if(permissionId != 0) {
        data_to_send += '&id=' + encodeURIComponent(permissionId);
    }

    var url = BI.Config.api_path + '/acl/createPermission';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            var id = data.resultSet.id;
            window.location = '/admin/permissions?message=' + 'Permission saved successfully!' + '&id=' + id;
        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            console.log( "error" );
    });
};

BI.Admin.getPermission = function(id) {

    var url = BI.Config.api_path + '/acl/getPermission?permission_id=' + encodeURIComponent(id);
    $.ajax({
        url: url,
        type: "GET"
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            var result = data.resultSet;
            var html = 'No records found';

            var title = result.title;
            var module_id = result.module_id;
            var is_backend = result.is_backend;

            $('#title').val(title);
            if(is_backend == "1") {
                $('#chk-IsBackend').click();
            }
            $('#sel-moduleList').val(module_id);

        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {
        var response = jqXHR.responseJSON;
        BI.Common.showGritterNotificationForFailure(response);
    });
};

BI.Admin.removeRole = function(id) {
    var user_id = id;
    var data_to_send = 'id=' + encodeURIComponent(id);
    var url = BI.Config.api_path + '/acl/removeRole';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            window.location = 'roles?message=' + 'Role deleted successfully!' + '&id=' + user_id;
        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {

            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            console.log( "error" );
    });
};

BI.Admin.removePermission = function(id) {
    var user_id = id;
    var data_to_send = 'id=' + encodeURIComponent(id);
    var url = BI.Config.api_path + '/acl/removePermission';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            window.location = 'permissions?message=' + 'Permission deleted successfully!' + '&id=' + user_id;
        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {

            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            console.log( "error" );

    });
};

BI.Admin.removeModule = function(id) {
    var user_id = id;
    var data_to_send = 'id=' + encodeURIComponent(id);
    var url = BI.Config.api_path + '/acl/removeModule';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            window.location = 'modules?message=' + 'Module deleted successfully!' + '&id=' + user_id;
        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {

            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            console.log( "error" );

    });
};

BI.Admin.listRoles = function() {

    var url = BI.Config.api_path + '/acl/getRoles';
    $.ajax({
        url: url,
        type: "GET"
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            var result = data.resultSet;
            var html = 'No records found';
            $.each(result, function(k, v){

                result[k]['options'] = [[],[],[]];

                result[k]['options'][0]['js'] = false;
                result[k]['options'][0]['label'] = 'Permissions';
                result[k]['options'][0]['label_link'] = '/admin/rolePermissions/' + result[k]['id'];

                result[k]['options'][1]['js'] = true;
                result[k]['options'][1]['class'] = 'editRole';
                result[k]['options'][1]['label'] = 'Edit';

                result[k]['options'][2]['js'] = true;
                result[k]['options'][2]['class'] = 'removeRole';
                result[k]['options'][2]['label'] = 'Delete';


            });
            if(result.length  > 0) {
                html = BI.Common.PrepareDataTableFromDataSource(result);
            }
            $('#div-Roles').html(html);
            BI.Common.AttachDataTable('#tbl-SimpleTable');


        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {
        var response = jqXHR.responseJSON;
        BI.Common.showGritterNotificationForFailure(response);

    });
};

BI.Admin.listPermissions = function() {

    var url = BI.Config.api_path + '/acl/getPermissions';
    $.ajax({
        url: url,
        type: "GET"
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            var result = data.resultSet;
            var html = 'No records found';
            $.each(result, function(k, v){

                result[k]['options'] = [[],[],[]];

                result[k]['options'][0]['js'] = false;
                result[k]['options'][0]['label'] = 'Roles';
                result[k]['options'][0]['label_link'] = '/admin/permissionRoles/' + result[k]['id'];

                result[k]['options'][1]['js'] = false;
                result[k]['options'][1]['class'] = 'editPermission';
                result[k]['options'][1]['label'] = 'Edit';
                result[k]['options'][1]['label_link'] = '/admin/editPermission/' + result[k]['id'];

                result[k]['options'][2]['js'] = true;
                result[k]['options'][2]['class'] = 'removePermission';
                result[k]['options'][2]['label'] = 'Delete';

            });
            if(result.length  > 0) {
                html = BI.Common.PrepareDataTableFromDataSource(result);
            }
            $('#div-Permissions').html(html);
            BI.Common.AttachDataTable('#tbl-SimpleTable');


        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {
        var response = jqXHR.responseJSON;
        BI.Common.showGritterNotificationForFailure(response);
    });
};

BI.Admin.listModules = function() {

    var url = BI.Config.api_path + '/acl/getModules';
    $.ajax({
        url: url,
        type: "GET"
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            var result = data.resultSet;
            var html = 'No records found';
            $.each(result, function(k, v){

                result[k]['options'] = [[],[],[]];

                result[k]['options'][0]['js'] = false;
                result[k]['options'][0]['label'] = 'Permissions';
                result[k]['options'][0]['label_link'] = '/admin/modulePermissions/' + result[k]['id'];

                result[k]['options'][1]['js'] = true;
                result[k]['options'][1]['class'] = 'editModule';
                result[k]['options'][1]['label'] = 'Edit';

                result[k]['options'][2]['js'] = true;
                result[k]['options'][2]['class'] = 'removeModule';
                result[k]['options'][2]['label'] = 'Delete';

            });
            if(result.length  > 0) {
                html = BI.Common.PrepareDataTableFromDataSource(result);
            }
            $('#div-Modules').html(html);
            BI.Common.AttachDataTable('#tbl-SimpleTable');


        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {
        var response = jqXHR.responseJSON;
        BI.Common.showGritterNotificationForFailure(response);

    });
};

BI.Admin.populateModules = function(permission_id) {

    var url = BI.Config.api_path + '/acl/getModules';
    $.ajax({
        url: url,
        type: "GET"
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            var result = data.resultSet;
            var html = 'No records found';

            if(result.length  > 0) {
                html = BI.Common.PrepareDropDownOptions(result);
            }
            $('#sel-moduleList').html(html);

            if(permission_id != 0) {
                BI.Admin.getPermission(permission_id);
            }

        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {
        var response = jqXHR.responseJSON;
        BI.Common.showGritterNotificationForFailure(response);

    });
};

BI.Admin.showUserRoles = function(id) {

    var url = BI.Config.api_path + '/acl/getuserRoles?user_id=' + encodeURIComponent(id);
    $.ajax({
        url: url,
        type: "GET"
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            var result = data.resultSet;
            var html = 'No records found';
            var dataSet = result.totalRoles;
            var dataSetSelected = result.userRoles;

            $.each(dataSet, function(k, v){
                dataSet[k]['url'] = '<a href="' + BI.Config.base_path + '/admin/rolePermissions/' + v['id'] + '">' + v['title'] + '</a>';
            });


            html = BI.Common.PrepareCheckListForDataSource(dataSet, dataSetSelected);

            var user = result.user;
            $('.dynamicData').html('- \'<b>' + user.first_name  + '</b>\'');

            $('#div-userRolesList').html(html);


        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {
        var response = jqXHR.responseJSON;
        BI.Common.showGritterNotificationForFailure(response);

    });
};

BI.Admin.showRolePermissions = function(id) {

    var url = BI.Config.api_path + '/acl/getRolePermissions?role_id=' + encodeURIComponent(id);
    $.ajax({
        url: url,
        type: "GET"
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            var result = data.resultSet;
            var html = 'No records found';
            var dataSet = result.totalPermissions;
            var dataSetSelected = result.rolePermissions;
            //html = BI.Common.PrepareCheckListForDataSource(dataSet, dataSetSelected);

            html = '';
            $.each(dataSet, function(k0, v0) {
                var module = '<div class="module">';
                module += '<div><h3><u>' + v0[0]['mTitle'] + '</u></h3></div>';
                module += '<div>';

                var backend = '<div class="backend pull-left"><div><h4><b>Back End</b></h4></div>';
                var frontend = '<div class="frontend pull-left" style="margin-left: 300px;"><div><h4><b>Front End</b></h4></div>';
                $.each(v0, function(k1, v1) {
                    var row = '';
                    var checked = false;
                    var html_checked = '';
                    if(dataSetSelected != undefined && dataSetSelected.length > 0) {

                        $.each(dataSetSelected, function(k2,v2) {
                            if(v2.id == v1.id) {
                                checked = true;
                                html_checked = 'checked=""';
                            }
                        });
                    }

                    row += '<input type="checkbox" class="chk-Generic" name="chk-Generic" value="' + v1.id+ '" ' + html_checked + '/>' + v1.title +'<br/>';
                    if(v1.is_backend == "1") {
                        backend += row;
                    } else {
                        frontend += row;
                    }
                });
                frontend += '</div>';
                backend += '</div>';
                module += backend;
                module += frontend;
                module += '<div class="clearfix"></div>';

                module += '</div><br/>';
                module += '</div>';
                html += module;
            });
            //return html;


            var role = result.role;
            $('.dynamicData').html('- \'<b>' + role.title  + '</b>\'');

            $('#div-rolePermissionsList').html(html);


        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {
        var response = jqXHR.responseJSON;
        BI.Common.showGritterNotificationForFailure(response);

    });
};

BI.Admin.showPermissionRoles = function(id) {

    var url = BI.Config.api_path + '/acl/getPermissionsRole?permission_id=' + encodeURIComponent(id);
    $.ajax({
        url: url,
        type: "GET"
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            var result = data.resultSet;
            var html = 'No records found';
            var dataSetSelected = result.permissionsRole;

            html = '';
            $.each(dataSetSelected, function(k1, v1) {
                var row = '';
                var num = k1 + 1;
                row +=  num + '. ' + v1.title +'<br/>';
                html += row;
            });

            var permission = result.permission;
            $('.dynamicData').html('- \'<b>' + permission.title  + '</b>\'');

            $('#div-permissionRolesList').html(html);


        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {
        var response = jqXHR.responseJSON;
        BI.Common.showGritterNotificationForFailure(response);

    });
};

BI.Admin.showModulePermissions = function(id) {

    var url = BI.Config.api_path + '/acl/getModulePermissions?module_id=' + encodeURIComponent(id);
    $.ajax({
        url: url,
        type: "GET"
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {

            var result = data.resultSet;
            var html = 'No records found';
            var dataSetSelected = result.modulePermissions;

            html = '';

            var module = '<div class="module">';
            module += '<div>';

            var backend = '<div class="backend pull-left"><div><h4><b>Back End</b></h4></div>';
            var frontend = '<div class="frontend pull-left" style="margin-left: 100px;"><div><h4><b>Front End</b></h4></div>';
            $.each(dataSetSelected, function(k1, v1) {
                var row = '';
                var checked = false;
                var html_checked = '';

                var num = k1+1;
                row += num + '. ' + v1.title +'<br/>';
                if(v1.is_backend == "1") {
                    backend += row;
                } else {
                    frontend += row;
                }
            });
            frontend += '</div>';
            backend += '</div>';
            module += backend;
            module += frontend;
            module += '<div class="clearfix"></div>';

            module += '</div><br/>';
            module += '</div>';
            html += module;

            var module = result.module;
            $('.dynamicData').html('- \'<b>' + module.title  + '</b>\'');
            $('#div-modulePermissionsList').html(html);

        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {
        var response = jqXHR.responseJSON;
        BI.Common.showGritterNotificationForFailure(response);

    });
};

BI.Admin.associateRolesToUser = function (user_id , role_ids) {
    var data_to_send = 'user_id=' + encodeURIComponent(user_id) + '&role_ids=' + encodeURIComponent(role_ids);
    var url = BI.Config.api_path + '/acl/associateRolesToUser';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            window.location = user_id + '?message=' + 'Roles Associated successfully!';
        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            console.log( "error" );
    });
};

BI.Admin.associatePermissionsToRole = function (role_id , permission_ids) {
    var data_to_send = 'role_id=' + encodeURIComponent(role_id) + '&permission_ids=' + encodeURIComponent(permission_ids);
    var url = BI.Config.api_path + '/acl/associatePermissionsToRole';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            window.location = role_id + '?message=' + 'Permissions Associated successfully!';
        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            console.log( "error" );

    });
};

BI.Common.CreateDataTable = function (tableId,data) {
    var heading_html = null;
    var cols_html = '';
    var rows_html = '';
    var table = $('#' + tableId);
    if(typeof data['heading'] != 'undefined') {
        if(table.prev('h3').length!=0) {
            table.prev('h3').remove();
        }
        heading_html = '<h3>' + data['heading'] + '</h3>';
        table.before(heading_html);
    }

    $.each(data['cols'],function(index,col_name) {
        cols_html += '<th>' + col_name + '</th>';
    });
    table.children('thead').children('tr').html(cols_html);

    $.each(data['rows'],function(index,rows) {
        rows_html += '<tr>';
        $.each(rows,function(index2,col_value) {
            rows_html += '<td>' + col_value + '</td>'
        });
        rows_html += '</tr>';
    });
    table.children('tbody').html(rows_html);
    BI.Common.AttachDataTable('#' + tableId);

};

BI.Helper.formatMySQLDate = function (mySQLDate) {
    mySQLDate_array = mySQLDate.split('-');
    mySQLDateObj = new Date(mySQLDate_array[0],mySQLDate_array[1]-1,mySQLDate_array[2]);
    return mySQLDateObj.toDateString();
}

BI.Helper.printDivContent = function (element) {

        var mywindow = window.open('', 'my div', 'height=400,width=600');
        mywindow.document.write('<html><head><title>my div</title>');
        /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write($(element).html());
        mywindow.document.write('</body></html>');

        mywindow.print();
        mywindow.close();

        return true;

}


/*
 * Admin Users start
 */

BI.Admin.User.listUsers = function() {

    var url = BI.Config.api_path + '/user/getAll';

//alert(url);
    $.ajax({
        url: url,
        type: "GET"
    })
        .done(function(data, textStatus, jqXHR ) {

            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                var result = data.resultSet;
                var html = 'No records found';
                $.each(result, function(k, v){

                    result[k]['options'] = [[],[]];

                    /* result[k]['options'][0]['js'] = false;
                     result[k]['options'][0]['label'] = 'Roles';
                     result[k]['options'][0]['label_link'] = 'admin/userRoles/' + result[k]['id'];
                     */
                    /*result[k]['options'][1]['js'] = false;
                     result[k]['options'][1]['label'] = 'Permissions';
                     result[k]['options'][1]['label_link'] = 'admin/userPermissions/' + result[k]['id'];*/

                    result[k]['options'][0]['js'] = false;
                    result[k]['options'][0]['label'] = 'Edit';
                    result[k]['options'][0]['label_link'] = '/admin/editUser/' + result[k]['id'];

                    result[k]['options'][1]['js'] = true;
                    result[k]['options'][1]['class'] = 'removeUser';
                    result[k]['options'][1]['label'] = 'Delete';


                });

                if(result.length  > 0) {
                    html = BI.Common.PrepareDataTableFromDataSource(result);
                }
                $('#div-Users').html(html);
                BI.Common.AttachDataTable('#tbl-SimpleTable');
            }


        })
        .fail(function( jqXHR, textStatus, errorThrown) {
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        });
};

BI.Admin.User.get = function (user_id) {
    var data_to_send = 'user_id=' + encodeURIComponent(user_id);
    var url = BI.Config.api_path + '/user/get?' + data_to_send;
    $.ajax({
        url: url,
        type: "GET"
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                BI.Config.name = data.resultSet.first_name + ' ' + data.resultSet.last_name;
                $('#span-userName').html(BI.Config.name);


            }

        })
        .fail(function( jqXHR, textStatus, errorThrown) {
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        });
};

BI.Admin.User.getUserCouponOptions = function (user_id) {
    var data_to_send = 'user_id=' + encodeURIComponent(user_id);
    var url = BI.Config.api_path + '/user/getUser?' + data_to_send;
    $.ajax({
        url: url,
        type: "GET"
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                var user = data.resultSet.user;

                var extra = data.resultSet.extra;
                var fix = extra.fix;
                var percent = extra.percent;

                var html = '';

                if(fix.isFix == 1) {
                    html += '<option value="Fixed Amount">Fixed Amount</option>';
                    BI.Config.fixValue = fix.fixValue;
                }

                if(percent.isPercent == 1) {
                    html += '<option value="Percentage">Percentage</option>';
                    BI.Config.percentValue = percent.percentValue;
                }

                if(html != '')
                {
                    $('#sel-couponType').html(html);
                    $('#div-couponType').removeClass('hide');
                    $('#div-couponTypeValue').removeClass('hide');

                    var selectedType = $('#sel-couponType').val();
                    if(selectedType == 'Fixed Amount') {
                        $('#spn-couponTypeValue').text(BI.Config.fixValue);
                        $('#spn-couponTypeValueAfter').text('$');
                        $('#couponValue').val(BI.Config.fixValue);
                    }
                    if(selectedType == 'Percentage') {
                        $('#spn-couponTypeValue').text(BI.Config.percentValue);
                        $('#couponValue').val(BI.Config.percentValue);
                        $('#spn-couponTypeValueAfter').text('%');
                    }

                    $('#sel-couponType').change(function(){
                        if($(this).val() == 'Fixed Amount') {
                            $('#spn-couponTypeValue').text(BI.Config.fixValue);
                            $('#couponValue').val(BI.Config.fixValue);
                            $('#spn-couponTypeValueAfter').text('$');
                        }
                        if($(this).val() == 'Percentage') {
                            $('#spn-couponTypeValue').text(BI.Config.percentValue);
                            $('#couponValue').val(BI.Config.percentValue);
                            $('#spn-couponTypeValueAfter').text('%');
                        }
                    });
                }

            }

        })
        .fail(function( jqXHR, textStatus, errorThrown) {
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        });
};

BI.Admin.User.getUser = function (user_id) {
    var data_to_send = 'user_id=' + encodeURIComponent(user_id);
    var url = BI.Config.api_path + '/user/getUser?' + data_to_send;
    $.ajax({
        url: url,
        type: "GET"
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                var user = data.resultSet.user;
                /* $('#user_id').val(user.id);
                 $('#username').val(user.username);
                 $('#email').val(user.email);
                 $('#first_name').val(user.first_name);
                 $('#last_name').val(user.last_name);*/

                $('#user_id').val(user.id);
                var extra = data.resultSet.extra;
                var fix = extra.fix;
                var percent = extra.percent;

                if(typeof(fix) != 'undefined') {
                    $('#chk-isFix').click();
                    $('#fixValue').val(fix.fixValue);
                }

                if(typeof(percent)!= 'undefined') {
                    $('#chk-isPercent').click();
                    $('#percentValue').val(percent.percentValue);
                }
            }

        })
        .fail(function( jqXHR, textStatus, errorThrown) {
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        });
};

 BI.Admin.User.authenticate = function(username, password) {

     var data_to_send = 'username=' + encodeURIComponent(username) + '&password=' + encodeURIComponent(password);
    var url = BI.Config.api_path + '/user/login';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                var token = data.resultSet.token;
                window.location = 'authenticate?token=' + token;
            }

        })
        .fail(function( jqXHR, textStatus, errorThrown) {
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        });
};

BI.Admin.User.removeUser = function(id) {
    var user_id = id;
    var data_to_send = 'id=' + encodeURIComponent(id);
    var url = BI.Config.api_path + '/acl/removeUser';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                window.location = 'users?message=' + 'User deleted successfully!' + '&id=' + user_id;
            }

        })
        .fail(function( jqXHR, textStatus, errorThrown) {

            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            console.log( "error" );

        });
};

BI.Admin.User.createUser = function(username, email , password, first_name, last_name, extra){

    var data_to_send = 'username=' + encodeURIComponent(username) + '&password=' + encodeURIComponent(password) + '&email=' + encodeURIComponent(email) + '&first_name=' + encodeURIComponent(first_name) + '&last_name=' + encodeURIComponent(last_name) + '&extra=' + encodeURIComponent(extra);


    var url = BI.Config.api_path + '/user/create';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                var id = data.resultSet.id;
                window.location = 'users?message=' + 'User created successfully!' + '&id=' + id;
            }

        })
        .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            console.log( "error" );
        });
};

BI.Admin.User.updateUser = function(user_id, extra){
    var data_to_send = 'user_id=' + encodeURIComponent(user_id) + '&extra=' + encodeURIComponent(extra);
    var url = BI.Config.api_path + '/user/update';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                var id = data.resultSet.id;
                window.location = '../users?message=' + 'User updated successfully!' + '&id=' + id;
            }

        })
        .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            console.log( "error" );
        });
};

/*
 * Admin Pages start
 */


BI.Admin.Pages.listAllPages = function() {
    var web_id = $.cookie("site_id");
    var url = BI.Config.api_path + '/page/getPages';
    $.ajax({
        url: url,
        type: "GET",
        data:{
            website_id:web_id
            }
    })
        .done(function(data, textStatus, jqXHR ) {

            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                var result = data.resultSet;
                var html = 'No records found';

                $.each(result, function(k, v){

                    result[k]['options'] = [[],[]];

                    /* result[k]['options'][0]['js'] = false;
                     result[k]['options'][0]['label'] = 'Roles';
                     result[k]['options'][0]['label_link'] = 'admin/userRoles/' + result[k]['id'];
                     */
                    /*result[k]['options'][1]['js'] = false;
                     result[k]['options'][1]['label'] = 'Permissions';
                     result[k]['options'][1]['label_link'] = 'admin/userPermissions/' + result[k]['id'];*/
                    result[k]['options'][0]['js'] = false;
                    result[k]['options'][0]['label'] = 'Edit';
                    result[k]['options'][0]['label_link'] = '/admin/editPage/' + result[k]['id'];

                    result[k]['options'][1]['modal'] = true;
                    result[k]['options'][1]['class'] = 'removePage';
                    result[k]['options'][1]['label'] = 'Delete';
                    result[k]['options'][1]['modal_type'] = 'basic';


                });
                if(result.length  > 0) {
                    html = BI.Common.PrepareDataTableFromDataSource(result);
                }
                $('#div-pages').html(html);
                BI.Common.AttachDataTable('#tbl-SimpleTable');
            }


        })
        .fail(function( jqXHR, textStatus, errorThrown) {
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        });
};

BI.Admin.Pages.createPage = function(data){
    var data_to_send = data;

    var url = BI.Config.api_path + '/page/create';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                var id = data.resultSet.id;
                $('#submit_form').attr("disabled", "disabled").html("Succes !");
                window.location.replace(BI.Config.base_path+"/admin/editPage/"+id);
            }

        })
        .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            $('#submit_form').removeAttr("disabled").html("Submit");
        });
};

BI.Admin.Pages.updatePage = function(data){
    var data_to_send = data;

    var url = BI.Config.api_path + '/page/update';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
    .done(function(data, textStatus, jqXHR ) {
        var response = data;
        if(data.resultSet != null && data.status == 'Successful') {
            var id = data.resultSet.id;
            $('#submit_form').attr("disabled", "disabled").html("Success !");
            window.location.replace(BI.Config.base_path+"/admin/pages/");
        }

    })
    .fail(function( jqXHR, textStatus, errorThrown) {


        var response = jqXHR.responseJSON;
        var msg = '';
        if(response.statusMessage != undefined) {
            msg = response.statusMessage;
        } else {
            msg = response.error.message;
        }
        $('.alert-danger').show();
        $('.alert-danger .message').html(msg).focus();
    });
};

BI.Admin.Pages.getPage = function(pageId){

    var data_to_send = 'page_id=' + encodeURIComponent(pageId);
    var url = BI.Config.api_path + '/page/getPage?' + data_to_send;

    $.ajax({
        url: url,
        type: "GET",
        data: data_to_send
    })
        .done(function(data,textStatus, jqXHR){

            var page = data.resultSet[0];
            var meta_details = JSON.parse(page.meta_details);

            CKEDITOR.instances['content'].setData(page.content);
            $('input[name=title]').val(page.title);
            $('input[name=page_id]').val(page.id);
            $('select[name=status]').val(page.status);



            $.each(meta_details, function( index, value ) {
                $('#meta_'+index).val(value);
            });

            $("#meta_keywords").select2({
                tags: []
            });


            $(document).ready(function(){
                $('select[name=website_id]').val(page.website_id);
            });
        })
        .fail(function(data, textStatus,jqXHR){
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        })
};

BI.Admin.Pages.removePage = function(pageId){

    var data_to_send = 'page_id=' + encodeURIComponent(pageId);
    var url = BI.Config.api_path + '/page/removePage?' + data_to_send;

    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
        .done(function(data,textStatus, jqXHR){

            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {

                $('tr#'+pageId).fadeOut();
                $('#basic').modal('hide');
            }

        })
        .fail(function(data, textStatus,jqXHR){
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        })
};

BI.Admin.Pages.getWebsitesList = function() {

    var url = BI.Config.api_path + '/website/listAll';
    $.ajax({
        url: url,
        type: "GET"
    })
        .done(function(data, textStatus, jqXHR ) {

            var response = data;

            if(data.resultSet != null && data.status == 'Successful') {
                var result = data.resultSet;
                $.each(result, function(k,v) {
                    $('#website_id').append($('<option>', {
                        value: v.id,
                        text: v.title
                    }));
                });
            }


        })
        .fail(function( jqXHR, textStatus, errorThrown) {
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        });

};


/*
 * Admin Pages start
 */

BI.Admin.Providers.listAll = function() {

    var url = BI.Config.api_path + '/provider/listAll';
    $.ajax({
        url: url,
        type: "GET"
    })
        .done(function(data, textStatus, jqXHR ) {

            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                var result = data.resultSet;
                var html = 'No records found';

                $.each(result, function(k, v){

                    result[k]['options'] = [[],[]];

                    /* result[k]['options'][0]['js'] = false;
                     result[k]['options'][0]['label'] = 'Roles';
                     result[k]['options'][0]['label_link'] = 'admin/userRoles/' + result[k]['id'];
                     */
                    /*result[k]['options'][1]['js'] = false;
                     result[k]['options'][1]['label'] = 'Permissions';
                     result[k]['options'][1]['label_link'] = 'admin/userPermissions/' + result[k]['id'];*/
                    result[k]['options'][0]['js'] = false;
                    result[k]['options'][0]['label'] = 'Edit';
                    result[k]['options'][0]['label_link'] = '/admin/editProvider/' + result[k]['id'];

                    result[k]['options'][1]['modal'] = true;
                    result[k]['options'][1]['class'] = 'removeProvider';
                    result[k]['options'][1]['label'] = 'Delete';
                    result[k]['options'][1]['modal_type'] = 'basic';


                });
                if(result.length  > 0) {
                    html = BI.Common.PrepareDataTableFromDataSource(result);
                }
                $('#div-providers').html(html);
                BI.Common.AttachDataTable('#tbl-SimpleTable');
            }


        })
        .fail(function( jqXHR, textStatus, errorThrown) {
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        });
};

BI.Admin.Providers.create = function(data){
    var data_to_send = data;

    var url = BI.Config.api_path + '/provider/create';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send,
        contentType: false,
        cache: false,
        processData:false
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                var id = data.resultSet.id;
                $('#submit_form').attr("disabled", "disabled").html("Success !");
                window.location = 'editProvider/' + id;
            }

        })
        .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            $('#submit_form').removeAttr("disabled").html("Submit");
        });
};

BI.Admin.Providers.update = function(data){
    var data_to_send = data;

    var url = BI.Config.api_path + '/provider/update';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send,
        contentType: false,
        cache: false,
        processData:false
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                var id = data.resultSet.id;
                $('#submit_form').attr("disabled", "disabled").html("Succes !");
                // window.location.replace(BI.Config.base_path+"/admin/providers");
            }

        })
        .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            $('#submit_form').removeAttr("disabled").html("Submit");
        });
};

BI.Admin.Providers.get = function(providerId){

    var data_to_send = 'provider_id=' + encodeURIComponent(providerId);
    var url = BI.Config.api_path + '/provider/get?' + data_to_send;

    $.ajax({
        url: url,
        type: "GET",
        data: data_to_send
    })
        .done(function(data,textStatus, jqXHR){
            var provider = data.resultSet[0];

            $('input[name=title]').val(provider.title);
            $('input[name=provider]').val(provider.id);
            $('select[name=status]').val(provider.status);
            $('input[name=price]').val(provider.price);
            $('input[name=discount]').val(provider.discount);
            $('input[name=visit_link]').val(provider.visit_link);
            $('input[name=logo]').val(provider.logo);
            $('.ckeditor').val(provider.description);

            if(provider.logo) {
                $('.fileinput').addClass("fileinput-exists").removeClass('fileinput-new');
                $('.fileinput-preview').addClass("fileinput-exists").append("<img src='"+provider_s3+provider.logo+"' />");
                $('.remove_image').attr("data-file", provider.logo);
            }
        })
        .fail(function(data, textStatus,jqXHR){
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        })
};

BI.Admin.Providers.remove = function(providerId){

    var data_to_send = 'provider_id=' + encodeURIComponent(providerId);
    var url = BI.Config.api_path + '/provider/remove?' + data_to_send;

    $.ajax({
        url: url,
        type: "GET",
        data: data_to_send
    })
        .done(function(data,textStatus, jqXHR){

            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {

                $('tr#'+providerId).fadeOut();
                $('#basic').modal('hide');
            }

        })
        .fail(function(data, textStatus,jqXHR){
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        })
};

BI.Admin.Providers.removeLogo = function(data){
    if(!data) {
        return false;
    }
    var data_to_send = 'file='+encodeURIComponent(data);
    var url = BI.Config.api_path + '/provider/removeLogo?'+data_to_send;

    $.ajax({
        url: url,
        type: "GET",
        data: data_to_send
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {

                return false;
            }


        })
        .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
        });
};



/*
* Admin Custom Fields
*
* */

BI.Admin.CustomField.listAll = function() {

    var url = BI.Config.api_path + '/customfield/listAll';
    $.ajax({
        url: url,
        type: "GET"
    })
        .done(function(data, textStatus, jqXHR ) {

            var response = data;
            console.log(data);
            if(data.resultSet != null && data.status == 'Successful') {
                var result = data.resultSet;
                var html = 'No records found';

                $.each(result, function(k, v){

                    result[k]['options'] = [[],[]];
                    result[k]['options'][0]['js'] = false;
                    result[k]['options'][0]['label'] = 'Edit';
                    result[k]['options'][0]['label_link'] = '/admin/editCustomfield/' + result[k]['id'];

                    result[k]['options'][1]['modal'] = true;
                    result[k]['options'][1]['class'] = 'removeProvider';
                    result[k]['options'][1]['label'] = 'Delete';
                    result[k]['options'][1]['modal_type'] = 'basic';


                });
                if(result.length  > 0) {
                    html = BI.Common.PrepareDataTableFromDataSource(result);
                }
                $('#div-providers').html(html);
                BI.Common.AttachDataTable('#tbl-SimpleTable');
                //Hide pro cons field group

            }


        })
        .fail(function( jqXHR, textStatus, errorThrown) {
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        });
};

BI.Admin.CustomField.createGroup = function(data){

    var data_to_send = data;
    var url = BI.Config.api_path + '/customfield/creategroup';
    data_to_send += '&fields_group=1';

    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                var id = data.resultSet.id;
            }

            $('#submit_form').attr("disabled", "disabled").html("Success !");
            window.location.replace(BI.Config.base_path+"/admin/editCustomfield/"+id);

        })
        .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            $('#submit_form').removeAttr("disabled").html("Submit");

        });
};

BI.Admin.CustomField.updateGroup = function(data){
    var data_to_send = data;
    var url = BI.Config.api_path + '/customfield/updategroup';

    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                var id = data.resultSet.id;
                $('#submit_form').attr("disabled", "disabled").html("Success !");
                $(".update_cf").trigger("click");
                window.location.replace(BI.Config.base_path+"/admin/customfields");
            }

        })
        .fail(function( jqXHR, textStatus, errorThrown) {

            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            $('#submit_form').removeAttr("disabled").html("Submit");

        });
};

BI.Admin.CustomField.removeFieldGroup = function(id) {


    var data_to_send = 'id=' + encodeURIComponent(id);
    var url = BI.Config.api_path + '/customfield/removeFieldGroup?' + data_to_send;

    $.ajax({
        url: url,
        type: "GET",
        data: data_to_send
    })
        .done(function(data,textStatus, jqXHR){

            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {

            }

        })
        .fail(function(data, textStatus,jqXHR){
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        })

};

BI.Admin.CustomField.removeField = function(id) {


        var data_to_send = 'id=' + encodeURIComponent(id);
        var url = BI.Config.api_path + '/customfield/removeField?' + data_to_send;

        $.ajax({
            url: url,
            type: "GET",
            data: data_to_send
        })
            .done(function(data,textStatus, jqXHR){

                var response = data;
                if(data.resultSet != null && data.status == 'Successful') {

                }

            })
            .fail(function(data, textStatus,jqXHR){
                var response = jqXHR.responseJSON;
                BI.Common.showGritterNotificationForFailure(response);
            })

};

BI.Admin.CustomField.getFieldGroup = function(id) {
    //alert("hi");
    var data_to_send = 'id=' + encodeURIComponent(id);
    var url = BI.Config.api_path + '/customfield/getFieldGroup?' + data_to_send;

    $.ajax({
        url: url,
        type: "GET",
        data: data_to_send
    })
        .done(function(data,textStatus, jqXHR){
            var result = data.resultSet[0];
            $('input[name=title]').val(result.title);
            $('select[name=module_type]').val(result.module_type);
            $('select[name=status]').val(result.status);

        })
        .fail(function(data, textStatus,jqXHR){
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        })
};

BI.Admin.CustomField.getGroupFields = function(cf_groupId, order_by) {
    var data_to_send = 'group_id=' + encodeURIComponent(cf_groupId) + '&orderby=' + encodeURIComponent(order_by);
    var url = BI.Config.api_path + '/customfield/getgroupfields?' + data_to_send;
    var cf_div = $("#cf_group_fields");
    $.ajax({
        url: url,
        type: "GET",
        data: data_to_send
    })
        .done(function(data,textStatus, jqXHR){
            var result = data.resultSet;
            $.each( result, function( k, v){
                console.log();
                $(cf_div).append(BI.Common.collapsible_row(v.id, v.title, v.key, v.input_type, v.default_value, v.module_type, v.id, 1));

                if(v.input_type !== 'boolean')
                    $("#cf_options_"+v.id).select2({
                        tags: []
                    });
            });

        })
        .fail(function(data, textStatus,jqXHR){
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        })
};

BI.Admin.CustomField.getFieldsByModule = function(module_type, relation_id){
    if(!relation_id) {
        $("#cf_group_fields").append("<p>Please create "+ module_type + " first !");
        return false;
    }
    var data_to_send = 'module_type=' + encodeURIComponent(module_type) + '&relation_id=' + encodeURIComponent(relation_id);
    var url = BI.Config.api_path + '/customfield/getfieldsbymodule?' + data_to_send;
     $.ajax({
        url: url,
        type: "GET",
        data: data_to_send
    })
        .done(function(data,textStatus, jqXHR){

            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {

                var result = data.resultSet;
                if(result == '') {
                    $("#cf_group_fields").append('<div class="alert alert-danger"><strong>Error!</strong> No fields defined. </div>'); return false;
                }

                $.each(result, function(k,v) {
                   $("#cf_group_fields").append(BI.Common.moduleFieldsRows(k, v, relation_id));

                    $.each(v.group_fields, function(k1,v2) {
                        if(v2.input_type == "tags") {
                            $('#' + v2.key).select2({
                                tags: []
                            })
                        }
                    })
                });

                BI.Common.initUniform();

            }

        })
        .fail(function(data, textStatus,jqXHR){
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        });

};

BI.Admin.CustomField.updateFieldByModule = function(module_type, data) {

    var data_to_send = data;
    data_to_send += '&module_type='+module_type;
    var url = BI.Config.api_path + '/customfield/updatefieldbymodule';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                var id = data.resultSet.id;

            }

        })
        .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
        });
    return false;
};

BI.Admin.CustomField.getFieldsValue = function(key, rel_id){

    var data_to_send = 'key=' + encodeURIComponent(key) + '&rel_id=' + encodeURIComponent(rel_id);
    var url = BI.Config.api_path + '/customfield/getFieldValue?' + data_to_send;
    $.ajax({
        url: url,
        type: "GET",
        data: data_to_send
    })
        .done(function(data,textStatus, jqXHR){
            var result = data.resultSet;

            return result;
        })
        .fail(function(data, textStatus,jqXHR){
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        });


};

BI.Admin.CustomField.manageFields = function(fieldGroupID) {

    if(!fieldGroupID) {
        $("#cf_group_fields").html("Please save group before creating fields !")
    }

    len = function (obj) {
        var L = 0;
        $.each(obj, function (i, elem) {
            L++;
        });
        return L;
    };

    $(".btn_link").click(function () {
        var row = 0;
        var type = $(this).data("cftype");
        //console.log(type);return false;
        var cf_div = $("#cf_group_fields");
        var rows = len($(".accor_row"));
        var key = "Custom field" + rows;

        if (type == "input") {
            $(cf_div).append(BI.Common.collapsible_row(0, key, key, type, "", "", rows, type, 0));
        }
        if (type == "textarea") {
            $(cf_div).append(BI.Common.collapsible_row(0, key, key, type, "", "", rows, type, 0));
        }
        if (type == "tags") {
            $(cf_div).append(BI.Common.collapsible_row(0, key, key, type, "", "", rows, type, 0));
        }
        if (type == "select" || type == "checkbox") {
            $(cf_div).append(BI.Common.collapsible_row(0, key, key, type, "", "", rows, type, 0));
            $("#cf_options_"+rows).select2({
                tags: []
            });
        }
        if(type == 'boolean') {
            $(cf_div).append(BI.Common.collapsible_row(0, key, key, type, "", "", rows, type, 0));
        }

        $('#cf_'+rows+' input').focus();

    });

    $(".update_cf").live('click', function(){

        var that = $(this);
        $(that).attr("disabled", "disabled")
        var current_row = $(this).closest('div.accor_row');
        var field_title = $(current_row).find(".cf_title").val();
        var default_value = $(current_row).find("input.cf_options").val();

        var update_field = $(this).data("update");
        var input_type = $(this).data("type");
        var group_id =  $(".group_id").val();
        var module_type = $(".module_type").val();

        var key = BI.Common.convertToSlug(field_title);
        var data_to_send = '';
        var url = '';

        update_field > 0 ? data_to_send += 'field_id=' + update_field +'&' : '';
        data_to_send += 'title='+ field_title + '&key=' + key + '&module_type=' + module_type + '&input_type=' + input_type + '&default_value=' + default_value + '&group_id=' + group_id ;
        update_field > 0 ? url += BI.Config.api_path + '/customfield/updatefield?' + data_to_send : url += BI.Config.api_path + '/customfield/createfield?' + data_to_send;
        $.ajax({
            url: url,
            type: "POST",
            data: data_to_send
        })
            .done(function(data,textStatus, jqXHR){
                $(current_row).find("h4.panel-title .accordion-toggle").html(field_title);
                $(that).html("Success !");
                return false;
            })
            .fail(function(data, textStatus,jqXHR){
                var response = jqXHR.responseJSON;
                var msg = '';
                if(response.statusMessage != undefined) {
                    msg = response.statusMessage;
                } else {
                    msg = response.error.message;
                }
                $('.alert-danger').show();
                $('.alert-danger .message').html(msg).focus();
            })
    });

    $(".remove_cf").live('click', function(id){
        var data_to_send = 'id=' + $(this).data("remove");
        var url = BI.Config.api_path + '/customfield/removeField?' + data_to_send;

        var that = this;
        var target = $(this).closest('div.accor_row');
        BI.Common.showModal({type: "basic", title: "Confirm Delete", body: 0, button: "Yes delete !" });
        $(".confirm").click(function(){

            if(data_to_send == 'id=0') {
                $(target).slideUp('slow', function(){ $(target).remove(); });
                $('#basic').modal('hide');
                return false;
            }

            $.ajax({
                url: url,
                type: "GET",
                data: data_to_send
            })

                .done(function(data,textStatus, jqXHR){
                    $(target).slideUp('slow', function(){ $(target).remove(); });
                    $('#basic').modal('hide');
                })

                .fail(function(data,textStatus, jqXHR){
                    var response = jqXHR.responseJSON;
                    var msg = '';
                    if(response.statusMessage != undefined) {
                        msg = response.statusMessage;
                    } else {
                        msg = response.error.message;
                    }
                    $('.alert-danger').show();
                    $('.alert-danger .message').html(msg).focus();
                });


        })

    });

};

/*
 * Admin Websites
 *
 * */

BI.Admin.Websites.listAll = function(dataTables) {

    var url = BI.Config.api_path + '/website/listAll';
    $.ajax({
        url: url,
        type: "GET"
    })
        .done(function(data, textStatus, jqXHR ) {

            var response = data;

            if(data.resultSet != null && data.status == 'Successful') {
                var result = data.resultSet;

                var html = 'No records found';
                $.each(result, function(k, v){

                    result[k]['options'] = [[],[]];

                    result[k]['options'][0]['js'] = false;
                    result[k]['options'][0]['label'] = 'Edit';
                    result[k]['options'][0]['label_link'] = '/admin/editWebsite/' + result[k]['id'];

                    result[k]['options'][1]['class'] = 'removePage';
                    result[k]['options'][1]['label'] = 'Delete';
                    result[k]['options'][1]['modal_type'] = 'basic';


                });
                if(result.length  > 0) {
                    html = BI.Common.PrepareDataTableFromDataSource(result);
                }
                $('#div-pages').html(html);
                BI.Common.AttachDataTable('#tbl-SimpleTable');

            }


        })
        .fail(function( jqXHR, textStatus, errorThrown) {
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        });

};

BI.Admin.Websites.createWebsite = function(data){
    var data_to_send = data;
    var url = BI.Config.api_path + '/website/createWebsite';
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send,
        contentType: false,
        cache: false,
        processData:false
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                var id = data.resultSet.id;

                $('#submit_form').attr("disabled", "disabled").html("Success !");
                window.location.replace(BI.Config.base_path+"/admin/editWebsite/"+id);
                return false;
            }
        })
        .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            $('#submit_form').removeAttr("disabled").html("Submit");
        });
};

BI.Admin.Websites.updateWebsite = function(data){
    var data_to_send = data;
    var url = BI.Config.api_path + '/website/updateWebsite';

    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send,
        contentType: false,
        cache: false,
        processData:false
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                $('#submit_form').attr("disabled", "disabled").html("Success !");
                window.location.replace(BI.Config.base_path+"/admin/websites");
                return false;
            }

        })
        .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            $('#submit_form').removeAttr("disabled").html("Submit");
        });
};

BI.Admin.Websites.removeLogo = function(data){
    if(!data) {
        return false;
    }
    var data_to_send = 'file='+encodeURIComponent(data);
    var url = BI.Config.api_path + '/website/removeLogo?'+data_to_send;

    $.ajax({
        url: url,
        type: "GET",
        data: data_to_send
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {

                return false;
            }


        })
        .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
        });
};

BI.Admin.Websites.getWebsite = function(websiteId) {

    var data_to_send = 'website_id=' + encodeURIComponent(websiteId);
    var url = BI.Config.api_path + '/website/getWebsite?' + data_to_send;
    var feature_pro_list = '';
    $.ajax({
        url: url,
        type: "GET",
        data: data_to_send
    })
        .done(function(data,textStatus, jqXHR){
            var result = data.resultSet[0];
            var meta_details = JSON.parse(result.meta_details);
            var setting = JSON.parse(result.setting);
            var theme = JSON.parse(result.theme);

            $('input[name=title]').val(result.title);
            $('input[name=domain]').val(result.domain);
            $('input[name=server_ip]').val(result.server_ip);
            $('input[name=git_repo]').val(result.git_repo);
            $('input[name=website_id]').val(result.id);
            $('select[name=website_type]').val(result.website_type);

            $.each(meta_details, function( index, value ) {
                $('#meta_'+index).val(value);
            });

            $.each(setting, function( index, value ) {
                $('#setting_'+index).val(value);
            });



                $.each(theme, function( index, value ) {
                    if(index == 'logo') {
                        if(value !== '') {
                        //var src = BI.Config.website_path+'public/uploads/'+value;
                        $('.fileinput').addClass("fileinput-exists").removeClass('fileinput-new');
                        $('.fileinput-preview').addClass("fileinput-exists").append("<img src='"+website_s3+value+"' />");
                        }
                    }
                    $('#theme_'+index).val(value);
                    $('.'+index).css("background-color", value);

                });

            $("#meta_keywords").select2({
                tags: [feature_pro_list]
            });

            $("#setting_compare_vpn").select2({
                tags: []
            });



        })
        .fail(function(data, textStatus,jqXHR){
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        })

};


/*Git repos*/


BI.Admin.Git.listAll = function(dataTables) {

    var url = BI.Config.api_path + '/gitrepo/listAll';
    $.ajax({
        url: url,
        type: "GET"
    })
        .done(function(data, textStatus, jqXHR ) {

            var response = data;

            if(data.resultSet != null && data.status == 'Successful') {
                var result = data.resultSet;
                      var html = 'No records found';
                $.each(result, function(k, v){
                    var repo = JSON.parse(v.setting);

                        html += '<tr id="row_"'+v.id+'">';
                            html += '<td > '+v.id+' </td>';
                            html += '<td> '+ v.domain+' </td>';
                            html += '<td> <a data-target="http://'+v.domain+'" data-id="'+v.id+'" href="javascript:void(0)" class="btn btn-xs dark git_pull">Pull <i class="fa fa-link "></i></a> </td>';
                            html += '<td class="response_'+ v.id+'"> False  </td>';
                        html += '</tr>';


                });

                if(result.length  > 0) {
                    $('#git-data').append(html);
                }



            }


        })
        .fail(function( jqXHR, textStatus, errorThrown) {
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        });

};

BI.Admin.Git.pull = function(params){
    var id = $(params).data('id');
    var target = $(params).data('target');
    var data_to_send = 'target=' + encodeURIComponent(target);
    var url = BI.Config.api_path + '/gitrepo/pull?'+data_to_send;
    $(params).attr("disabled", "disabled").text('Pulling..');
    $.ajax({
        url: url,
        type: "GET"
    })
        .done(function(data, textStatus, jqXHR ) {

            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                $(params).removeAttr("disabled").text('Success').removeClass("dark").addClass("green");
                $('.response_'+id).text(data.resultSet);


            }


        })
        .fail(function( jqXHR, textStatus, errorThrown) {
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        });
};

/*Media*/
BI.Admin.Media.upload = function(data){
    var data_to_send = data;
    var url = BI.Config.api_path + '/media/upload';
   alert(url);
    $.ajax({
        url: url,
        type: "POST",
        data: data_to_send,
        contentType: false,
        cache: false,
        processData:false
    })
        .done(function(data, textStatus, jqXHR ) {
            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                console.log(response);
                $('#submit_form').attr("disabled", "disabled").html("Success !");
                var image_url = BI.Config.media_s3 + response.resultSet;
                $('.image_url').append('<hr />Copy the following link :  <br /><input class="form-control" type="text" value="'+image_url+'">');return false;
            }

        })
        .fail(function( jqXHR, textStatus, errorThrown) {


            var response = jqXHR.responseJSON;
            var msg = '';
            if(response.statusMessage != undefined) {
                msg = response.statusMessage;
            } else {
                msg = response.error.message;
            }
            $('.alert-danger').show();
            $('.alert-danger .message').html(msg).focus();
            $('#submit_form').removeAttr("disabled").html("Submit");
        });
};

BI.Admin.Media.listAll = function(data){

    var data_to_send = data;
    var url = BI.Config.api_path + '/media/getAll';

    $.ajax({
        url: url,
        type: "GET"
    })
        .done(function(data, textStatus, jqXHR ) {

            var response = data;
            if(data.resultSet != null && data.status == 'Successful') {
                var result = data.resultSet;
                var html = 'No records found';
                $.each(result, function(k, v){
                    //var repo = JSON.parse(v.setting);

                    html += '<tr id="row_"'+v.id+'">';
                    html += '<td > '+v.id+' </td>';
                    html += '<td> <img src="' + BI.Config.media_s3 + v.file_name+'" class="media_list" style="max-width: 80px;cursor:pointer"/> </td>';
                    html += '<td> <input type="text" value="' + BI.Config.media_s3 + v.file_name+'"/> </td>';
                    html += '<td> '+ v.timestamp+' </td>';

                    html += '</tr>';


                });

                if(result.length  > 0) {
                    $('#media-data').append(html);
                    $('.media_list').click(function(){
                        var image_link = '<img src="'+$(this).attr("src")+'">';
                        BI.Common.showModal({type: "basic", title: "", body: image_link, button: 0 });
                    });
                }



            }


        })
        .fail(function( jqXHR, textStatus, errorThrown) {
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        });
};


BI.Common.getWebsiteList = function() {

    var url = BI.Config.api_path + '/website/listAll';
    $.ajax({
        url: url,
        type: "GET"
    })
        .done(function(data, textStatus, jqXHR ) {


            var response = data;
            var select_website = $('.select_website');

            if(data.resultSet != null && data.status == 'Successful') {
                var result = data.resultSet;
                    select_website.prepend( '<li><a  class="select_sites"   data-website_id="'+ 0 +'" data-website_title="All Websites"  href="javascript:void(0);">All Websites</a></li>' );

                $.each(result, function(k,v) {
                    select_website.append('<li><a  class="select_sites"  data-website_id="'+ v.id+'"  data-website_title="'+ v.title +'"  href="javascript:void(0);">' + v.title + '</a></li>');
                });
            }


        })
        .fail(function( jqXHR, textStatus, errorThrown) {
            var response = jqXHR.responseJSON;
            BI.Common.showGritterNotificationForFailure(response);
        });

};