@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    
    

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Regional Manager Efficiency List</h1>
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Regional Manager Efficiency List</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">    
                                                     
                            
                            <div class="col-xs-6 form-inline" style="z-index: 2; float:right; padding:0px 0 0 155px;">
                                <div class="input-group" style="width: 29%;">
                                    <select id="efficiency_month" class="input-sm form-control"  data-token="{{csrf_token()}}"  name="efficiency_month">
                                        <option value="">Select Month</option>
                                        <?php
                                        $month_list = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec');
                                        foreach($month_list as $key=>$month) { 
                                        $key++;
                                        ?>
                                        <option value="{{$key}}">{{$month}}</option>
                                        <?php    
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="input-group" style="width: 29%;">
                                    <select id="efficiency_year" class="input-sm form-control"  data-token="{{csrf_token()}}"  name="efficiency_year">
                                        <option value="">Select Year</option>
                                        <?php
                                        for($year=2018; $year<=date('Y'); $year++) { ?>
                                            <option value="{{$year}}">{{$year}}</option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <input type="hidden" class="input-sm form-control" name="token" id="token" value="{{csrf_token()}}"/>
                                <button id="clear" class="btn btn-sm ">reset</button>
                                <button type="button" id="dateSearch" class="btn btn-sm btn-primary" style="margin:0;">Search</button>
                            </div>
                            
                            <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>ECN</th>
                                        <th>Name</th>
                                        <th>Month</th>
                                        <th>Year</th>

                                        <th>CONSTANT Percent Deduction</th>
                                        <th>CONSTANT Activity Per Month</th>
                                        <th>CONSTANT Average Farmer Registration</th>

                                        <th>Total Activity</th>
                                        <th>Total Registered Farmer</th>
                                        <th>Total Contacted Farmer</th>
                                        <th>Total Validated Farmer</th>
                                        <th>Total NADA Farmer</th>
                                        <th>Total PNC Farmer</th>
                                        <th>Total BN Farmer</th>

                                        <th>NADA Percent</th>
                                        <th>PNC Percent</th>
                                        <th>BN Percent</th>

                                        <th>Farmer Valid Percent</th>
                                        <th>Total ex valid Farmers</th>
                                        <th>Average Activity Efficiency</th>
                                        <th>Average Validate Farmer </th>                                
                                        <th>Average Farmer Efficiency</th>

                                        <th>NADA Deduction</th>
                                        <th>PNC Deduction</th>
                                        <th>BN Deduction</th>
                                        <th>Efficiency</th>
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


<script>
$("#clear").click(function(){
    $("#efficiency_month, #efficiency_year").val('');
    drawTable();
});

$( document ).on('ready',function() {
    drawTable();
    $('#dateSearch').on('click',function(){
        var efficiency_month    = $('#efficiency_month').val();
        var efficiency_year     = $('#efficiency_year').val();
        drawTable('regional', efficiency_month, efficiency_year);
    });
});

function drawTable(manager_type = "regional", efficiency_month = $('#efficiency_month').val(), efficiency_year = $('#efficiency_year').val()) {
    // Datatable
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'DD-MM-YYYY, hh:mm A' );
    table   =   $('.datatable').DataTable({
                "destroy" : true,
                "language": {
                    searchPlaceholder: ""
                },
                "dom": 'lBfrtip',
                //"dom": 'lBrtip',
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
                "lengthMenu": [[25, 50, 100], ['25 rows', '50 rows', '100 rows']],
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax": {
                            datatype:   'json',
                            type:       "POST",
                            url:        "{{url('efficiency/allbdoefficiency')}}",
                            data:       { _token: "{{csrf_token()}}", manager_type: "regional", efficiency_month: efficiency_month, efficiency_year: efficiency_year},
                            error:      function (request, status, error) {
                                        }
                        },
                "columnDefs": [
                                { orderable: false, targets: 0 },
                            ],
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
                            { "data": "21" },   
                            { "data": "22" },   
                            { "data": "23" },   
                            { "data": "24" },   
                            { "data": "25" }, 
                            { "data": "26" },     
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
        _token:             $('#token').val(),
        manager_type:       "regional",
        search:             $('.dataTables_filter input').val(),
        efficiency_month:   $('#efficiency_month').val(),
        efficiency_year:    $('#efficiency_year').val()
    }
    $.ajax({
        type: 'POST',
        url: "{{route('postexportefficiency')}}",
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