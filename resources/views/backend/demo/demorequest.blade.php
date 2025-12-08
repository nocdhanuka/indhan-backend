@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Demo Summary</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Demo Summary</li>
        </ol>
    </section>
    <!-- <ul class="nav nav-tabs">
       <li class="active"><a data-toggle="tab" href="#manual">Distributors</a></li>
       <li><a data-toggle="tab" href="#free">Retaliers</a></li>
   </ul> -->
   <!-- <div class="tab-content"> -->

       <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <!-- <div style="margin-left:780px">
                       <a href="add-distributor"><input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Add Distributor"></a>
                   </div> -->

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
                                <th>DD ECN</th>
                                <th>DD Name</th>
                                <th>Crop Name</th>
                                <th>Product</th>
                                <th>Farmer Name</th>
                                <th>Farmer Type</th>
                                <th>Contact Number</th>
                                <th>Village</th>
                                <th>Tehshil</th>
                                <th>District</th>                                
                                <th>State</th>
                                <th>Pincode</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>DD Demo Upload</th>
                                <th>Verified</th>
                                <th>Distance (metres)</th>
                                <th>FL Name</th>
                                <th>FL Upload</th>
                                <th>FL Feedback</th>
                                <th>FL Rating</th>
                                <th>Supervisor</th>
                                <th>Supervisor Upload</th>
                                <th>Supervisor Feedback</th>
                                <th>Supervisor Rating</th>
                                <th>Demo Number</th>
                                <th>Evaluation Status</th>
                                <th>Results Expected By</th>
                                <th>Evaluation Date</th>
                                <th>Ticket Count</th>
                                <th>Ticket Numbers</th>
                                <th>Creation Time</th>

                            </tr>
                        </thead>
                    </table>
                    <div class="success"></div>

                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
</section><!-- /.content -->


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
</style>


<script>
$( document ).on('ready',function() {
    // drawTable();
    $('#dateSearch').on('click',function(){
        var startDate   = $('#startdate').val();
        var endDate     = $('#enddate').val();
        drawTable(startDate, endDate);
    });
});


// Export All Data In Excel
var oldExportAction = function (self, e, dt, button, config) { console.log(self,e,dt,button,config)
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
    var oldStart    = dt.settings()[0]._iDisplayStart; console.log('were');
    dt.one('preXhr', function (e, s, data) {
        // Just this once, load all data from the server...
        data.start  = 0;
        data.length = 2147483647;        console.log('where');
        dt.one('preDraw', function (e, settings) { console.log('there');
            // Call the original action function 
            oldExportAction(self, e, dt, button, config);            
            dt.one('preXhr', function (e, s, data) { console.log('here');
                // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                // Set the property to what it was before exporting.
                settings._iDisplayStart = oldStart;
                data.start = oldStart;
            });            
            // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
            setTimeout(dt.ajax.reload, 0);
            //Prevent rendering of the full data to the DOM
            return false;
        });
    });    
    console.log('asdre');
    // Requery the server with the new one-time export settings
    dt.ajax.reload(); console.log('afasdfasdre');
    // setInterval( function () {
    //     try{
    //         dt.ajax.reload(function(){
    //             console.log('\n[' + moment(new Date()).format("DD-MM-YYYY HH:mm:ss") + ']: Se ha actualizado el contenido del DataTables...');
    //         }, false);
    //     } catch(e){
    //         console.log('Error al refrescar automaticamente la tabla de accesorios: ', e);
    //     }
    // }, 30000 );
};
// Export All Data In Excel

// Datatable

