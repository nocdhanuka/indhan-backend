@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Area Sales Manager List</h1>
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Area Sales Manager List</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            
                            <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>HQ Code</th>
                                        <th>Zonal Manager</th>
                                        <th>Regional Manager</th>
                                        <th>Regional Sales Manager</th>
                                        <th>Area Sales Manager</th>
                                        <th>Mobile</th>
                                        <th>Pincode</th>
                                        <th>State</th>
                                        <th>Product Category</th>
                                        <th>Status</th>
                                        <th>Username</th>
                                        <th>Password</th>
                                        <th>Date Created</th>
                                        <th>Last Active Time</th>
                                        <?php
                                        $user_role = Session::get('login_role_id');
                                        if($user_role=='8') {
                                        ?>
                                        <th>Action</th>
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



    
    
// Datatable
$(document).ready(function() {    
    // Datatable
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'YYYY-MM-DD, hh:mm A' );
    table   =   $('.datatable').DataTable({
                "language": {
                    searchPlaceholder: "Ticket, BDO, Data Source, State, District, Tehsil, Village, Product, Crop"
                },
                "dom": 'lfrtip',
                "buttons": [                        
                            //'pageLength',
                            {
                                extend: 'excel',
                                text: '<span class="fa fa-file-excel-o"></span> Excel (Current)',
                                title: 'Area Sales Manager List (Current)'
                            },
                            {
                                extend: 'excel',
                                text: '<span class="fa fa-file-excel-o"></span> Excel (All)',
                                action: newExportAction,
                                exportOptions: {
                                    columns: ':visible'
                                    //columns: [ 2, ':visible' ]
                                },
                                title: 'Area Sales Manager List (All)'
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
                            url:        "{{url('salesarea/allsalesareamanagerlistorn')}}",
                            data:       { _token: "{{csrf_token()}}"}
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
                            { "data": "1" },
                            { "data": "2" },
                            { "data": "3" },
                            { "data": "4" },
                            { "data": "5" },
                            { "data": "6" },
                            { "data": "7" },
                            { "data": "8" },
                            { "data": "9" },
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                       data = '<?php
                            $user_role = Session::get('login_role_id');
                            if($user_role=='8') {
                            ?><a href="javascript:void(0);" id="editSalesAreaStatus" data-token="{{csrf_token()}}" data-ticket="'+row[15]+'" data-checkpendding = "'+row[10]+'"><?php
                                    }
                                    ?>'+row[10]+'<?php
                            $user_role = Session::get('login_role_id');
                            if($user_role=='8') {
                            ?></a><?php
                                    }
                                    ?>';
                                   }
                                   return data;
                                }
                            },
                            
                            { "data": "11" },
                            { "data": "12" },                                    
                            { "data": "13" },
                            { "data": "14" },
                            <?php
                            $user_role = Session::get('login_role_id');
                            if($user_role=='8') {
                            ?>
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                        // data = '<a href="javascript:void(0);" id="delSalesAreaManager" data-token="{{csrf_token()}}" data-ticket="'+row[15]+'"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                                        data = '<a href="javascript:void(0);" id="editSalesAreaManager" data-token="{{csrf_token()}}" data-ticket="'+row[15]+'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
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
                //"fixedHeader":    true,
                "fixedColumns":   true,
            }); 
});   
</script>




<script>
$(document.body).on('click', '#editSalesAreaManager', function () {
    var _ticket =   $(this).data('ticket');
    window.location.assign("{{url('salesarea/edit-sales-area-manager-orn')}}/"+_ticket);
});

$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});



$(document.body).on('click', '#editSalesAreaStatus', function () {
    var checkpendding = $(this).data('checkpendding');
    if (checkpendding == 'PENDDING') {
        alert('You have already requested for change status. Please wait for Super Admin approval.');
    }
    else
    {
        var r = confirm("Do you want change the area sales manager status?");
        if(r==true){
            var ele         = $(this);
            var data = {
                _token:     $(this).data('token'),
                _ticket:    $(this).data('ticket')
            }
            $.ajax({
                type:       'POST',
                url:        "{{url('salesarea/update-sales-area-status-orn')}}",
                data:       data,
                success:    function (data) {
                                if(data=='1') {
                                    alert('ASM status update request sent successfully. Please wait for Super Admin approval.');
                                    window.location = "{{url('salesarea/sales-area-manager-list-orn')}}";
                                    // if(ele.text()=='ACTIVE') {
                                    //     var areaStatus  = 'INACTIVE';
                                    // }
                                    // else {
                                    //     var areaStatus  = 'ACTIVE';
                                    // }
                                    // ele.text(areaStatus);
                                }
                                else if(data=='2')
                                {
                                    alert('Please try again.')
                                }
                            },
                error:      function (jqXHR, textStatus, errorThrown) {
                                console.log(textStatus, errorThrown);
                            }
            });
        }
    }
});

$(document.body).on('click', '#delSalesAreaManager', function () {
    var r = confirm("Do you want delete the area sales manager?");
    if(r==true){
        $(this).closest('tr').addClass('selected');
        var ele         = $(this);
        var data = {
            _token:     $(this).data('token'),
            _ticket:    $(this).data('ticket')
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('salesarea/sales-area-delete-orn')}}",
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

</script>
