@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    
    

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Customer Queries</h1>
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Customer Queries</li>
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
                                        <input type="text" class="input-sm form-control" id="startdate"  name="start"   />
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="input-sm form-control" id="enddate" name="end"   />
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
                                        <th>Token Number</th>
                                        <th>Farmer Name</th>
                                        <th>Mobile</th>
                                        <th>State</th>
                                        <th>District</th>
                                        <th>Tehsil</th>
                                        <th>Village</th>
                                        <th>Pincode</th>
                                        <th>Product</th>
                                        <th>Crop</th>
                                        <th>Disease</th>
                                        <th>Call Type</th>
                                        <th>Is Follow Up</th>
                                        <th>Remark</th>
                                        <th>ECN</th>
                                        <th>Query Assigned To</th>
                                        <th>Query Created At</th>
                                        <th>Status</th>
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



<!-- Farmer List -->
<div class="modal fade in" id="closeQueryModel" role="dialog" style="padding-right: 12px;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button data-dismiss="modal" class="close" type="button">×</button>
            </div>
            <div class="modal-body">
                <div id="tabs" class="ui-tabs ui-corner-all ui-widget ui-widget-content">
                    <div id="closeQueryData" aria-labelledby="ui-id-2" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true">
                        <h4>Close Query</h4>
                        <hr/>
                        <textarea id="remark" name="remark"></textarea>
                        <input type='hidden' id='queryId' name='queryId' value='' />
                        <input type='button' id='queryClosed' name='queryClosed' value='submit' />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<!-- Farmer List -->


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

<style>
    table.dataTable td.dataTables_empty {
        text-align: center;    
    }
</style>


<script>
$( document ).on('ready',function() {
    drawTable();
});

function drawTable(start_date = $('#datepicker').find('input[name="start"]').val(), end_date = $('#datepicker').find('input[name="end"]').val()) {
    // Datatable

    searchtype = 'customer'
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'DD-MM-YYYY, hh:mm A' );
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
                            url:        "{{url('customer/allquerylist')}}",
                            data:       { 
                                            _token: "{{csrf_token()}}", 
                                            searchtype: searchtype,
                                            start_date: start_date, 
                                            end_date: end_date,

                                        },
                            error:      function (request, status, error) {
                                        }
                        },
                "columnDefs": [
                                
                                { 
                                    render: function (data, type, full, meta) {
                                        return "<div class='text-wrap width-200'>" + data + "</div>";
                                    },
                                    targets: 14
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
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                        data = '<a href="javascript:void(0);" id="viewfarmers" data-token="{{csrf_token()}}" data-toggle="modal" data-target="#ticketFarmers" data-bdo="'+row[19]+'" data-tokennumber="'+row[1]+'" data-complaintid="'+row[20]+'">view</a>';
                                        if(row[18]!='CLOSED') {
                                            data += '&emsp; <a href="javascript:void(0);" id="editfarmers" data-token="{{csrf_token()}}" data-toggle="modal" data-target="#ticketFarmers" data-bdo="'+row[19]+'" data-tokennumber="'+row[1]+'" data-complaintid="'+row[20]+'">assign</a>';
                                            data += '&emsp; <a href="javascript:void(0);" id="closequery" data-token="{{csrf_token()}}" data-toggle="modal" data-target="#closeQueryModel" data-bdo="'+row[19]+'" data-tokennumber="'+row[1]+'" data-complaintid="'+row[20]+'">close</a>';
                                        }
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

$(document.body).on('click', '#editfarmers', function () {
    $("#farmerList").html('');
    var data = {
        _token:         $(this).data('token'),
        _bdo:           $(this).data('bdo'),
        _tokennumber:   $(this).data('tokennumber'),
        _queryid:       $(this).data('complaintid')
    }
    $.ajax({
        type: 'POST',
        url: "{{route('postbdolist')}}",
        data: data,
        success: function (data) {
            $("#farmerList").html(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
});


$(document.body).on('click', '#assignedTo', function () {
    var _bdolist = $('#bdolist').val();
    var data = {
        _bdolist:   _bdolist,
        _queryId:   $('#queryId').val()
    }
    $.ajax({
        type: 'POST',
        url: "{{route('postupdatecomplaint')}}",
        data: data,
        success: function (data) {
                    if(data==1) {
                        $('.close').click();
                        drawTable();
                    }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
});



$(document.body).on('click', '#viewfarmers', function () {
    $("#farmerList").html('');
    var data = {
        _token:         $(this).data('token'),
        _bdo:           $(this).data('bdo'),
        _tokennumber:   $(this).data('tokennumber'),
        _queryid:       $(this).data('complaintid')
    }
    $.ajax({
        type: 'POST',
        url: "{{route('postbdoresponse')}}",
        data: data,
        success: function (data) {
            $("#farmerList").html(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
});



$(document.body).on('click', '#closequery', function () {
    $('#remark').val('');
    $('#queryId').val($(this).data('complaintid'));
});


$(document.body).on('click', '#queryClosed', function () {
    if($('#remark').val()!='') {
        var r = confirm("Are you sure? You want to close the query.");
        if(r==true){
            var data = {
                _token:         $(this).data('token'),
                _queryid:       $('#queryId').val(),
                _remark:        $('#remark').val()
            }
            $.ajax({
                type: 'POST',
                url: "{{route('postqueryclosed')}}",
                data: data,
                success: function (data) {
                    if(data==1) {
                        $('.close').click();
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
        alert('Please fill remark.');
    }
});

</script>

<script>
$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});

$(document.body).on('click', '.redButton', function () {
    $('.redButton').html('please wait, it might take few minutes...');

 var  s_date = $('#datepicker').find('input[name="start"]').val();
 var  e_date = $('#datepicker').find('input[name="end"]').val();


    var data = {
        _token:         $('#token').val(),
        search:         $('.dataTables_filter input').val(),
        searchtype:     'customer',
        start_date: s_date, 
        end_date: e_date
    }
    $.ajax({
        type: 'POST',
        url: "{{route('postexportallqueries')}}",
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