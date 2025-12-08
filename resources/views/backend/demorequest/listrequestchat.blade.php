@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    <style type="text/css">
    .formlabel {
        text-align: center;
        width: 80%;
        font-size: 30px;
    }
    </style>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Demo Material Request Chat</h1>
            @if(Session::has('message'))
                <div class="col-md-12" style="margin-top:20px;">
                    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                </div>
            @endif
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{URL::to('demorequest/list-request')}}"><i class="fa fa-dashboard"></i> Demo Material Request</a></li>
                <li class="active">Demo Material Request Chat</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <form role="form" method="post" class="search-form"  id="createquery" action="{{url('demorequest/create-material-chat')}}">
                                <div class="col-lg-2"></div>
                                <div class="col-lg-8">
                                    <label class="formlabel">CHAT</label>
                                    <textarea id="comments" class="frm_txt inputs" name="comments" rows="5" cols="100" placeholder="Add New Comments" value=""></textarea>
                                    <div class="rerror"><label for="chat_content" generated="true" class="error">{{ $errors->first('chat_content') }}</label></div>
                                </div>
                                <div class="col-lg-2"></div>
                                <div class="clearfix"></div>
                                <div class="col-lg-4"></div>
                                <div class="col-lg-4">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                    <input type="hidden" name="demorequest_id" id="demorequest_id" value="{{ $demorequest_id }}" />
                                    <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit inputs" style="margin:0px; width: 80%;" id="reg" value="Add">
                                </div>
                                <div class="col-lg-4"></div>
                                <div class="clearfix"></div>
                                <!-- <div class="col-lg-12"><hr></div> -->
                                <!-- <div class="clearfix"></div> -->
                            </form>
                            
                            <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Date</th>
                                        <th>User</th>
                                        <th>Comments</th>
                                        <!-- <th>Comments</th> -->
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
<script>
$( document ).on('ready',function() {
    drawTable();
});

// Export All Data In Excel
var oldExportAction = function (self, e, dt, button, config) {
    if (button[0].className.indexOf('buttons-excel') >= 0) {
        if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
            $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
        } else {
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

function drawTable() {
    var demorequest_id = $('#demorequest_id').val();
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'YYYY-MM-DD, hh:mm A' );
    table   =   $('.datatable').DataTable({
                    "destroy" : true,
                    "language": {
                        searchPlaceholder: "Char Name"
                    },
                    "order": [[ 1, "desc" ]],
                    "dom": 'lBfrtip',
                    "select": false,
                    "buttons": [     
                                {
                                    extend: 'excel',
                                    text: '<span class="fa fa-file-excel-o"></span> Excel (All)',
                                    action: newExportAction,
                                    exportOptions: {
                                        columns: ':visible'
                                    },
                                    title: 'Chat List'
                                },
                        ],
                    "lengthMenu": [[50, 100, -1], ['50 rows', '100 rows', 'All']],
                    "pageLength": 50,
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                                datatype:   'json',
                                type:       "POST",
                                url:        "{{url('demorequest/all-material-chatlist')}}",
                                data:       { _token: "{{csrf_token()}}", demorequest_id: demorequest_id}
                            },
                    "columnDefs": [
                                    { orderable: false, targets: 0 },
                                    { "className": "text-center",orderable: false,targets: [3] },
                                ],
                    "columns": [
                                {
                                    render: function (data, type, row, meta) {
                                        return meta.row + meta.settings._iDisplayStart + 1;
                                    }
                                },
                                { "data": "1" },
                                { "data": "2" },
                                { "data": "3" },
                                // { "data": "4" },
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

$("#createquery").validate({
    errorPlacement: function (error, element) {
                        console.log($(element).next());
                        $(element).next('.rerror').html(error);
                        $(element).next().next('.rerror').html(error);
                    },
    ignore: "input[type='text']:hidden",
    rules: {
        comments: {
            required:   true,
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

$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});
</script>
