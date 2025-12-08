@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>All Sugarmill ATP List</h1>
            @if(Session::has('message'))
                <div class="col-md-12" style="margin-top:20px;">
                    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                </div>
            @endif
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Sugarmill List</li>
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
                                <div class="col-xs-2"></div>
                                <div class="col-xs-2">
                                    <select name="sugarmill_id" id="sugarmill_id" style="float: right;">
                                        <option value="">--Select Sugarmill--</option>
                                        <?php foreach ($sugarmill as $sugar) { ?>
                                            <option value="{{$sugar->sugarmill_id}}">{{$sugar->name}}</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-xs-2">&nbsp;</div>
                                <div class="col-xs-6 form-inline" style="z-index: 2; float:right; padding:0px 0 0 155px;">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 70%;">
                                        <input type="text" class="input-sm form-control" id="startdate" name="start"   />
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="input-sm form-control" id="enddate" name="end"   />
                                        <input type="hidden" class="input-sm form-control" name="token" id="token" value="{{csrf_token()}}"/>
                                    </div>
                                    <button id="clear" class="btn btn-sm ">reset</button>
                                    <button type="button" id="dateSearch" class="btn btn-sm btn-primary" style="margin:0;">Search</button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Date</th>
                                        <th>Ticket No</th>
                                        <th>Created For</th>
                                        <th>Assigned FL ECN</th>
                                        <th>Manager ECN</th>
                                        <th>Sugarmill ID</th>
                                        <th>Sugarmill Name</th>
                                        <th>Office Type</th>
                                        <th>Sugarmill Type</th>
                                        <th>Sugarmill Category</th>
                                        <th>State</th>
                                        <th>District</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <!-- <th>Action</th> -->
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
$("#clear").click(function() {
    $("#startdate, #enddate").val('');
    drawTable();
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
<!-- AdminLTE App -->
<script src="{{asset('assets/admin/dist/js/app.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->
<style>
table.dataTable td.dataTables_empty {
    text-align: center;
}
</style>

<script>
$(document).on('ready',function() {
    drawTable();
    $('#dateSearch').on('click',function(){
        drawTable();
    });
});

$('#sugarmill_id').on('change',function(){
    drawTable();
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

function drawTable() {
    let start_date  = $('#datepicker').find('input[name="start"]').val();
    let end_date    = $('#datepicker').find('input[name="end"]').val();
    let sugarmill_id = $('#sugarmill_id').val();

    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'YYYY-MM-DD, hh:mm A' );
    table   =   $('.datatable').DataTable({
                "destroy" : true,
                "language": {
                    searchPlaceholder: "Ticket, Sugarmill Name, State, District, Address"
                },
                "dom": 'lBfrtip',
                "select": true,
                "buttons": [
                        //'pageLength',
                        /*{
                            extend: 'excel',
                            text: '<span class="fa fa-file-excel-o"></span> Excel (Current)',
                            title: 'BDO List (Current)'
                        },*/
                        {
                            extend: 'excel',
                            text: '<span class="fa fa-file-excel-o"></span> Excel (All)',
                            action: newExportAction,
                            exportOptions: {
                                columns: ':visible'
                                //columns: [ 2, ':visible' ]
                            },
                            title: 'Sugarmill ATP List (All)'
                        },
                        {
                            extend: 'colvis',
                            collectionLayout: 'fixed  four-column',
                            columns: ':gt(0)',
                        },
                    ],
            "lengthMenu": [[25, 50, 100, -1], ['25 rows', '50 rows', '100 rows', 'All']],
            "pageLength": 25,
            "processing": true,
            "serverSide": true,
            "ajax": {
                        datatype:   'json',
                        type:       "POST",
                        url:        "{{url('sugarmill/all-atp-list')}}",
                        data:       { _token: "{{csrf_token()}}", start_date: start_date, end_date: end_date, sugarmill_id: sugarmill_id }
                    },
            "columnDefs": [
                            { orderable: false, targets: 0 },
                            { "className": "text-center", orderable: false, targets: 2 }
                            //{ "targets": 2, "visible": false},
                        ],
            "columns": [
                        {
                            render:   function (data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
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
                        /*{
                            "render": function(data, type, row, meta){
                                if(type === 'display'){
                                   data = '<a href="javascript:void(0);" id="editSugarmill" data-token="{{csrf_token()}}" data-report="'+row[1]+'">Edit</a>';
                                }
                                return data;
                            }
                        }*/
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
</script>

<script>
$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});
</script>
