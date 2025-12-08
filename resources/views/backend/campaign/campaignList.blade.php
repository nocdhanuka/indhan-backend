@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')



<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>List of Campaigns</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">List of Campaigns</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-inline">
                            <!-- <div class="input-daterange input-group" id="datepicker" style="width: 70%;">
                                        <input type="text" placeholder=" Start Date From" class="input-sm form-control" id="startdate"  name="start"   />
                                        <span class="input-group-addon">From</span>
                                        <input type="text" class="input-sm form-control" placeholder=" Start Date TO" id="enddate" name="end"   />
                                        <span class="input-group-addon">to</span>
                                        <input type="hidden" class="input-sm form-control" name="token" id="token" value="{{csrf_token()}}"/>
                                    </div>
                                    <button id="clear" class="btn btn-sm ">reset</button> -->
                            <a type="button" href="{{URL::to('admin/campaign/createCampaign')}}" id="dateSearch" class="btn btn-sm btn-primary" style="float:right">Create</a>
                        </div>
                        <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                            <thead>
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>Campaign Name</th>
                                    <th>Status</th>
                                    <th>Response</th>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <?php $i=1; foreach ($result as $data) {
                                        //echo json_encode($result);
                                    ?>
                                        <td><?php echo $i; $i++; ?></td>
                                        <td>{{$data->campaign_name}}</td>
                                        <td>{{$data->status_name}}</td>
                                        <td><a style="text-decoration:underline;" href="{{URL::to('campaign/rawData/'.$data->campaign_id)}}"><?php echo $data->resp_count; ?></a></td>                                        
					<td style="display: flex;">
                                            <div style="display: inline-table;">
                                            <?php if ($data->campaign_status == 1) { ?>
                                                <a href="{{URL::to('admin/campaign/editCampaignStatus/id=')}}<?php echo $data->campaign_id; ?>/status=2" type="button" id="editId" class="btn btn-sm" style="margin-top: 0px;"><i class="fa fa-pause"></i></a></div>    
                                            <?php } else { ?>
                                                <a href="{{URL::to('admin/campaign/editCampaignStatus/id=')}}<?php echo $data->campaign_id; ?>/status=1" type="button" id="editId" class="btn btn-sm" style="margin-top: 0px;"><i class="fa fa-play"></i></a></div>
                                                <!-- <i class="fa fa-play"></i> -->
                                            <?php } ?>
                                            <a href="{{URL::to('admin/campaign/editCampaign/id=')}}<?php echo $data->campaign_id; ?>" type="button" id="editId" class="btn btn-sm btn-primary" style="margin-top: 0px;">Edit</a></div>
                                        </td>
                                </tr>
                            <?php   } ?>
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

!-- ./wrapper --><!-- jQuery 2.1.4 -->
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

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>


<!-- SlimScroll -->
<script src="{{asset('assets/admin/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('assets/admin/dist/js/app.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->
<script>
$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});
</script>
