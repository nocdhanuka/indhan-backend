@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>All Product List</h1>
            
            @if(Session::has('message'))
            <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            </div>
            @endif

            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Product List</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">

                            <!-- <div class="col-xs-8 form-inline" style="z-index: 2; float:right; padding:0px 0 0 155px;">
                                <div class="input-daterange input-group" id="datepicker" style="width: 70%;">
                                    <input type="text" class="input-sm form-control" id="startdate"  name="start"   />
                                    <span class="input-group-addon">to</span>
                                    <input type="text" class="input-sm form-control" id="enddate" name="end"   />
                                    <input type="hidden" class="input-sm form-control" name="token" id="token" value="{{csrf_token()}}"/>
                                </div>
                                <button id="clear" class="btn btn-sm ">reset</button>
                                <button type="button" id="dateSearch" class="btn btn-sm btn-primary" style="margin:0;">Search</button>
                            </div> -->
                            <div class="row">
                            <form role="form" method="post" class="search-form"  id="createproduct" action="{{url('product/create-product')}}"  >                                                                       
                                <div class="col-lg-3">
                                    <label class="formlabel">Product Name</label>
                                    <input type="text" id="product_name" class="frm_txt inputs"  name="product_name" placeholder="Product Name" maxlength="50" value="{{ old('product_name') }}">
                                    <div class="rerror"><label for="product_name" generated="true" class="error">{{ $errors->first('product_name') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                                    <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit inputs" id="reg" value="Create Product" >
                                </div>
                                
                                <div class="clearfix"></div>
                                <div class="col-lg-12"><hr></div>
                                <div class="clearfix"></div>
                            </form>
                            </div>
                            
                            <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                                <thead>
                                    <tr>

                                        <th>Sr. No.</th>
                                        <th>Product</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th><i class="fa fa-wrench" aria-hidden="true"></i></th>
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

<!--<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/select/1.2.5/js/dataTables.select.min.js"></script>
<script type="text/javascript" language="javascript" src="https://editor.datatables.net/extensions/Editor/js/dataTables.editor.min.js"></script>
<link  rel="stylesheet" href="https://editor.datatables.net/extensions/Editor/css/editor.dataTables.min.css">
<link  rel="stylesheet" href="https://cdn.datatables.net/select/1.2.5/css/select.dataTables.min.css">-->


<!-- SlimScroll 
<script src="{{asset('assets/admin/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
  FastClick -->
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE App -->
<!-- <script src="{{asset('assets/admin/dist/js/app.min.js')}}"></script> -->
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

 function drawTable(start_date = $('#datepicker').find('input[name="start"]').val(), end_date = $('#datepicker').find('input[name="end"]').val()) {
 
//$.fn.dataTable.ext.errMode = 'none';
$.fn.dataTable.moment( 'YYYY-MM-DD, hh:mm A' );
table   =   $('.datatable').DataTable({
                "language": {
                    searchPlaceholder: "Product Name"
                },
                "destroy" : true,

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
                                    //columns: [ 2, ':visible' ]
                                },
                                title: 'Product List (All)'
                            },
                    ],
                "lengthMenu": [[25, 50, 100, -1], ['25 rows', '50 rows', '100 rows', 'All']],
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax": {
                            datatype:   'json',
                            type:       "POST",
                            url:        "{{url('product/allproductlist')}}",
                            data:       { _token: "{{csrf_token()}}",
                                            start_date: start_date, end_date: end_date}
                        },
                "columnDefs": [
                                { orderable: false, targets: 0 },
                                { "className": "text-center", orderable: false, targets: [4] },
                                //{ "targets": 2, "visible": false},
                            ],
                "columns": [                
                            { 
                                render: function (data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                                } 
                            },
                            { "data": "1" },
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                       data = '<a href="javascript:void(0);" id="editProductStatus" data-token="{{csrf_token()}}" data-ticket="'+row[4]+'">'+row[2]+'</a>';
                                   }
                                   return data;
                                }
                            },
                            { "data": "3" },                                  
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                       data = '<a href="javascript:void(0);" id="delProduct" data-token="{{csrf_token()}}" data-ticket="'+row[4]+'"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                                       data += '&emsp;<a href="javascript:void(0);" id="editProduct" data-token="{{csrf_token()}}" data-ticket="'+row[4]+'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
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



$("#createproduct").validate({
    errorPlacement: function (error, element) {
                        console.log($(element).next());
                        $(element).next('.rerror').html(error);
                        $(element).next().next('.rerror').html(error);
                    },
    ignore: "input[type='text']:hidden",
    rules: {
        product_name: {
            required:   true,
            minlength:  2,
            maxlength:  50
        },
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
$(document.body).on('click', '#editProduct', function () {
    var _ticket =   $(this).data('ticket');
    window.location.assign("{{url('product/edit-product')}}/"+_ticket);
}); 
$(document.body).on('click', '#delProduct', function () {
    var r = confirm("Do you want delete the product?");
    if(r==true){
        $(this).closest('tr').addClass('selected');
        var ele         = $(this);
        var data = {
            _token:     $(this).data('token'),
            _ticket:    $(this).data('ticket'),
           
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('product/product-delete')}}",
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



$(document.body).on('click', '#editProductStatus', function () {
    var r = confirm("Do you want change the product status?");
    if(r==true){
        var ele         = $(this);
        var data = {
            _token:     $(this).data('token'),
            _ticket:    $(this).data('ticket')
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('product/update-product-status')}}",
            data:       data,
            success:    function (data) {
                            if(data=='1') {
                                if(ele.text()=='ACTIVE') {
                                    var productStatus  = 'INACTIVE';
                                }
                                else {
                                    var productStatus  = 'ACTIVE';
                                }
                                ele.text(productStatus);
                            }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    }
});

$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});

$('.sidebar-menu li').click(function(){
    $(this).find('ul').toggle();
});
</script>
