<?php //dd($zonal_manager_list); ?>
@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Edit SME</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Edit SME</li>
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
                        //echo "<pre>"; print_r($regional_manager_state); exit;
                        ?>
                        <form role="form" method="post" class="search-form"  id="createregionalmanager" action="{{url('regional/update-regional-manager')}}"  >                             
                            <div class="col-lg-12"> 
                                <label class="formlabel">Zonal Manager</label>
                                <select id="zonal_manager" class="frm_txt mdb-select"  name="zonal_manager">
                                    <option value="" >Choose Zonal Manager</option>
                                    <?php
                                    foreach($zonal_manager_list as $zonal_manager) {
                                        if($regional_manager->zonal_manager_id==$zonal_manager->zonal_manager_id) { ?>
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
                                <label class="formlabel">HQ Code</label>
                                <input type="text" id="regional_manager_hq_code" class="frm_txt" name="regional_manager_hq_code" placeholder="HQ Code" maxlength="10" value="{{ old('regional_manager_hq_code', $regional_manager->regional_manager_hq_code) }}">   
                                <div class="rerror"><label for="regional_manager_hq_code" generated="true" class="error">{{ $errors->first('regional_manager_hq_code') }}</label></div>
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Name</label>
                                <input type="text" id="regional_name" class="frm_txt" name="regional_name" placeholder="Name" value="{{ old('regional_name', $regional_manager->regional_manager_name) }}">   
                                <div class="rerror"><label for="regional_mobile_number" generated="true" class="error">{{ $errors->first('regional_name') }}</label></div>
                            </div>
                            <div class="col-lg-3">
                                <label class="formlabel">Mobile Number</label>
                                <input type="text" id="regional_mobile_number" class="frm_txt" name="regional_mobile_number" placeholder="Mobile Number" maxlength="10" value="{{ old('regional_mobile_number', $regional_manager->regional_manager_mobile) }}">   
                                <div class="rerror"><label for="regional_mobile_number" generated="true" class="error">{{ $errors->first('regional_mobile_number') }}</label></div>
                            </div>                                
                            <div class="col-lg-3"> 
                                <label class="formlabel">State</label>
                                <select id="regional_state" class="frm_txt mdb-select"  name="regional_state[]" multiple="multiple">
                                    <option value="">Choose your country</option>
                                    <?php
                                    foreach($state_list as $state) { 
                                        $state_id   = explode(',',$regional_manager_state->state_id);
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
                                <div class="rerror"><label for="regional_mobile_number" generated="true" class="error">{{ $errors->first('regional_state') }}</label></div>
                            </div>                                
                            <div class="col-lg-3">
                                <label class="formlabel">Pincode</label>
                                <input type="text" id="regional_pincode" class="frm_txt"  name="regional_pincode" placeholder="Pincode" maxlength="6" value="{{ old('regional_pincode', $regional_manager->regional_manager_pincode) }}">
                                <div class="rerror"><label for="regional_mobile_number" generated="true" class="error">{{ $errors->first('regional_pincode') }}</label></div>
                            </div>
                                
                            <div class="clearfix"></div>

                            <div class="col-lg-3"> 
                                <label class="formlabel">Crop Product Category</label>
                                <select id="product_category" class="frm_txt mdb-select"  name="product_category[]" multiple="multiple">
                                    <option value="">Choose category</option>
                                    <?php
                                    foreach($category_list as $category) { 
                                        $category_id   = explode(',',$regional_manager_category_list->category_id);
                                        if(in_array($category->product_crop_category_id, $category_id)) {
                                            ?>
                                            <option selected="selected" value="{{$category->product_crop_category_id}}">{{$category->category_name}}</option>
                                        <?php    
                                        }
                                        else { ?>
                                            <option value="{{$category->product_crop_category_id}}">{{$category->category_name}}</option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="product_category" generated="true" class="error">{{ $errors->first('product_category') }}</label></div>
                            </div> 
                            
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">Change Password</label> 
                                <input class="frm_chk" id="change_password" type="checkbox" name="change_password" > 
                                <div class="rerror"></div>
                            </div>
                            
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            <input type="hidden" name="regional_id" id="regional_id" value="{{ $regional_manager->regional_manager_id }}" /> 
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Update SME" >
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

<script type="text/javascript">
$(document).ready(function () {   
    jQuery.validator.addMethod("domain", function(value, element) {
        var isSuccess   = false;
        var regional_manager_hq_code    = $('#regional_manager_hq_code').val();
        var _regional                   = $('#regional_id').val();
        $.ajax({ 
                url:    "{{url('regional/check-regional-ecn')}}", 
                data:   {'regional_manager_hq_code':regional_manager_hq_code, '_regional': _regional}, 
                async:  false,
                type:   "POST",
                success: 
                        function(msg) { if(msg === "true"){
                                isSuccess = true;
                                }else if(msg === "truetrue"){
                                    isSuccess = true;
                                }
                                else{

                                    isSuccess = false;
                                } }
                });
         return isSuccess;
    }, "ENC already exists, enter a new ECN.");
    
    $('.step2submit').keypress(function (e) {
        if (e.which == 13) {
            $("#createregionalmanager").click();
        }
    });   
    
            
    $.ajaxSetup({
       headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });

    $("#createregionalmanager").validate({
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
            regional_manager_hq_code: {
                required:   true,
                minlength:  4,
                maxlength:  10,
                domain:     true
            },
            regional_name: {
                required:   true,
                minlength:  4,
                maxlength:  50
            },
            regional_mobile_number: {
                required:   true,
                minlength:  10,
                maxlength:  10,
                number: true
            },
            regional_pincode: {
                required:   true,
                minlength:  6,
                maxlength:  6,
                number:     true
            },
            regional_state: {
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









