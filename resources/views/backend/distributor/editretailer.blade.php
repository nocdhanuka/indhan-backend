<?php //dd($zonal_manager_list); ?>
@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->
<?php $distArray = explode(',', $retailer->distributor); ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Add Retailer</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add Retailer</li>
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
                        <form role="form" method="post" class="search-form"  id="createdistributor" action="{{url('distributor/update-retailer')}}"  >                                                          
                            
                            <div class="col-lg-6"> 
                                <label class="formlabel">Name</label>
                                <input type="text" id="name" class="frm_txt" name="name" placeholder="Name"  value="{{$retailer->name }}">   
                                <div class="rerror"><label for="name" generated="true" class="error">{{ $errors->first('name') }}</label></div>
                            </div> 
                            <div class="col-lg-6"> 
                                <label class="formlabel">Contact Number</label>
                                <input type="text" id="contact_number" class="frm_txt" name="contact_number" placeholder="Contact Number" value="{{ $retailer->contact_number }}">   
                                <div class="rerror"><label for="contact_number" generated="true" class="error">{{ $errors->first('contact_number') }}</label></div>
                            </div>
                            <div class="col-lg-6">
                                <label class="formlabel">Address</label>
                                <textarea  id="address" class="frm_txt" name="address" placeholder="Address"  >  {{$retailer->address}} </textarea>
                                <div class="rerror"><label for="address" generated="true" class="error">{{ $errors->first('address') }}</label></div>
                            </div>                                
                            
                            <div class="col-lg-6">
                                <label class="formlabel">Distributors</label>
                                <select id="retailDis" multiple="multiple" name="distributor[]">
                                    @foreach($distributor as $key => $value)
                                    <option <?php if(in_array($value->distributor_id,$distArray)) { echo 'selected'; } ?> value="{{ $value->distributor_id }}">{{$value->name}}</option>
                                    @endforeach
                                </select>
                                <!-- <input type="text" id="type" class="frm_txt"  name="type" placeholder="Type" maxlength="6" value="{{ old('type') }}"> -->
                                <div class="rerror"><label for="type" generated="true" class="error">{{ $errors->first('distributor') }}</label></div>
                               
                            </div>
                                
                            <div class="clearfix"></div>

                            
                            
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="hidden" name="distId" value="{{ $retailer->retailer_id }}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Update Retailer" >
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
 <script src="{{asset('assets/js/bootstrap-multiselect.js')}}"></script>
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->

<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function () {   
    jQuery.validator.addMethod("domain", function(value, element) {
        var isSuccess   = false;
        var regional_manager_hq_code    = $('#regional_manager_hq_code').val();
        $.ajax({ 
                url:    "{{url('regional/check-regional-ecn')}}", 
                data:   {'regional_manager_hq_code':regional_manager_hq_code, '_regional': '0'}, 
                async:  false,
                type:   "POST",
                success: 
                        function(msg) { isSuccess = msg === "true" ? true : false }
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

    $("#createdistributor").validate({
        errorPlacement: function (error, element) {
                            console.log($(element).next());
                            $(element).next('.rerror').html(error);
                            $(element).next().next('.rerror').html(error);
                        },
        ignore: "input[type='text']:hidden",
        rules: {
            name: {
                required:   true,
            },
            address: {
                required:   true,
                
            },
            contact_number: {
                required:   true,
                
            },
            type: {
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
$(function() {

$('#retailDis').multiselect({

includeSelectAllOption: true

});

});
</script>













