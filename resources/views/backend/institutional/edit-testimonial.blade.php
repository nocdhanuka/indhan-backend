@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Edit Testimonial List</h1>
            @if(Session::has('message'))
            <div class="col-md-12" style="margin-top:20px;">
                <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            </div>
            @endif
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Edit Testimonial</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <form role="form" method="post" enctype="multipart/form-data" class="search-form"  id="updatestimonial" action="{{url('institutional/update-testimonial')}}">
                                <div class="col-lg-3">
                                    <label class="formlabel">Date: </label>
                                    <input type="datetime-local" id="date_time" class="frm_txt inputs"  name="date_time" placeholder="Date" value="{{ strftime('%Y-%m-%dT%H:%M:%S', strtotime($testimonialdetail->date_time))}}">
                                    <div class="rerror">
                                        <label for="date_time" generated="true" class="error">{{ $errors->first('date_time') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Testimonial Name: </label>
                                    <input type="text" id="testimonial_name" class="frm_txt inputs"  name="testimonial_name" placeholder="Testimonial Name" value="{{ $testimonialdetail->testimonial_name }}">
                                    <div class="rerror">
                                        <label for="testimonial_name" generated="true" class="error">{{ $errors->first('testimonial_name') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Description: </label>
                                    <textarea id="description" rows="2" style="width: 300px;" class="frm_txt inputs"  name="description" placeholder="Description">{{ $testimonialdetail->description }}</textarea>
                                    <div class="rerror">
                                        <label for="description" generated="true" class="error">{{ $errors->first('description') }}</label>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Image: </label>
                                    <input type="file" id="image_video" class="frm_txt inputs"  name="image_video">
                                    <?php
                                    if(!empty($testimonialdetail->image_video)) {
                                        $base_url = 'https://dhanuka-app.s3.ap-south-1.amazonaws.com/';
                                        $image_video_path = $base_url.$testimonialdetail->image_video;
                                    ?>
                                        <a href="{{$image_video_path}}" target="_blank" class="btn btn-info" style="margin-top: 15px;">View</a>
                                    <?php } ?>
                                    <div class="rerror">
                                        <label for="image_video" generated="true" class="error">{{ $errors->first('image_video') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Language</label>
                                    <input type="hidden" name="language_name" id="language_name" value="" />
                                    <select name="language_id" id="language_id" class="frm_txt mdb-select inputs">
                                        <option value="">--Select Language--</option>
                                        <?php
                                        foreach ($languages as $key=>$value) {
                                            if($value->id ==$testimonialdetail->language_id) {
                                                $selected = "selected='selected'";
                                            } else {
                                                $selected = "";
                                            }
                                            ?>
                                            <option value="{{$value->id}}" {{$selected}}>{{$value->language}}</option>
                                        <?php } ?>
                                    </select>
                                    <div class="rerror"><label for="language_id" generated="true" class="error">{{ $errors->first('language_id') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <input type="hidden" name="institutional_testimonial_id" id="institutional_testimonial_id" value="{{ $testimonialdetail->institutional_testimonial_id }}" />
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                                    <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit inputs" id="reg" value="Update" />
                                    <input type="button" name="reset" class="btn btn-primary btn-lg gap5040 inputs" id="reset" value="Reset" />
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
<!-- DataTables -->
<script src="{{asset('assets/admin/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/admin/plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.colVis.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js"></script>
<link  rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
<link  rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.2.4/css/fixedColumns.dataTables.min.css">
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

<!-- FastClick -->
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('assets/admin/dist/js/app.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->

<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
<script>
$.ajaxSetup({
   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});
$("#updatestimonial").validate({
    errorPlacement: function (error, element) {
                        console.log($(element).next());
                        $(element).next('.rerror').html(error);
                        $(element).next().next('.rerror').html(error);
                    },
    ignore: "input[type='text']:hidden",
    rules: {
        testimonial_name: {
            required:   true
        },
        description: {
            required:   true
        },
        date_time: {
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
$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});
$('#language_id').on('change',function(){
    var language_name = $( "#language_id option:selected" ).text();
    $('#language_name').val(language_name);
});
</script>
