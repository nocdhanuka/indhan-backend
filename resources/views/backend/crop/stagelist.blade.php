@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Stage List</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Stage List</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div style="margin-left:780px">
                       <a href="add-stage"><input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Add Stage"></a>
                   </div>

                   <!-- /.box-header -->
                   <div class="box-body">

                    <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
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
</section>
    
   
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

table.dataTable td.dataTables_empty {
        text-align: center;    
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
            url:        "{{url('crop/stage-list')}}",
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
        <?php
        $user_role = Session::get('login_role_id');
        if($user_role=='5') {
            ?>                              
            { 
                "render": function(data, type, row, meta){
                 if(type === 'display'){
                    data = '<a href="javascript:void(0);" id="delZonalManager" data-token="{{csrf_token()}}" data-ticket="'+row[0]+'"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    data += '&emsp;<a href="javascript:void(0);" id="editZonalManager" data-token="{{csrf_token()}}" data-ticket="'+row[0]+'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
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
    $(document.body).on('click', '#editZonalManager', function () {
        var _ticket =   $(this).data('ticket');
        window.location.assign("{{url('crop/edit-stage')}}/"+_ticket);
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
        var r = confirm("Do you want delete the stage?");
        if(r==true){
            $(this).closest('tr').addClass('selected');
            var ele         = $(this);
            var data = {
                _token:     $(this).data('token'),
                _ticket:    $(this).data('ticket')
            }
            $.ajax({
                type:       'POST',
                url:        "{{url('crop/stage-delete')}}",
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





















