@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    
    

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>All Ticket List</h1>
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Ticket List</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- DateFilter:- <input type="text" name="datefilter" value="" /> -->
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-5"><span id="selected_date" style="float: right; font-weight: bold;"></span></div>
                                <div class="col-xs-3" id="show_only_activity" style="padding: 0 10px;display: none;">
                                    <label>Show Activity Ticket: </label>
                                    <select name="only_activity" id="only_activity">
                                        <option value="">---Select---</option>
                                        <option value="0">All Ticket</option>
                                        <option value="1">Only Activity</option>
                                    </select>
                                </div>
                                <div class="col-xs-4 form-inline" style="z-index: 2; float:right; padding:0px 0 0 0px;">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 70%;">
                                        <!-- <input type="text" class="input-sm form-control" id="startdate"  name="start"   />
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="input-sm form-control" id="enddate" name="end"   /> -->
                                        <input type="text" id="datefilter" name="datefilter" class="input-sm form-control" value="" placeholder="YYYY-MM-DD" autocomplete="off" readonly='readonly'/>
                                        <input type="hidden" class="input-sm form-control" name="token" value="{{csrf_token()}}"/>
                                    </div>
                                    <button id="clear" class="btn btn-sm ">reset</button>
                                    <button type="button" id="dateSearch" class="btn btn-sm btn-primary" style="margin:0;">Search</button>
                                    <span id="filter-error" style="color: red;"></span>
                                </div>
                            </div>
                            
                            <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Ticket</th>
                                        <th>Assign To DD ECN</th>
                                        <th>Assign To DD</th>
                                        <th>ECN (created by)</th>
                                        <th>User (created by)</th>
                                        <th>Role (created by)</th>
                                        <th>Language</th>
                                        <th>Data Source</th>
                                        <th>State</th>
                                        <th>District</th>
                                        <th>Tehsil</th>
                                        <th>Territory</th>
                                        <th>Village</th>
                                        <!--<th>Product</th>
                                        <th>Crop</th>-->
                                        <th>Crop 1</th>
                                        <th>Product 1</th>
                                        <th>Crop 2</th>
                                        <th>Product 2</th>
                                        <th>Crop 3</th>
                                        <th>Product 3</th>
                                        <th>Expected PDA</th>
                                        <th>Expected Farmers</th>
                                        <th>Total Farmers</th>
                                        <th>Total Invited Farmers</th>
                                        <th>Total Highlights</th>
                                        <th>Total IFC Farmers</th>
                                        <th>Special Participants</th>
                                        <th>Activity Duration</th>
                                        <th>Activity Distance</th>
                                        <th>Demo Number</th>
                                        <th>Status</th> 
                                        <th>Is Qualified</th> 
                                        <!-- <th>Validated Farmers</th> --> 
                                        <th>Uploaded File</th>
                                        <th>Uploader Name</th>
                                        <th>Uploader ECN</th>
                                        <th>Verified</th>
                                        <th>Distance (metres)</th>
                                        <th>Officer Name</th>
                                        <th>Officer Upload</th>  
                                        <th>Latitude</th> 
                                        <th>Longitude</th> 
                                        <th>Date Created</th> 
                                        <th>Date Modified</th> 
                                        <th>Closing Date</th>
                                        <th>DD Ticket Role</th>
                                        <th>&emsp;&nbsp;<i class="fa fa-wrench" aria-hidden="true"></i></th>
                                    </tr>
                                </thead>
                            </table>
                            <div class="success"></div>
                            <!--<a href="javascript:void(0);" id="viewfarmers" data-token="{{csrf_token()}}" data-toggle="modal" data-target="#ticketFarmers" data-ticket="3">sdfsdfsdfs</a>-->
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div><!-- /.col -->
            </div><!-- /.row -->
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->


    @include('include.admin.footer')
    
</div>


