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
        <h1>Edit Reminder</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Edit Reminder</li>
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
                        // foreach ($data['cropList'] as $key => $value) {
                        //     print_r($data['reminder']->crop_id);
                        //     print_r($data['cropList'][$key]->crop_id);die;
                        // }die;
                        // echo "<pre>"; print_r($data);die;
                        ?>
                        <form role="form" method="post" class="search-form"  id="addreminder" action="{{url('admin/update-reminder')}}"  >                             
                            <div class="col-lg-3"> 
                                <label class="formlabel">Crop</label>
                                <select id="crop" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="crop">
                                    <option value="">--Select Crop--</option>
                                    <?php
                                    foreach($data['cropList'] as $crop) { 
                                        if ($crop->crop_id == $data['reminder']->crop_id) {
                                    ?>

                                    <option selected value="<?php echo $crop->crop_id; ?>"><?php echo $crop->crop; ?></option>
                                    
                                    <?php 
                                     }
                                     else
                                     {
                                    ?>
                                    <option value="<?php echo $crop->crop_id; ?>"><?php echo $crop->crop; ?></option>
                                    <?php 
                                        }   
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="crop" generated="true" class="error">{{ $errors->first('crop') }}</label></div>
                            </div> 

                            <div class="col-lg-3"> 
                                <label class="formlabel">Product</label>
                                <select id="product" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="product">
                                    <?php

                                    foreach($data['productList'] as $product) { 
                                        if ($product->product_id == $data['reminder']->product_id) {
                                            
                                        ?>
                                    <option selected value="<?php echo $product->product_id; ?>"><?php echo $product->product; ?></option>
                                    <?php 
                                     }
                                     else
                                     {
                                    ?>
                                        <option value="<?php echo $product->product_id; ?>"><?php echo $product->product; ?></option>
                                    <?php 
                                        }   
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="product" generated="true" class="error">{{ $errors->first('product') }}</label></div>
                            </div> 

                                                       
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix">
                            
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">Stage</label>
                                <select id="stage" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="stage">
                                    <option selected value="<?php echo $data['reminder']->stage_id; ?>"><?php echo $data['reminder']->stage_name;?></option>
                                    <?php
                                    foreach($data['stageList'] as $stage) { 
                                        if ($stage->stage_id != $data['reminder']->stage_id ) {
                                        ?>
                                    <option value="<?php echo $stage->stage_id;?>"><?php echo $stage->stage_name;?></option>
                                    <?php 
                                        }
                                    ?>
                                    
                                    <?php
                                           
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="stage" generated="true" class="error">{{ $errors->first('stage') }}</label></div>
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Spray</label>
                                <select id="spray" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="spray">
                                    <option selected value="{{$data['reminder']->spray}}">{{$data['reminder']->spray}}</option>
                                    
                                    <?php if ($data['reminder']->spray != 'Spray 1') {?>
                                    <option value="Spray 1">Spray 1</option>
                                    <?php } 

                                    if ($data['reminder']->spray != 'Spray 2') {?>
                                    <option value="Spray 2">Spray 2</option>
                                    <?php } ?>
                                </select>
                                <div class="rerror"><label for="spray" generated="true" class="error">{{ $errors->first('spray') }}</label></div>
                            </div>

                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix">

                            <div class="col-lg-3"> 
                                <label class="formlabel">No of Days for review</label>
                                <select id="review" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="review">
                                    <option selected value="{{$data['reminder']->number_day_review}}">{{$data['reminder']->number_day_review}}</option>
                                    <?php for ($i=1; $i <= 20 ; $i++) { ?>
                                        <?php if ($i != $data['reminder']->number_day_review ) {?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php } ?> 
                                    <?php } ?>
                                </select>
                                <div class="rerror"><label for="review" generated="true" class="error">{{ $errors->first('review') }}</label></div>
                            </div>

                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix">
                            
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="hidden" name="reminder_id" value="{{ $data['reminder']->reminder_id }}" />
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Add Reminder" >
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
    $(document.body).on('change', '#crop', function () {
        var option = $(this).val();
        var type = 'crop';
            $.ajax({
                type:       'POST',
                url:        "{{url('call/call-filter-ajax')}}",
                 data:       { _token: "{{csrf_token()}}",option:option,type:type},
                success:    function (data) {
                    var data = $.parseJSON(data);
                    var product = '<option value="">--Select Product--</option>';
                    $.each(data, function(key,value)
                    {
                        product += '<option value="'+value.product_id+'">' + value.product + '</option>';
                        
                    });                  
                    $('#product').html(product);
                },
                error:      function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
    });
    
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $("#addreminder").validate({
        errorPlacement: function (error, element) {
                            console.log($(element).next());
                            $(element).next('.rerror').html(error);
                            $(element).next().next('.rerror').html(error);
                        },
        ignore: "input[type='text']:hidden",
        rules: {
            crop: {
                required:   true
            },
            product: {
                required:   true,
            },
            stage: {
                required:   true,
            },
            spray: {
                required:   true,
            },
            review: {
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


