@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Season List</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Season List</li>
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
                    <div id="seasonData">
                    </div>

                    <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>Season Name</th>
                                <?php
                                $user_role = Session::get('login_role_id');
                                if($user_role=='5') {
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
// Datatable

$(document).ready(function() {    

    // Datatable
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'YYYY-MM-DD, hh:mm A' );
    table   =   $('.datatable').DataTable({
        "language": {
            searchPlaceholder: ""
        },
        "dom": 'lfrtip',
        "lengthMenu": [[25, 50, 100, -1], ['25 rows', '50 rows', '100 rows', 'All']],
        "pageLength": 25,
        "processing": true,
        "serverSide": true,
        "ajax": {
            dataType:   "json",
            type:       "POST",
            url:        "{{url('demo/season-list')}}",
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
{ 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                       data = '<a href="javascript:void(0);" id="delSeason" data-token="{{csrf_token()}}" data-ticket="'+row[2]+'"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                                       data += '&emsp;<a href="javascript:void(0);" id="editSeason" data-token="{{csrf_token()}}" data-ticket="'+row[2]+'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
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
                //"fixedHeader":    true,
                "fixedColumns":   true,
            }); 


}); 

$(document.body).on('click', '#delSeason', function () {
    var r = confirm("Do you want delete the season?");
    if(r==true){
        $(this).closest('tr').addClass('selected');
        var ele         = $(this);
        var data = {
            _token:     $(this).data('token'),
            _ticket:    $(this).data('ticket'),
           
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('demo/season-delete')}}",
            data:       data,
            success:    function (data) {
                            if(data=='1') {
                                table.row('.selected').remove().draw(false);
                                $('#seasonData').html('<div class="alert alert-success">Season Deleted Successfully</div>');
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
 $(document.body).on('click', '#editSeason', function () {
        var _ticket =   $(this).data('ticket');
        window.location.assign("{{url('demo/edit-season')}}/"+_ticket);
    });  
</script>






















