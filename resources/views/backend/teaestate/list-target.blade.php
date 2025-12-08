@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>All Target List</h1>
            @if(Session::has('message'))
                <div class="col-md-12" style="margin-top:20px;">
                    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                </div>
            @endif
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Target List</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <form role="form" method="post" class="search-form"  id="createtarget" action="{{url('teaestate/create-target')}}"  >
                                <div class="col-lg-3">
                                    <label class="formlabel">User Type</label>
                                    <select name="user_types" id="user_types" class="frm_txt mdb-select inputs">
                                        <option value="">--Select UserType--</option>
                                        <?php
                                        foreach ($user_types as $key=>$value) {
                                            ?>
                                            <option value="{{$key}}">{{$value}}</option>
                                        <?php } ?>
                                    </select>
                                    <div class="rerror"><label for="user_types" generated="true" class="error">{{ $errors->first('user_types') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">User</label>
                                    <select id="user_ecns" class="frm_txt mdb-select inputs" name="user_ecns[]" multiple>
                                        <option value="">--Select User--</option>
                                    </select>
                                    <div class="rerror"><label for="user_ecns" generated="true" class="error">{{ $errors->first('user_ecns') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Month</label>
                                    <select name="months[]" id="months" class="frm_txt mdb-select inputs" multiple>
                                        <option value="">--Select Month--</option>
                                        <?php foreach($months as $key=>$month) { ?>
                                            <option value="{{$key}}">{{$month}}</option>
                                        <?php } ?>
                                    </select>
                                    <div class="rerror"><label for="months" generated="true" class="error">{{ $errors->first('months') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Year</label>
                                    <select name="years" id="years" class="frm_txt mdb-select inputs">
                                        <option value="">--Select Year--</option>
                                        <?php for($year=$crr_year; $year<=$years; $year++) { ?>
                                            <option value="{{$year}}">{{$year}}</option>
                                        <?php } ?>
                                    </select>
                                    <div class="rerror"><label for="years" generated="true" class="error">{{ $errors->first('years') }}</label></div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Category A Target</label>
                                    <input type="text" id="category_a_target" class="frm_txt inputs"  name="category_a_target" placeholder="A Target" value="{{ old('category_a_target') }}">
                                    <div class="rerror">
                                        <label for="category_a_target" generated="true" class="error">{{ $errors->first('category_a_target') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Category B Target</label>
                                    <input type="text" id="category_b_target" class="frm_txt inputs"  name="category_b_target" placeholder="B Target" value="{{ old('category_b_target') }}">
                                    <div class="rerror">
                                        <label for="category_b_target" generated="true" class="error">{{ $errors->first('category_b_target') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Category C Target</label>
                                    <input type="text" id="category_c_target" class="frm_txt inputs"  name="category_c_target" placeholder="C Target" value="{{ old('category_c_target') }}">
                                    <div class="rerror">
                                        <label for="category_c_target" generated="true" class="error">{{ $errors->first('category_c_target') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Office Type</label>
                                    <select name="office_type" id="office_type" class="frm_txt mdb-select inputs">
                                        <option value="">--Select Office Type--</option>
                                        <option value="1">Head Office</option>
                                        <option value="2">Branch Office</option>
                                    </select>
                                    <div class="rerror"><label for="years" generated="true" class="error">{{ $errors->first('years') }}</label></div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-lg-3">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                                    <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit inputs" id="reg" value="Create Target" >
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-lg-12"><hr></div>
                                <div class="clearfix"></div>
                            </form>
                            <div class="row">
                                <div class="col-lg-2">
                                    <!-- <label class="formlabel">User Type</label> -->
                                    <select name="user_type" id="user_type" class="frm_txt mdb-select inputs">
                                        <option value="">--Select UserType--</option>
                                        <?php 
                                        foreach ($user_types as $key=>$value) {
                                            ?>
                                            <option value="{{$key}}">{{$value}}</option>
                                        <?php } ?>
                                    </select>
                                    <div class="rerror"><label for="user_type" generated="true" class="error">{{ $errors->first('user_type') }}</label></div>
                                </div>
                                <div class="col-lg-2">
                                    <!-- <label class="formlabel">User</label> -->
                                    <select id="user_name" class="frm_txt mdb-select inputs" name="user_name" style="width:150px;">
                                        <option value="">--Select User--</option>
                                    </select>
                                    <div class="rerror"><label for="user_name" generated="true" class="error">{{ $errors->first('user_name') }}</label></div>
                                </div>
                                <div class="col-lg-2">
                                    <!-- <label class="formlabel">Month</label> -->
                                    <select name="month" id="month" class="frm_txt mdb-select inputs">
                                        <option value="">--Select Month--</option>
                                        <?php foreach($months as $key=>$month) { ?>
                                            <option value="{{$key}}">{{$month}}</option>
                                        <?php } ?>
                                    </select>
                                    <div class="rerror"><label for="month" generated="true" class="error">{{ $errors->first('month') }}</label></div>
                                </div>
                                <div class="col-lg-2">
                                    <!-- <label class="formlabel">Year</label> -->
                                    <select name="year" id="year" class="frm_txt mdb-select inputs">
                                        <option value="">--Select Year--</option>
                                        <?php for($year=$crr_year; $year<=$years; $year++) { ?>
                                            <option value="{{$year}}">{{$year}}</option>
                                        <?php } ?>
                                    </select>
                                    <div class="rerror"><label for="year" generated="true" class="error">{{ $errors->first('year') }}</label></div>
                                </div>
                                <div class="col-xs-4 form-inline" style="z-index:2;float:right;">
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
                                        <th>User Name</th>
                                        <th>User Ecn</th>
                                        <th>User Type</th>
                                        <th>Office Type</th>
                                        <th>Month</th>
                                        <th>Year</th>
                                        <th>Category A Target</th>
                                        <th>Category B Target</th>
                                        <th>Category C Target</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>&emsp;&nbsp;<i class="fa fa-wrench" aria-hidden="true"></i></th>
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

<!-- FastClick -->
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('assets/admin/dist/js/app.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->

<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
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
<script>    
$( document ).on('ready',function() {
    drawTable();
    $('#user_type').on('change',function(){
        // drawTable();
        $("#user_name").empty();
        $("#user_name").append("<option value = ''>--Select UserName--</option>");
        var user_type   = $('#user_type').val();
        var data = {
            _token: "{{csrf_token()}}",
            user_type: user_type
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('teaestate/user-details')}}",
            data:       data,
            success:    function (data) {
                            var jsonData1   = JSON.parse(data);
                            var appenddata1 = '';
                            $("#user_name").empty();
                            appenddata1 += "<option value = ''>--Select User--</option>";
                            for (var i = 0; i < jsonData1.length; i++) {
                                appenddata1 += "<option value = '" + jsonData1[i].ECN + "'>" + jsonData1[i].name + " </option>";
                            }
                            $("#user_name").append(appenddata1);
                            return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });

    $('#dateSearch').on('click',function(){
        drawTable();
    });
    $('#user_type').on('change',function(){
        drawTable();
    });
    $('#user_name').on('change',function(){
        var user_name_title = $( "#user_name option:selected" ).text();
        $('#user_name_title').val(user_name_title);
        drawTable();
    });
    $('#year').on('change',function(){
        drawTable();
    });
    $('#month').on('change',function(){
        drawTable();
    });
    $('#user_types').on('change',function(){
        // drawTable();
        $("#user_ecns").empty();
        $("#user_ecns").append("<option value = ''>--Select UserName--</option>");
        var user_type   = $('#user_types').val();
        var data = {
            _token: "{{csrf_token()}}",
            user_type: user_type
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('teaestate/user-details')}}",
            data:       data,
            success:    function (data) {
                            var jsonData1   = JSON.parse(data);
                            var appenddata1 = '';
                            $("#user_ecns").empty();
                            appenddata1 += "<option value = ''>--Select User--</option>";
                            for (var i = 0; i < jsonData1.length; i++) {
                                appenddata1 += "<option value = '" + jsonData1[i].ECN + "'>" + jsonData1[i].name + " </option>";
                            }
                            $("#user_ecns").append(appenddata1);
                            return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
});

// Export All Data In Excel
var oldExportAction = function (self, e, dt, button, config) {
    if (button[0].className.indexOf('buttons-excel') >= 0) {
        if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
            $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button,config);
        } else {
            $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button,config);
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
    let user_type   = $('#user_type').val();
    let user_name   = $('#user_name').val();
    let year    = $('#year').val();
    let month   = $('#month').val();
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'YYYY-MM-DD, hh:mm A' );
    table   =   $('.datatable').DataTable({
                "destroy" : true,
                "language": {
                    searchPlaceholder: "Target Category"
                },
                "order": [[ 4, "desc" ]],
                "dom": 'lBfrtip',
                "select": true,
                "buttons": [     
                            {
                                extend: 'excel',
                                text: '<span class="fa fa-file-excel-o"></span> Excel (All)',
                                action: newExportAction,
                                exportOptions: {
                                    columns: ':visible'
                                },
                                title: 'Target List'
                            },
                    ],
                "lengthMenu": [[25, 50, 100, -1], ['25 rows', '50 rows', '100 rows', 'All']],
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax": {
                            datatype:   'json',
                            type:       "POST",
                            url:        "{{url('teaestate/all-target')}}",
                            data:       { _token: "{{csrf_token()}}", start_date: start_date, end_date: end_date, year: year, month: month, user_type: user_type, user_name: user_name }
                        },
                "columnDefs": [
                                { orderable: false, targets: 0 },
                                { "className": "text-center", orderable: false, targets: [4] },
                            ],
                "columns": [
                            {
                                render: function (data, type, row, meta) {
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
                            {
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                       data = '<a href="javascript:void(0);" id="editTarget" data-token="{{csrf_token()}}" data-ticket="'+row[1]+'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
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
                "searching":      false,
                "paging":         true,
                "info":           true,
                "orderCellsTop":  true,
                "fixedColumns":   true,
            }); 
}
            
$.ajaxSetup({
   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});

$("#createtarget").validate({
    errorPlacement: function (error, element) {
                        // console.log($(element).next());
                        $(element).next('.rerror').html(error);
                        $(element).next().next('.rerror').html(error);
                    },
    ignore: "input[type='text']:hidden",
    rules: {
        user_types: {
            required:   true,
        },
        user_names: {
            required:   true,
        },
        months: {
            required:   true,
        },
        years: {
            required:   true,
        },
        category_a_target: {
            required:   true,
        },
        category_b_target: {
            required:   true,
        },
        category_c_target: {
            required:   true,
        },
        office_type: {
            required: true,
        }
    },
    messages: {
    },
    highlight: function (element) {
        $(element).addClass('red');
    },
    unhighlight: function (element) {
        $(element).removeClass('red');
    },

    focusInvalid: false,
    invalidHandler: function(form, validator) {
        if (!validator.numberOfInvalids())
            return;
        $('html, body').animate({
            scrollTop: $(validator.errorList[0].element).offset().top-130
        }, 1000);
    },
});

$(document.body).on('click', '#editTarget', function () {
    var _ticket =   $(this).data('ticket');
    window.location.assign("{{url('teaestate/edit-target')}}/"+_ticket);
}); 

$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});
</script>