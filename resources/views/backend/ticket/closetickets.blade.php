@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Closed Ticket List</h1>
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Closed Ticket List</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">

                            <div class="row">
                                <div class="col-xs-6"><span id="selected_date" style="float: right; font-weight: bold;"></span></div>
                                <div class="col-xs-6 form-inline" style="z-index: 2; float:right; padding:0px 0 0 155px;">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 70%;">
                                        <input type="text" class="input-sm form-control" id="startdate"  name="start"   />
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="input-sm form-control" id="enddate" name="end"   />
                                        <input type="hidden" class="input-sm form-control" name="token" id="token" value="{{csrf_token()}}"/>
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
                                        <th>ECN</th>
                                        <th>User</th>
                                        <th>Role</th>
                                        <th>Language</th>
                                        <th>Data Source</th>
                                        <th>State</th>
                                        <th>District</th>
                                        <th>Tehsil</th>
                                        <th>Village</th>
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
                                        <th>Total IFC Farmers</th>
                                        <th>Special Participants</th>
                                        <th>Activity Duration</th>
                                        <th>Activity Distance</th>
                                        <th>Demo Number</th>
                                        <th>Status</th>
                                        <th>Validated Farmers</th>
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

<div class="modal fade in" id="ticketFarmers2" role="dialog" style="padding-right: 12px;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <!-- <button data-dismiss="modal" class="close" type="button">×</button> -->
            </div>
            <div class="modal-body">
                <div id="tabs" class="ui-tabs ui-corner-all ui-widget ui-widget-content">
                    <div id="farmerList1" aria-labelledby="ui-id-2" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true">
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<?php
$end_date   = date("Y-m-d");
$start_date = date('Y-m-d', strtotime($end_date. ' -1 months'));
?>

