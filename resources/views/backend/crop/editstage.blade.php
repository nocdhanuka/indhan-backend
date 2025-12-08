@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Edit Stage</h1>
            
            @if(Session::has('message'))
            <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            </div>
            @endif

            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Edit Stage</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
        <div class="row">
            <div class="col-md-offset-2 col-xs-12 col-md-8">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="success"></div>
                        <?php
                        //echo "<pre>"; print_r($language_list);
                        ?>
                        <form role="form" method="post" class="search-form"  id="updatestage" action="{{url('crop/update-stage')}}"  >                                                          
                            
                            <div class="col-lg-6"> 
                                <label class="formlabel">Stage Name</label>
                                <input type="text" id="stage_name" class="frm_txt form-control" name="stage_name" placeholder="Name"  value="{{$stage->stage_name }}">   
                                <div class="rerror"><label for="stage_name" generated="true" class="error">{{ $errors->first('stage_name') }}</label></div>
                            </div> 
                            <div class="col-lg-6"> 
                                <label class="formlabel">Start Date Range</label>
                                <input type="text" id="stage_start_date1" class="frm_txt form-control" name="stage_start_date" placeholder="Start Date" value="{{$stage->stage_start_date }}" readonly>   
                                <div class="rerror"><label for="stage_start_date" generated="true" class="error">{{ $errors->first('stage_start_date') }}</label></div>
                            </div>
                            <div class="col-lg-6"> 
                                <label class="formlabel">End Date Range</label>
                                <input type="text" id="stage_end_date1" class="frm_txt form-control" name="stage_end_date" placeholder="End Date" value="{{$stage->stage_end_date }}" readonly="">   
                                <div class="rerror"><label for="stage_end_date" generated="true" class="error">{{ $errors->first('stage_end_date') }}</label></div>
                            </div>                                
                            
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            <input type="hidden" name="stageId" value="{{ $stage->stage_id }}" /> 
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Add Stage" >
                        </form>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    @include('include.admin.footer')
    
</div>



<!-- ./wrapper --><!-- jQuery 2.1.4 -->
<script src="{{asset('assets/admin/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
<!-- SlimScroll 
<script src="{{asset('assets/admin/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('assets/admin/dist/js/app.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->







<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
<script type="text/javascript" src="{{asset('assets/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function () {  
$("#stage_start_date1").datepicker({
        minDate: 0,
        dateFormat: 'yy-mm-dd',
        onSelect: function(selected_date) {
            $("#stage_end_date1").datepicker("option", "minDate", selected_date);
        }
    });
 $("#stage_end_date1").datepicker({
        minDate: 0,
        dateFormat: 'yy-mm-dd',
        onSelect: function(selected_date) {
            $("#stage_start_date1").datepicker("option", "maxDate", selected_date);
        }
    }); 

    $("#updatestage").validate({
        errorPlacement: function (error, element) {
                            console.log($(element).next());
                            $(element).next('.rerror').html(error);
                            $(element).next().next('.rerror').html(error);
                        },
        ignore: "input[type='text']:hidden",
        rules: {
            stage_name: {
                required:   true,
            },
            stage_start_date: {
                required:   true,
                
            },
            stage_end_date: {
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
