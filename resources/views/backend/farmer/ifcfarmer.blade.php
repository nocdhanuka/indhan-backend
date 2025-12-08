@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>All IFC Farmer List</h1>
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">IFC Farmer List</li>
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
                                        <input type="text" class="input-sm form-control" id="startdate" name="start"   />
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
                                        <th>Ticket Number</th>
                                        <th>ECN Number</th>
                                        <th>ECN Name</th>
                                        <th>Farmer Name</th>
                                        <th>Farmer Mobile</th>
                                        <th>PDA</th>
                                        <th>Distance</th>
                                        <th>Image</th>
                                        <th>Latitude</th>
                                        <th>Longitude</th>
                                        <th>Image Date</th>
                                        <th>Created Date</th>
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
<?php
$end_date   = date("Y-m-d");
$start_date = date('Y-m-d', strtotime($end_date. ' -2 days'));
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

$("#clear").click(function(){
    $("#startdate, #enddate").val('');
    let start_date  = '<?php echo $start_date; ?>'; 
    let end_date    = '<?php echo $end_date; ?>'; 
    let selected_date = "Selected Date: "+start_date+" - "+end_date;
    $('#selected_date').text(selected_date);
    drawTable(start_date, end_date);
});
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
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->

<style>
table.dataTable td.dataTables_empty {
    text-align: center;
}
</style>

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

function drawTable(start_date = $('#datepicker').find('input[name="start"]').val(), end_date = $('#datepicker').find('input[name="end"]').val()) {
    $('#dateSearch').prop('disabled', true);
    $('#clear').prop('disabled', true);
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'YYYY-MM-DD, hh:mm A' );
    table   =   $('.datatable').DataTable({
                "destroy" : true,
                "language": {
                    searchPlaceholder: "Ticket No, Farmer Name, Mobile, ECN Name"
                },
                "order": [[ 1, "desc" ]],
                "dom": 'lBfrtip',
                "buttons": [
                            {
                                text: 'Export All',
                                className: 'redButton'
                            },
                            {
                                extend: 'colvis',
                                collectionLayout: 'fixed  four-column',
                                columns: ':gt(0)',
                            },
                    ],

                "lengthMenu": [[10,25, 50, 100], ['10 rows','25 rows', '50 rows', '100 rows']],
                "pageLength": 10,
                "processing": true,
                "serverSide": true,
                "ajax": {
                            datatype:   'json',
                            type:       "POST",
                            url:        "{{url('admin/allifcfarmer')}}",
                            data:       { _token: "{{csrf_token()}}", start_date: start_date, end_date: end_date},
                            error:      function (request, status, error) {
                                        }
                        },
                "columnDefs": [
                                { orderable: false, targets: 0 },
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
                            // { "data": "12" },
                            { 
                                "render": function(data, type, row, meta){
                                    if(type === 'display'){
                                        if(row[12] != null) {
                                            data = row[12]/1000;
                                            data = parseFloat(data).toFixed(2);
                                        } else {
                                            data = 0.00;
                                        }
                                    }
                                    return data;
                                }
                            },
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                       data = '<a href="'+row[7]+'" target="_blank"><img src="'+row[7]+'" style="height:50px;" /></a>';
                                   }
                                   return data;
                                }
                            },
                            { "data": "8" },
                            { "data": "9" },
                            { "data": "10" },
                            { "data": "11" },
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
   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});

$('.sidebar-menu li').click(function(){
    $(this).find('ul').toggle();
});
</script>

<script>
$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});

$(document.body).on('click', '.redButton', function () {
    $('.redButton').html('please wait, it might take few minutes...');
    var data = {
        _token:         $('#token').val(),
        startdate:      $('#startdate').val(),
        enddate:        $('#enddate').val(),
        search:         $('.dataTables_filter input').val()
    }
    $.ajax({
        type: 'POST',
        url: "{{route('postexportallifcfarmers')}}",
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
</script>















