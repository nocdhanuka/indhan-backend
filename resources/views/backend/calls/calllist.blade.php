@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<style type="text/css">
    .filterBtn{    margin-bottom: 40px;
    margin-top: 10px;
    margin-right: 50px;}
    
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Call List</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Call List</li>
        </ol>
    </section>
    
            <div class="filterBtn">
                <button id="filterCallBtn" class="btn btn-info pull-right" data-toggle="modal" data-target="#callFilter">Filter</button>
            </div>
<?php //echo "<pre>";print_r($merged);die; ?>
       <section class="content">
        <div class="row">

            <div class="col-xs-12">

                <div class="box">
                    <!-- /.box-header -->
                   <div class="box-body">
                    <div class="col-xs-6 form-inline" style="z-index: 2; float:right; padding:0px 0 0 155px;">
                        <div class="input-daterange input-group" id="datepicker" style="width: 70%;">
                            <input type="text" class="input-sm form-control" id="startdate"  name="start"   />
                            <span class="input-group-addon">to</span>
                            <input type="text" class="input-sm form-control" id="enddate" name="end"   />
                            <input type="hidden" class="input-sm form-control" name="token" id="token" value="{{csrf_token()}}"/>
                        </div>
                        <button id="clear" class="btn btn-sm ">reset</button>
                        <button type="button" id="dateSearch" class="btn btn-sm btn-primary" style="margin:0;">Search</button>
                    </div>

                    <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>User Name</th>
                                <th>User ECN</th>
                                <th>Ticket Number</th>
                                <th>Lead Source</th>
                                <th>Name</th>
                                <th>Lead Phone no</th>
                                <th>Date of call</th>
                                <th>Lead ID</th>
                                <th>Distributor Name</th>
                                <th>Product</th>
                                <th>Crop</th>
                                <th>Lead Status</th>
                                <th>Lead Open Date</th>
                                <th>Lead Close Date</th>
                                <th>Diffrence Open & Close Date</th>
                            </tr>
                        </thead>
                    </table>

                    <table id="onfilter"  style="display: none;" class="table table-bordered table-striped datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>User Name</th>
                                        <th>User ECN</th>
                                        <th>Ticket Number</th>
                                        <th>Lead Source</th>
                                        <th>Name</th>
                                        <th>Lead Phone no</th>
                                        <th>Date of call</th>
                                        <th>Lead ID</th>
                                        <th>Distributor Name</th>
                                        <th>Product</th>
                                        <th>Crop</th>
                                        <th>Lead Status</th>
                                        <th>Lead Open Date</th>
                                        <th>Lead Close Date</th>
                                        <th>Diffrence Open & Close Date</th>
                                    </tr>
                                </thead>
                            </table>

                    <div class="success"></div>

                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
</section><!-- /.content -->
<!-- Modal -->
  <div class="modal fade" id="callFilter" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Filter</h4>
        </div>
        <div class="modal-body">
            <form id="callFIlterForm">
         <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>RSM</label>
                    <select class="form-control" name="filter_RSM" id="filter_RSM">
                        <option value="">Select RSM</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>ASM</label>
                    <select class="form-control" name="filter_ASM" id="filter_ASM">
                        <option value="">Select ASM</option>
                    </select>
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>FL</label>
                    <select class="form-control" name="filter_FL" id="filter_FL">
                        <option value="">Select FL</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>DD</label>
                    <select class="form-control" name="filter_DD" id="filter_DD">
                        <option value="">Select DD</option>
                    </select>
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Crop</label>
                    <select class="form-control" name="filter_crop" id="filter_crop">
                        <option value="">Select Crop</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Product</label>
                    <select class="form-control" name="filter_product" id="filter_product">
                        <option value="">Select Product</option>
                    </select>
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Start Date</label>
                     <input type="text" class="form-control" name="filter_start" id="filter_start" placeholder="Select Date" readonly="">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>End Date</label>
                    <input type="text" class="form-control" name="filter_end" id="filter_end" placeholder="Select Date" readonly="">
                </div>
            </div>
         </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" id="callSubmit" class="btn btn-default">Submit</button>
        </div>
      </div>
    </div>
  </div>

<!-- </div> -->
<!-- Main content -->

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
<script type="text/javascript" src="{{asset('assets/js/jquery-ui.min.js')}}"></script>


<!-- SlimScroll -->
<script src="{{asset('assets/admin/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('assets/admin/dist/js/app.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->

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
        $('#onfilter_wrapper').css('display','none');
        $('#onfilter').hide();
        $("#startdate, #enddate").val('');
        drawTable();
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

