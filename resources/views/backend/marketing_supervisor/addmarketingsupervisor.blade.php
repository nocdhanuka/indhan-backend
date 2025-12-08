<?php //dd($zonal_manager_list); ?>
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
        <h1>Add New Marketing Supervisor</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add Marketing Supervisor</li>
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
                        <form role="form" method="post" class="search-form"  id="createmarketingsupervisor" action="{{url('supervisor/create-marketing-supervisor')}}"  >                                                          
                            <div class="col-lg-12"> 
                                <label class="formlabel">DGM/S2</label>
                                <select id="sales_regional_manager_id" multiple class="frm_txt mdb-select"  name="sales_regional_manager_id[]">
                                    <option value="" >Choose DGM/S2</option>
                                    <?php
                                    foreach($SalesRegionalManager_list as $SalesRegionalManager) { ?>
                                    <option value="{{$SalesRegionalManager->sales_regional_manager_id}}">{{$SalesRegionalManager->sales_regional_manager_name}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="sales_regional_manager_id" generated="true" class="error">{{ $errors->first('sales_regional_manager_id') }}</label></div>
                            </div> 
                            <div class="col-lg-12"> 
                                <label class="formlabel">Marketing Supervisor HQ Code</label>
                                <input type="text" id="marketing_supervisor_hq_code" class="frm_txt" name="marketing_supervisor_hq_code" placeholder="HQ Code" maxlength="10" value="{{ old('marketing_supervisor_hq_code') }}">   
                                <div class="rerror"><label for="marketing_supervisor_hq_code" generated="true" class="error">{{ $errors->first('marketing_supervisor_hq_code') }}</label></div>
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Marketing Supervisor Name</label>
                                <input type="text" id="marketing_supervisor_name" class="frm_txt" name="marketing_supervisor_name" placeholder="Name" value="{{ old('marketing_supervisor_name') }}">   
                                <div class="rerror"><label for="marketing_supervisor_name" generated="true" class="error">{{ $errors->first('marketing_supervisor_name') }}</label></div>
                            </div>
                            <div class="col-lg-3">
                                <label class="formlabel">Marketing Supervisor Mobile Number</label>
                                <input type="text" id="marketing_supervisor_mobile" class="frm_txt" name="marketing_supervisor_mobile" placeholder="Mobile Number" maxlength="10" value="{{ old('marketing_supervisor_mobile') }}">   
                                <div class="rerror"><label for="marketing_supervisor_mobile" generated="true" class="error">{{ $errors->first('marketing_supervisor_mobile') }}</label></div>
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
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Create Marketing Supervisor" >
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
    // jQuery.validator.addMethod("domain", function(value, element) {
    //     var isSuccess   = false;
    //     var sales_regional_manager_hq_code    = $('#sales_regional_manager_hq_code').val();
    //     $.ajax({ 
    //             url:    "{{url('salesregional/check-sales-regional-ecn')}}", 
    //             data:   {'sales_regional_manager_hq_code':sales_regional_manager_hq_code, '_sales_regional': '0'}, 
    //             async:  false,
    //             type:   "POST",
    //             success: 
    //                     function(msg) { isSuccess = msg === "true" ? true : false }
    //             });
    //      return isSuccess;
    // }, "ENC already exists, enter a new ECN.");
    
    $('.step2submit').keypress(function (e) {
        if (e.which == 13) {
            $("#createmarketingsupervisor").click();
        }
    });   
    
            
    $.ajaxSetup({
       headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });
    
    $("#createmarketingsupervisor").validate({
        errorPlacement: function (error, element) {
                            console.log($(element).next());
                            $(element).next('.rerror').html(error);
                            $(element).next().next('.rerror').html(error);
                        },
        ignore: "input[type='text']:hidden",
        rules: {
            sales_regional_manager_id: {
                required:   true,
            },
            marketing_supervisor_hq_code: {
                required:   true,
                minlength:  4,
                maxlength:  10,
                //domain:     true
            },
            marketing_supervisor_name: {
                required:   true,
                minlength:  4,
                maxlength:  50
            },
            marketing_supervisor_mobile: {
                required:   true,
                minlength:  10,
                maxlength:  10,
                number: true
            },
            product_category: {
                required:   true,
                
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
   $('#sales_regional_manager_id').selectpicker({
     style: 'btn-info2',
});
   
  });
</script>






