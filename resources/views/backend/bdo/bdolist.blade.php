@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>All BDO List</h1>
            
            @if(Session::has('message'))
            <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            </div>
            @endif

            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">BDO List</li>
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
                                    <div class="input-daterange input-group" id="checkActive" style="width: 70%;float: right;">
                                       
                                        <select id="checkStatus" name="checkStatus">
                                            <option unseleted value="">---Select Status---</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        <input type="hidden" class="input-sm form-control" name="token" id="token" value="{{csrf_token()}}"/>
                                    </div>
                                    
                                </div>
                            </div>                            
                            <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                                <thead>
                                <tr>
                                        <th>Sr. No.</th>
                                        <th>BDO Code</th>
                                        <th>BDO HQ</th>
                                        <th>BDO Name</th>
                                        <th>BDO Mobile No</th>
                                        <th>Department</th>
                                        <th>Designation</th>
                                        <th>State</th>                           
                                        <th>District</th>                       
                                        <th>Language</th>
                                        <th>Zonal Manager</th>
                                        <th>Zonal Mobile No</th>
                                        <th>Zonal Pincode</th>
                                        <th>SME</th>
                                        <th>SME Mobile No</th>
                                        <th>SME Pincode</th>
                                        <th>DGM</th>
                                        <th>DGM Mobile No</th>
                                        <th>DGM Pincode</th>
                                        <th>BD S1</th>
                                        <th>BD S1 Mobile No</th>
                                        <th>BD S1 Pincode</th>
                                        <th>ASM/S1</th>
                                        <th>ASM/S1 Mobile No</th>
                                        <th>ASM/S1 Pincode</th>
                                        <th>Product Category</th>
                                        <th>Deputation Status</th>
                                        <th>Username</th>                                       
                                        <th>Password</th>
                                        <th>Districts</th>
                                        <th>Territory</th>
                                        <th>Market</th>
                                        <th>Created At</th>
                                        <th>Last Active Time</th>
                                        <th>Status</th>
                                        <?php
                                        $user_role = Session::get('login_role_id');
                                        if($user_role=='5') {
                                        ?>
                                        <th>&emsp;&nbsp;<i class="fa fa-wrench" aria-hidden="true"></i></th>
                                        <?php
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



<style>
    table.dataTable td.dataTables_empty {
        text-align: center;    
    }
</style>



<script>
$(document).on('ready',function() {
    drawTable();
    $('#checkStatus').on('change',function(){
        var status   = $('#checkStatus').val();
        drawTable(status);
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

    function drawTable(status = $('#checkStatus').val()) {     
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
                            // 'pageLength',
                            {
                                extend: 'excel',
                                text: '<span class="fa fa-file-excel-o"></span> Excel (Current)',
                                title: 'BDO List (Current)'
                            },
                            {
                                extend: 'excelHtml5',
                                text: '<span class="fa fa-file-excel-o"></span> Excel (All)',
                                action: newExportAction,
                                exportOptions: {
                                    columns: ':visible',
                                    // orthogonal: 'export',
                                    // columns: [ 2, ':visible' ]
                                },
                                title: 'BDO List (All)'
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
                            url:        "{{url('admin/allbdolist')}}",
                            data:       { _token: "{{csrf_token()}}", status:status }
                        },
                "columnDefs": [
                                { orderable: false, targets: 0 },
                                { "className": "text-center", orderable: false, targets: 23 },
                                //{ "targets": 2, "visible": false},
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
                            { "data": "36" },
                            { "data": "34" },
                            { "data": "35" },
                            { "data": "29" },
                            { "data": "30" },
                            { 
                                "render": function(data, type, row, meta){
                                    if(type === 'display'){
                                       data = '<?php
                                                $user_role = Session::get('login_role_id');
                                                if($user_role=='5') {
                                                ?><a href="javascript:void(0);" id="editSalesAreaStatus" data-token="{{csrf_token()}}" data-ticket="'+row[32]+'"><?php
                                                }
                                                ?>'+row[31]+'<?php
                                                $user_role = Session::get('login_role_id');
                                                if($user_role=='5') {
                                                ?></a><?php
                                                }
                                    ?>';
                                    }
                                    return data;
                                }
                            },
                            <?php
                            $user_role = Session::get('login_role_id');
                            if($user_role=='5') {
                            ?>                                  
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                       data = '<a href="javascript:void(0);" id="editBdo" data-token="{{csrf_token()}}" data-ticket="'+row[32]+'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
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


$(document.body).on('click', '#editBdo', function () {
    var _ticket =   $(this).data('ticket');
    window.location.assign("{{url('admin/edit-bdo')}}/"+_ticket);
});



$(document.body).on('click', '#editSalesAreaStatus', function () {
    var r = confirm("Do you want change the bdo status?");
    if(r==true){
        var ele         = $(this);
        var data = {
            _token:     $(this).data('token'),
            _ticket:    $(this).data('ticket')
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('admin/update-bdo-status')}}",
            data:       data,
            success:    function (data) {
                            if(data=='1') {
                                if(ele.text()=='ACTIVE') {
                                    var areaStatus  = 'INACTIVE';
                                }
                                else {
                                    var areaStatus  = 'ACTIVE';
                                }
                                ele.text(areaStatus);
                            }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    }
});

</script>





<script>
$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});
</script>































































































