@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>All Company Wise Data List</h1>
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">All Company Wise Data List</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="col-lg-3">
                                <label class="formlabel">State Name</label>
                                <select id="state_name" class="frm_txt mdb-select inputs"  name="state_name">
                                    <option value="" >Choose State</option>
                                    <?php foreach($states as $state) { ?>
                                        <option value="{{$state->state_id}}">{{$state->state}}</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label class="formlabel">District Name</label>
                                <select id="district_name" class="frm_txt mdb-select inputs"  name="district_name">
                                    <option value="" >Choose District</option>
                                    <?php foreach($districts as $district) { ?>
                                        <option value="{{$district->district_id}}">{{$district->district}}</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-xs-6 form-inline" style="z-index: 2; float:right; padding:40px 0 0 155px;">
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
                                        <th>ECN Number</th>
                                        <th>ECN Name</th>
                                        <th>State</th>
                                        <th>District</th>
                                        <th>Class</th>
                                        <th>Formulation</th>
                                        <th>Volume</th>
                                        <th>Value</th>
                                        <th>Adama</th>
                                        <th>Atul</th>
                                        <th>Bayer</th>
                                        <th>BASF</th>
                                        <th>Bharat Certis</th>
                                        <th>Bio Stadt</th>
                                        <th>Cheminova</th>
                                        <th>Coromandal Inter Ltd</th>
                                        <th>Coromandel Agri Co</th>
                                        <th>Crystal</th>
                                        <th>Corteva</th>
                                        <th>Devidayal</th>
                                        <th>Dhanuka Agritech</th>
                                        <th>Excel Crop Care</th>
                                        <th>F.I.L.</th>
                                        <th>F.M.C.</th>
                                        <th>Gharda Chemicals</th>
                                        <th>Godrej Agrovet Ltd</th>
                                        <th>G.S.P.</th>
                                        <th>Hindustan Insecticides Ltd</th>
                                        <th>Hindustan Pulverising</th>
                                        <th>HPM</th>
                                        <th>Heranba</th>
                                        <th>Indofil</th>
                                        <th>Indo Gulf</th>
                                        <th>Insecticides India Ltd</th>
                                        <th>Ichiban</th>
                                        <th>I.P.L.</th>
                                        <th>JU Agri Science</th>
                                        <th>Jubilant</th>
                                        <th>Krishi Rasayan</th>
                                        <th>Meghmani</th>
                                        <th>Nagarjuna Agri Chem</th>
                                        <th>Nichino</th>
                                        <th>Multiplex</th>
                                        <th>Mahindra</th>
                                        <th>PI Industries</th>
                                        <th>Parijat</th>
                                        <th>Punjab Chemicals</th>
                                        <th>Rallis</th>
                                        <th>Safex</th>
                                        <th>Shivalik Crop</th>
                                        <th>Sudarshan Chemical</th>
                                        <th>Sulphur Mills</th>
                                        <th>Sumitomo</th>
                                        <th>Swal Corporation</th>
                                        <th>Syngenta</th>
                                        <th>UPL Ltd</th>
                                        <th>Willowood Crop Sciences</th>
                                        <th>Zuari Agro Chemicals</th>
                                        <th>Others</th>
                                        <th>Grand Total</th>
                                        <th>check</th>
                                        <th>Date Created</th>
                                        <th>Date Modified</th>
                                        <!-- <th>&emsp;&nbsp;<i class="fa fa-wrench" aria-hidden="true"></i></th> -->
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
    </div><!-- /.content-wrapper -->
    @include('include.admin.footer')
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
    // drawTable();
    $('#dateSearch').on('click',function(){
        var startDate   = $('#startdate').val();
        var endDate     = $('#enddate').val();
        drawTable(startDate, endDate);
    });
    $('#state_name').on('change',function(){
        var state_name   = $('#state_name').val();
        var data = {
            _token:     $(this).data('token'),
            state_name: state_name
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('tehsil/tehsil-district')}}",
            data:       data,
            success:    function (data) {
                            var jsonData1   = JSON.parse(data);
                            var appenddata1 = '';
                            $("#district_name").empty();
                            appenddata1 += "<option value = ''>--Select District--</option>";
                            for (var i = 0; i < jsonData1.length; i++) {
                                appenddata1 += "<option value = '" + jsonData1[i].dId + "'>" + jsonData1[i].dName + " </option>";
                            }
                            $("#district_name").append(appenddata1);
                            drawTable(state_name);
                            return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });

    //district
    $('#district_name').on('change',function(){
        var district_name   = $('#district_name').val();
        drawTable(district_name);
    });
});