function drawTable(start_date = $('#datepicker').find('input[name="start"]').val(), end_date = $('#datepicker').find('input[name="end"]').val()) {    

    // Datatable
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'YYYY-MM-DD, hh:mm A' );
    table   =   $('.datatable').DataTable({
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
                                title: 'Demo List (Current)'
                            },
                            {
                                text: 'Excel (All)',
                                className: 'redButton'
                            },
                            // {
                            //     extend: 'excel',
                            //     text: '<span class="fa fa-file-excel-o"></span> Excel (All)',
                            //     action: newExportAction,
                            //     exportOptions: {
                            //         columns: ':visible'
                            //         //columns: [ 2, ':visible' ]
                            //     },
                            //     title: 'Demo List (All)'
                            // },
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
            url:        "{{url('demo/demo-request')}}",
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
        { "data": "4" },
        { "data": "5" },
        { "data": "6" },
        { "data": "7" },
        { "data": "8" },
        { "data": "23" },
        { "data": "9" },
        { "data": "10" },
        { "data": "11" },
        { "data": "12" },
        { "data": "13" },
        { "data": "15" },
        { "data": "16" },
        { 
            "render": function(data, type, row, meta){
               if(type === 'display'){
                    var str1 = row[17];
                    if(str1!=undefined && str1.includes("assets")) {
                        var str2 = str1.replace('demo/','');
                        data = "<a href='"+str2+"' target='_blank'>view</a>";
                    }
                    else {
                        data = "";
                    }
               }
               return data;
            }
        },
        { "data": "25" },
        { "data": "27" },
        { "data": "28" },
        { 
            "render": function(data, type, row, meta){
               if(type === 'display'){
                    var str1 = row[29];
                    if(str1!=undefined && str1.includes("assets")) {
                        var str2 = str1.replace('demo/','');
                        data = "<a href='"+str2+"' target='_blank'>view</a>";
                    }
                    else {
                        data = "";
                    }
               }
               return data;
            }
        },
        { "data": "30" },
        { "data": "31" },
        { "data": "32" },
        { 
            "render": function(data, type, row, meta){
               if(type === 'display'){
                    var str1 = row[33];
                    if(str1!=undefined && str1.includes("assets")) {
                        var str2 = str1.replace('demo/','');
                        data = "<a href='"+str2+"' target='_blank'>view</a>";
                    }
                    else {
                        data = "";
                    }
               }
               return data;
            }
        },
        { "data": "34" },
        { "data": "35" },
        { "data": "18" },
        { "data": "19" },
        { "data": "24" },
        { "data": "22" },
        { "data": "20" },
        { "data": "21" },
        { "data": "3" },


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




<script>
    $(document.body).on('click', '#editZonalManager', function () {
        var _ticket =   $(this).data('ticket');
        window.location.assign("{{url('distributor/edit-distributor')}}/"+_ticket);
    });

    $('.user-image').click(function(){
        $('.dropdown-menu').toggle();
    });
</script>




<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {   
        jQuery.validator.addMethod("domain", function(value, element) {
            var isSuccess   = false;
            var bdo_code    = $('#ECN').val();
            $.ajax({ 
                url:    "{{url('zonal/check-ecn')}}", 
                data:   {'bdo_code':bdo_code, '_bdo': '0'}, 
                async:  false,
                type:   "POST",
                success: 
                function(msg) { isSuccess = msg === "true" ? true : false }
            });
            return isSuccess;
        }, "ENC already exists, enter a new ECN.");

        $('.step2submit').keypress(function (e) {
            if (e.which == 13) {
                $("#createzonalmanager").click();
            }
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#createzonalmanager").validate({
            errorPlacement: function (error, element) {
                console.log($(element).next());
                $(element).next('.rerror').html(error);
                $(element).next().next('.rerror').html(error);
            },
            ignore: "input[type='text']:hidden",
            rules: {
                zonal_name: {
                    required:   true,
                    minlength:  4,
                    maxlength:  50
                },
                zonal_mobile_number: {
                    required:   true,
                    minlength:  10,
                    maxlength:  10,
                    number: true
                },
                zonal_pincode: {
                    required:   true,
                    minlength:  6,
                    maxlength:  6,
                    number:     true
                },
                ECN: {
                    required:   true,
                    minlength:  4,
                    maxlength:  6,
                    number:     true,
                    domain:     true
                },
                "zonal_state[]": {
                    required:   true
                },
                "product_category[]": {
                    required:   true
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
    });




    $(document.body).on('click', '#editZonalStatus', function () {
        var r = confirm("Do you want change the zonal manager status?");
        if(r==true){
            var ele         = $(this);
            var data = {
                _token:     $(this).data('token'),
                _ticket:    $(this).data('ticket')
            }
            $.ajax({
                type:       'POST',
                url:        "{{url('zonal/update-zonal-status')}}",
                data:       data,
                success:    function (data) {
                    if(data=='1') {
                        if(ele.text()=='ACTIVE') {
                            var zonalStatus  = 'INACTIVE';
                        }
                        else {
                            var zonalStatus  = 'ACTIVE';
                        }
                        ele.text(zonalStatus);
                    }
                },
                error:      function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    });

    $(document.body).on('click', '#delZonalManager', function () {
        var r = confirm("Do you want delete the zonal manager?");
        if(r==true){
            $(this).closest('tr').addClass('selected');
            var ele         = $(this);
            var data = {
                _token:     $(this).data('token'),
                _ticket:    $(this).data('ticket')
            }
            $.ajax({
                type:       'POST',
                url:        "{{url('distributor/distributor-delete')}}",
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
        url: "{{route('postexportdemo')}}",
        data: data,        
        success: function (response, textStatus, request) {
                    var a       = document.createElement("a");
                    a.href      = response.file; 
                    a.download  = response.name;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    $('.redButton').html('Excel All');
                },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
});
</script>





















