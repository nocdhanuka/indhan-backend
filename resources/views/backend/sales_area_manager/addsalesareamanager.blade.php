<?php //dd($zonal_manager_list); ?>
@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Add New ASM/S1</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add ASM/S1</li>
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
                        //echo "<pre>"; print_r($language_list);
                        ?>
                        <form role="form" method="post" class="search-form"  id="createsalesareamanager" action="{{url('salesarea/create-sales-area-manager')}}"  >                                                          
                            <div class="col-lg-12"> 
                                <label class="formlabel">Zonal Manager</label>
                                <select id="zonal_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="zonal_manager">
                                    <option value="">Choose Zonal Manager</option>
                                    <?php
                                    foreach($zonal_manager_list as $zonal_manager) { ?>
                                    <option value="{{$zonal_manager->zonal_manager_id}}">{{$zonal_manager->zonal_manager_name}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="zonal_manager" generated="true" class="error">{{ $errors->first('zonal_manager') }}</label></div>
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">SME</label>
                                <select id="regional_manager" class="frm_txt mdb-select"  name="regional_manager">
                                    <option value="">Choose SME</option>                                    
                                </select>
                                <div class="rerror"><label for="regional_manager" generated="true" class="error">{{ $errors->first('regional_manager') }}</label></div>
                            </div>
                            <div class="col-lg-3"> 
                                <label class="formlabel">DGM</label>
                                <select id="sales_regional_manager" class="frm_txt mdb-select"  name="sales_regional_manager">
                                    <option value="">Choose DGM</option>                                    
                                </select>
                                <div class="rerror"><label for="sales_regional_manager" generated="true" class="error">{{ $errors->first('sales_regional_manager') }}</label></div>
                            </div>
                            <div class="col-lg-12"> 
                                <label class="formlabel">HQ Code</label>
                                <input type="text" id="sales_area_manager_hq_code" class="frm_txt" name="sales_area_manager_hq_code" placeholder="HQ Code" maxlength="10" value="{{ old('sales_area_manager_hq_code') }}">   
                                <div class="rerror"><label for="sales_area_manager_hq_code" generated="true" class="error">{{ $errors->first('sales_area_manager_hq_code') }}</label></div>
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Name</label>
                                <input type="text" id="sales_area_name" class="frm_txt" name="sales_area_name" placeholder="Name" value="{{ old('sales_area_name') }}">   
                                <div class="rerror"><label for="sales_area_name" generated="true" class="error">{{ $errors->first('sales_area_name') }}</label></div>
                            </div>
                            <div class="col-lg-3">
                                <label class="formlabel">Mobile Number</label>
                                <input type="text" id="sales_area_mobile_number" class="frm_txt" name="sales_area_mobile_number" placeholder="Mobile Number" maxlength="10" value="{{ old('sales_area_mobile_number') }}">   
                                <div class="rerror"><label for="sales_area_mobile_number" generated="true" class="error">{{ $errors->first('sales_area_mobile_number') }}</label></div>
                            </div>                                
                            <div class="col-lg-3"> 
                                <label class="formlabel">State</label>
                                <select id="sales_area_state" class="frm_txt mdb-select"  name="sales_area_state[]" multiple="multiple">
                                    <option value="" disabled selected>Choose your country</option>
                                    <?php
                                    foreach($state_list as $state) { ?>
                                    <option value="{{$state->state_id}}">{{$state->state}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="sales_area_state" generated="true" class="error">{{ $errors->first('sales_area_state') }}</label></div>
                            </div>                                
                            <div class="col-lg-3">
                                <label class="formlabel">Pincode</label>
                                <input type="text" id="sales_area_pincode" class="frm_txt"  name="sales_area_pincode" placeholder="Pincode" maxlength="6" value="{{ old('sales_area_pincode') }}">
                                <div class="rerror"><label for="sales_area_pincode" generated="true" class="error">{{ $errors->first('sales_area_pincode') }}</label></div>
                            </div>
                                
                            <div class="clearfix"></div>

                            <div class="col-lg-3"> 
                                <label class="formlabel">Crop Product Category</label>
                                <select id="product_category" class="frm_txt mdb-select"  name="product_category[]" multiple="multiple">
                                    <option value="" disabled selected>Choose category</option>
                                    <?php
                                    foreach($category_list as $category) { ?>
                                    <option value="{{$category->product_crop_category_id}}">{{$category->category_name}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="product_category" generated="true" class="error">{{ $errors->first('product_category') }}</label></div>
                            </div> 
                            
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Create ASM/S1" >
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
        var sales_area_manager_hq_code    = $('#sales_area_manager_hq_code').val();
        $.ajax({ 
                url:    "{{url('salesarea/check-sales-area-ecn')}}", 
                data:   {'sales_area_manager_hq_code':sales_area_manager_hq_code, '_sales_area': '0'}, 
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
            $("#createsalesareamanager").click();
        }
    });   
    
            
    $.ajaxSetup({
       headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });
     
    $("#createsalesareamanager").validate({
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
            regional_manager: {
                required:   true,
            },
            sales_regional_manager: {
                required:   true,
            },
            sales_area_manager: {
                required:   true,
            },
            sales_sales_area_manager: {
                required:   true,
            },
            sales_area_manager_hq_code: {
                required:   true,
                minlength:  4,
                maxlength:  10,
                domain:     true
            },
            sales_area_name: {
                required:   true,
                minlength:  4,
                maxlength:  50
            },
            sales_area_mobile_number: {
                required:   true,
                minlength:  10,
                maxlength:  10,
                number: true
            },
            sales_area_pincode: {
                required:   true,
                minlength:  6,
                maxlength:  6,
                number:     true
            },
            "sales_area_state[]": {
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
          
$.ajaxSetup({
   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});

$('#zonal_manager').on('change',function(){
    var zonal_manager   = $('#zonal_manager').val();
    var data = {
        _token:         $(this).data('token'),
        zonal_manager:  zonal_manager
    }
    $.ajax({
        type:       'POST',
        url:        "{{url('salesarea/zonal-regional')}}",
        data:       data,
        success:    function (data) {
                            var jsonData1   = JSON.parse(data);
                            var appenddata1 = '';
                            $("#regional_manager").empty();
                            appenddata1 += "<option value = ''>--Select SME--</option>";
                            for (var i = 0; i < jsonData1.length; i++) {
                                appenddata1 += "<option value = '" + jsonData1[i].regional_manager_id + "'>" + jsonData1[i].regional_manager_name + " </option>";
                            }
                            $("#regional_manager").append(appenddata1);
                            return false;
                    },
        error:      function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
    });
});

$('#regional_manager').on('change',function(){
    var zonal_manager   = $('#zonal_manager').val();
    var data = {
        _token:         $(this).data('token'),
        zonal_manager:  zonal_manager
    }
    $.ajax({
        type:       'POST',
        url:        "{{url('salesarea/zonal-sales-regional')}}",
        data:       data,
        success:    function (data) {
                            var jsonData1   = JSON.parse(data);
                            var appenddata1 = '';
                            $("#sales_regional_manager").empty();
                            appenddata1 += "<option value = ''>--Select DGM--</option>";
                            for (var i = 0; i < jsonData1.length; i++) {
                                appenddata1 += "<option value = '" + jsonData1[i].sales_regional_manager_id + "'>" + jsonData1[i].sales_regional_manager_name + " </option>";
                            }
                            $("#sales_regional_manager").append(appenddata1);
                            return false;
                    },
        error:      function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
    });
});
</script>
