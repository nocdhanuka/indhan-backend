<?php //dd($zonal_manager_list); ?>
@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>DD Permission</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">DD Permission</li>
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
                        //echo "<pre>"; print_r($language_list);
                        ?>
                        <form role="form" method="post" class="search-form"  id="createareamanager" action="{{url('permission/create-permission')}}"  >                                                          
                            <div class="col-lg-3"> 
                                <label class="formlabel">Zonal Mangers</label>
                                <select id="zonal_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="zonal_manager">
                                    <option value="">--Select Zonal Manager--</option>
                                    <?php
                                    foreach($zonal_list as $zonal) { ?>
                                    <option value="{{$zonal->zonal_manager_id}}">{{$zonal->zonal_manager_name}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="zonal_manager" generated="true" class="error">{{ $errors->first('zonal_manager') }}</label></div>
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Regional Mangers</label>
                                <select id="regional_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="regional_manager">
                                    <option value="">--Select Regional Manager--</option>
                                </select>
                                <div class="rerror"><label for="regional_manager" generated="true" class="error">{{ $errors->first('regional_manager') }}</label></div>
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Area Mangers</label>
                                <select id="area_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="area_manager">
                                    <option value="">--Select Area Manager--</option>
                                </select>
                                <div class="rerror"><label for="area_manager" generated="true" class="error">{{ $errors->first('area_manager') }}</label></div>
                            </div>
                            <div class="col-lg-3"> 
                                <label class="formlabel">BDO</label>
                                <select id="bdo"   class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="bdo">
                                    <option value="">--Select BDO--</option>
                                </select>
                                <div class="rerror"><label for="bdo" generated="true" class="error">{{ $errors->first('bdo') }}</label></div>
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">DD</label>
                                <select id="user_dept_id" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="user_dept_id">
                                    <option value="">--Select BDO--</option>
                                </select>
                                <div class="rerror"><label for="user_dept_id" generated="true" class="error">{{ $errors->first('user_dept_id') }}</label></div>
                            </div>
                            <div class="col-lg-12"> 
                                <label class="formlabel">App Menu</label>                                
                                <?php
                                foreach($menu_list as $menu) { ?>
                                <input type="checkbox" name="appmenu[]" id="appmenu" value="{{$menu->menu_id}}" >{{$menu->menu}}&emsp;
                                <?php
                                }
                                ?>
                                <div class="rerror"><label for="appmenu" generated="true" class="error">{{ $errors->first('appmenu') }}</label></div>
                            </div> 
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            <input type="hidden" name="user_type" id="user_type" value="dhanuka_doctor" />
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Set Permission" >
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
    $.ajaxSetup({
       headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });
    
    $('#zonal_manager').on('change',function(){
        $("input[name='appmenu[]']").prop('checked', false); 
        var zonal_manager   = $('#zonal_manager').val();
        $("#regional_manager").empty();                            
        $("#area_manager").empty();
        $("#bdo").empty();
        $("#user_dept_id").empty();
        var data = {
            _token:         $(this).data('token'),
            zonal_manager:  zonal_manager
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('area/zonal-regional')}}",
            data:       data,
            success:    function (data) {
                            $("#regional_manager").empty();                            
                            $("#area_manager").empty();
                            $("#bdo").empty();
                            $("#user_dept_id").empty();
                            if(data) {
                                var jsonData1   = JSON.parse(data);
                                var appenddata1 = '';
                                appenddata1 += "<option value = ''>--Select Regional Manger--</option>";
                                for (var i = 0; i < jsonData1.length; i++) {
                                    appenddata1 += "<option value = '" + jsonData1[i].regional_manager_id + "'>" + jsonData1[i].regional_manager_name + " </option>";
                                }
                                $("#regional_manager").append(appenddata1);
                                return false;
                            }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
    
    
    $('#regional_manager').on('change',function(){
        $("input[name='appmenu[]']").prop('checked', false); 
        var regional_manager   = $('#regional_manager').val();                          
        $("#area_manager").empty();
        $("#bdo").empty();
        $("#user_dept_id").empty();
        var data = {
            _token:             $(this).data('token'),
            regional_manager:   regional_manager
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('area/area-manager')}}",
            data:       data,
            success:    function (data) {                      
                                $("#area_manager").empty();
                                $("#bdo").empty();
                                $("#user_dept_id").empty();
                                if(data) {
                                    var jsonData1   = JSON.parse(data);
                                    var appenddata1 = '';
                                    $("#area_manager").empty();
                                    appenddata1 += "<option value = ''>--Select Area Manger--</option>";
                                    for (var i = 0; i < jsonData1.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1[i].area_manager_id + "'>" + jsonData1[i].area_manager_name + " </option>";
                                    }
                                    $("#area_manager").append(appenddata1);
                                    return false;
                                }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
    
    
    $('#area_manager').on('change',function(){
        $("input[name='appmenu[]']").prop('checked', false); 
        var area_manager   = $('#area_manager').val();  
        $("#bdo").empty();  
        $("#user_dept_id").empty();
        var data = {
            _token:         $(this).data('token'),
            area_manager:   area_manager
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('area/bdo')}}",
            data:       data,
            success:    function (data) {
                                $("#bdo").empty();
                                $("#user_dept_id").empty();
                                if(data) {
                                    var jsonData1   = JSON.parse(data);
                                    var appenddata1 = '';
                                    $("#bdo").empty();
                                    $("#user_dept_id").empty();
                                    appenddata1 += "<option value = ''>--Select BDO--</option>";
                                    for (var i = 0; i < jsonData1.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1[i].id + "'>" + jsonData1[i].name + " </option>";
                                    }
                                    $("#bdo").append(appenddata1);
                                    // $("#user_dept_id").append(appenddata1);
                                    return false;
                                }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });


    $('#bdo').on('change',function(){
        $("input[name='appmenu[]']").prop('checked', false); 
        var bdo   = $('#bdo').val(); 
        $("#user_dept_id").empty();
        var data = {
            _token:         $(this).data('token'),
            area_manager:   bdo
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('area/dd')}}",
            data:       data,
            success:    function (data) {
                                $("#user_dept_id").empty();
                                if(data) {
                                    var jsonData1   = JSON.parse(data);
                                    var appenddata1 = '';
                                    $("#user_dept_id").empty();
                                    appenddata1 += "<option value = ''>--Select DD--</option>";
                                    for (var i = 0; i < jsonData1.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1[i].dd_id + "'>" + jsonData1[i].name + " </option>";
                                    }
                                    $("#user_dept_id").append(appenddata1);
                                    return false;
                                }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
    
        
    $('#user_dept_id').on('change',function(){
        var area_manager   = $('#user_dept_id').val();
        var data = {
                    _token:             $(this).data('token'),
                    user_dept_id:       area_manager,
                    _dept:              'dhanuka_doctor'
                };
        $.ajax({
            type:       'POST',
            url:        "{{url('permission/team-permission')}}",
            data:       data,
            success:    function (data) {
                                $("input[name='appmenu[]']").prop('checked', false); 
                                var jsonData1   = JSON.parse(data);
                                for (var i = 0; i < jsonData1.length; i++) {
                                    $("input[value='"+jsonData1[i]+"']").prop('checked', true); 
                                }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });

});
</script>































