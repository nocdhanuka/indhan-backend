@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Add IFC Farmer</h1>
        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Add IFC Farmers</li>
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
                        <form role="form" method="post" enctype="multipart/form-data" class="search-form"  id="createifcfarmer" action="{{url('admin/createifcfarmer')}}"  >
                            <div class="col-lg-12">
                                <div class="col-lg-4">
                                    <label class="formlabel">Farmer Name</label>
                                    <input type="text" id="farmer_name" class="frm_txt"  name="farmer_name" placeholder="Farmer Name">
                                    <div class="rerror"><label for="farmer_name" generated="true" class="error">{{ $errors->first('farmer_name') }}</label></div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="formlabel">Mobile No</label>
                                    <input type="text" id="mobile" class="frm_txt"  name="mobile" placeholder="Mobile Number">
                                    <div class="rerror"><label for="mobile" generated="true" class="error">{{ $errors->first('mobile') }}</label></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr>
                            <div class="col-lg-12">
                                <div class="col-lg-4">
                                    <label class="formlabel">Latitude</label>
                                    <input type="text" id="latitude" class="frm_txt"  name="latitude" placeholder="Latitude">
                                    <div class="rerror"><label for="latitude" generated="true" class="error">{{ $errors->first('latitude') }}</label></div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="formlabel">Longitude</label>
                                    <input type="text" id="longitude" class="frm_txt"  name="longitude" placeholder="Longitude">
                                    <div class="rerror"><label for="longitude" generated="true" class="error">{{ $errors->first('longitude') }}</label></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr>
                            <div class="col-lg-12">
                                <div class="col-lg-4"> 
                                    <label class="formlabel">Image</label>
                                    <input type="file" id="image" class="frm_txt"  name="image">
                                    <div class="rerror"><label for="image" generated="true" class="error">{{ $errors->first('image') }}</label></div>
                                </div>
                                <div class="col-lg-4"> 
                                    <label class="formlabel">Acreage</label>
                                    <input type="text" id="acreage" class="frm_txt"  name="acreage" placeholder="Acreage">
                                    <div class="rerror"><label for="acreage" generated="true" class="error">{{ $errors->first('acreage') }}</label></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr/>
                            <!-- <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div> -->
                            <div class="col-lg-12">
                                <div class="col-lg-4"> 
                                    <label class="formlabel">Activity Distance</label>
                                    <input type="text" id="activity_distance" class="frm_txt"  name="activity_distance" placeholder="Activity Distance">
                                    <div class="rerror"><label for="activity_distance" generated="true" class="error">{{ $errors->first('activity_distance') }}</label></div>
                                </div> 
                                <div class="col-lg-4">&nbsp;</div>
                            </div>
                            <div class="clearfix"></div>                            
                            <input type="hidden" name="ticket_no" value="{{ $ticketno }}" />
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Create" >
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