<!-- ./wrapper --><!-- jQuery 2.1.4 -->
<script src="{{asset('assets/admin/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
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
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('assets/admin/dist/js/app.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->

<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>

<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.common-material.min.css" />
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.min.css" />
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.mobile.min.css" />
<script src="https://kendo.cdn.telerik.com/2018.1.221/js/kendo.all.min.js"></script>  
<script>
$( document ).on('ready',function() {
    // drawTable();
    $('#dateSearch').on('click',function(){
        var startDate   = $('#startdate').val();
        var endDate     = $('#enddate').val();
        let start_date = $('#datepicker').find('input[name="start"]').val();
        let end_date = $('#datepicker').find('input[name="end"]').val();
        if((start_date == "") || (end_date == "")) {
            $('#filter-error').text('Please select start and end date');
            return false;
        } else {
            $('#filter-error').text('');
            let selected_date = "Selected Date: "+startDate+" - "+endDate;
            $('#selected_date').text(selected_date);
            drawTable(startDate, endDate);
        }
    });
});
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

$("#clear").click(function(){
    $("#startdate, #enddate").val('');
    let start_date  = '<?php echo $start_date; ?>'; 
    let end_date    = '<?php echo $end_date; ?>'; 
    let selected_date = "Selected Date: "+start_date+" - "+end_date;
    $('#selected_date').text(selected_date);
    drawTable(start_date, end_date);
});
</script>

<style>
/* Ensure that the demo table scrolls */
th, td { white-space: nowrap; }
div.dataTables_wrapper {
    width: 100%;
    margin: 0 auto;
}
th input {
    width: 100%;
}
</style>

<script>
// Export All Data In Excel
var oldExportAction = function (self, e, dt, button, config) {
    if (button[0].className.indexOf('buttons-excel') >= 0) {
        if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
            $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
        } else {
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

function buildSelect( table ) {
  table.columns().every( function () {
    var column = table.column( this, {search: 'applied'} );
    var select = $('<select><option value=""></option></select>')
    .appendTo( $(column.footer()).empty() )
    .on( 'change', function () {
      var val = $.fn.dataTable.util.escapeRegex(
        $(this).val()
      );

      column
      .search( val ? '^'+val+'$' : '', true, false )
      .draw();
    } );

    column.data().unique().sort().each( function ( d, j ) {
      select.append( '<option value="'+d+'">'+d+'</option>' );
    } );
    
    // The rebuild will clear the exisiting select, so it needs to be repopulated
    var currSearch = column.search();
    if ( currSearch ) {
      select.val( currSearch.substring(1, currSearch.length-1) );
    }
  } );
}   
    

function drawTable(start_date = $('#datepicker').find('input[name="start"]').val(), end_date = $('#datepicker').find('input[name="end"]').val()) {
    $('#dateSearch').prop('disabled', true);
    $('#clear').prop('disabled', true);
    // Datatable
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'YYYY-MM-DD, hh:mm A' );
    table   =   $('.datatable').DataTable({
                "language": {
                    searchPlaceholder: "Ticket, BDO, Data Source, State, District, Tehsil, Village, Product, Crop"
                },
                "dom": 'lBfrtip',
                "destroy" : true,
                "buttons": [
                            {
                                extend: 'excel',
                                text: '<span class="fa fa-file-excel-o"></span> Excel (Current)',
                                title: 'All Closed Tickets (Current)'
                            },
                            {
                                extend: 'excel',
                                text: '<span class="fa fa-file-excel-o"></span> Excel (All)',
                                action: newExportAction,
                                messageTop: 'Dhanuka Ticket Data',
                                exportOptions: {
                                    columns: ':visible'
                                },
                                title: 'All Closed Tickets (All)'
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
                "ajax": {
                            dataType:   "json",
                            type:       "POST",
                            url:        "{{url('admin/allcloseticket')}}",
                            data:       { _token: "{{csrf_token()}}",
                                            start_date: start_date, 
                                            end_date: end_date,
                                        }
                        },
                "columnDefs": [
                                
                                { 
                                    render: function (data, type, full, meta) {
                                        return "<div class='text-wrap width-200'>" + data + "</div>";
                                    },
                                    targets: 20
                                },
                            ],
                "initComplete": function( settings, json ) {
                    $('#dateSearch').prop('disabled', false);
                    $('#clear').prop('disabled', false);
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
                                       data = '<a href="#" onclick="event.preventDefault()" id="viewifcfarmers" data-token="{{csrf_token()}}" data-toggle="modal" data-target="#ticketFarmers" data-ticket="'+row[1]+'">'+row[36]+'</a>';
                                   }
                                   return data;
                                }
                            },
                            { "data": "23" },
                            // { "data": "37" },
                            // { "data": "38" },
                            { 
                                "render": function(data, type, row, meta){
                                    if(type === 'display'){
                                        if(row[37] != null) {
                                            let decimalTimeString = parseFloat(row[37]).toFixed(2);
                                            let n = new Date(0,0);
                                            n.setSeconds(+decimalTimeString * 60 * 60);
                                            data = n.toTimeString().slice(0, 8);
                                        } else {
                                            data = row[37]
                                        }
                                        // data = parseFloat(row[37]).toFixed(2);
                                    }
                                    return data;
                                }
                            },
                            {
                                "render": function(data, type, row, meta){
                                    if(type === 'display'){
                                        if(row[38] != null) {
                                            data = row[38]/1000;
                                            data = parseFloat(data).toFixed(2);
                                        } else {
                                            data = 0.00;
                                        }
                                    }
                                    return data;
                                }
                            },
                            { "data": "24" },
                            { "data": "25" },
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                       data = '<a href="javascript:void(0);" id="viewvalidatedfarmers" data-token="{{csrf_token()}}" data-toggle="modal" data-target="#ticketFarmers2" data-ticket="'+row[1]+'">'+row[26]+'</a>';
                                   }
                                   return data;
                                }
                            }, 
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                        var str1 = row[27];
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
                            { "data": "28" },
                            { "data": "40" },
                            { "data": "41" },
                            { "data": "42" },
                            { "data": "43" },
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                        var str1 = row[44];
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
                            { "data": "29" },
                            { "data": "30" },
                            { "data": "31" },
                            { "data": "32" },
                            { "data": "33" },
                            { "data": "39" },
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
                // "fixedHeader":    true,
                "fixedColumns":   true,
            }); 
}

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

$(document.body).on('click', '#viewvalidatedfarmers', function () {
    $("#farmerList1").html('');
    $('#ticketFarmers div.modal-dialog').css('width','');
    var data = {
        _token:     $(this).data('token'),
        _ticket:    $(this).data('ticket')
    }
    $.ajax({
        type: 'POST',
        url: "{{url('admin/viewvalidatedfarmers')}}",
        data: data,
        success: function (data) { 
            $("#farmerList1").html(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
});


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

$(document.body).on('click', '#editTicket', function () {
    var ticket_id = $(this).data('ticket');
    window.location = "{{url('admin/editticket')}}"+"/"+ticket_id;
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
</script>

<script>
$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});
</script>
































