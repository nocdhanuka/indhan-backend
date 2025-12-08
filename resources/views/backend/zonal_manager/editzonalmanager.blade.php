@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Edit Zonal Manager</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Edit Zonal Manager</li>
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
                        //echo "<pre>"; print_r($zonal_manager_state); exit;
                        ?>
                        <form role="form" method="post" class="search-form"  id="createzonalmanager" action="{{url('zonal/update-zonal-manager')}}"  >                             
                            <div class="col-lg-3">
                                <label class="formlabel">HQ District Code No./ BDO Code</label>
                                <input type="text" id="ECN" class="frm_txt"  name="ECN" placeholder="ECN Code" value="{{$zonal_manager->ECN}}">
                                @if ($errors->has('your_custom_error'))
                                    <div class="error">{{ $errors->first('your_custom_error') }}</div>
                                @endif
                                <div class="rerror"><label for="ECN" generated="true" class="error">{{ $errors->first('ECN') }}</label></div>
                            </div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">Name</label>
                                <input type="text" id="zonal_name" class="frm_txt" name="zonal_name" placeholder="Name" value="{{ old('zonal_name', $zonal_manager->zonal_manager_name) }}">   
                                <div class="rerror"><label for="zonal_name" generated="true" class="error">{{ $errors->first('zonal_name') }}</label></div>
                            </div>
                            <div class="col-lg-3">
                                <label class="formlabel">Mobile Number</label>
                                <input type="text" id="zonal_mobile_number" class="frm_txt" name="zonal_mobile_number" placeholder="Mobile Number" maxlength="10" value="{{ old('zonal_mobile_number', $zonal_manager->zonal_manager_mobile) }}">   
                                <div class="rerror"><label for="zonal_mobile_number" generated="true" class="error">{{ $errors->first('zonal_mobile_number') }}</label></div>
                            </div>                                
                            <div class="col-lg-3"> 
                                <label class="formlabel">State</label>
                                <select id="zonal_state" class="frm_txt mdb-select"  name="zonal_state[]" multiple="multiple">
                                    <option value="">Choose your country</option>
                                    <?php
                                    foreach($state_list as $state) { 
                                        $state_id   = explode(',',$zonal_manager_state->state_id);
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
                                <div class="rerror"><label for="zonal_state" generated="true" class="error">{{ $errors->first('zonal_state') }}</label></div>
                            </div>                                
                            <div class="col-lg-3">
                                <label class="formlabel">Pincode</label>
                                <input type="text" id="zonal_pincode" class="frm_txt"  name="zonal_pincode" placeholder="Pincode" maxlength="6" value="{{ old('zonal_pincode', $zonal_manager->zonal_manager_pincode) }}">
                                <div class="rerror"><label for="zonal_pincode" generated="true" class="error">{{ $errors->first('zonal_pincode') }}</label></div>
                            </div>
                                                         
                            <div class="col-lg-3"> 
                                <label class="formlabel">Crop Product Category</label>
                                <select id="product_category" class="frm_txt mdb-select"  name="product_category[]" multiple="multiple">
                                    <option value="">Choose category</option>
                                    <?php
                                    foreach($category_list as $category) { 
                                        $category_id   = explode(',',$zonal_manager_category_list->category_id);
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
                            <input type="hidden" name="zonal_id" id="zonal_id" value="{{ $zonal_manager->zonal_manager_id }}" /> 
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Update Zonal Manager" >
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
    jQuery.validator.addMethod("domain", function(value, element) {
        var isSuccess   = false;
        var bdo_code    = $('#ECN').val();
        var _bdo        = $('#zonal_id').val();
        $.ajax({ 
                url:    "{{url('zonal/check-ecn')}}", 
                data:   {'bdo_code':bdo_code, '_bdo': _bdo}, 
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
                maxlength:  10,
                //number:     true,
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
</script>




















