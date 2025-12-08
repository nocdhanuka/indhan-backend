<?php //dd($zonal_manager_list); ?>
@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Edit Season</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Edit Season</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-offset-2 col-md-8 col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="success"></div>
                        <?php
                        //echo "<pre>"; print_r($language_list);
                        ?>
                        
                        <form role="form" method="post" class="search-form"  id="createseason" action="{{url('demo/update-season')}}"  >                                                          
                            
                            <div class="col-lg-6"> 
                                <label class="formlabel">Name</label>
                                <input type="text" id="season_name" class="frm_txt form-control" name="season_name" placeholder="Season Name"  value="{{ $season->season_name }}">   
                                <div class="rerror"><label for="season_name" generated="true" class="error">{{ $errors->first('season_name') }}</label></div>
                            </div> 
                            <div class="clearfix"></div>
                                
                            <div class="col-lg-4"> 
                                <label class="formlabel">State</label>
                                <select id="state" class="frm_txt"  data-token="{{csrf_token()}}"  name="state">
                                    <option value="">Select State</option>
                                    <?php
                                    if (isset($userdata) && !empty($userdata)) {
                                    foreach($state_list as $state) { ?>
                                    <option value="{{$state->state_id}}" <?php if($userdata->state_id==$state->state_id) echo 'selected="selected"'; ?> >{{$state->state}}</option>
                                    <?php    
                                    }
                                    }
                                    else 
                                    {
                                    foreach($state_list as $state) { ?>
                                    <option value="{{$state->state_id}}" >{{$state->state}}</option>
                                    <?php    
                                    }
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="state" generated="true" class="error">{{ $errors->first('state') }}</label></div>
                            </div>
                            
                            <div class="col-lg-4"> 
                                <label class="formlabel">District</label>
                                <select id="district" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="district">
                                    <option value="">Select District</option>
                                    <?php
                                    if (isset($district_list)) {
                                    foreach($district_list as $district) { ?>
                                    <option value="{{$district->district_id}}" <?php if($userdata->district_id==$district->district_id) echo 'selected="selected"'; ?> >{{$district->district}}</option>
                                    <?php    
                                    }
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="district" generated="true" class="error">{{ $errors->first('district') }}</label></div>
                            </div> 

                            <div class="col-lg-4"> 
                                <label class="formlabel">Tehsil</label>
                                <select id="tehsil" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="tehsil">
                                <option value="">--Select Tehsil--</option>
                                <?php
                                if (isset($tehsil_list)) {
                                foreach($tehsil_list as $tehsil) { ?>
                                <option value="{{$tehsil->tehsil_id}}" <?php if($userdata->tehsil_id==$tehsil->tehsil_id) echo 'selected="selected"'; ?> >{{$tehsil->tehsil}}</option>
                                <?php    
                                }
                                }
                                ?>
                                </select>
                                <div class="rerror"><label for="tehsil" generated="true" class="error">{{ $errors->first('tehsil') }}</label></div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-3"> 
                                <label class="formlabel">Village</label>
                                <select id="village" multiple class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="village[]">
                                <option value="">--Select Village--</option>
                            <?php 
                                if (isset($village_list)) {
                                foreach($village_list as $village) { 
                                  $ddvillage_id = explode(',',$dd_village_list->ddvillage_id);
                                  if(in_array($village->village_id, $ddvillage_id)) {
                            ?>
                                <option value="{{$village->village_id}}" selected="selected">{{$village->village}}</option>
                            <?php }else{ ?>
                                <option value="{{$village->village_id}}">{{$village->village}}</option>
                                <?php } 
                             
                            }
                            }
                            ?>
                                </select>
                                <div class="rerror"><label for="village" generated="true" class="error">{{ $errors->first('village') }}</label></div>
                            </div>

                            
                            <div class="clearfix"></div>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                             <input type="hidden" name="seasonId" value="{{ $season->id }}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Update Season" >
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
        $("#createseason").validate({
        errorPlacement: function (error, element) {
                            console.log($(element).next());
                            $(element).next('.rerror').html(error);
                            $(element).next().next('.rerror').html(error);
                        },
        ignore: "input[type='text']:hidden",
        rules: {
            season_name: {
                required:   true,
            },
            state: {
                required:   true,
            },
            district: {
                required:   true,
            },
            tehsil: {
                required:   true,
            },
            village: {
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

    $(document).ready(function () {
        $('#state').on('change',function(){
            var state   = $('#state').val();
            var data = {
                _token: $(this).data('token'),
                state:  state
            }
            $.ajax({
                type:       'POST',
                url:        "{{url('district/state-district')}}",
                data:       data,
                success:    function (data) {
                                if(data) {
                                    $("#district").empty();
                                    var jsonData1   = JSON.parse(data);
                                    if(jsonData1.district!='undefined') {
                                        var appenddata1 = '';
                                        appenddata1 += "<option value = ''>--Select District--</option>";
                                        for (var i = 0; i < jsonData1.district.length; i++) {
                                            appenddata1 += "<option value = '" + jsonData1.district[i].district_id + "'>" + jsonData1.district[i].district + " </option>";
                                        }
                                        $("#district").append(appenddata1);
                                    }
                                    return false;
                                }
                            },
                error:      function (jqXHR, textStatus, errorThrown) {
                                console.log(textStatus, errorThrown);
                            }
            });
        });

        $('#district').on('change',function(){
            var district   = $('#district').val();
            var data = {
                _token: $(this).data('token'),
                district:  district
            }
            $.ajax({
                type:       'POST',
                url:        "{{url('district/district-tehsil')}}",
                data:       data,
                success:    function (data) {
                                if(data) { 
                                    $("#tehsil").empty();
                                    var jsonData1   = JSON.parse(data);
                                    if(jsonData1.tehsil!='undefined') {
                                        var appenddata1 = '';
                                        appenddata1 += "<option value = ''>--Select Tehsil--</option>";
                                        for (var i = 0; i < jsonData1.tehsil.length; i++) {
                                            appenddata1 += "<option value = '" + jsonData1.tehsil[i].tehsil_id + "'>" + jsonData1.tehsil[i].tehsil + " </option>";
                                        }
                                        $("#tehsil").append(appenddata1);
                                    }
                                    return false;
                                }
                            },
                error:      function (jqXHR, textStatus, errorThrown) {
                                console.log(textStatus, errorThrown);
                            }
            });
        });

        $('#tehsil').on('change',function(){
            var tehsil   = $('#tehsil').val();console.log(tehsil);
            var data = {
                _token: $(this).data('token'),
                tehsil:  tehsil
            }
            $.ajax({
                type:       'POST',
                url:        "{{url('district/tehsil-village')}}",
                data:       data,
                success:    function (data) {
                                if(data) {
                                    $("#village").empty();
                                    var jsonData1   = JSON.parse(data);console.log(jsonData1);
                                    if(jsonData1.village!='undefined') {
                                        var appenddata1 = '';
                                        appenddata1 += "<option value = ''>--Select Village--</option>";
                                        for (var i = 0; i < jsonData1.village.length; i++) {
                                            appenddata1 += "<option value = '" + jsonData1.village[i].village_id + "'>" + jsonData1.village[i].village + " </option>";
                                        }
                                        $("#village").append(appenddata1);
                                        $('#village').selectpicker('refresh');
                                    }
                                    else
                                    {   
                                        var appenddata1 = '';
                                        appenddata1 += "<option value = ''>--Select Village--</option>";
                                        for (var i = 0; i < jsonData1.village.length; i++) {
                                            appenddata1 += "<option value = ''> </option>";
                                        }
                                        $("#village").append(appenddata1);
                                        $('#village').selectpicker('refresh');
                                    
                                    }
                                    return false;
                                }
                            },
                error:      function (jqXHR, textStatus, errorThrown) {
                                console.log(textStatus, errorThrown);
                            }
            });
        });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.10/js/bootstrap-select.js"></script>  
<script type="text/javascript">
$(document).ready(function() {
    $('#village').selectpicker({
        style: 'btn-info2',
    });
   
});
</script>












