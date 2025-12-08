@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    
    

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Syllabus Plan List</h1>
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Syllabus Plan List</li>
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
                                <div class="input-group" style="width: 38%;">
                                    <select id="area_manager"  class="input-sm form-control" data-token="{{csrf_token()}}"  name="area_manager">
                                        <option value="">Select Area Manager</option>
                                        <?php
                                        foreach($area_manager_list as $area_manager) { ?>
                                        <option value="{{$area_manager->area_manager_id}}">{{$area_manager->area_manager_name}} - ({{$area_manager->ECN}})</option>
                                        <?php    
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="input-group" style="width: 30%;">
                                    <select id="bdo"  class="input-sm form-control" data-token="{{csrf_token()}}"  name="bdo">
                                        <option value="">Select BDO</option>
                                    </select>
                                </div>
                                <div class="input-group" style="width: 29%;">
                                    <select id="syllabus_month" class="input-sm form-control"  data-token="{{csrf_token()}}"  name="syllabus_month">
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
                                <div class="input-group" style="width: 38%;">
                                    <select id="syllabus_year"  class="input-sm form-control"  data-token="{{csrf_token()}}"  name="syllabus_year">
                                        <option value="">Select Year</option>
                                        <option value="{{date('Y')}}">{{date('Y')}}</option>
                                    </select>
                                </div>
                                <input type="hidden" class="input-sm form-control" name="token" id="token" value="{{csrf_token()}}"/>
                                <button id="clear" class="btn btn-sm ">reset</button>
                                <button type="button" id="dateSearch" class="btn btn-sm btn-primary" style="margin:0;">Search</button>
                                <button type="button" id="deleteSearch" class="btn btn-sm btn-primary" style="margin:0;">Delete Syllabus</button>
                            </div>
                            
                            <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Area Manager</th>
                                        <th>BDO</th>
                                        <th>Month</th>
                                        <th>Year</th>
                                        <th>Product</th>
                                        <th>Crop</th>
                                        <th>Activities</th>
                                        <th>No of DD</th>
                                        <th>Created At</th>
                                        <th>Total ATP Created</th>
                                        <?php
                                        $user_role = Session::get('login_role_id');
                                        if($user_role=='5') {
                                        ?>
                                        <th>Action</th><?php
                                        }
                                        ?>
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

$("#clear").click(function(){
    $("#area_manager, #syllabus_year, #syllabus_month").val('');
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
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->


<script>
$( document ).on('ready',function() {
    drawTable();
    $('#dateSearch').on('click',function(){
        var area_manager    = $('#area_manager').val();
        var bdo             = $('#bdo').val();
        var syllabus_year   = $('#syllabus_year').val();
        var syllabus_month  = $('#syllabus_month').val();
        drawTable(area_manager, bdo, syllabus_year, syllabus_month);
    });
});

function drawTable(area_manager = $('#area_manager').val(), bdo = $('#bdo').val(), syllabus_year = $('#syllabus_year').val(), syllabus_month = $('#syllabus_month').val()) {
    // Datatable
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'DD-MM-YYYY, hh:mm A' );
    table   =   $('.datatable').DataTable({
                "destroy" : true,
                "language": {
                    searchPlaceholder: ""
                },
                //"dom": 'lBfrtip',
                "dom": 'lBrtip',
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
                            url:        "{{url('syllabus/allsyllabuslist')}}",
                            data:       { _token: "{{csrf_token()}}", area_manager: area_manager, bdo: bdo, syllabus_year: syllabus_year, syllabus_month: syllabus_month},
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
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                       data = moment(row[3], 'MM').format('MMMM');
                                   }
                                   return data;
                                }
                            },
                            { "data": "4" },                            
                            { "data": "5" },
                            { "data": "6" },
                            { "data": "7" },
                            { "data": "8" },
                            { "data": "9" },  
                            { "data": "10" },
                             <?php
                            $user_role = Session::get('login_role_id');
                            if($user_role=='5') {
                            ?>
                            { 
                                "render": function(data, type, row, meta){

                                   if(type === 'display'){
                                        data = '<a href="javascript:void(0);" id="deleteSyllabusByid" data-token="{{csrf_token()}}" data-ticket="'+row[11]+'"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                                        
                                   }
                                   return data;
                                }
                            },
                            <?php
                            }
                            ?>  
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
        area_manager:   $('#area_manager').val(),
        bdo:            $('#bdo').val(),
        syllabus_year:  $('#syllabus_year').val(),
        syllabus_month: $('#syllabus_month').val()
    }
    $.ajax({
        type: 'POST',
        url: "{{route('postexportallsyllabus')}}",
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


$(document.body).on('click', '#deleteSearch', function () {    
    if($('#area_manager').val()!='' && $('#bdo').val()!='' && $('#syllabus_year').val()!='' && $('#syllabus_month').val()!='') {
        var r = confirm("Are You Sure? You Want To Delete All Syllabus.");
        if(r==true){
            var data = {
                _token:         $('#token').val(),
                area_manager:   $('#area_manager').val(),
                bdo:            $('#bdo').val(),
                syllabus_year:  $('#syllabus_year').val(),
                syllabus_month: $('#syllabus_month').val()
            }
            $.ajax({
                type: 'POST',
                url: "{{route('postdeletesyllabus')}}",
                data: data,        
                success: function (data) {
                            if(data=='1') {
                                $("#area_manager, #bdo, #syllabus_year, #syllabus_month").val('');
                                drawTable();
                            }
                        },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    }
    else {
        alert('Please select all search criteria.');
    }
});


$(document.body).on('click', '#deleteSyllabusByid', function () {   
        var id=$(this).data('ticket');

        
    

        var r = confirm("Are You Sure? You Want To Delete Syllabus.");
        if(r==true){
            var data = {
                 _token:         $(this).data('token'),
                    id:          $(this).data('ticket')
               
            }
            $.ajax({
                type: 'POST',
                url: "{{route('postdeletesyllabusbyid')}}",
                data: data,        
                success: function (data) {
                            if(data=='1') {
                                $("#area_manager, #bdo, #syllabus_year, #syllabus_month").val('');
                                drawTable();
                            }
                        },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    
    // else {
    //     alert('Please select all search criteria.');
    // }
});

$('#area_manager').on('change',function(){
    var area_manager   = $('#area_manager').val();
    var data = {
        _token:         $(this).data('token'),
        area_manager:  area_manager
    }
    $.ajax({
        type:       'POST',
        url:        "{{url('area/bdo')}}",
        data:       data,
        success:    function (data) {
                        if(data) {
                            $("#bdo").empty();
                            var jsonData1   = JSON.parse(data);
                            if(typeof jsonData1!='undefined') {
                                var appenddata1 = '';
                                appenddata1 += "<option value = ''>--Select BDO--</option>";
                                for (var i = 0; i < jsonData1.length; i++) {
                                    appenddata1 += "<option value = '" + jsonData1[i].id + "'>" + jsonData1[i].name + " - (" + jsonData1[i].ECN + ") </option>";
                                }
                                $("#bdo").append(appenddata1);
                            }
                            else {
                                var appenddata1 = "<option value = ''>--Select BDO--</option>";
                                $("#bdo").append(appenddata1);
                            }
                            return false;
                        }
                    },
        error:      function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
    });
});
</script>