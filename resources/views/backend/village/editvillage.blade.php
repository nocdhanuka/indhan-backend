<?php //dd($all_districts); ?>
@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Edit Village List</h1>
            
            @if(Session::has('message'))
            <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            </div>
            @endif

            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Edit Village</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <form role="form" method="post" class="search-form"  id="createdistrict" action="{{url('village/update-village')}}"  >
                                <div class="col-lg-3">
                                    <label class="formlabel">State Name</label>
                                    <select id="state_name" class="frm_txt mdb-select inputs"  name="state_name">
                                        <option value="" >Choose State</option>
                                        <?php
                                        foreach($state_detail as $state) {
                                            if($state->state_id==$district_detail->state_id) { ?>
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
                                    <div class="rerror"><label for="state_name" generated="true" class="error">{{ $errors->first('state_name') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">District Name</label>
                                    <select id="district_name" class="frm_txt mdb-select inputs"  name="district_name">
                                        <option value="" >Choose District</option>                                        
                                        <?php
                                        foreach($all_districts as $district) {
                                            if($district->district_id==$district_detail->district_id) { ?>
                                                <option selected="selected" value="{{$district->district_id}}">{{$district->district}}</option>
                                            <?php    
                                            }
                                            else { ?>
                                                <option value="{{$district->district_id}}">{{$district->district}}</option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="district_name" generated="true" class="error">{{ $errors->first('district_name') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Tehsil Name</label>
                                    <select id="tehsil_name" class="frm_txt mdb-select inputs"  name="tehsil_name">
                                        <option selected="selected" value="{{$tehsil_detail->tehsil_id}}">{{$tehsil_detail->tehsil}}</option>
                                        
                                    </select>
                                    <div class="rerror"><label for="tehsil_name" generated="true" class="error">{{ $errors->first('tehsil_name') }}</label></div>
                                </div>

                                <div class="col-lg-3">
                                    <label class="formlabel">Village Name</label>
                                    <input type="text" id="village_name" class="frm_txt inputs"  name="village_name" placeholder="Village Name" maxlength="50" value="{{ $village_detail->village }}">
                                    
                                    <div class="rerror"><label for="village_name" generated="true" class="error">{{ $errors->first('village_name') }}</label></div>
                                </div>

                                <div class="clearfix"></div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Latitude</label>
                                    <input type="text" id="village_latitude" class="frm_txt inputs"  name="village_latitude" placeholder="Latitude" value="{{ $village_detail->latitude }}">
                                    <div class="rerror"><label for="village_latitude" generated="true" class="error">{{ $errors->first('village_latitude') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Longitude</label>
                                    <input type="text" id="village_longitude" class="frm_txt inputs"  name="village_longitude" placeholder="Longitude" value="{{ $village_detail->longitude }}">
                                    <div class="rerror"><label for="village_longitude" generated="true" class="error">{{ $errors->first('village_longitude') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Radius (In meters)</label>
                                    <input type="text" id="village_radius" class="frm_txt inputs"  name="village_radius" placeholder="Radius" maxlength="50" value="{{ $village_detail->radius }}">
                                    <div class="rerror"><label for="village_radius" generated="true" class="error">{{ $errors->first('village_radius') }}</label></div>
                                </div>

                                <!-- <div class="col-lg-3">
                                    <label class="formlabel">Tehsil Name</label>
                                    <input type="text" id="tehsil_name" class="frm_txt inputs"  name="tehsil_name" placeholder="Tehsil Name" maxlength="50" value="{{ $tehsil_detail->tehsil }}">
                                    <div class="rerror"><label for="tehsil_name" generated="true" class="error">{{ $errors->first('tehsil_name') }}</label></div>
                                </div> -->
                                
                                <div class="col-lg-3">
                                    <input type="hidden" name="village_id" id="village_id" value="{{ $village_detail->village_id }}" />
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


<!--<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/select/1.2.5/js/dataTables.select.min.js"></script>
<script type="text/javascript" language="javascript" src="https://editor.datatables.net/extensions/Editor/js/dataTables.editor.min.js"></script>
<link  rel="stylesheet" href="https://editor.datatables.net/extensions/Editor/css/editor.dataTables.min.css">
<link  rel="stylesheet" href="https://cdn.datatables.net/select/1.2.5/css/select.dataTables.min.css">-->

<!-- SlimScroll 
<script src="{{asset('assets/admin/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('assets/admin/dist/js/app.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->

<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>

<script>
$( document ).on('ready',function() {
    $('#state_name').on('change',function(){
        var state_name   = $('#state_name').val();
        var data = {
            _token:     $(this).data('token'),
            state_name: state_name
        }
        $("#district_name").empty();
        // $("#tehsil_name").empty();
        $.ajax({
            type:       'POST',
            url:        "{{url('tehsil/tehsil-district')}}",
            data:       data,
            success:    function (data) {
                                var jsonData1   = JSON.parse(data);
                                var appenddata1 = '';
                                appenddata1 += "<option value = ''>--Select District--</option>";
                                for (var i = 0; i < jsonData1.length; i++) {
                                    appenddata1 += "<option value = '" + jsonData1[i].dId + "'>" + jsonData1[i].dName + " </option>";
                                }
                                $("#district_name").append(appenddata1);
                                return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });

    //district
    $('#district_name').on('change',function(){
        var district_name   = $('#district_name').val();
        var data = {
            _token:     $(this).data('token'),
            district_name: district_name
        }
        $("#tehsil_name").empty();
        $("#tehsil_name").append('<option value="">--Choose Tehsil--</option>');
        $.ajax({
            type:       'POST',
            url:        "{{url('village/village-tehsil')}}",
            data:       data,
            success:    function (data) {
                                var jsonData1   = JSON.parse(data);
                                var appenddata1 = '';
                                $("#tehsil_name").empty();
                                appenddata1 += "<option value = ''>--Choose Tehsil--</option>";
                                for (var i = 0; i < jsonData1.length; i++) {
                                    appenddata1 += "<option value = '" + jsonData1[i].tId + "'>" + jsonData1[i].tName + " </option>";
                                }
                                $("#tehsil_name").append(appenddata1);
                                // drawTable(district_name);
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
$("#createdistrict").validate({
    errorPlacement: function (error, element) {
                        console.log($(element).next());
                        $(element).next('.rerror').html(error);
                        $(element).next().next('.rerror').html(error);
                    },
    ignore: "input[type='text']:hidden",
    rules: {
        state_name: {
            required:   true
        },
        district_name: {
            required:   true
        },
        tehsil_name: {
            required:   true
        },
        village_latitude: {
            required:   true,
        },
        village_longitude: {
            required:   true,
        },
        village_radius: {
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
$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});
$('#reset').click(function(){
    window.location.assign('{{url('village/add-village')}}')
});


</script>
