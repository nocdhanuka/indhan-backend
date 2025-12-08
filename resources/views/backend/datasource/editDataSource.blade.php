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
        <h1>Edit Data Source</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Edit Data Source</li>
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
                       //echo "<pre>"; print_r($data['user_types'][0]);die;
                        // foreach($data['user_types'] as $key => $value) { 
                        //     echo $value;die;
                        // }
                        ?>
                        <form role="form" method="post" class="search-form"  id="editdatasource" action="{{url('admin/edit-data-source-process')}}"  >                             
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">User Type</label>
                                <select id="user_type" multiple class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="user_type[]">
                                <?php 
                                foreach($data['user_types'] as $key => $value) { 
                                    $dataUserType = explode(',',$data['datasource']->user_type);
                                    if(in_array($value, $dataUserType)) {
                                ?>
                                    <option value="<?php echo $value;?>" selected="selected"><?php echo $value;?></option>
                                <?php }else{ ?>
                                    <option value="<?php echo $value;?>"><?php echo $value;?></option>
                                <?php } 
                                }
                                ?>
                                
                                </select>
                                <div class="rerror"><label for="user_type" generated="true" class="error">{{ $errors->first('user_type') }}</label></div>
                            </div>

                            <div class="clearfix"></div>
                            
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel"> Data Source</label>
                                <input type="text" id="data_source" class="frm_txt"  name="data_source" placeholder="data source" value="{{$data['datasource']->data_source}}">
                                <div class="rerror"><label for="data_source" generated="true" class="error">{{ $errors->first('data_source') }}</label></div>
                            </div>
                            
                            <div class="clearfix"></div>                      
                            <input type="hidden" name="data_source_id" value="{{$data['datasource']->data_source_id}}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Edit Data Source" >
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
    
    $('.step2submit').keypress(function (e) {
        if (e.which == 13) {
            $("#editdatasource").click();
        }
    });
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $("#editdatasource").validate({
        errorPlacement: function (error, element) {
                            console.log($(element).next());
                            $(element).next('.rerror').html(error);
                            $(element).next().next('.rerror').html(error);
                        },
        ignore: "input[type='text']:hidden",
        rules: {
            user_type: {
                required:   true,
            },
            data_source: {
                required:   true,
                // minlength:  4,
                maxlength:  50
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
    $('#user_type').selectpicker({
        style: 'btn-info2',
    });
});
</script>


