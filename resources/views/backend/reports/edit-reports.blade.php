@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Add Reports and Guidelines</h1>
        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add Reports and Guidelines</li>
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
                        <form role="form" method="post" enctype="multipart/form-data" class="search-form"  id="createreports" action="{{url('reports/update-reports')}}"  >
                            <div class="col-lg-12">
                                <div class="col-lg-4">
                                    <label class="formlabel">Report Name</label>
                                    <input type="text" id="report_name" class="frm_txt" name="report_name" placeholder="Report Name" value="{!! $reportData->report_name !!}">
                                    <div class="rerror"><label for="report_name" generated="true" class="error">{{ $errors->first('report_name') }}</label></div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="formlabel">Report Type</label>
                                    <select id="report_type" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="report_type">
                                        <option value="">--Select Report Type--</option>
                                        <?php
                                        foreach($reports_type as $type) { ?>
                                            <option value="{{$type->report_type_id}}" <?php if($reportData->report_type_id==$type->report_type_id) echo 'selected="selected"'; ?>>{{$type->name}}</option>
                                        <?php    
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="report_type" generated="true" class="error">{{ $errors->first('report_type') }}</label></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr>
                            <div class="col-lg-12">
                                <div class="col-lg-4">
                                    <label class="formlabel">Year</label>
                                    <select id="year" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="year">
                                        <option value="">--Select Year--</option>
                                        <?php for($year=$crr_year; $year<=$years; $year++) { ?>
                                            <option value="{{$year}}" <?php if($reportData->year==$year) echo 'selected="selected"'; ?>>{{$year}}</option>
                                        <?php } ?>
                                    </select>
                                    <div class="rerror"><label for="year" generated="true" class="error">{{ $errors->first('year') }}</label></div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="formlabel">Month</label>
                                    <select id="month" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="month">
                                        <option value="">--Select Month--</option>
                                        <?php foreach($months as $key=>$month) { ?>
                                            <option value="{{$key}}" <?php if($reportData->month==$key) echo 'selected="selected"'; ?>>{{$month}}</option>
                                        <?php } ?>
                                    </select>
                                    <div class="rerror"><label for="month" generated="true" class="error">{{ $errors->first('month') }}</label></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr>
                            <div class="col-lg-12">
                                <div class="col-lg-4"> 
                                    <label class="formlabel">Zonal Managers</label>
                                    <select id="zonal_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="zonal_manager">
                                        <option value="">--Select Zonal Manager--</option>
                                        <?php
                                        foreach($zonal_list as $zonal) { ?>
                                            <option value="{{$zonal->zonal_manager_id}}" <?php if($reportData->zonal_manager_id==$zonal->zonal_manager_id) echo 'selected="selected"'; ?>>{{$zonal->zonal_manager_name}}</option>
                                        <?php    
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="zonal_manager" generated="true" class="error">{{ $errors->first('zonal_manager') }}</label></div>
                                </div>
                                <div class="col-lg-4"> 
                                    <label class="formlabel">Regional Managers</label>
                                    <select id="regional_manager" class="frm_txt mdb-select" data-token="{{csrf_token()}}"  name="regional_manager[]" multiple="multiple">
                                        <option value="">--Select Regional Manager--</option>
                                        <?php
                                        foreach($regional_list as $regional) {
                                            $regional_manager_ids = $reportData->regional_manager_id;
                                            if (($regional_manager_ids) && (in_array($regional->regional_manager_id,explode(',', $regional_manager_ids)))) {
                                            ?>
                                                <option selected="selected" value="{{$regional->regional_manager_id}}">{{$regional->regional_manager_name}}</option>
                                            <?php } else { ?>
                                                <option value="{{$regional->regional_manager_id}}">{{$regional->regional_manager_name}}</option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="regional_manager" generated="true" class="error">{{ $errors->first('regional_manager') }}</label></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr/>
                            <!-- <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div> -->
                            <div class="col-lg-12">
                                <div class="col-lg-4"> 
                                    <label class="formlabel">Sales Regional Managers</label>
                                    <select id="sales_regional_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="sales_regional_manager[]" multiple="multiple">
                                        <option value="">--Select Sales Regional Manager--</option>
                                        <?php
                                        foreach($sales_regional_list as $sales_regional) {
                                            $sales_regional_manager_ids = $reportData->sales_regional_manager_id;
                                            if (($sales_regional_manager_ids) && (in_array($sales_regional->sales_regional_manager_id,explode(',', $sales_regional_manager_ids)))) {
                                            ?>
                                                <option selected="selected" value="{{$sales_regional->sales_regional_manager_id}}">{{$sales_regional->sales_regional_manager_name}}</option>
                                            <?php } else { ?>
                                                <option value="{{$sales_regional->sales_regional_manager_id}}">{{$sales_regional->sales_regional_manager_name}}</option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="sales_regional_manager" generated="true" class="error">{{ $errors->first('sales_regional_manager') }}</label></div>
                                </div> 
                                <div class="col-lg-4"> 
                                    <label class="formlabel">Area Managers</label>
                                    <select id="area_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="area_manager[]" multiple="multiple">
                                        <option value="">--Select Area Manager--</option>
                                        <?php
                                        foreach($area_manager_list as $area) {
                                            $area_manager_ids = $reportData->area_manager_id;
                                            if (($area_manager_ids) && (in_array($area->area_manager_id,explode(',', $area_manager_ids)))) {
                                            ?>
                                                <option selected="selected" value="{{$area->area_manager_id}}">{{$area->area_manager_name}}</option>
                                            <?php } else { ?>
                                                <option value="{{$area->area_manager_id}}">{{$area->area_manager_name}}</option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="area_manager" generated="true" class="error">{{ $errors->first('area_manager') }}</label></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr/>
                            <div class="col-lg-12">
                                <div class="col-lg-4"> 
                                    <label class="formlabel">Sales Area Managers</label>
                                    <select id="sales_area_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}" name="sales_area_manager[]" multiple="multiple">
                                        <option value="">--Select Sales Area Manager--</option>
                                        <?php
                                        foreach($sales_area_manager_list as $sales_area) {
                                            $sales_area_manager_ids = $reportData->sales_area_manager_id;
                                            if (($sales_area_manager_ids) && (in_array($sales_area->sales_area_manager_id,explode(',', $sales_area_manager_ids)))) {
                                            ?>
                                                <option selected="selected" value="{{$sales_area->sales_area_manager_id}}">{{$sales_area->sales_area_manager_name}}</option>
                                            <?php } else { ?>
                                                <option value="{{$sales_area->sales_area_manager_id}}">{{$sales_area->sales_area_manager_name}}</option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="sales_area_manager" generated="true" class="error">{{ $errors->first('sales_area_manager') }}</label></div>
                                </div>
                                <!-- <div class="clearfix"></div>
                                <div class="col-lg-12"><hr></div>
                                <div class="clearfix"></div> -->
                                
                                <div class="col-lg-4"> 
                                    <label class="formlabel">Front Liner</label>
                                    <select id="front_liner" class="frm_txt mdb-select"  data-token="{{csrf_token()}}" name="user_id[]" multiple="multiple">
                                        <option value="">--Select Front Liner--</option>
                                        <?php
                                        foreach($bdo_list as $bdo) {
                                            $bdo_ids = $reportData->user_id;
                                            if (($bdo_ids) && (in_array($bdo->id,explode(',', $bdo_ids)))) {
                                            ?>
                                                <option selected="selected" value="{{$bdo->id}}">{{$bdo->name}}</option>
                                            <?php } else { ?>
                                                <option value="{{$bdo->id}}">{{$bdo->name}}</option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="front_liner" generated="true" class="error">{{ $errors->first('front_liner') }}</label></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr>
                            <div class="col-lg-12">
                                <div class="col-lg-4">
                                    <label class="formlabel">Add Files</label>
                                    <input type="file" id="report_file" class="frm_txt"  name="report_file">
                                    <?php
                                    if(!empty($reportData->uploaded_file)) {
                                        $base_url = 'https://dhanuka-app.s3.ap-south-1.amazonaws.com/';
                                        $uploaded_file_path = $base_url.$reportData->uploaded_file;
                                    ?>
                                        <a href="{{$uploaded_file_path}}" target="_blank" class="btn btn-info" style="margin-top: 15px;">View File</a>
                                    <?php } ?>
                                    <div class="rerror"><label for="report_file" generated="true" class="error">{{ $errors->first('report_file') }}</label></div>
                                </div>                      
                                <div class="col-lg-4">  
                                    <label class="formlabel">Publish</label>
                                    <input type="radio" name="status" value="0" checked="checked"> Inactive &nbsp;
                                    <input type="radio" name="status" value="1" <?php echo ($reportData->status ==1) ? 'checked' : '' ;?>> Active
                                </div>
                            </div>
                            <div class="clearfix"></div>                            
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <input type="hidden" name="report_id" id="report_id" value="{{ $reportData->report_id }}" />
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Update Report">
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

<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function () {
    $('#zonal_manager').on('change',function(){
        var zonal_manager   = $('#zonal_manager').val();
        var data = {
            _token:         $(this).data('token'),
            zonal_manager:  zonal_manager
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('area/regional-sales')}}",
            data:       data,
            success:    function (data) {
                            if(data) {
                                $("#regional_manager").empty();
                                $("#sales_regional_manager").empty();
                                $("#area_manager").empty();
                                $("#sales_area_manager").empty();
                                $("#front_liner").empty();
                                var jsonData1   = JSON.parse(data);
                                if(typeof jsonData1.regional!='undefined') {
                                    var appenddata1 = '';
                                    appenddata1 += "<option value = ''>--Select Regional Manger--</option>";
                                    for (var i = 0; i < jsonData1.regional.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1.regional[i].regional_manager_id + "'>" + jsonData1.regional[i].regional_manager_name + " </option>";
                                    }
                                    $("#regional_manager").append(appenddata1);
                                } else {
                                    var appenddata1 = "<option value = ''>--Select Regional Manager--</option>";
                                    $("#regional_manager").append(appenddata1);
                                }
                                
                                if(typeof jsonData1.sales!='undefined') {
                                    var appenddata2 = '';
                                    appenddata2 += "<option value = ''>--Select Sales Regional Manger--</option>";
                                    for (var i = 0; i < jsonData1.sales.length; i++) {
                                        appenddata2 += "<option value = '" + jsonData1.sales[i].sales_regional_manager_id + "'>" + jsonData1.sales[i].sales_regional_manager_name + " </option>";
                                    }
                                    $("#sales_regional_manager").append(appenddata2);
                                }
                                else {
                                    var appenddata1 = "<option value = ''>--Select Regional Sales Manager--</option>";
                                    $("#sales_regional_manager").append(appenddata1);
                                }
                                var appenddata3 = "<option value = ''>--Select Area Manager--</option>";
                                $("#area_manager").append(appenddata3);
                                var appenddata4 = "<option value = ''>--Select Area Sales Manager--</option>";
                                $("#sales_area_manager").append(appenddata4);
                                var appenddata5 = "<option value = ''>--Select Front Liner--</option>";
                                $("#front_liner").append(appenddata5);
                                return false;
                            }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
    
    $('#regional_manager').on('change',function(){
        var regional_manager   = $('#regional_manager').val();
        var data = {
            _token:             $(this).data('token'),
            regional_manager:   regional_manager
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('area/area-sales-for-report')}}",
            data:       data,
            success:    function (data) {
                                $("#area_manager").empty();
                                $("#sales_area_manager").empty();
                                var jsonData1   = JSON.parse(data);
                                if(typeof jsonData1.area!='undefined') {
                                    var appenddata1 = '';
                                    appenddata1 += "<option value = ''>--Select Regional Manger--</option>";
                                    for (var i = 0; i < jsonData1.area.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1.area[i].area_manager_id + "'>" + jsonData1.area[i].area_manager_name + " </option>";
                                    }
                                    $("#area_manager").append(appenddata1);
                                } else {
                                    appenddata1 += "<option value = ''>--Select Regional Manger--</option>";
                                    $("#area_manager").append(appenddata1);
                                }
                                if(typeof jsonData1.sales!='undefined') {
                                    var appenddata2 = '';
                                    appenddata2 += "<option value = ''>--Select Sales Regional Manger--</option>";
                                    for (var i = 0; i < jsonData1.sales.length; i++) {
                                        appenddata2 += "<option value = '" + jsonData1.sales[i].sales_area_manager_id + "'>" + jsonData1.sales[i].sales_area_manager_name + " </option>";
                                    }
                                    $("#sales_area_manager").append(appenddata2);
                                } else {
                                    appenddata1 += "<option value = ''>--Select Regional Manger--</option>";
                                    $("#area_manager").append(appenddata1);
                                }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });

    $('#sales_area_manager').on('change',function(){
        var sales_area_manager = $('#sales_area_manager').val();
        var data = {
            _token:             $(this).data('token'),
            sales_area_manager:   sales_area_manager
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('area/front-liners-for-report')}}",
            data:       data,
            success:    function (data) {
                                $("#front_liner").empty();
                                var jsonData1   = JSON.parse(data);
                                if(typeof jsonData1!='undefined') {
                                    var appenddata1 = '';
                                    appenddata1 += "<option value = ''>--Select Front Liner--</option>";
                                    for (var i = 0; i < jsonData1.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1[i].id + "'>" + jsonData1[i].name + " </option>";
                                    }
                                    $("#front_liner").append(appenddata1);
                                }
                                
                                return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
    $("#createreports").validate({
        errorPlacement: function (error, element) {
                            console.log($(element).next());
                            $(element).next('.rerror').html(error);
                            $(element).next().next('.rerror').html(error);
                        },
        ignore: "input[type='text']:hidden",
        rules: {
            report_name: {
                required:   true,
            },
            report_type: {
                required:   true
            },
            year: {
                required:   true
            },
            month: {
                required:   true
            },
            zonal_manager: {
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('.step2submit').keypress(function (e) {
        if (e.which == 13) {
            $("#createreports").click();
        }
    });
});
</script>






