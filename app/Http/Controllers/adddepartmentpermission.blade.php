<?php //dd($zonal_permission); ?>
<?php
$zonal_permission           = explode(',',$zonal_list[0]['app_menu_id']);
$regional_permission        = explode(',',$regional_list[0]['app_menu_id']);
$sales_regional_permission  = explode(',',$sales_regional_list[0]['app_menu_id']);
$area_permission            = explode(',',$area_list[0]['app_menu_id']);
$sales_area_permission      = explode(',',$sales_area_list[0]['app_menu_id']);
$marketing_supervisor_permission      = explode(',',$marketing_supervisor_list[0]['app_menu_id']);
$bdo_permission             = explode(',',$bdo_list[0]['app_menu_id']);
$dd_permission             = explode(',',$dd_list[0]['app_menu_id']);
//dd($area_permission);
?>
@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Department Wise Permission</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Department Wise Permission</li>
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
                        <form role="form" method="post" class="search-form"  id="createareamanager" action="{{url('permission/create-department-permission')}}"  >                                                          
                            <div class="col-lg-12"> 
                                <label class="formlabel">Zonal App Menu</label>                                
                                <?php
                                foreach($menu_list as $menu) { 
                                    if(in_array($menu->menu_id, $zonal_permission)) { ?>
                                        <input type="checkbox" name="zonalappmenu[]" checked="checked" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                    <?php
                                    }
                                    else { ?>
                                        <input type="checkbox" name="zonalappmenu[]" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                    <?php }
                                }
                                ?>
                                <div class="rerror"><label for="zonalappmenu" generated="true" class="error">{{ $errors->first('zonalappmenu') }}</label></div>
                            </div>                                
                            <div class="col-lg-12"> 
                                <label class="formlabel">Regional App Menu</label>                                
                                <?php
                                foreach($menu_list as $menu) { 
                                    if(in_array($menu->menu_id, $regional_permission)) { ?>
                                        <input type="checkbox" name="regionalappmenu[]" checked="checked" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                    <?php
                                    }
                                    else { ?>
                                        <input type="checkbox" name="regionalappmenu[]" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                    <?php }    
                                }
                                ?>
                                <div class="rerror"><label for="regionalappmenu" generated="true" class="error">{{ $errors->first('regionalappmenu') }}</label></div>
                            </div>
                            
                            
                            <div class="col-lg-12"> 
                                <label class="formlabel">Sales Regional App Menu</label>                                
                                <?php
                                foreach($menu_list as $menu) { 
                                    if(in_array($menu->menu_id, $sales_regional_permission)) { ?>
                                        <input type="checkbox" name="salesregionalappmenu[]" checked="checked" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                    <?php
                                    }
                                    else { ?>
                                        <input type="checkbox" name="salesregionalappmenu[]" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                    <?php }    
                                }
                                ?>
                                <div class="rerror"><label for="salesregionalappmenu" generated="true" class="error">{{ $errors->first('salesregionalappmenu') }}</label></div>
                            </div>
                            
                            
                            <div class="col-lg-12"> 
                                <label class="formlabel">Area App Menu</label>                                
                                <?php
                                foreach($menu_list as $menu) {
                                    if(in_array($menu->menu_id, $area_permission)) { ?>
                                        <input type="checkbox" name="areaappmenu[]" checked="checked" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                    <?php
                                    }
                                    else { ?>
                                        <input type="checkbox" name="areaappmenu[]" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                    <?php } 
                                }
                                ?>
                                <div class="rerror"><label for="areaappmenu" generated="true" class="error">{{ $errors->first('areaappmenu') }}</label></div>
                            </div>
                            
                            
                            <div class="col-lg-12"> 
                                <label class="formlabel">Sales Area App Menu</label>                                
                                <?php
                                foreach($menu_list as $menu) {
                                    if(in_array($menu->menu_id, $sales_area_permission)) { ?>
                                        <input type="checkbox" name="salesareaappmenu[]" checked="checked" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                    <?php
                                    }
                                    else { ?>
                                        <input type="checkbox" name="salesareaappmenu[]" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                    <?php } 
                                }
                                ?>
                                <div class="rerror"><label for="salesareaappmenu" generated="true" class="error">{{ $errors->first('salesareaappmenu') }}</label></div>
                            </div>

                            <div class="col-lg-12"> 
                                <label class="formlabel">Marketing Supervisor App Menu</label>                                
                                <?php
                                foreach($menu_list as $menu) { 
                                    if(in_array($menu->menu_id, $marketing_supervisor_permission)) { ?>
                                        <input type="checkbox" name="marketingsupervisorappmenu[]" checked="checked" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                    <?php
                                    }
                                    else { ?>
                                        <input type="checkbox" name="marketingsupervisorappmenu[]" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                    <?php }    
                                }
                                ?>
                                <div class="rerror"><label for="marketingsupervisorappmenu" generated="true" class="error">{{ $errors->first('marketingsupervisorappmenu') }}</label></div>
                            </div>
                            
                            
                            <div class="col-lg-12"> 
                                <label class="formlabel">BDO App Menu</label>                                
                                <?php
                                foreach($menu_list as $menu) {
                                    if(in_array($menu->menu_id, $bdo_permission)) { ?>
                                        <input type="checkbox" name="bdoappmenu[]" checked="checked" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                    <?php
                                    }
                                    else { ?>
                                        <input type="checkbox" name="bdoappmenu[]" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                    <?php } 
                                }
                                ?>
                                <div class="rerror"><label for="bdoappmenu" generated="true" class="error">{{ $errors->first('bdoappmenu') }}</label></div>
                            </div>


                            <div class="col-lg-12"> 
                                <label class="formlabel">DD App Menu</label>                                
                                <?php
                                foreach($menu_list as $menu) {
                                    if(in_array($menu->menu_id, $dd_permission)) { ?>
                                        <input type="checkbox" name="ddappmenu[]" checked="checked" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                    <?php
                                    }
                                    else { ?>
                                        <input type="checkbox" name="ddappmenu[]" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                    <?php } 
                                }
                                ?>
                                <div class="rerror"><label for="ddappmenu" generated="true" class="error">{{ $errors->first('ddappmenu') }}</label></div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="clearfix"></div>                            
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Set Permission" >
                                 
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>  
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
<script type="text/javascript">
$(document).ready(function () {
    $.ajaxSetup({
       headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });
});
</script>




















































































