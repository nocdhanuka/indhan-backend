@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>All Demo Material Request List</h1>
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">All Demo Material Request List</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">

                            <div class="col-xs-4 form-inline" style="z-index: 2; padding:10px 0 0 10px;">
                                <label>Needed By Date Range</label>
                                <div class="input-daterange input-group" id="needed_datepicker" style="width: 70%;">
                                    <input type="text" class="input-sm form-control" id="needed_startdate"  name="needed_start"   />
                                    <span class="input-group-addon">to</span>
                                    <input type="text" class="input-sm form-control" id="needed_enddate" name="needed_end"   />
                                    <input type="hidden" class="input-sm form-control" name="token" value="{{csrf_token()}}"/>
                                </div>
                                <button id="needed_clear" class="btn btn-sm ">reset</button>
                                <button type="button" id="needed_dateSearch" class="btn btn-sm btn-primary" style="margin:0;">Search</button>
                            </div>
                            <div class="col-xs-4 form-inline"></div>
                            <div class="col-xs-4 form-inline" style="z-index: 2; float:right; padding:10px 0 0 10px;">
                                <label>Created By Date Range</label>
                                <div class="input-daterange input-group" id="datepicker" style="width: 70%;">
                                    <input type="text" class="input-sm form-control" id="startdate"  name="start"   />
                                    <span class="input-group-addon">to</span>
                                    <input type="text" class="input-sm form-control" id="enddate" name="end"   />
                                    <input type="hidden" class="input-sm form-control" name="token" value="{{csrf_token()}}"/>
                                </div>
                                <button id="clear" class="btn btn-sm ">reset</button>
                                <button type="button" id="dateSearch" class="btn btn-sm btn-primary" style="margin:0;">Search</button>
                            </div>
                        </div>
                        <div class="box-body">                            
                            <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Requested By</th>
                                        <th>Product</th>
                                        <th>Crop</th>
                                        <th>Expected Demos</th>
                                        <th>Need By</th>
                                        <th>Requested Units</th>
                                        <th>Godown Name</th>
                                        <th>Requested Date</th>
                                        <th>RSM Approval Status</th>
                                        <th>RSM Approved Unit</th>
                                        <th>RSM ECN No</th>
                                        <th>RSM Approval Date</th>
                                        <th>Admin Approval Status</th>
                                        <th>Admin Approval Date</th>
                                        <th>Dispatch Status</th>
                                        <th>Dispatch Units</th>
                                        <th>Actual Dispatch Date</th>
                                        <th>Dispatch Status Timestamp</th>
                                        <th>Receiving Status</th>
                                        <th>Receiving Date</th>
                                        <th>Receiving Captured Date</th>
                                        <th>Received Comment</th>
                                        <th>Action</th>
                                        <th>Action</th>
                                        <th>Action</th>
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
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    </div><!-- /.content-wrapper -->
    @include('include.admin.footer')
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <input type="hidden" name="request_id" id="request_id" value="0">
            <div class="modal-header" style="background-color: #228B22;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                 <h4 class="modal-title" id="myModalLabel" style="text-align: center; margin:0px;color: #FFFFF1;">Add / Update Dispatch</h4>
            </div>
            <div class="modal-body" style="border: 1px solid #a8d4b1; margin: 20px;border-radius: 2px;">
                <div class="row">
                    <div class="col-lg-4">
                        <h4>ET of Dispatch</h4>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <input type="datetime-local" class="frm_txt inputs" placeholder="Select Date" name="et_dispatch" id="et_dispatch">
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-lg-4">
                        <h4>Dispatch Status</h4>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <select name="dispatchStatus" id="dispatchStatus" class="frm_txt inputs">
                                <option value="0">Select</option>
                                <option value="1">Dispatch</option>
                            </select>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-lg-4">
                        <h4>Dispatched Units</h4>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <input type="text" name="DispatchUnit" id="DispatchUnit" placeholder="KG/Ltr" class="frm_txt inputs">
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-lg-4">
                        <h4>Actual Dispatch Date</h4>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <input type="datetime-local" class="frm_txt inputs" placeholder="Select Date" name="actual_dispatch_date" id="actual_dispatch_date">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveDispatch">Add/Update</button>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
.dataTables_empty {
    text-indent: 75px;
}
</style>
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
    drawTable();
});