<!-- Farmer List -->
<div class="modal fade in" id="ticketFarmers" role="dialog" style="padding-right: 12px;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button data-dismiss="modal" class="close" type="button">×</button>
            </div>
            <div class="modal-body">
                <div id="tabs" class="ui-tabs ui-corner-all ui-widget ui-widget-content">
                    <div id="farmerList" aria-labelledby="ui-id-2" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true">
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<!-- Farmer List -->
<!-- Add Farmer -->
<div class="modal fade in" id="ticketFarmers1" role="dialog" style="padding-right: 12px;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button data-dismiss="modal" class="close" type="button">×</button>
            </div>
            <div class="modal-body" style="    width: 80%;    margin: 0 auto;">
                <div id="tabs" class="ui-tabs ui-corner-all ui-widget ui-widget-content">
                    <!-- <div id="farmerList" aria-labelledby="ui-id-2" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true">
                        
                    </div> -->
                    <form id="newFarmerAddForm" name="newFarmerAddForm">
                <div class="form-group">
                        <label>Farmer Contact Number</label>
                    <input type="text" name="farmerContact" id="farmerContact" class="form-control">
                    <input type="hidden" name="ticketId" value="" id="newticketId">
                </div>
                <div class="form-group">
                    <button type="submit" id="addNewFarmers">Submit</button>
                </div>
                </form>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<!--Add Farmer -->

<?php
$end_date   = date("Y-m-d");
$start_date = date('Y-m-d', strtotime($end_date. ' -9 days'));
?>



<!-- ./wrapper --><!-- jQuery 2.1.4 -->
<script src="{{asset('assets/admin/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
<!--Datepicker-->
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.common-material.min.css" />
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.min.css" />
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.mobile.min.css" />
<script src="https://kendo.cdn.telerik.com/2018.1.221/js/kendo.all.min.js"></script>  
<script>
$("#startdate, #enddate").kendoDatePicker({
    // defines the start view
    start: "date",

    // defines when the calendar should return date
    depth: "year",

    // display month and year in the input
    format: "yyyy-MM-dd",

    // specifies that DateInput is used for masking the input element
    dateInput: false,
});
//DISABLE inputs
$("#startdate, #enddate").attr("readonly", true);
</script>
<!--Datepicker-->

<!-- Bootstrap 3.3.5 -->
<script src="{{asset('assets/admin/bootstrap/js/bootstrap.min.js')}}"></script>
<!-- DataTables -->
<script src="{{asset('assets/admin/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/admin/plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.colVis.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js"></script>
<link  rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
<link  rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.2.4/css/fixedColumns.dataTables.min.css">
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

<!-- SlimScroll -->
<script src="{{asset('assets/admin/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->

<script>

// Export All Data In Excel
var oldExportAction = function (self, e, dt, button, config) {
    if (button[0].className.indexOf('buttons-excel') >= 0) {
        if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
            $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
        }
        else {
            $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
        }
    } else if (button[0].className.indexOf('buttons-print') >= 0) {
        $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
    }
};

var newExportAction = function (e, dt, button, config) {
    var self        = this;
    var oldStart    = dt.settings()[0]._iDisplayStart;    
    dt.one('preXhr', function (e, s, data) {
        // Just this once, load all data from the server...
        data.start  = 0;
        data.length = 2147483647;        
        dt.one('preDraw', function (e, settings) {
            // Call the original action function 
            oldExportAction(self, e, dt, button, config);            
            dt.one('preXhr', function (e, s, data) {
                // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                // Set the property to what it was before exporting.
                settings._iDisplayStart = oldStart;
                data.start = oldStart;
            });            
            // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
            setTimeout(dt.ajax.reload, 0);            
            // Prevent rendering of the full data to the DOM
            return false;
        });
    });    
    // Requery the server with the new one-time export settings
    dt.ajax.reload();
};
// Export All Data In Excel

/*$( document ).on('ready',function() {
    drawTable();
});*/

$("#clear").click(function(){
    $("#datefilter").val('');
    $("#dateSearch").prop('disabled', true);
    $('#clear').prop('disabled', true);
    // return false;
    var start_date  = '<?php echo $start_date; ?>'; 
    var end_date    = '<?php echo $end_date; ?>'; 
    let selected_date = "Selected Date: "+start_date+" - "+end_date;
    $('#selected_date').text(selected_date);
    drawTable(start_date, end_date);
});

$('#dateSearch').on('click',function(){
    let datefilter   = $('#datefilter').val();
    $("#dateSearch").prop('disabled', true);
    $('#clear').prop('disabled', true);
    if(datefilter == "") {
        $('#filter-error').text('Please select date range');
        $("#dateSearch").prop('disabled', false);
        $('#clear').prop('disabled', false);
        return false;
    } else {
        let expDate     = datefilter.split(" - ");
        let startDate   = expDate[0];
        let endDate     = expDate[1];
        // console.log("startDate=>"+startDate+"-endDate=>"+endDate);
        // return false;
        $('#filter-error').text('');
        let selected_date = "Selected Date: "+startDate+" - "+endDate;
        $('#selected_date').text(selected_date);
        drawTable(startDate, endDate);
    }
});