table.dataTable td.dataTables_empty {
        text-align: center;    
    }
</style>
<script type="text/javascript">
     $(document).ready(function() {
      // $('#example1').DataTable();
        $("#filter_start").datepicker({
        minDate: 0,
        dateFormat: 'yy-mm-dd',
        onSelect: function(selected_date) {
            $("#filter_end").datepicker("option", "minDate", selected_date);
        }
    });
 $("#filter_end").datepicker({
        minDate: 0,
        dateFormat: 'yy-mm-dd',
        onSelect: function(selected_date) {
            $("#filter_start").datepicker("option", "maxDate", selected_date);
        }
    });
    });
</script>
<script>
$( document ).on('ready',function() {
    $('#dateSearch').on('click',function(){
         $('#onfilter_wrapper').css('display','none');
        $('#onfilter').hide();
        var startDate   = $('#startdate').val();
        var endDate     = $('#enddate').val();
        drawTable(startDate, endDate);
    });
});

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
</script>
<script>    
// Datatable
$( document ).on('ready',function() {
    drawTable();
    //$('#onfilter_wrapper').css('display','none');
});
function drawTable(start_date = $('#datepicker').find('input[name="start"]').val(), end_date = $('#datepicker').find('input[name="end"]').val()) { 

     // Datatable
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'YYYY-MM-DD, hh:mm A' );
    table = $('#example1').DataTable({
        "destroy" : true,
                "language": {
                    searchPlaceholder: ""
                },
                "dom": 'lBfrtip',
                "select": true,
                "buttons": [                        
                            //'pageLength',
                            {
                                extend: 'excel',
                                text: '<span class="fa fa-file-excel-o"></span> Excel (Current)',
                                title: 'Call List (Current)'
                            },
                            {
                                extend: 'excel',
                                text: '<span class="fa fa-file-excel-o"></span> Excel (All)',
                                action: newExportAction,
                                exportOptions: {
                                    columns: ':visible'
                                    //columns: [ 2, ':visible' ]
                                },
                                title: 'Call List (All)'
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
            dataType:   "json",
            type:       "POST",
            url:        "{{url('call/call-list')}}",
            data:       { _token: "{{csrf_token()}}",start_date: start_date, end_date: end_date}
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
        { "data": "2" },
        { "data": "1" },
        { "data": "3" },
        { "data": "15" },
        { "data": "4" },
        { "data": "5" },
        { "data": "6" },
        { "data": "7" },
        { "data": "9" },
        { "data": "10" },
        { "data": "14" },
        { "data": "8" },
        { "data": "11" },
        { "data": "12" },
        { "data": "13" }
        


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
                //"fixedHeader":    true,
                "fixedColumns":   true,
            }); 


};   


</script>

<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
<script type="text/javascript">
   

    $(document.body).on('click', '#filterCallBtn', function () {
            $.ajax({
                type:       'GET',
                url:        "{{url('call/call-filter')}}",
                success:    function (data) {
                    var data = $.parseJSON(data);
                    var filter_RSM = '<option value="">Select RSM</option>';
                    var filter_crop = '<option value="">Select Crop</option>';
                    $.each(data.rmsList, function(key,value)
                    {
                        filter_RSM += '<option value="'+value.sales_regional_manager_id+'">' + value.sales_regional_manager_name + '</option>';
                        
                    });
                     $.each(data.cropList, function(key,value)
                    {
                        filter_crop += '<option value="'+value.crop_id+'">' + value.crop + '</option>';
                        
                    });               
                    $('#filter_RSM').html(filter_RSM);
                    $('#filter_crop').html(filter_crop);
                    $('#filter_product').html('<option value="">Select Product</option>');
                 
                },
                error:      function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });

    });
    $(document.body).on('click', '#filter_RSM', function () {
        var option = $(this).val();
        var type = 'ASM';
            $.ajax({
                type:'POST',
                url: "{{url('call/call-filter-ajax')}}",
                 data:{ _token: "{{csrf_token()}}",option:option,type:type},
                success:function (data) {
                    var data = $.parseJSON(data);
                    var filter_ASM = '<option value="">Select ASM</option>';
                    $.each(data, function(key,value)
                    {
                        filter_ASM += '<option value="'+value.sales_area_manager_id+'">' + value.sales_area_manager_name + '</option>';
                        
                    });                  
                    $('#filter_ASM').html(filter_ASM);
                    $('#filter_FL').html('<option value="">Select FL</option>');
                    $('#filter_DD').html('<option value="">Select DD</option>');
                },
                error:      function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
    });

    $(document.body).on('click', '#filter_ASM', function () {
        var option = $(this).val();
        var type = 'FL';
            $.ajax({
                type:       'POST',
                url:        "{{url('call/call-filter-ajax')}}",
                 data:       { _token: "{{csrf_token()}}",option:option,type:type},
                success:    function (data) {
                    var data = $.parseJSON(data);
                    var filter_FL = '<option value="">Select FL</option>';
                    $.each(data, function(key,value)
                    {
                        filter_FL += '<option value="'+value.id+'">' + value.name + '</option>';
                        
                    });                  
                    $('#filter_FL').html(filter_FL);
                    $('#filter_DD').html('<option value="">Select DD</option>');
                },
                error:      function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
    });
    $(document.body).on('click', '#filter_FL', function () {
        var option = $(this).val();
        var type = 'DD';
            $.ajax({
                type:       'POST',
                url:        "{{url('call/call-filter-ajax')}}",
                 data:       { _token: "{{csrf_token()}}",option:option,type:type},
                success:    function (data) {
                    var data = $.parseJSON(data);
                    var filter_DD = '<option value="">Select DD</option>';
                    $.each(data, function(key,value)
                    {
                        filter_DD += '<option value="'+value.dd_id+'">' + value.name + '</option>';
                        
                    });                  
                    $('#filter_DD').html(filter_DD);

                },
                error:      function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
    });

    $(document.body).on('click', '#filter_crop', function () {
        var option = $(this).val();
        var type = 'crop';
            $.ajax({
                type:       'POST',
                url:        "{{url('call/call-filter-ajax')}}",
                 data:       { _token: "{{csrf_token()}}",option:option,type:type},
                success:    function (data) {
                    var data = $.parseJSON(data);
                    var filter_product = '<option value="">Select Product</option>';
                    $.each(data, function(key,value)
                    {
                        filter_product += '<option value="'+value.product_id+'">' + value.product + '</option>';
                        
                    });                  
                    $('#filter_product').html(filter_product);
                },
                error:      function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
    });

    $(document.body).on('click', '#callSubmit', function (e) {
        var filter_RSM = $('#filter_RSM').val();
        var filter_ASM = $('#filter_ASM').val();
        var filter_FL = $('#filter_FL').val();
        var filter_DD = $('#filter_DD').val();
        var filter_crop = $('#filter_crop').val();
        var filter_product = $('#filter_product').val();
        // debugger;
        //=========================================   
        $('#callFilter').modal('hide');
        $('#example1_wrapper').hide();
        //$('#onfilter_wrapper').css('display','block');
        $('#onfilter').show();
        $.fn.dataTable.ext.errMode = 'none';
        $.fn.dataTable.moment( 'DD-MM-YYYY, hh:mm A' );
        table = $('#onfilter').DataTable({
                    "destroy" : true,
                    "language": {
                        searchPlaceholder: ""
                    },
                    // "order": [[ 4, "desc" ]],
                    "dom": 'lBfrtip',
                    "buttons": [
                                {
                                extend: 'excel',
                                text: '<span class="fa fa-file-excel-o"></span> Excel (Current)',
                                title: 'Call List (Current)'
                            },
                            {
                                extend: 'excel',
                                text: '<span class="fa fa-file-excel-o"></span> Excel (All)',
                                action: newExportAction,
                                exportOptions: {
                                    columns: ':visible'
                                    //columns: [ 2, ':visible' ]
                                },
                                title: 'Call List (All)'
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
                                url:        "{{url('call/call-list-filter')}}",
                                data:       { _token: "{{csrf_token()}}",filter_RSM:filter_RSM,filter_ASM:filter_ASM,filter_FL:filter_FL,filter_DD:filter_DD,filter_crop:filter_crop,filter_product:filter_product},
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
        { "data": "2" },
        { "data": "1" },
        { "data": "3" },
        { "data": "15" },
        { "data": "4" },
        { "data": "5" },
        { "data": "6" },
        { "data": "7" },
        { "data": "9" },
        { "data": "10" },
        { "data": "14" },
        { "data": "8" },
        { "data": "11" },
        { "data": "12" },
        { "data": "13" }
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
                //"fixedHeader":    true,
                "fixedColumns":   true,
        
    });

});


    

</script>





















