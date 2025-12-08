@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->
<style>
.bootstrap-select .btn-info2 {
    font-weight: bold !important;
    color: #121212 !important;
    background: #f7f6f5 !important;
    border: 1px solid #c0c0c0;
    font-weight: normal !important;
}
.bootstrap-select .dropdown-menu{
    background: #fff!important;
    color: #121212 !important;
    border: 1px solid #c0c0c0;
    font-weight: normal !important;
}
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Add New DD</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add DD</li>
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
                        <form role="form" method="post" class="search-form"  id="createdd" action="{{url('organogram/create-dd-orn')}}"  >                             
                            <div class="col-lg-3"> 
                                <label class="formlabel">Zonal Mangers</label>
                                <select id="zonal_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="zonal_manager">
                                    <option value="">--Select Zonal Manager--</option>
                                    <?php
                                    foreach($zonal_list as $zonal) { ?>
                                    <option value="{{$zonal->zonal_manager_id}}">{{$zonal->zonal_manager_name}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="zonal_manager" generated="true" class="error">{{ $errors->first('zonal_manager') }}</label></div>
                            </div> 
                            
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix">
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">Regional Mangers</label>
                                <select id="regional_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="regional_manager">
                                    <option value="">--Select Regional Manager--</option>
                                </select>
                                <div class="rerror"><label for="regional_manager" generated="true" class="error">{{ $errors->first('regional_manager') }}</label></div>
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Sales Regional Mangers</label>
                                <select id="sales_regional_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="sales_regional_manager">
                                    <option value="">--Select Sales Regional Manager--</option>
                                </select>
                                <div class="rerror"><label for="sales_regional_manager" generated="true" class="error">{{ $errors->first('sales_regional_manager') }}</label></div>
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Area Mangers</label>
                                <select id="area_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="area_manager">
                                    <option value="">--Select Area Manager--</option>
                                </select>
                                <div class="rerror"><label for="area_manager" generated="true" class="error">{{ $errors->first('area_manager') }}</label></div>
                            </div>
                            <div class="col-lg-3"> 
                                <label class="formlabel">Sales Area Mangers</label>
                                <select id="sales_area_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="sales_area_manager">
                                    <option value="">--Select Sales Area Manager--</option>
                                </select>
                                <div class="rerror"><label for="sales_area_manager" generated="true" class="error">{{ $errors->first('sales_area_manager') }}</label></div>
                            </div>
                            </div>
                             <div class="clearfix"> </div>
                            <div class="col-lg-3"> 
                                <label class="formlabel">Front Liner</label>
                                <select id="front_liner" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="bdo_id">
                                    <option value="">--Select Front Liner--</option>
                                </select>
                                <div class="rerror"><label for="front_liner" generated="true" class="error">{{ $errors->first('front_liner') }}</label></div>
                            </div>
                            
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
                                
                            <div class="col-lg-3"> 
                                <label class="formlabel">State</label>
                                <select id="state" class="frm_txt"  data-token="{{csrf_token()}}"  name="state">
                                    <option value="">Select State</option>
                                    <?php
                                    foreach($state_list as $state) { ?>
                                    <option value="{{$state->state_id}}">{{$state->state}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="state" generated="true" class="error">{{ $errors->first('state') }}</label></div>
                            </div>
                            
                            <div class="col-lg-3"> 

                                <label class="formlabel">District</label>
                                <select id="district" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="district">
                                    <option value="">--Select District--</option>
                                </select>
                                <div class="rerror"><label for="district" generated="true" class="error">{{ $errors->first('district') }}</label></div>
                            </div> 

                            <div class="col-lg-3"> 
                                <label class="formlabel">Tehsil</label>
                                <select id="tehsil" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="tehsil">
                                    <option value="">--Select Tehsil--</option>
                                </select>
                                <div class="rerror"><label for="tehsil" generated="true" class="error">{{ $errors->first('tehsil') }}</label></div>
                            </div>

                            <div class="col-lg-3"> 
                                <label class="formlabel">Village</label>
                                <select id="village" multiple class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="village[]">
                                <option value="">--Select Village--</option>
                                </select>
                                <div class="rerror"><label for="village" generated="true" class="error">{{ $errors->first('village') }}</label></div>
                            </div>

                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>

                            <div class="col-lg-3">
                                <label class="formlabel">Language</label>
                                <select id="language" class="frm_txt"  name="language">
                                    <option value="">--Select Language</option>
                                    <?php 
                                    foreach($language_list as $language) { ?>
                                    <option value="{{$language->id}}">{{$language->language}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="language" generated="true" class="error">{{ $errors->first('language') }}</label></div>
                            </div>
                            
                            <div class="col-lg-3">  
                                <label class="formlabel">Department</label>
                                <input type="text" id="department" class="frm_txt"  name="department" placeholder="Department">
                                <div class="rerror"><label for="department" generated="true" class="error">{{ $errors->first('department') }}</label></div>
                            </div>
                            
                            <div class="clearfix"></div>


                            <div class="col-lg-3">  
                                <label class="formlabel">Designation</label>
                                <input type="text" id="designation" class="frm_txt"  name="designation" placeholder="Designation" value="Dhanuka Doctor" readonly>
                                <div class="rerror"><label for="designation" generated="true" class="error">{{ $errors->first('designation') }}</label></div>
                            </div>
                            
                            <div class="col-lg-3">
                                <label class="formlabel">HQ District Code No./ DD Code</label>
                                <input type="text" id="dd_code" class="frm_txt"  name="dd_code" placeholder="DD Code" maxlength="10">
                                @if ($errors->has('your_custom_error'))
                                    <div class="error">{{ $errors->first('your_custom_error') }}</div>
                                @endif
                                <div class="rerror"><label for="dd_code" generated="true" class="error">{{ $errors->first('dd_code') }}</label></div>
                            </div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel"> DD Name</label>
                                <input type="text" id="name" class="frm_txt"  name="name" placeholder="Name">
                                <div class="rerror"><label for="name" generated="true" class="error">{{ $errors->first('name') }}</label></div>
                            </div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">Mobile No.</label>
                                <input type="text" id="mobile_no" class="frm_txt"  name="mobile_no" placeholder="Mobile No.">
                                <div class="rerror"><label for="mobile_no" generated="true" class="error">{{ $errors->first('mobile_no') }}</label></div>
                            </div>    
                           
                            <div class="clearfix"></div>                      
                           
                            <div class="col-lg-3">  
                                <label class="formlabel">Alternate Mobile No.</label>
                                <input type="text" id="dd_alt_mobile_no" class="frm_txt"  name="dd_alt_mobile_no" placeholder="Alternate Mobile No.">
                                <div class="rerror"><label for="dd_alt_mobile_no" generated="true" class="error">{{ $errors->first('dd_alt_mobile_no') }}</label></div>
                            </div>

                             <div class="col-lg-3">  
                                <label class="formlabel">DD HQ</label>
                                <input type="text" id="dd_hq" class="frm_txt"  name="dd_hq" placeholder="DD HQ">
                                <div class="rerror"><label for="dd_hq" generated="true" class="error">{{ $errors->first('dd_hq') }}</label></div>
                            </div>

                             <div class="col-lg-3">  
                                <label class="formlabel">Email</label>
                                <input type="text" id="dd_email" class="frm_txt"  name="dd_email" placeholder="DD Emial">
                                <div class="rerror"><label for="dd_email" generated="true" class="error">{{ $errors->first('dd_email') }}</label></div>
                            </div>

                             <div class="col-lg-3">  
                                <label class="formlabel">DD Address</label>
                               <!--  <input type="text" id="dd_address" class="frm_txt"  name="dd_hq" placeholder="DD Address"> -->
                                <textarea id="dd_address" class="frm_txt"  name="dd_address" placeholder="DD Address"></textarea>
                                <div class="rerror"><label for="dd_address" generated="true" class="error">{{ $errors->first('dd_address') }}</label></div>
                            </div>
                                
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <div class="col-lg-4"> 
                                <label class="formlabel">App Permission To Edit Ticket</label>	
                                <input class="frm_chk" id="app_permission_edit_ticket" type="checkbox" name="app_permission_edit_ticket"> 
                                <div class="rerror"></div>
                            </div>
                            <div class="col-lg-4"> 
                                <label class="formlabel">App Permission To Close Ticket</label>	
                                <input class="frm_chk" id="app_permission_close_ticket" type="checkbox" name="app_permission_close_ticket"> 
                                <div class="rerror"></div>
                            </div>
                            
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Create DD" >
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
    $('#zonal_manager').on('change',function(){
        var zonal_manager   = $('#zonal_manager').val();
        var data = {
            _token:         $(this).data('token'),
            zonal_manager:  zonal_manager
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('area/regional-sales')}}",
            data:       data,
            success:    function (data) {
                            if(data) {
                                $("#regional_manager").empty();
                                $("#sales_regional_manager").empty();
                                $("#area_manager").empty();
                                $("#sales_area_manager").empty();
                                var jsonData1   = JSON.parse(data);
                                if(typeof jsonData1.regional!='undefined') {
                                    var appenddata1 = '';
                                    appenddata1 += "<option value = ''>--Select Regional Manger--</option>";
                                    for (var i = 0; i < jsonData1.regional.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1.regional[i].regional_manager_id + "'>" + jsonData1.regional[i].regional_manager_name + " </option>";
                                    }
                                    $("#regional_manager").append(appenddata1);
                                }
                                else {
                                    var appenddata1 = "<option value = ''>--Select Regional Manager--</option>";
                                    $("#regional_manager").append(appenddata1);
                                }
                                
                                if(typeof jsonData1.sales!='undefined') {
                                    var appenddata2 = '';
                                    appenddata2 += "<option value = ''>--Select Sales Regional Manger--</option>";
                                    for (var i = 0; i < jsonData1.sales.length; i++) {
                                        appenddata2 += "<option value = '" + jsonData1.sales[i].sales_regional_manager_id + "'>" + jsonData1.sales[i].sales_regional_manager_name + " </option>";
                                    }
                                    $("#sales_regional_manager").append(appenddata2);
                                }
                                else {
                                    var appenddata1 = "<option value = ''>--Select Regional Sales Manager--</option>";
                                    $("#sales_regional_manager").append(appenddata1);
                                }
                                var appenddata3 = "<option value = ''>--Select Area Manager--</option>";
                                $("#area_manager").append(appenddata3);
                                var appenddata4 = "<option value = ''>--Select Area Sales Manager--</option>";
                                $("#sales_area_manager").append(appenddata4);
                                return false;
                            }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
    
    $('#regional_manager').on('change',function(){
        var regional_manager   = $('#regional_manager').val();
        var data = {
            _token:             $(this).data('token'),
            regional_manager:   regional_manager
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('area/area-sales')}}",
            data:       data,
            success:    function (data) {
                                $("#area_manager").empty();
                                $("#sales_area_manager").empty();
                                var jsonData1   = JSON.parse(data);
                                if(jsonData1.area!='undefined') {
                                    var appenddata1 = '';
                                    appenddata1 += "<option value = ''>--Select Regional Manger--</option>";
                                    for (var i = 0; i < jsonData1.area.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1.area[i].area_manager_id + "'>" + jsonData1.area[i].area_manager_name + " </option>";
                                    }
                                    $("#area_manager").append(appenddata1);
                                }
                                if(jsonData1.sales!='undefined') {
                                    var appenddata2 = '';
                                    appenddata2 += "<option value = ''>--Select Sales Regional Manger--</option>";
                                    for (var i = 0; i < jsonData1.sales.length; i++) {
                                        appenddata2 += "<option value = '" + jsonData1.sales[i].sales_area_manager_id + "'>" + jsonData1.sales[i].sales_area_manager_name + " </option>";
                                    }
                                    $("#sales_area_manager").append(appenddata2);
                                }
                                return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });

    $('#sales_area_manager').on('change',function(){
        var sales_area_manager = $('#sales_area_manager').val();
        var data = {
            _token:             $(this).data('token'),
            sales_area_manager:   sales_area_manager
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('area/front-liners')}}",
            data:       data,
            success:    function (data) {
                                $("#front_liner").empty();
                                var jsonData1   = JSON.parse(data);
                                if(jsonData1!='undefined') {
                                    var appenddata1 = '';
                                    appenddata1 += "<option value = ''>--Select Front Liner--</option>";
                                    for (var i = 0; i < jsonData1.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1[i].id + "'>" + jsonData1[i].name + " </option>";
                                    }
                                    $("#front_liner").append(appenddata1);
                                }
                                
                                return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
        
    $('#state').on('change',function(){
        var state   = $('#state').val();
        var data = {
            _token: $(this).data('token'),
            state:  state
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('district/state-district')}}",
            data:       data,
            success:    function (data) {
                            if(data) {
                                $("#district").empty();
                                var jsonData1   = JSON.parse(data);
                                if(jsonData1.district!='undefined') {
                                    var appenddata1 = '';
                                    appenddata1 += "<option value = ''>--Select District--</option>";
                                    for (var i = 0; i < jsonData1.district.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1.district[i].district_id + "'>" + jsonData1.district[i].district + " </option>";
                                    }
                                    $("#district").append(appenddata1);
                                }
                                return false;
                            }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });

    $('#district').on('change',function(){
        var district   = $('#district').val();
        var data = {
            _token: $(this).data('token'),
            district:  district
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('district/district-tehsil')}}",
            data:       data,
            success:    function (data) {
                            if(data) { 
                                $("#tehsil").empty();
                                var jsonData1   = JSON.parse(data);
                                if(jsonData1.tehsil!='undefined') {
                                    var appenddata1 = '';
                                    appenddata1 += "<option value = ''>--Select Tehsil--</option>";
                                    for (var i = 0; i < jsonData1.tehsil.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1.tehsil[i].tehsil_id + "'>" + jsonData1.tehsil[i].tehsil + " </option>";
                                    }
                                    $("#tehsil").append(appenddata1);
                                }
                                return false;
                            }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });


    $('#tehsil').on('change',function(){
        var tehsil   = $('#tehsil').val();
        var data = {
            _token: $(this).data('token'),
            tehsil:  tehsil
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('district/tehsil-village')}}",
            data:       data,
            success:    function (data) {
                            if(data) {
                                $("#village").empty();
                                var jsonData1   = JSON.parse(data);console.log(jsonData1);
                                if(jsonData1.village!='undefined') {
                                    var appenddata1 = '';
                                    appenddata1 += "<option value = ''>--Select Village--</option>";
                                    for (var i = 0; i < jsonData1.village.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1.village[i].village_id + "'>" + jsonData1.village[i].village + " </option>";
                                    }
                                    $("#village").append(appenddata1);
                                    $('#village').selectpicker('refresh');
                                }
                                return false;
                            }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
    
    jQuery.validator.addMethod("domain", function(value, element) {
        var isSuccess   = false;
        var dd_code    = $('#dd_code').val();
        $.ajax({ 
                url:    "{{url('organogram/check-dd-ecn')}}", 
                data:   {'dd_code':dd_code, '_dd': '0'}, 
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
            $("#createdd").click();
        }
    });
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $("#createdd").validate({
        errorPlacement: function (error, element) {
                            console.log($(element).next());
                            $(element).next('.rerror').html(error);
                            $(element).next().next('.rerror').html(error);
                        },
        ignore: "input[type='text']:hidden",
        rules: {
            zonal_manager: {
                required:   true
            },
            regional_manager: {
                required:   true,
            },
            sales_regional_manager: {
                required:   true,
            },
            area_manager: {
                required:   true,
            },
            sales_area_manager: {
                required:   true,
            },
            front_liner: {
                required:   true,
            },
            product_category: {
                required:   true,
            },
            state: {
                required:   true,
            },
            district: {
                required:   true,
            },
            tehsil: {
                required:   true,
            },
            village: {
                required:   true,
            },
            language: {
                required:   true,
            },
            department: {
                required:   true,
                minlength:  4,
                maxlength:  50
            },
            designation: {
                required:   true,
                minlength:  4,
                maxlength:  50
            },
            dd_code: {
                required:   true,
                minlength:  4,
                maxlength:  10,
                domain:     true
            },
            name: {
                required:   true,
                minlength:  4,
                maxlength:  50
            },
            mobile_no: {
                required:   true,
                minlength:  10,
                maxlength:  10,
                number: true
            },
            dd_hq: {
                required:   true,
                minlength:  4,
                maxlength:  50
            },
            dd_email: {
                required:   true
            },
            dd_address: {
                required:   true,
                minlength:  10
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



<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.10/js/bootstrap-select.js"></script>  
<script type="text/javascript">
  $(document).ready(function() {
   $('#village').selectpicker({
     style: 'btn-info2',
});
   
  });
</script>


