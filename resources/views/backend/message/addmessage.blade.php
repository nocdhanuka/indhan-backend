<?php //dd($zonal_manager_list); ?>

@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->
<style type="text/css">
    .addM{
        width: 600px;
        margin: 0 auto;
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Messages</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Messages</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-md-offset-10 col-md-2"><a href="{{url('demo/history-message')}}" class="btn btn-info text-right">View History</a></div>
                        <div class="success"></div>
                        
                        <form role="form" method="post" class="search-form"  id="createMessage" action="{{url('demo/create-message')}}"  > 
                        <div class="col-lg-3"> 
                        <label class="formlabel">Crop</label>
                        <select id="crop" class="frm_txt form-control"  data-token="{{csrf_token()}}"  name="crop">
                            <option value="">Select Crop</option>
                            <?php
                            foreach($cropList as $crop) { ?>
                            <option value="{{$crop->crop_id}}">{{$crop->crop}}</option>
                            <?php    
                            }
                            ?>
                        </select>
                        <div class="rerror"><label for="crop" generated="true" class="error">{{ $errors->first('crop') }}</label></div>
                        </div>

                        <div class="col-lg-3"> 
                        <label class="formlabel">Product</label>
                        <select id="product" class="frm_txt form-control"  data-token="{{csrf_token()}}"  name="product">
                            <option value="">Select Product</option>
                        </select>
                        <div class="rerror"><label for="product" generated="true" class="error">{{ $errors->first('product') }}</label></div>
                        </div> 
                        <div class="col-lg-3"> 
                        <label class="formlabel">State</label>
                        <select id="state" class="frm_txt form-control"  data-token="{{csrf_token()}}"  name="state">
                            <option value="">Select State</option>
                            <?php
                            foreach($stateList as $state) { ?>
                            <option value="{{$state->state_id}}">{{$state->state}}</option>
                            <?php    
                            }
                            ?>
                        </select>
                        <div class="rerror"><label for="state" generated="true" class="error">{{ $errors->first('state') }}</label></div>
                        </div>

                        <div class="col-lg-3"> 
                        <label class="formlabel">Language</label>
                        <select id="language" class="frm_txt form-control"  data-token="{{csrf_token()}}"  name="language">
                            <option value="">Select Language</option>
                            <?php
                            foreach($language as $lang) { ?>
                            <option value="{{$lang->id}}">{{$lang->language}}</option>
                            <?php    
                            }
                            ?>
                        </select>
                        <div class="rerror"><label for="language" generated="true" class="error">{{ $errors->first('language') }}</label></div>
                        </div> 
                        <div class="clearfix"></div>
                        <div class="col-lg-12"><hr></div> 
                        <div class="clearfix"></div>
                        <div class="col-lg-12">
                        <label class="formlabel">Add Message</label>
                            <textarea type="text" id="message" class="frm_txt form-control" rows="6" name="message" placeholder="Enter Here"></textarea>   
                            <div class="rerror"><label for="message" generated="true" class="error">{{ $errors->first('message') }}</label></div>
                        </div>
                            <div class="clearfix"></div>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Submit" >
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
    $(document.body).on('click', '#crop', function () {
        var option = $(this).val();
        var type = 'crop';
            $.ajax({
                type:       'POST',
                url:        "{{url('call/call-filter-ajax')}}",
                 data:       { _token: "{{csrf_token()}}",option:option,type:type},
                success:    function (data) {
                    var data = $.parseJSON(data);
                    var filter_product = '<option value="">Select Product</option>';
                    $.each(data, function(key,value)
                    {
                        filter_product += '<option value="'+value.product_id+'">' + value.product + '</option>';
                        
                    });                  
                    $('#product').html(filter_product);
                },
                error:      function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
    });

     $(document).ready(function () {
        $("#createMessage").validate({
        errorPlacement: function (error, element) {
                            console.log($(element).next());
                            $(element).next('.rerror').html(error);
                            $(element).next().next('.rerror').html(error);
                        },
        ignore: "input[type='text']:hidden",
        rules: {
            crop: {
                required:   true,
            },
            product: {
                required:   true,
            },
            language: {
                required:   true,
            },
            message: {
                required:   true,
            },
            
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
    })
</script>














