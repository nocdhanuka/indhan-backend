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

</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Message History</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Message History</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="history_search">
                            <input type="text" name="search" id="search" placeholder="Search by crop,product,language">
                            <i class="fa fa-search"></i>
                        </div> 
                        <div class="dynamicData"> 
                        @foreach($message as $key => $value)
                        <?php $normal_farmer = $value->normal_farmer;
                        $register_farmer = $value->register_farmer;
                        $normalfarmersArr = array();
                        $registerfarmersArr = array();
                        if($normal_farmer != ''){
                           $normalfarmersArr =  explode(',', $normal_farmer);
                        }
                         if($register_farmer != ''){
                           $registerfarmersArr =  explode(',', $register_farmer);
                        }
                        $totalFarmer = array_merge($normalfarmersArr,$registerfarmersArr);
                         ?>
                       <div class="message_view"> 
                        <div class="row">
                            <div class="col-md-8">
                                <div class="col-md-3">
                                    <p><strong>Crop</strong></p>
                                    <p>{{ $value->crop}}</p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Product</strong></p>
                                    <p>{{ $value->product}}</p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>State</strong></p>
                                    <p>{{ $value->state}}</p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Language</strong></p>
                                    <p>{{ $value->language}}</p>
                                </div>

                            </div>
                            </div>
        <div class="row">
        <div class="col-md-12">
            @if(count($totalFarmer) > 0)           
              <h3>{{count($totalFarmer)}}  
                @if(count($totalFarmer) == 1)
                Farmer
                @else
                Farmers
                @endif <a target="_blank" href="{{url('demo/view-farmer-history')}}/{{$value->id}}"> <i class="fa fa-share"></i></a></h3>  
          @endif                         
        </div>
        </div>
                             
                            <div class="row">
                                <div class="col-md-11">
                                    <p style="margin-left: 15px;">{{ $value->message}}</p>
                                </div>
                                <div class="col-md-1">
                                <p style="text-align: right;">
                                    <a href="{{url('demo/edit-message')}}/{{$value->id}}"><i class="fa fa-pencil"></i></a>
                                </p>
                                </div>
                            </div>
                    </div>
                        @endforeach
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















