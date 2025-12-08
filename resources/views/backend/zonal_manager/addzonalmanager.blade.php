@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Add New Zonal Manager</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add Zonal Manager</li>
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
                        //echo "<pre>"; print_r(Session::get('login_role_id')); exit;
                        ?>
                        <form role="form" method="post" class="search-form"  id="createzonalmanager" action="{{url('zonal/create-zonal-manager')}}"  >                             
                            <div class="col-lg-3"> 
                                <label class="formlabel">Name</label>
                                <input type="text" id="zonal_name" class="frm_txt" name="zonal_name" placeholder="Name" value="{{ old('zonal_name') }}">   
                                <div class="rerror"><label for="zonal_name" generated="true" class="error">{{ $errors->first('zonal_name') }}</label></div>
                            </div>
                            <div class="col-lg-3">
                                <label class="formlabel">Mobile Number</label>
                                <input type="text" id="zonal_mobile_number" class="frm_txt" name="zonal_mobile_number" placeholder="Mobile Number" maxlength="10" value="{{ old('zonal_mobile_number') }}">   
                                <div class="rerror"><label for="zonal_mobile_number" generated="true" class="error">{{ $errors->first('zonal_mobile_number') }}</label></div>
                            </div>                                
                            <div class="col-lg-3"> 
                                <label class="formlabel">State</label>
                                <select id="zonal_state" class="frm_txt mdb-select"  name="zonal_state[]" multiple="multiple">
                                    <option value="" disabled selected>Choose your state</option>
                                    <?php
                                    foreach($state_list as $state) { ?>
                                    <option value="{{$state->state_id}}">{{$state->state}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="zonal_state" generated="true" class="error">{{ $errors->first('zonal_state') }}</label></div>
                            </div>                                
                            <div class="col-lg-3">
                                <label class="formlabel">HQ Pincode</label>
                                <input type="text" id="zonal_pincode" class="frm_txt"  name="zonal_pincode" placeholder="Pincode" maxlength="6" value="{{ old('zonal_pincode') }}">
                                <div class="rerror"><label for="zonal_pincode" generated="true" class="error">{{ $errors->first('zonal_pincode') }}</label></div>
                            </div>
                            
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Create Zonal Manager" >
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