$("#needed_startdate, #needed_enddate").kendoDatePicker({
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
$("#needed_startdate, #needed_enddate").attr("readonly", true);

$("#needed_clear").click(function(){
    $("#needed_startdate, #needed_enddate").val('');
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
        // data.length = 2147483647;
        data.length = 10147483647;
        // data.length = -1;
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

$( document ).on('ready',function() {
    drawTable();
    $('#dateSearch').on('click',function(){
        var startDate   = $('#startdate').val();
        var endDate     = $('#enddate').val();
        drawTable(startDate, endDate);
    });
    $('#needed_dateSearch').on('click',function(){
        var NeededStartDate   = $('#needed_startdate').val();
        var NeededEndDate     = $('#needed_enddate').val();
        drawTable(NeededStartDate, NeededEndDate);
    });
    /*$('.updateDispatch').click(function(){
        let request_id = $(this).data('id');
        $('#request_id').val(request_id);
    });*/

});

function drawTable(start_date = $('#datepicker').find('input[name="start"]').val(), end_date = $('#datepicker').find('input[name="end"]').val()) {
    var startDate = $('#datepicker').find('input[name="start"]').val();
    var endDate = $('#datepicker').find('input[name="end"]').val();
    var needed_startDate = $('#needed_datepicker').find('input[name="needed_start"]').val();
    var needed_endDate = $('#needed_datepicker').find('input[name="needed_end"]').val();
    // console.log("needed_startDate=>"+needed_startDate+" needed_endDate=>"+needed_endDate+"startDate=>"+startDate+" endDate=>"+endDate);
    // Datatable
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'YYYY-MM-DD, hh:mm A' );
    table   =   $('.datatable').DataTable({
                "destroy" : true,
                "language": {
                    searchPlaceholder: "Requested ECN, Product, Crop, Godown"
                },
                "order": [[ 8, "desc" ]],
                "dom": 'lBfrtip',
                "buttons": [
                        // 'pageLength',
                        /*{
                            text: 'Export All',
                            className: 'redButton'
                        },*/
                        {
                            extend: 'excel',
                            text: '<span class="fa fa-file-excel-o"></span> Excel (Current)',
                            title: 'All Requested Material (Current)'
                        },
                        {
                            extend: 'excel',
                            text: '<span class="fa fa-file-excel-o"></span> Excel (All)',
                            action: newExportAction,
                            // messageTop: 'Requested Material Data',
                            exportOptions: {
                                columns: ':visible'
                            },
                            title: 'All Requested Material (All)'
                        },
                        /*{
                            extend: 'colvis',
                            collectionLayout: 'fixed  four-column',
                            columns: ':gt(0)',
                        },*/
                ],
                "lengthMenu": [[25, 50, 100,500], ['25 rows','50 rows','100 rows','500 rows']],
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax": {
                            datatype:   'json',
                            type:       "POST",
                            url:        "{{url('demorequest/alllistrequestdata')}}",
                            data:       { _token: "{{csrf_token()}}", start_date: startDate, end_date: endDate, needed_start_date: needed_startDate, needed_end_date: needed_endDate},
                            error:      function (request, status, error) {
                                        }
                        },
                "columnDefs": [
                                { 
                                    render: function (data, type, full, meta) {
                                        return "<div class='text-wrap width-200'>" + data + "</div>";
                                    },
                                    // targets: 22
                                },
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
                            {
                                "render": function(data, type, row, meta){
                                    if(row[9] === 2) {
                                        data = "Rejected";
                                    } else if(row[9] === 1) {
                                        data = "Approved";
                                    } else {
                                        data = "Open";
                                    }
                                    return data;
                                }
                            },
                            { "data": "10" },
                            { "data": "11" },
                            { "data": "12" },
                            {
                                "render": function(data, type, row, meta){
                                    if(row[13] === 2) {
                                        data = "Rejected";
                                    } else if(row[13] === 1) {
                                        data = "Approved";
                                    } else {
                                        data = "Open";
                                    }
                                    return data;
                                }
                            },
                            { "data": "14" },
                            {
                                "render": function(data, type, row, meta){
                                    if(row[15] === 1) {
                                        data = "Dispatched";
                                    } else {
                                        data = "Not Dispatched";
                                    }
                                    return data;
                                }
                            },
                            { "data": "16" },
                            { "data": "17" },
                            { "data": "18" },
                            {
                                "render": function(data, type, row, meta){
                                    if(row[19] === 1) {
                                        data = "Received";
                                    } else {
                                        data = "Not Received";
                                    }
                                    return data;
                                }
                            },
                            { "data": "20" },
                            { "data": "21" },
                            { "data": "22" },
                            {
                                render: function (data, type, row, meta) {
                                    if(row[13]!=0) {
                                        let status =(row[13]==1) ? 'Approved':'Rejected';
                                        data = "<select disabled='disabled' name='adminStatus' id='adminStatus' onchange='UpdateMaterialAdminStatus(this.value,"+row[23]+");' class='frm_txt inputs' style='padding:5px;'>";
                                        data += "<option value='"+row[13]+"'>"+status+"</option>";
                                        data += "</select>";
                                    } else {
                                        data = "<select name='adminStatus' id='adminStatus' onchange='UpdateMaterialAdminStatus(this.value,"+row[23]+");' class='frm_txt inputs' style='padding:5px;'>";
                                        data += "<option value='0'>Select</option>";
                                        data += "<option value='1'>Approve</option>";
                                        data += "<option value='2'>Reject</option>";
                                        data += "</select>";
                                    }
                                    return data;
                                }
                            },
                            {
                                render: function (data, type, row, meta) {
                                    if(row[15] === 1) {
                                        data = "<button class='btn btn-primary updateDispatch' style='margin: 0px;' id='updateDispatch"+row[23]+"' data-toggle='modal' data-target='#myModal"+row[23]+"' data-id='"+row[23]+"'>Update Dispatch</button>";
                                    } else {
                                        if(row[13]==1) {
                                            data = "<button class='btn btn-primary updateDispatch' style='margin: 0px;' id='updateDispatch"+row[23]+"' data-toggle='modal' data-target='#myModal' data-id='"+row[23]+"'>Update Dispatch</button>";
                                        } else {
                                            data = "<button class='btn btn-primary updateDispatch' style='margin: 0px;' id='updateDispatch"+row[23]+"' data-id='"+row[23]+"'>Update Dispatch</button>";
                                        }
                                    }
                                    return data;
                                }
                            },
                            {
                                render: function (data, type, row, meta) {
                                    data = "<button class='btn btn-primary' style='margin: 0px;' id='openChat' onclick='return chatPage("+row[23]+");'>CHAT</button>";
                                    return data;
                                }
                            }
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

$('.sidebar-menu li').click(function(){
    $(this).find('ul').toggle();
});

$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});
$(document).on('click','#add-farmers', function(){
   $('#ticketCNumber').css('display','block'); 
})
$(document).on('click','#viewfarmers1', function(){
   var ticket =  $(this).data("ticket");
   $('#newticketId').val(ticket);
})

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

function UpdateMaterialAdminStatus(status,requestId) {
    // console.log("status=>"+status+"-requestId=>"+requestId);
    if(status==0) {
        return false;
    }
    let data = {
        _token: $(this).data('token'),
        status: status,
        requestId: requestId
    }
    $.ajax({
        type:       'POST',
        url:        "{{url('demorequest/update-admin-status')}}",
        data:       data,
        success:    function (data) {
                        // console.log(data);
                        if(data=='1') {
                            drawTable();
                        }
                    },
        error:      function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
    });
}

$('#saveDispatch').click(function(){
    let et_dispatch = $('#et_dispatch').val();
    let dispatchStatus = $('#dispatchStatus').val();
    let dispatchunit = $('#DispatchUnit').val();
    let actual_dispatch = $('#actual_dispatch_date').val();
    if(!et_dispatch || !dispatchStatus || !dispatchunit || !actual_dispatch) {
        alert('Please Fill the fields ET of Dispatch, Dispatch Status, Dispatched Units, Actual Dispatch Date');
        return false;
    }
    let request_id = $('#request_id').val();
    let data = {
        _token: $(this).data('token'),
        et_dispatch: et_dispatch,
        dispatchStatus: dispatchStatus,
        dispatchunit: dispatchunit,
        actual_dispatch: actual_dispatch,
        request_id: request_id
    }
    $.ajax({
        type:       'POST',
        url:        "{{url('demorequest/update-dispatch-status')}}",
        data:       data,
        success:    function (data) {
                        // console.log("=>"+data);
                        /*if(data=='1') {
                            document.getElementById("updateDispatch"+request_id).setAttribute("data-target", "#myModal0");
                        }*/
                        $('#myModal').modal('hide');
                        drawTable();
                    },
        error:      function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
    });
});

$('#myModal').on('shown.bs.modal', function(event) {
    var reference_tag = $(event.relatedTarget); 
    var id = reference_tag.data('id');
    // console.log('id=>'+id);
    $('#request_id').val(id);
    $('#et_dispatch').val('');
    $('#dispatchStatus').val('0');
    $('#DispatchUnit').val('');
    $('#actual_dispatch_date').val('');
});

function chatPage($id) {
    // alert("id=>"+$id);
    window.location.assign("{{url('demorequest/material-chat')}}/"+$id);
}

</script>