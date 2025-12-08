<?php //dd($zonal_manager_list); ?>

@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->
<style type="text/css">
    .addM{
        width: 600px;
        margin: 0 auto;
    }
    .message_view li{ display: inline-block; list-style: none;     margin-right: 10%; }
    .message_view ul{ margin:0; padding: 0;     margin-bottom: 15px;}
    .message_view {    border: 2px solid #ccc;
    padding: 25px;    margin-bottom: 30px;}
    .message_view h3{    padding: 20px 0;     margin-left: 15px;}
    .farmerList li{ margin-right: 0;margin-bottom: 10px; }
    .history_search input{      width: 20%;
    border: 0;
    border-bottom: 1px solid #ccc;
    margin-bottom: 20px;
    padding: 10px 0;
    padding-left: 5px;     font-size: 15px;   outline: 0;}
    .history_search { text-align: right;     margin-right: 1%;}
    .history_search .fa{    font-size: 20px;
    position: absolute;
       right: 25px;
    top: 20px;}
    table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}


</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Farmer History</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Farmer History</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12 viewFarmer">
                                 <table>
                                    <tr>
                                    <th>Farmer Name</th>
                                    <th>Contact Number</th>
                                    </tr>
                                @foreach($totalFarmer as $farmer)
                                    <tr>
                                    <td>{{$farmer->farmer}}</td>
                                    <td>{{$farmer->contact_number}}</td>
                                    </tr>
                                @endforeach
                                 </table>
                            </div>
                        </div>
                         
                        
                        
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
@include('include.admin.footer')
<!-- Control Sidebar -->
<!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>

</div>

<!-- ./wrapper --><!-- jQuery 2.1.4 -->
<script src="{{asset('assets/admin/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
<!-- Bootstrap 3.3.5 -->
<script src="{{asset('assets/admin/bootstrap/js/bootstrap.min.js')}}"></script>
<!-- DataTables -->
<script src="{{asset('assets/admin/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/admin/plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<!-- SlimScroll -->
<script src="{{asset('assets/admin/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('assets/admin/dist/js/app.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->

<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        // $('#search').on('keydown',function () {
        //     var search =  $(this).val();
        //     if(search != '')
        //     {
        //     $.ajax({
        //         type:       'GET',
        //         url:        "{{url('demo/history-filter')}}/"+search,
        //         success:    function (data) {
        //             $('.dynamicData').html(data);
        //         }


        //     })
        // }
        // })
         $('#search').on('keyup',function () {
            var search =  $(this).val();
            if(search == '')
            {
                search = 'all';
            }
            $.ajax({
                type:       'GET',
                url:        "{{url('demo/history-filter')}}/"+search,
                success:    function (data) {
                    $('.dynamicData').html(data);
                }


            })
       
        })
    })
</script>