function drawTable(start_date = $('#datepicker').find('input[name="start"]').val(), end_date = $('#datepicker').find('input[name="end"]').val(), state = $('#state_name').val(), district = $('#district_name').val()) {
    var startDate = $('#datepicker').find('input[name="start"]').val();
    var endDate = $('#datepicker').find('input[name="end"]').val();
    // Datatable
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'YYYY-MM-DD, hh:mm A' );
    table   =   $('.datatable').DataTable({
                "destroy" : true,
                "language": {
                    searchPlaceholder: "ECN Number, ECN Name, Class, Formulation"
                },
                "order": [[ 1, "desc" ]],
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
                            title: 'All Companywise Data (Current)'
                        },
                        /*{
                            extend: 'excel',
                            text: '<span class="fa fa-file-excel-o"></span> Excel (All)',
                            action: newExportAction,
                            messageTop: 'Dhanuka Companywise Data',
                            exportOptions: {
                                columns: ':visible'
                            },
                            title: 'All Companywise Data (All)'
                        },*/
                        /*{
                            extend: 'colvis',
                            collectionLayout: 'fixed  four-column',
                            columns: ':gt(0)',
                        },*/
                ],
                "lengthMenu": [[25, 50, 100,500,1000,1500,1800], ['25 rows', '50 rows', '100 rows','500 rows','1000 rows','1500 rows','1800 rows']],
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax": {
                            datatype:   'json',
                            type:       "POST",
                            url:        "{{url('chemical/allcompanywisedata')}}",
                            data:       { _token: "{{csrf_token()}}", start_date: startDate, end_date: endDate, state: state, district: district},
                            error:      function (request, status, error) {
                                        }
                        },
                "columnDefs": [
                                { 
                                    render: function (data, type, full, meta) {
                                        return "<div class='text-wrap width-200'>" + data + "</div>";
                                    },
                                    targets: 63
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
                            { "data": "19" },
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
                            { "data": "30" },
                            { "data": "31" },
                            { "data": "32" },
                            { "data": "33" },
                            { "data": "34" },
                            { "data": "35" },
                            { "data": "36" },
                            { "data": "37" },
                            { "data": "38" },
                            { "data": "39" },
                            { "data": "40" },
                            { "data": "41" },
                            { "data": "42" },
                            { "data": "43" },
                            { "data": "44" },
                            { "data": "45" },
                            { "data": "46" },
                            { "data": "47" },
                            { "data": "48" },
                            { "data": "49" },
                            { "data": "50" },
                            { "data": "51" },
                            { "data": "52" },
                            { "data": "53" },
                            { "data": "54" },
                            { "data": "55" },
                            { "data": "56" },
                            { "data": "57" },
                            { "data": "58" },
                            { "data": "59" },
                            { "data": "60" },
                            { "data": "61" },
                            { "data": "62" },
                            { "data": "63" }
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


$(document.body).on('click', '#editTicket', function () {
    var ticket_id = $(this).data('ticket');
    window.location = "{{url('admin/editticket')}}"+"/"+ticket_id;
});

$('.sidebar-menu li').click(function(){
    $(this).find('ul').toggle();
});
</script>




<script>
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
</script>