$('#only_activity').on('change',function(){
    $("#dateSearch").prop('disabled', true);
    $('#clear').prop('disabled', true);
    let datefilter   = $('#datefilter').val();
    if(datefilter == "") {
        $('#filter-error').text('Please select date range');
        $("#dateSearch").prop('disabled', false);
        $('#clear').prop('disabled', false);
        return false;
    } else {
        $('#filter-error').text('');
        let expDate     = datefilter.split(" - ");
        let startDate   = expDate[0];
        let endDate     = expDate[1];
        let selected_date = "Selected Date: "+endDate+" - "+endDate;
        $('#selected_date').text(selected_date);
        drawTable(startDate, endDate);
    }
});

function drawTable(start_date, end_date) {
    jQuery('#dateSearch').prop('disabled', true);
    jQuery('#clear').prop('disabled', true);
    let only_activity = $('#only_activity').val();
    console.log('start_date=>'+start_date+'-end_date=>'+end_date);

    // Datatable
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'YYYY-MM-DD, hh:mm A' );
    table   =   $('.datatable').DataTable({
                "destroy" : true,
                "language": {
                    searchPlaceholder: "Ticket, BDO, Data Source, State, District, Tehsil, Village, Product, Crop"
                },
                "order": [[ 1, "desc" ]],
                "dom": 'lBfrtip',
                "buttons": [                        
                            // 'pageLength',
                            {
                                text: 'Export All',
                                className: 'redButton'
                            },
                            {
                                extend: 'excel',
                                text: '<span class="fa fa-file-excel-o"></span> Excel (Current)',
                                title: 'All Tickets (Current)'
                            },
                            {
                                extend: 'excel',
                                text: '<span class="fa fa-file-excel-o"></span> Excel (All)',
                                action: newExportAction,
                                messageTop: 'Dhanuka Ticket Data',
                                exportOptions: {
                                    columns: ':visible'
                                },
                                title: 'All Tickets (All)'
                            },
                            {
                                extend: 'colvis',
                                collectionLayout: 'fixed  four-column',
                                columns: ':gt(0)',
                            },
                    ],

                "lengthMenu": [[25, 50, 100], ['25 rows', '50 rows', '100 rows']],
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "responsive": false,

                "ajax": {
                            datatype:   'json',
                            type:       "POST",
                            url:        "{{url('admin/allticket')}}",
                            data:       { _token: "{{csrf_token()}}", start_date: start_date, end_date: end_date, only_activity: only_activity},
                            error:      function (request, status, error) {
                                        }
                        },
                "columnDefs": [
                                {
                                    render: function (data, type, full, meta) {
                                        return "<div class='text-wrap width-200'>" + data + "</div>";
                                    },
                                    targets: 22
                                },
                            ],
                "initComplete": function( settings, json ) {
                    let datefilter = $('#datefilter').val();
                    if(datefilter != "") {
                        jQuery('#show_only_activity').show();
                    }
                    jQuery('#dateSearch').prop('disabled', false);
                    jQuery('#clear').prop('disabled', false);
                },
                "columns": [
                            {
                                render:   function (data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                                }
                            },
                            { "data": "1" },
                            { "data": "2" },
                            { "data": "3" },
                            { "data": "4" },                            
                            { "data": "5" },
                            { "data": "6" },
                            { "data": "7" },
                            { "data": "8" },
                            { "data": "9" },
                            { "data": "10" },
                            { "data": "11" },
                            { "data": "42" },
                            { "data": "12" },
                            { "data": "13" },
                            { "data": "14" },                            
                            { "data": "15" },
                            { "data": "16" },                            
                            { "data": "17" },
                            { "data": "18" },
                            { "data": "19" },
                            { "data": "20" },
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                       data = '<a href="javascript:void(0);" id="viewfarmers" data-token="{{csrf_token()}}" data-toggle="modal" data-target="#ticketFarmers" data-ticket="'+row[1]+'">'+row[21]+'</a>';
                                   }
                                   return data;
                                }
                            }, 
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                       data = '<a href="javascript:void(0);" id="viewinvitedfarmers" data-token="{{csrf_token()}}" data-toggle="modal" data-target="#ticketFarmers" data-ticket="'+row[1]+'">'+row[22]+'</a>';
                                   }
                                   return data;
                                }
                            },
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                       data = '<a href="javascript:void(0);" id="viewhighlightedfarmers"  data-ticket="'+row[35]+'">'+row[23]+'</a>';
                                   }
                                   return data;
                                }
                            },
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                       data = '<a href="javascript:void(0);" id="viewifcfarmers" data-token="{{csrf_token()}}" data-toggle="modal" data-target="#ticketFarmers" data-ticket="'+row[1]+'">'+row[38]+'</a>';
                                   }
                                   return data;
                                }
                            },
                            { "data": "24" },
                            // { "data": "39" },
                            // { "data": "40" },
                            { 
                                "render": function(data, type, row, meta){
                                    if(type === 'display'){
                                        if(row[39] != null) {
                                            let decimalTimeString = parseFloat(row[39]).toFixed(2);
                                            let n = new Date(0,0);
                                            n.setSeconds(+decimalTimeString * 60 * 60);
                                            data = n.toTimeString().slice(0, 8);
                                        } else {
                                            data = row[39]
                                        }
                                   }
                                   return data;
                                }
                            },
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                        if(row[40] != null) {
                                            data = row[40]/1000;
                                            data = parseFloat(data).toFixed(2);
                                        } else {
                                            data = 0.00;
                                        }
                                   }
                                   return data;
                                }
                            },
                            { "data": "25" },
                            { "data": "26" },
                            { "data": "27" },
                            // { 
                            //     "render": function(data, type, row, meta){
                            //        if(type === 'display'){
                            //            data = '<a href="javascript:void(0);" id="viewvalidatedfarmers" data-token="{{csrf_token()}}" data-toggle="modal" data-target="#ticketFarmers2" data-ticket="'+row[1]+'">'+row[26]+'</a>';
                            //        }
                            //        return data;
                            //     }
                            // }, 
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                        var str1 = row[29];
                                        if(str1!=undefined && str1.includes("assets")) {
                                            var str2 = str1.replace('admin/','');
                                            data = "<a href='"+str2+"' target='_blank'>view</a>";
                                        }
                                        else {
                                            data = "";
                                        }
                                   }
                                   return data;
                                }
                            },
                            { "data": "30" },
                            { "data": "43" },
                            { "data": "44" },
                            { "data": "45" },
                            { "data": "46" },
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                        var str1 = row[47];
                                        if(str1!=undefined && str1.includes("assets")) {
                                            var str2 = str1.replace('admin/','');
                                            data = "<a href='"+str2+"' target='_blank'>view</a>";
                                        }
                                        else {
                                            data = "";
                                        }
                                   }
                                   return data;
                                }
                            },
                            { "data": "31" },
                            { "data": "32" },
                            { "data": "33" },
                            { "data": "34" },
                            { "data": "35" },
                            { "data": "41" },
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                       data = '<a href="javascript:void(0);" id="editTicket" data-token="{{csrf_token()}}" data-toggle="modal"  data-ticket="'+row[1]+'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                                   }
                                   return data;
                                }
                            },
                        ],
                "deferRender":    true,
                "scrollX":        true,
                "scrollY":        350,
                "scrollCollapse": true,
                "scroller":       true,
                "searching":      true,
                "paging":         true,
                "info":           true,

                "orderCellsTop":  true,
                "fixedColumns":   true,
            }); 
}

