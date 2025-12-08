@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    
    

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>BDO ATP Plan List</h1>
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">BDO ATP Plan List</li>
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
                                <div class="col-xs-4">
                                    <label class="formlabel" style="display: inline !important;">Meeting Type</label>
                                    <select name="meetingType" id="meetingType" class="frm_txt mdb-select inputs" style="margin: 0 0 10px 5px;">
                                        <option value="">Select Meeting Type</option>
                                        <?php foreach($dataSourceList as $dataSourceList) { ?>
                                            <option value="{{ $dataSourceList->data_source_id }}">{{$dataSourceList->data_source}}</option>
                                        <?php } ?>
                                        <option value="16">E-Meeting</option>
                                    </select>
                                </div>
                                <div class="col-xs-8 form-inline" style="z-index: 2; float:right; padding:0px 0 0 155px;">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 70%;">
                                        <input type="text" placeholder=" Start Date From" class="input-sm form-control" id="startdate"  name="start"   />
                                        <span class="input-group-addon">From</span>
                                        <input type="text" class="input-sm form-control" placeholder=" Start Date TO" id="enddate" name="end"   />
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
                                        <th>BDO</th>
                                        <th>ECN</th>
                                        <th>Assigned DD</th>
                                        <th>Assigned DD ECN</th>
                                        <th>Counterpart FL</th>
                                        <th>Counterpart FL ECN</th>
                                        <th>Plan Date</th>
                                        <th>Product 1</th>
                                        <th>Product 2</th>
                                        <th>Product 3</th>
                                        <th>Crop</th>
                                        <th>Data Source</th>
                                        <th>State</th>
                                        <th>District</th>
                                        <th>Tehsil</th>
                                        <th>Territory</th>
                                        <th>Village</th>
                                        <th>Expected Farmers</th>
                                        <th>Expected PDA</th>
                                        <th>Reason By</th>
                                        <th>Reason By Type</th>
                                        <th>Reason</th>
                                        <th>Created At</th>
                                        <th>ATP No.</th>
                                        <th>Generated Ticket</th>
                                        <th>Presenter Name</th>
                                        <th>Monitor Name</th>
                                        <th>Action</th>
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

<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>

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

<style>
table.dataTable td.dataTables_empty {
    text-align: center;
}
.hiden {
  display: none;
}
    
.myDIV:hover + .hiden {
  display: block;
  color: red;
}
</style>

<script>

    // ><div class="myDIV">Hover over me.</div><div class="hiden">I am shown when someone hovers over the div above.</div>
$( document ).on('ready',function() {
    drawTable();
});

//district
$('#meetingType').on('change',function(){
    var meetingType   = $('#meetingType').val();
    drawTable(meetingType);
});

function drawTable(start_date = $('#datepicker').find('input[name="start"]').val(), end_date = $('#datepicker').find('input[name="end"]').val(), meetingType = $('#meetingType').val()) {
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
                            url:        "{{url('atp/allatplist')}}",
                            data:       { _token: "{{csrf_token()}}", start_date: start_date, end_date: end_date,meetingType: meetingType},
                            error:      function (request, status, error) {
                                        }
                        },
                "columnDefs": [
                                
                                { 
                                    render: function (data, type, full, meta) {
                                        return "<div class='text-wrap width-200'>" + data + "</div>";
                                    },
                                    targets: 13
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
                            { "data": "21" },
                            { "data": "22" },
                            { "data": "23" },
                            { "data": "24" },
                            { "data": "3" },
                            { "data": "4" },                            
                            { "data": "5" },
                            { "data": "6" },
                            { "data": "7" },
                            { "data": "8" },
                            /*{
                                "render": function(data, type, row, meta){
                                    if(type === 'display'){
                                        if(row[25] == 16) {
                                            data = '<a href="javascript:void(0);" id="getMeetingDetails" data-toggle="modal" data-token="{{csrf_token()}}" data-planid="'+row[20]+'">'+row[8]+'</a>';
                                        } else {
                                            data += row[8];
                                        }
                                    }
                                    return data;
                                }
                            },*/
                            { "data": "9" }, 
                            { "data": "10" },
                            { "data": "11" },
                            { "data": "29" },  
                            { "data": "12" },   
                            { "data": "13" },  
                            { "data": "14" },  
                            { "data": "15" },  
                            { "data": "16" },
                            { "data": "17" },
                            { "data": "18" },
                            { "data": "20" },
                            { "data": "19" },
                            { "data": "27" },
                            { "data": "28" },
                            {
                                "render": function(data, type, row, meta){
                                    if(type === 'display'){
                                        if(row[25] == 16) {
                                            if(row[26] == 'Y') {
                                                data = '<a href="javascript:void(0);" id="editPlan" data-token="{{csrf_token()}}" data-toggle="modal"  data-planid="'+row[20]+'" style="padding-right: 10px;"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                                            } else {
                                                data = '<a href="javascript:void(0);" data-token="{{csrf_token()}}" data-toggle="modal"  data-planid="'+row[20]+'" style="padding-right: 10px;color: #d5d5d5; cursor: default;"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                                            }
                                        } else {
                                            data ='';     
                                        }
                                       data += '<a href="javascript:void(0);" id="deletePlan" data-token="{{csrf_token()}}" data-planid="'+row[20]+'">delete</a>';
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
    headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});

$('.sidebar-menu li').click(function(){
    $(this).find('ul').toggle();
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
        url: "{{route('postexportallatp')}}",
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

<script>
$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});

$(document.body).on('click', '#deletePlan', function () {
    var r = confirm("Do you want delete the plan?");
    if(r==true){
        $(this).closest('tr').addClass('selected');
        var ele         = $(this);
        var data = {
            _token:     $(this).data('token'),
            _planid:    $(this).data('planid')
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('atp/atp-delete')}}",
            data:       data,
            success:    function (data) {
                            if(data=='1') {
                                table.row('.selected').remove().draw(false);
                            }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    }
    else {
        $(this).closest('tr').removeClass('selected');
    }
});

$(document.body).on('click', '#editPlan', function () {
    var planid = $(this).data('planid');
    window.location = "{{url('atp/editatp')}}"+"/"+planid;
});

/*$(document.body).on('click', '#getMeetingDetails', function () {
    var planid = $(this).data('planid');
    window.location = "{{url('atp/editatp')}}"+"/"+planid;
});*/

</script>