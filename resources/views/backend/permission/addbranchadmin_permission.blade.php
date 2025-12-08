<?php //dd($zonal_manager_list); ?>
@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Branch Admin Permission</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Branch Admin Permission</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="success"></div>
                        <?php
                       // echo "<pre>"; print_r($menu_list);die;
                        ?>
                        <form role="form" method="post" class="search-form"  id="createareamanager" action="{{url('permission/create-branch-permission')}}"  >  
                            <div class="col-lg-12"> 
                                <label class="formlabel">Admin Menu</label>                                
                                <?php
                                foreach($menu_list as $menu) {
                                     $menuArr = explode(',', $menu->page_id);
                                     if($menu->menu == 'Dashboard'){
                                    ?>
                                    <input <?php if(array_intersect($menuArr, $checked)) { echo "checked";} ?> type="checkbox" name="adminmenu[]" style="visibility: hidden;" id="adminmenu" value="{{$menu->page_id}}" >
                                    <?php
                                    }else{
                                        if($menu->menu == "Demo Requests") {
                                            $menu_name = str_replace('Demo Requests', 'Create Demo', $menu->menu);
                                        } elseif ($menu->menu == "Demo Request") {
                                            $menu_name = str_replace('Demo Request', 'Demo Summary', $menu->menu);
                                        } else {
                                            $menu_name = $menu->menu;
                                        }
                                    ?>
                                        <input <?php if(array_intersect($menuArr, $checked)) { echo "checked";} ?> type="checkbox" name="adminmenu[]" id="adminmenu" value="{{$menu->page_id}}" >{{$menu_name}}&emsp;
                                    <?php } 
                                }
                                ?>
                                <div class="rerror"><label for="appmenu" generated="true" class="error">{{ $errors->first('appmenu') }}</label></div>
                            </div> 
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            <input type="hidden" name="role_id" id="role_id" value="8" />
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Set Permission" >
                        </form>
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
































