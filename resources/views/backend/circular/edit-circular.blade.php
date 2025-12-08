@include('include.admin.backend_header')
    <!-- Multi Select Dropdown -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"> -->
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
            <h1>Edit Circular List</h1>
            @if(Session::has('message'))
            <div class="col-md-12" style="margin-top:20px;">
                <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            </div>
            @endif
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Edit Circular</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <form role="form" method="post" enctype="multipart/form-data" class="search-form"  id="updatecircular" action="{{url('circulars/update-circular')}}">
                                <div class="col-lg-3">
                                    <input type="hidden" name="circular_type" id="circular_type" value="<?php echo $circulardetail->institutionalType; ?>">
                                    <label class="formlabel">Circular Type: </label>
                                    <select name="institutionalType" id="institutionalType" class="frm_txt mdb-select inputs" disabled='disabled'>
                                        <option value="1" <?php if($circulardetail->institutionalType ==1) { echo "selected='selected'"; } ?>>Tea Estate</option>
                                        <option value="2" <?php if($circulardetail->institutionalType ==2) { echo "selected='selected'"; } ?>>Sugarmill</option>
                                        <option value="3" <?php if($circulardetail->institutionalType ==3) { echo "selected='selected'"; } ?>>Influencer</option>
                                    </select>
                                    <div class="rerror"><label for="institutionalType" generated="true" class="error">{{ $errors->first('institutionalType') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Publish Date: </label>
                                    <input type="datetime-local" id="published_date" class="frm_txt inputs"  name="published_date" placeholder="Date" value="{{ strftime('%Y-%m-%dT%H:%M:%S', strtotime($circulardetail->published_date))}}">
                                    <div class="rerror">
                                        <label for="published_date" generated="true" class="error">{{ $errors->first('published_date') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Circular Name: </label>
                                    <input type="text" id="circular_name" class="frm_txt inputs"  name="circular_name" placeholder="Testimonial Name" value="{{ $circulardetail->circular_name }}">
                                    <div class="rerror">
                                        <label for="circular_name" generated="true" class="error">{{ $errors->first('circular_name') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Whatsapp Message: </label>
                                    <textarea id="whatsapp_message" rows="2" style="width: 300px;" class="frm_txt inputs"  name="whatsapp_message" placeholder="Description">{{ $circulardetail->whatsapp_message }}</textarea>
                                    <div class="rerror">
                                        <label for="whatsapp_message" generated="true" class="error">{{ $errors->first('whatsapp_message') }}</label>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-lg-3">
                                    <label class="formlabel">State: </label>
                                    <select name="state_id" id="state_id" class="frm_txt mdb-select inputs" required>
                                        <option value="0">--Select State--</option>
                                        <?php
                                        foreach($state_detail as $state) {
                                            if($state->state_id==$circulardetail->state_id) { ?>
                                                <option selected="selected" value="{{$state->state_id}}">{{$state->state}}</option>
                                            <?php } else { ?>
                                                <option value="{{$state->state_id}}">{{$state->state}}</option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="state_id" generated="true" class="error">{{ $errors->first('state_id') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">District: </label>
                                    <select name="district_id" id="district_id" class="frm_txt mdb-select inputs" required>
                                        <option value="0">--Select District--</option>
                                        <?php
                                        foreach($district_detail as $district) {
                                            if($district->district_id==$circulardetail->district_id) { ?>
                                                <option selected="selected" value="{{$district->district_id}}">{{$district->district}}</option>
                                            <?php } else { ?>
                                                <option value="{{$district->district_id}}">{{$district->district}}</option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="district_id" generated="true" class="error">{{ $errors->first('district_id') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Circular Contacts: </label>
                                    <select name="contact_ids[]" id="contact_ids" class="frm_txt mdb-select" data-token="{{csrf_token()}}" style="width: 85%;" multiple>
                                        <!-- <option>-- Select Contacts --</option> -->
                                        <?php
                                        $exp_contact_ids = explode(',', $circulardetail->contact_ids);
                                        foreach ($contactList as $contact) {
                                            if(in_array($contact->$contact_by_id, $exp_contact_ids)) {
                                        ?>
                                            <option value="{{$contact->$contact_by_id}}" selected='selected'>{{$contact->wa_number}}, {{$contact->name}}</option>
                                        <?php } else { ?>
                                            <option value="{{$contact->$contact_by_id}}">{{$contact->wa_number}}, {{$contact->name}}</option>
                                        <?php } } ?>
                                    </select>
                                    <div class="rerror">
                                        <label for="contact_ids" generated="true" class="error">{{ $errors->first('contact_ids') }}</label>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <label class="formlabel">Circular Testimonial:</label>
                                <?php foreach ($circulartestimonial as $testimonial) { ?>
                                    <div class="col-lg-4">
                                        <label class="formlabel">Description: </label>
                                        <textarea id="description" rows="2" style="width: 300px;" class="frm_txt inputs"  name="description[{{ $testimonial->circular_testimonial_id }}]" placeholder="Description">{{ $testimonial->description }}</textarea>
                                        <?php
                                        if(!empty($testimonial->image_video)) {
                                            $base_url = 'https://dhanuka-app.s3.ap-south-1.amazonaws.com/';
                                            $image_video_path = $base_url.$testimonial->image_video;
                                        ?>
                                            <label class="formlabel" style="padding:0px;">
                                                <a href="{{$image_video_path}}" target="_blank" class="btn btn-info" style="margin-top: 15px;">View File</a>
                                            </label>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <div class="clearfix"></div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Status</label>
                                    <select name="status" id="status" class="frm_txt mdb-select inputs">
                                        <option value="0" <?php if($circulardetail->status ==0) { echo "selected='selected'"; } ?>>ON HOLD</option>
                                        <option value="1" <?php if($circulardetail->status ==1) { echo "selected='selected'"; } ?>>APPROVE</option>
                                    </select>
                                    <div class="rerror"><label for="status" generated="true" class="error">{{ $errors->first('status') }}</label></div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-lg-3">
                                    <input type="hidden" name="circular_id" id="circular_id" value="{{ $circulardetail->circular_id }}" />
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
<!-- Multi Select Dropdown -->
<script>
$(document).ready(function() {
    /*$('#contact_ids_').multiselect({
        nonSelectedText: 'Select Contacts'
    });*/

    $('#state_id').on('change',function(){
        var state_id = $('#state_id').val();
        var circular_type = $('#circular_type').val();
        var data = {
            _token: $(this).data('token'),
            state_name: state_id
        }
        $.ajax({
            type:   'POST',
            url:    "{{url('tehsil/tehsil-district')}}",
            data:   data,
            success:    function (data) {
                            var jsonData1   = JSON.parse(data);
                            var appenddata1 = '';
                            $("#district_id").empty();
                            appenddata1 += "<option value = ''>--Select District--</option>";
                            for (var i = 0; i < jsonData1.length; i++) {
                                appenddata1 += "<option value = '" + jsonData1[i].dId + "'>" + jsonData1[i].dName + " </option>";
                            }
                            $("#district_id").append(appenddata1);
                            return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
        var circulardata = {
            _token: $(this).data('token'),
            state_id: state_id,
            circular_type: circular_type
        }
        $.ajax({
            type:   'POST',
            url:    "{{url('circulars/circlar-contact')}}",
            data:   circulardata,
            success:    function (data) {
                            if(data == 0) {
                                $("#contact_ids").empty();
                                // appenddata1 += "<option value = ''>--Select Contacts--</option>";
                                $("#contact_ids").append(appenddata1);
                                $('#contact_ids').selectpicker('refresh');
                                return false;
                            }
                            var jsonData1   = JSON.parse(data);
                            var appenddata1 = '';
                            $("#contact_ids").empty();
                            // appenddata1 += "<option value = ''>--Select Contacts--</option>";
                            for (var i = 0; i < jsonData1.length; i++) {
                                appenddata1 += "<option value = '" + jsonData1[i].institutional_contact_id + "'>" + jsonData1[i].wa_number + "," + jsonData1[i].name + " </option>";
                            }
                            $("#contact_ids").append(appenddata1);
                            $('#contact_ids').selectpicker('refresh');
                            return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });

    $('#district_id').on('change',function(){
        var state_id      = $('#state_id').val();
        var district_id   = $('#district_id').val();
        var circular_type = $('#circular_type').val();
        var data = {
            _token: $(this).data('token'),
            state_id: state_id,
            district_id: district_id,
            circular_type: circular_type
        }
        $.ajax({
            type:   'POST',
            url:    "{{url('circulars/circlar-contact')}}",
            data:   data,
            success:    function (data) {
                            if(data == 0) {
                                $("#contact_ids").empty();
                                // appenddata1 += "<option value = ''>--Select Contacts--</option>";
                                $("#contact_ids").append(appenddata1);
                                $('#contact_ids').selectpicker('refresh');
                                return false;
                            }
                            var jsonData1   = JSON.parse(data);
                            var appenddata1 = '';
                            $("#contact_ids").empty();
                            // appenddata1 += "<option value = ''>--Select Contacts--</option>";
                            for (var i = 0; i < jsonData1.length; i++) {
                                appenddata1 += "<option value = '" + jsonData1[i].institutional_contact_id + "'>" + jsonData1[i].wa_number + "," + jsonData1[i].name + " </option>";
                            }
                            $("#contact_ids").append(appenddata1);
                            $('#contact_ids').selectpicker('refresh');
                            return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
});
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
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.10/js/bootstrap-select.js"></script>  
<script type="text/javascript">
$(document).ready(function() {
    $('#contact_ids').selectpicker({
        style: 'btn-info2',
    });
});
</script>