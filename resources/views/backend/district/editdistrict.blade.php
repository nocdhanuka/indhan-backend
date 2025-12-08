@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>All District List</h1>
            
            @if(Session::has('message'))
            <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            </div>
            @endif

            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">District List</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <form role="form" method="post" class="search-form"  id="createdistrict" action="{{url('district/update-district')}}"  >                                                          
                                <div class="col-lg-3">
                                    <label class="formlabel">State Name</label>
                                    <select id="state_name" class="frm_txt mdb-select inputs"  name="state_name">
                                        <option value="" >Choose State</option>
                                        <?php
                                        foreach($state_detail as $state) {
                                            if($state->state_id==$district_detail->state_id){ ?>
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
                                    <div class="rerror"><label for="category_name" generated="true" class="error">{{ $errors->first('category_name') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">District Name</label>
                                    <input type="text" id="district_name" class="frm_txt inputs"  name="district_name" placeholder="District Name" maxlength="50" value="{{ old('district_name', $district_detail->district) }}">
                                    <div class="rerror"><label for="district_name" generated="true" class="error">{{ $errors->first('district_name') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <input type="hidden" name="district_id" id="district_id" value="{{ $district_detail->district_id }}" />
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                                    <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit inputs" id="reg" value="Update" >
                                    <input type="button" name="reset" class="btn btn-primary btn-lg gap5040 inputs" id="reset" value="Reset" >
                                </div>
                                
                                <div class="clearfix"></div>
                                <div class="col-lg-12"><hr></div>
                                <div class="clearfix"></div>
                            </form>
                            <div class="success"></div>
                            
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
<script>
$.ajaxSetup({
   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});
$("#createdistrict").validate({
    errorPlacement: function (error, element) {
                        console.log($(element).next());
                        $(element).next('.rerror').html(error);
                        $(element).next().next('.rerror').html(error);
                    },
    ignore: "input[type='text']:hidden",
    rules: {
        district_name: {
            required:   true,
            minlength:  2,
            maxlength:  50
        },
        state_name: {
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
$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});
$('#reset').click(function(){
    window.location.assign('{{url('district/add-district')}}')
});
</script>