$.ajaxSetup({
   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content'),"Access-Control-Allow-Origin": "*" }
});

$(document.body).on('click', '#viewfarmers', function () {
    $("#farmerList").html('');
    $('#ticketFarmers div.modal-dialog').css('width','');
    var data = {
        _token:     $(this).data('token'),
        _ticket:    $(this).data('ticket')
    }
    $.ajax({
        type: 'POST',
        url: "{{route('postviewfarmers')}}",
        data: data,
        success: function (data) {
            $("#farmerList").html(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
});


$(document.body).on('click', '#viewinvitedfarmers', function () {
    $("#farmerList").html('');
    $('#ticketFarmers div.modal-dialog').css('width','');
    var data = {
        _token:     $(this).data('token'),
        _ticket:    $(this).data('ticket')
    }
    $.ajax({
        type: 'POST',
        url: "{{url('admin/viewinvitedfarmers')}}",
        data: data,
        success: function (data) {
            $("#farmerList").html(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
});

$(document.body).on('click', '#viewhighlightedfarmers', function () {
    var _ticket = $(this).data('ticket');
    window.location.assign("{{url('admin/ticket-highlight')}}/"+_ticket);
});

// Open Modal for ifc Farmer List
$(document.body).on('click', '#viewifcfarmers', function () {
    $('#ticketFarmers div.modal-dialog').css('width','90%');
    $("#farmerList").html('');
    var data = {
        _token:     $(this).data('token'),
        _ticket:    $(this).data('ticket')
    }
    $.ajax({
        type: 'POST',
        url: "{{url('admin/viewifcfarmers')}}",
        data: data,
        success: function (data) {
            $("#farmerList").html(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
});

$(document.body).on('click', '#export-ifcfarmers', function () {
    var data = {
        _token:     $(this).data('token'),
        _ticket:    $(this).data('ticket')
    }
    $.ajax({
        type: 'POST',
        url: "{{url('admin/exportifcfarmers')}}",
        data: data,
        success: function (response, textStatus, request) {
            var a       = document.createElement("a");
            a.href      = response.file; 
            a.download  = response.name;
            document.body.appendChild(a);
            a.click();
            a.remove();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
});

// $(document.body).on('click', '#viewvalidatedfarmers', function () {
//     $("#farmerList").html('');
//     var data = {
//         _token:     $(this).data('token'),
//         _ticket:    $(this).data('ticket')
//     }
//     $.ajax({
//         type: 'POST',
//         url: "{{url('admin/viewvalidatedfarmers')}}",
//         data: data,
//         success: function (data) { 
//             $("#farmerList").html(data);
//         },
//         error: function (jqXHR, textStatus, errorThrown) {
//             console.log(textStatus, errorThrown);
//         }
//     });
// });

$(document.body).on('click', '#export-farmers', function () {
    var data = {
        _token:     $(this).data('token'),
        _ticket:    $(this).data('ticket')
    }
    $.ajax({
        type: 'POST',
        url: "{{route('postexportfarmers')}}",
        data: data,
        success: function (response, textStatus, request) {
                    var a       = document.createElement("a");
                    a.href      = response.file; 
                    a.download  = response.name;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
});

$(document.body).on('click', '#export-invited-farmers', function () {
    var data = {
        _token:     $(this).data('token'),
        _ticket:    $(this).data('ticket')
    }
    $.ajax({
        type: 'POST',
        url: "{{route('postexportallinvitedfarmers')}}",
        data: data,
        success: function (response, textStatus, request) {
                    var a       = document.createElement("a");
                    a.href      = response.file; 
                    a.download  = response.name;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
});

$(document.body).on('click', '#editTicket', function () {
    var ticket_id = $(this).data('ticket');
    window.location = "{{url('admin/editticket')}}"+"/"+ticket_id;
});

$('.sidebar-menu li').click(function(){
    $(this).find('ul').toggle();
});
</script>

<script>
$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});
$(document).on('click','#add-farmers', function(){
   $('#ticketCNumber').css('display','block'); 
})
$(document).on('click','#viewfarmers1', function(){
   var ticket =  $(this).data("ticket");
   $('#newticketId').val(ticket);
});

$(document.body).on('click', '.redButton', function () {
    $('.redButton').html('please wait, it might take few minutes...');
    let datefilter  = $('#datefilter').val();
    let expDate     = datefilter.split(" - ");
    let startDate   = expDate[0];
    let endDate     = expDate[1];

    var data = {
        _token:         $('#token').val(),
        startdate:      startDate,
        enddate:        endDate,
        only_activity:  $('#only_activity').val(),
        search:         $('.dataTables_filter input').val()
    }
    $.ajax({
        type: 'POST',
        url: "{{route('postexportalltickets')}}",
        data: data,        
        success: function (response, textStatus, request) {
                    var a       = document.createElement("a");
                    a.href      = response.file; 
                    a.download  = response.name;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    $('.redButton').html('Export All');
                },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
});
$(document).ready(function () {
    $('#newFarmerAddForm').validate({ // initialize the plugin
        rules: {
            farmerContact: {
                required: true,
                minlength: 6
            },
        },
        submitHandler: function(form) {
             var data = {
                _token:     "{{csrf_token()}}",
                _ticket:    $('#newticketId').val(),
                mobile:    $('#farmerContact').val()
            }
            $.ajax({
                type: 'POST',
                url: "{{route('postaddnewfarmers')}}",
                data: data,
                success: function (response, textStatus, request) {
                    $('#newFarmerAddForm')[0].reset();
                   location.reload();

                        },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    });
});
</script>

<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script> -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript">
$(function() {
    $('input[name="datefilter"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        },
        dateLimit: {
            /*'months': 1,
            'days': -1,*/
            'days': 9
        }
    });
    $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });
    $('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
});
</script>