<?php //dd($zonal_manager_list); ?>
@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Edit ASM/S1</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Edit ASM/S1</li>
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
                        //echo "<pre>"; print_r($area_manager_state); exit;
                        ?>
                        <form role="form" method="post" class="search-form"  id="createareamanager" action="{{url('area/update-area-manager')}}"  >                             
                            <div class="col-lg-12"> 
                                <label class="formlabel">Zonal Manager</label>
                                <select id="zonal_manager" class="frm_txt mdb-select"  name="zonal_manager">
                                    <option value="" >Choose Zonal Manager</option>
                                    <?php
                                    foreach($zonal_manager_list as $zonal_manager) {
                                        if($area_manager->zonal_manager_id==$zonal_manager->zonal_manager_id) { ?>
                                            <option selected="selected" value="{{$zonal_manager->zonal_manager_id}}">{{$zonal_manager->zonal_manager_name}}</option>
                                        <?php    
                                        }
                                        else { ?>
                                            <option value="{{$zonal_manager->zonal_manager_id}}">{{$zonal_manager->zonal_manager_name}}</option>
                                            <?php
                                        }    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="zonal_manager" generated="true" class="error">{{ $errors->first('zonal_manager') }}</label></div>
                            </div> 
                            
                            <div class="col-lg-12"> 
                                <label class="formlabel">SME</label>
                                <select id="regional_manager" class="frm_txt mdb-select"  name="regional_manager">
                                    <option value="" >Choose SME</option>
                                    <?php
                                    foreach($regional_manager_list as $regional_manager) {
                                        if($area_manager->regional_manager_id==$regional_manager->regional_manager_id) { ?>
                                            <option selected="selected" value="{{$regional_manager->regional_manager_id}}">{{$regional_manager->regional_manager_name}}</option>
                                        <?php    
                                        }
                                        else { ?>
                                            <option value="{{$regional_manager->regional_manager_id}}">{{$regional_manager->regional_manager_name}}</option>
                                            <?php
                                        }    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="regional_manager" generated="true" class="error">{{ $errors->first('regional_manager') }}</label></div>
                            </div> 
                            <div class="col-lg-12"> 
                                <label class="formlabel">DGM</label>
                                <select id="sales_regional_manager" class="frm_txt mdb-select"  name="sales_regional_manager">
                                    <option value="" >Choose DGM</option>
                                    <?php
                                    foreach($sales_regional_manager_list as $sales_regional_manager) {
                                        if($area_manager->sales_regional_manager_id==$sales_regional_manager->sales_regional_manager_id) { ?>
                                            <option selected="selected" value="{{$sales_regional_manager->sales_regional_manager_id}}">{{$sales_regional_manager->sales_regional_manager_name}}</option>
                                        <?php    
                                        }
                                        else { ?>
                                            <option value="{{$sales_regional_manager->sales_regional_manager_id}}">{{$sales_regional_manager->sales_regional_manager_name}}</option>
                                            <?php
                                        }    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="sales_regional_manager" generated="true" class="error">{{ $errors->first('sales_regional_manager') }}</label></div>
                            </div> 
                            <div class="col-lg-12"> 
                                <label class="formlabel">HQ Code</label>
                                <input type="text" id="area_manager_hq_code" class="frm_txt" name="area_manager_hq_code" placeholder="HQ Code" maxlength="10" value="{{ old('area_manager_hq_code', $area_manager->area_manager_hq_code) }}">   
                                <div class="rerror"><label for="area_manager_hq_code" generated="true" class="error">{{ $errors->first('area_manager_hq_code') }}</label></div>
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Name</label>
                                <input type="text" id="area_name" class="frm_txt" name="area_name" placeholder="Name" value="{{ old('area_name', $area_manager->area_manager_name) }}">   
                                <div class="rerror"><label for="area_mobile_number" generated="true" class="error">{{ $errors->first('area_name') }}</label></div>
                            </div>
                            <div class="col-lg-3">
                                <label class="formlabel">Mobile Number</label>
                                <input type="text" id="area_mobile_number" class="frm_txt" name="area_mobile_number" placeholder="Mobile Number" maxlength="10" value="{{ old('area_mobile_number', $area_manager->area_manager_mobile) }}">   
                                <div class="rerror"><label for="area_mobile_number" generated="true" class="error">{{ $errors->first('area_mobile_number') }}</label></div>
                            </div>                                
                            <div class="col-lg-3"> 
                                <label class="formlabel">State</label>
                                <select id="area_state" class="frm_txt mdb-select"  name="area_state[]" multiple="multiple">
                                    <option value="">Choose your country</option>
                                    <?php
                                    foreach($state_list as $state) { 
                                        $state_id   = explode(',',$area_manager_state->state_id);
                                        if(in_array($state->state_id, $state_id)) {
                                            ?>
                                            <option selected="selected" value="{{$state->state_id}}">{{$state->state}}</option>
                                        <?php    
                                        }
                                        else { ?>
                                            <option value="{{$state->state_id}}">{{$state->state}}</option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="area_mobile_number" generated="true" class="error">{{ $errors->first('area_state') }}</label></div>
                            </div>                                
                            <div class="col-lg-3">
                                <label class="formlabel">Pincode</label>
                                <input type="text" id="area_pincode" class="frm_txt"  name="area_pincode" placeholder="Pincode" maxlength="6" value="{{ old('area_pincode', $area_manager->area_manager_pincode) }}">
                                <div class="rerror"><label for="area_mobile_number" generated="true" class="error">{{ $errors->first('area_pincode') }}</label></div>
                            </div>
                            
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            <input type="hidden" name="area_id" id="area_id" value="{{ $area_manager->area_manager_id }}" /> 
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Update ASM/S1" >
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
    $("#createareamanager").validate({
        errorPlacement: function (error, element) {
                            console.log($(element).next());
                            $(element).next('.rerror').html(error);
                            $(element).next().next('.rerror').html(error);
                        },
        ignore: "input[type='text']:hidden",
        rules: {
            zonal_manager: {
                required:   true,
            },
            area_manager_hq_code: {
                required:   true,
                minlength:  4,
                maxlength:  10
            },
            area_name: {
                required:   true,
                minlength:  4,
                maxlength:  50
            },
            area_mobile_number: {
                required:   true,
                minlength:  10,
                maxlength:  10,
                number: true
            },
            area_pincode: {
                required:   true,
                minlength:  6,
                maxlength:  6,
                number:     true
            },
            area_state: {
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
</script>
