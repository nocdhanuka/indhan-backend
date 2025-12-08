@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    
    

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Self Participation</h1>
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Self Participation</li>
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
                                <div class="col-xs-8 form-inline" style="z-index: 2; float:right; padding:0px 0 0 155px;">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 70%;">
                                        <input type="text" placeholder="Plan Start Date From" class="input-sm form-control" id="startdate"  name="start"   />
                                        <span class="input-group-addon">From</span>
                                        <input type="text" class="input-sm form-control" placeholder="Plan Start Date TO" id="enddate" name="end"   />
                                        <span class="input-group-addon">to</span>
                                        <input type="hidden" class="input-sm form-control" name="token" id="token" value="{{csrf_token()}}"/>
                                    </div>
                                    <button id="clear" class="btn btn-sm ">reset</button>
                                    <button type="button" id="dateSearch" class="btn btn-sm btn-primary" style="margin:0;">Search</button>
                                </div>
                            </div>   

                             
                            <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Ticket Number</th>
                                        <th>ECN</th>
                                        <th>Username</th>
                                        <th>User Role</th>
                                        <th>Zonal ECN</th>
                                        <th>Zonal Manager</th>
                                        <th>Regional ECN</th>
                                        <th>Regional Manager</th>
                                        <th>Sales Regional ECN</th>
                                        <th>Sales Regional Manager</th>
                                        <th>Area ECN</th>
                                        <th>Area Manager</th>
                                        <th>Sales Area ECN</th>
                                        <th>Sales Area Manager</th>
                                        <th>Arival DateTime</th>
                                        <th>Arival Description</th>
                                        <th>Arival Image URL</th>
                                        <th>Arival Latitude</th>
                                        <th>Arival Longitude</th>
                                        <th>Departure DateTime</th>
                                        <th>Departure Description</th>
                                        <th>Departure Image URL</th>
                                        <th>Departure Latitude</th>
                                        <th>Departure Longitude</th>
                                        <th>Activity Duration</th>
                                        <th>Verified</th>
                                        <th>Distance (metres)</th>
                                        <th>Ticket Date</th>
                                        <th>Created At</th>
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

<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>

<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.common-material.min.css" />
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.min.css" />
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.mobile.min.css" />
<script src="https://kendo.cdn.telerik.com/2018.1.221/js/kendo.all.min.js"></script>  
<script>
$( document ).on('ready',function() {
    drawTable();
    $('#dateSearch').on('click',function(){
        var startDate   = $('#startdate').val();
        var endDate     = $('#enddate').val();
        drawTable(startDate, endDate);
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
    drawTable();
});
</script>

<script>
/*$( document ).on('ready',function() {
    drawTable();
});*/

function drawTable(start_date = $('#datepicker').find('input[name="start"]').val(), end_date = $('#datepicker').find('input[name="end"]').val()) {
    // Datatable
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'YYYY-MM-DD, hh:mm A' );
    table   =   $('.datatable').DataTable({
                "destroy" : true,
                "language": {
                    searchPlaceholder: "Ticket, BDO, Data Source, State, District, Tehsil, Village, Product, Crop"
                },
                "dom": 'lBfrtip',
                "buttons": [
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
                            datatype:   'json',
                            type:       "POST",
                            url:        "{{url('selfparticipation/allquerylist')}}",
                            data:       { _token: "{{csrf_token()}}", start_date: start_date, end_date: end_date},
                            error:      function (request, status, error) {
                                        }
                        },
                "columnDefs": [
                                
                                { 
                                    render: function (data, type, full, meta) {
                                        return "<div class='text-wrap width-200'>" + data + "</div>";
                                    },
                                    // targets: 5
                                },
                            ],
                "columns": [
                            { 
                                render:   function (data, type, row, meta) {
                                                        return meta.row + meta.settings._iDisplayStart + 1;
                                                    } 
                            },
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                        var str1 = row[1];
                                        if(str1!=undefined) {
                                            var str2 = str1.replace('admin/','');
                                            data = "<a href='highlight-detail/"+str2+"' target='_blank'>"+str1+"</a>";
                                        }
                                        else {
                                            data = "";
                                        }
                                   }
                                   return data;
                                }
                            },
                            { "data": "3" },
                            { "data": "4" },
                            { "data": "5" },
                            { "data": "20" }, 
                            { "data": "21" }, 
                            { "data": "22" }, 
                            { "data": "23" }, 
                            { "data": "24" }, 
                            { "data": "25" }, 
                            { "data": "26" }, 
                            { "data": "27" }, 
                            { "data": "28" }, 
                            { "data": "29" },
                            { "data": "6" },
                            { "data": "7" },
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                        var str1 = row[8];
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
                            { "data": "9" },
                            { "data": "10" },
                            { "data": "11" },
                            { "data": "12" },
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                        var str1 = row[13];
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
                            { "data": "14" },
                            { "data": "15" },
                            { "data": "16" },
                            { "data": "30" },                           
                            { "data": "31" },                           
                            { "data": "17" },
                            { "data": "18" },
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

    start_date = $('#datepicker').find('input[name="start"]').val();

    end_date = $('#datepicker').find('input[name="end"]').val();


    var data = {
        _token:         $('#token').val(),
        search:         $('.dataTables_filter input').val(),
        start_date: start_date, 
        end_date: end_date
    }
    $.ajax({
        type: 'POST',
        url: "{{route('postexportallparticipants')}}",
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