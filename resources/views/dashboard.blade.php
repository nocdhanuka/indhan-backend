
@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->

@include('include.admin.backend_sidebar')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Dashboard</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="row">
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <a href="open-tickets"><span class="info-box-icon bg-yellow"><i class="fa fa-file"></i></span></a>
                    <div class="info-box-content">
                        <span class="info-box-text">Open Ticket</span>
                        <span class="info-box-number">{{$open_ticket}}</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
            <!-- /.col -->

            <!-- fix for small devices only -->
            <div class="clearfix visible-sm-block"></div>
            
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <a href="tickets-list">  <span class="info-box-icon bg-aqua">
                            <i class="fa fa-list"></i></span></a>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Ticket</span>
                        <span class="info-box-number">{{$ticket_count}}</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->

            <!-- fix for small devices only -->
            <div class="clearfix visible-sm-block"></div>
                        
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <a href="#">  <span class="info-box-icon bg-green"><i class="ion ion-ios-people-outline"></i></span></a>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Farmer</span>
                        <span class="info-box-number">{{$farmer_count}}</span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div><!-- /.col -->
        </div><!-- /.row -->

        <!-- Main row -->
        <div class="row">
            <!-- Left col -->
            <div class="col-md-8">
                <!-- /.box -->
                <div class="row">
                    <div class="col-md-6">
                    </div><!-- /.col -->

                    <div class="col-md-6">
                    </div><!-- /.col -->
                </div><!-- /.row -->

                <!-- TABLE: LATEST ORDERS -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Open Ticket List</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table no-margin">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Ticket</th>
                                        <th>BDO</th>
                                        <th>State</th>
                                        <th>District</th>
                                        <th>Tehsil</th>
                                        <th>Village</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $serial = 1;
                                        foreach ($top_open_ticket as $ticket) {
                                            ?>
                                            <tr>
                                                <td><?php echo $serial++; ?></td>
                                                <td><?php echo $ticket->ticket_no; ?></td>
                                                <td><?php echo $ticket->name; ?></td>
                                                <td><?php echo $ticket->state; ?></td>
                                                <td><?php echo $ticket->district; ?></td>
                                                <td><?php echo $ticket->tehsil; ?></td>
                                                <td><?php echo $ticket->village; ?></td>
                                            </tr>
                                            <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div><!-- /.table-responsive -->
                    </div><!-- /.box-body -->
                    <div class="box-footer clearfix">
                        <a href="open-tickets" class="btn btn-sm btn-default btn-flat pull-right">View All Open Tickets</a>
                    </div><!-- /.box-footer -->
                </div><!-- /.box -->
            </div><!-- /.col -->

            <div class="col-md-4">
                <!-- PRODUCT LIST -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Per Ticket Farmer (Closed)</h3>
                    </div><!-- /.box-header -->
                    <?php
                    //echo "<pre>"; print_r($farmer_query_result); exit;
                    foreach ($farmer_query_result as $farmers) { ?>                    
                    <div class="box-body">
                        <ul class="products-list product-list-in-box">
                            <li class="item ticket_block">
                                <div class="product-info">
                                    BDO Name - {{$farmers->name}}
                                    <br>
                                    <span class="">ECN - {{$farmers->ticket_no}} </span>                                    
                                    <span class="pull-right ticket_box">{{$farmers->farmers}}</span>
                                    <span class="product-description">Ticket Created - {{$farmers->created_at}}</span>
                                </div>
                            </li><!-- /.item -->
                        </ul>
                    </div><!-- /.box-body -->                    
                    <?php 
                    }
                    ?>
                    <div class="box-footer text-center">
                        <!--<a href="user-payment-list" class="uppercase">View All</a>-->
                    </div><!-- /.box-footer -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@include('include.admin.footer')

<!-- Control Sidebar -->
<!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>

</div><!-- ./wrapper -->

<!-- jQuery 2.1.4 -->
<script src="{{asset('assets/admin/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
<!-- Bootstrap 3.3.5 -->
<script src="{{asset('assets/admin/bootstrap/js/bootstrap.min.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('assets/admin/dist/js/app.min.js')}}"></script>

<!-- Sparkline -->
<script src="{{asset('assets/admin/plugins/sparkline/jquery.sparkline.min.js')}}"></script>
<!-- jvectormap -->
<script src="{{asset('assets/admin/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
<script src="{{asset('assets/admin/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
<!-- SlimScroll 1.3.0 -->
<script src="{{asset('assets/admin/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- ChartJS 1.0.1 -->
<script src="{{asset('assets/admin/plugins/chartjs/Chart.min.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{asset('assets/admin/dist/js/pages/dashboard2.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
</body>
</html>
