<?php
//dd($deputation_list);
?>

@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Edit BDO</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Edit BDO</li>
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
                        <form role="form" method="post" class="search-form"  id="createbdo" action="{{url('organogram/update-bdo-orn')}}"  >                             
                            <div class="col-lg-3"> 
                                <label class="formlabel">Zonal Mangers</label>
                                <select id="zonal_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="zonal_manager">
                                    <option value="">--Select Zonal Manager--</option>
                                    <?php
                                    foreach($zonal_list as $zonal) { ?>
                                    <option value="{{$zonal->zonal_manager_id}}" <?php if($userdata->zonal_manager_id==$zonal->zonal_manager_id) echo 'selected="selected"'; ?> >{{$zonal->zonal_manager_name}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="zonal_manager" generated="true" class="error">{{ $errors->first('zonal_manager') }}</label></div>
                            </div> 
                            
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">Regional Mangers</label>
                                <select id="regional_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="regional_manager">
                                    <option value="">--Select Regional Manager--</option>
                                    <?php
                                    foreach($regional_list as $regional) { ?>
                                    <option value="{{$regional->regional_manager_id}}" <?php if($userdata->regional_manager_id==$regional->regional_manager_id) echo 'selected="selected"'; ?> >{{$regional->regional_manager_name}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="regional_manager" generated="true" class="error">{{ $errors->first('regional_manager') }}</label></div>
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Sales Regional Mangers</label>
                                <select id="sales_regional_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="sales_regional_manager">
                                    <option value="">--Select Regional Sales Manager--</option>
                                    <?php
                                    foreach($sales_regional_list as $sales_regional) { ?>
                                    <option value="{{$sales_regional->sales_regional_manager_id}}" <?php if($userdata->sales_regional_manager_id==$sales_regional->sales_regional_manager_id) echo 'selected="selected"'; ?> >{{$sales_regional->sales_regional_manager_name}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="sales_regional_manager" generated="true" class="error">{{ $errors->first('sales_regional_manager') }}</label></div>
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Area Mangers</label>
                                <select id="area_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="area_manager">
                                    <option value="">--Select Area Manager--</option>
                                    <?php
                                    foreach($area_list as $area) { ?>
                                    <option value="{{$area->area_manager_id}}" <?php if($userdata->area_manager_id==$area->area_manager_id) echo 'selected="selected"'; ?> >{{$area->area_manager_name}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="area_manager" generated="true" class="error">{{ $errors->first('area_manager') }}</label></div>
                            </div>
                            <div class="col-lg-3"> 
                                <label class="formlabel">Sales Area Mangers</label>
                                <select id="sales_area_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="sales_area_manager">
                                    <option value="">--Select Area Sales Manager--</option>
                                    <?php
                                    foreach($sales_area_list as $sales_area) { ?>
                                    <option value="{{$sales_area->sales_area_manager_id}}" <?php if($userdata->sales_area_manager_id==$sales_area->sales_area_manager_id) echo 'selected="selected"'; ?> >{{$sales_area->sales_area_manager_name}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="sales_area_manager" generated="true" class="error">{{ $errors->first('sales_area_manager') }}</label></div>
                            </div>
                            
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                                
                            <div class="col-lg-3"> 
                                <label class="formlabel">State</label>
                                <select id="state" class="frm_txt"  data-token="{{csrf_token()}}"  name="state">
                                    <option value="">Select State</option>
                                    <?php
                                    foreach($state_list as $state) { ?>
                                    <option value="{{$state->state_id}}" <?php if($userdata->state_id==$state->state_id) echo 'selected="selected"'; ?> >{{$state->state}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="state" generated="true" class="error">{{ $errors->first('state') }}</label></div>
                            </div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">District</label>
                                <select id="district" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="district">
                                    <option value="">Select District</option>
                                    <?php
                                    foreach($district_list as $district) { ?>
                                    <option value="{{$district->district_id}}" <?php if($userdata->district_id==$district->district_id) echo 'selected="selected"'; ?> >{{$district->district}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="district" generated="true" class="error">{{ $errors->first('district') }}</label></div>
                            </div> 

                            <div class="col-lg-3">
                                <label class="formlabel">Language</label>
                                <select id="language" class="frm_txt"  name="language">
                                    <option value="">--Select Language</option>
                                    <?php
                                    foreach($language_list as $language) { ?>
                                        <option value="{{$language->id}}" <?php if($userdata->language_id==$language->id) echo 'selected="selected"'; ?> >{{$language->language}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="language" generated="true" class="error">{{ $errors->first('language') }}</label></div>
                            </div>
                            
                            <div class="col-lg-3">  
                                <label class="formlabel">Department</label>
                                <input type="text" id="department" class="frm_txt"  name="department" placeholder="Department" value="{{$userdata->department}}">
                                <div class="rerror"><label for="department" generated="true" class="error">{{ $errors->first('department') }}</label></div>
                            </div>
                            
                            <div class="clearfix"></div>


                            <div class="col-lg-3">  
                                <label class="formlabel">Designation</label>
                                <!-- <input type="text" id="designation" class="frm_txt"  name="designation" placeholder="Designation" value="{{$userdata->designation}}"> -->
                                <select id="designation" class="frm_txt"  name="designation">
                                    <option value="">Select Designation</option>
                                    <option <?php if($userdata->designation == 'BDO-FL') { echo 'selected'; } ?> value="BDO-FL">BDO-FL</option>
                                    <option <?php if($userdata->designation == 'SALES-FL') { echo 'selected'; } ?> value="SALES-FL">SALES-FL</option>
                                    <option <?php if($userdata->designation == 'COUNTERPART-FL') { echo 'selected'; } ?> value="COUNTERPART-FL">COUNTERPART-FL</option>
                                </select>
                                <div class="rerror"><label for="designation" generated="true" class="error">{{ $errors->first('designation') }}</label></div>
                            </div>
                            <?php if ($userdata->designation == 'BDO-FL') { ?>
                            <div class="col-lg-3 counterpart_fl" 
                                style="display: block;">  
                                <label class="formlabel">COUNTERPART-FL</label>
                                <!-- <input type="text" id="designation" class="frm_txt"  name="designation" placeholder="Designation"> -->
                                <select id="counterpart_fl" class="frm_txt" required=""  name="counterpart_fl">
                                    <option value="">Select COUNTERPART-FL</option>
                                    <?php foreach ($counterpart_fl as $key => $value) { ?>
                                        <option <?php if($value['id'] == $userdata->counterpart_fl) { echo 'selected'; } ?> value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                       
                                    <?php } ?>
                                   
                                   
                                </select>
                                <div class="rerror"><label for="designation" generated="true" class="error">{{ $errors->first('designation') }}</label></div>
                            </div>
                        <?php } else { ?>
                             <div class="col-lg-3 counterpart_fl"  style="display: none;" >  
                                <label class="formlabel">COUNTERPART-FL</label>
                                <!-- <input type="text" id="designation" class="frm_txt"  name="designation" placeholder="Designation"> -->
                               <select id="counterpart_fl" class="frm_txt"  name="counterpart_fl">
                                    <option value="">Select COUNTERPART-FL</option>
                                   
                                </select>
                                <div class="rerror"><label for="designation" generated="true" class="error">{{ $errors->first('designation') }}</label></div>
                            </div>
                        <?php } ?>
                            
                            
                            <div class="col-lg-3">
                                <label class="formlabel">HQ District Code No./ BDO Code</label>
                                <input type="text" id="bdo_code" class="frm_txt"  name="bdo_code" placeholder="BDO Code" value="{{$userdata->bdo_code}}" maxlength="10">
                                @if ($errors->has('your_custom_error'))
                                    <div class="error">{{ $errors->first('your_custom_error') }}</div>
                                @endif
                                <div class="rerror"><label for="bdo_code" generated="true" class="error">{{ $errors->first('bdo_code') }}</label></div>
                            </div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel"> BDO Name</label>
                                <input type="text" id="name" class="frm_txt"  name="name" placeholder="Name" value="{{$userdata->name}}">
                                <div class="rerror"><label for="name" generated="true" class="error">{{ $errors->first('name') }}</label></div>
                            </div>
                             <div class="clearfix"></div>
                            <div class="col-lg-4"> 
                                <label class="formlabel">Mobile No.</label>
                                <input type="text" id="mobile_no" class="frm_txt"  name="mobile_no" placeholder="Mobile No." value="{{$userdata->bdo_mobile_no}}">
                                <div class="rerror"><label for="mobile_no" generated="true" class="error">{{ $errors->first('mobile_no') }}</label></div>
                            </div>    
                                                        
                            <div class="col-lg-4">  
                                <label class="formlabel">BDO HQ</label>
                                <input type="text" id="bdo_hq" class="frm_txt"  name="bdo_hq" placeholder="BDO HQ" value="{{$userdata->bdo_hq}}">
                                <div class="rerror"><label for="bdo_hq" generated="true" class="error">{{ $errors->first('bdo_hq') }}</label></div>
                            </div>
                                                      
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">App Permission To Edit Ticket</label>	
                                <input class="frm_chk" id="app_permission_edit_ticket" type="checkbox" name="app_permission_edit_ticket" <?php if($userdata->app_permission_edit_ticket=='1') echo 'checked="checked"';?>> 
                                <div class="rerror"></div>
                            </div>
                            <div class="col-lg-3"> 
                                <label class="formlabel">App Permission To Close Ticket</label>	
                                <input class="frm_chk" id="app_permission_close_ticket" type="checkbox" name="app_permission_close_ticket" <?php if($userdata->app_permission_close_ticket=='1') echo 'checked="checked"';?>> 
                                <div class="rerror"></div>
                            </div>
                                
                            <div class="clearfix"></div>
                            <?php //echo "<pre>";print_r($user_category_list);die; ?>
                            <div class="col-lg-3"> 
                                <label class="formlabel">Crop Product Category</label>
                                <select id="product_category" class="frm_txt mdb-select"  name="product_category[]" multiple="multiple">
                                    <option value="">Choose category</option>
                                    <?php
                                    foreach($category_list as $category) { 
                                        $category_id   = explode(',',$user_category_list->category_id);
                                        if(in_array($category->product_crop_category_id, $category_id)) {
                                            ?>
                                            <option selected="selected" value="{{$category->product_crop_category_id}}">{{$category->category_name}}</option>
                                        <?php    
                                        }
                                        else { ?>
                                            <option value="{{$category->product_crop_category_id}}">{{$category->category_name}}</option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="product_category" generated="true" class="error">{{ $errors->first('product_category') }}</label></div>
                            </div>
                            
                                                      
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">On Deputation</label>	
                                <input class="frm_chk" id="on_deputation" type="checkbox" name="on_deputation" <?php if($deputation_list && $deputation_list->status=='1') echo 'checked="checked"';?>> 
                                <div class="rerror"></div>
                            </div>
                                                      
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">Zonal Mangers</label>
                                <select id="deputation_zonal_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="deputation_zonal_manager">
                                    <option value="">--Select Zonal Manager--</option>
                                    <?php
                                    foreach($zonal_list as $zonal) { ?>
                                    <option value="{{$zonal->zonal_manager_id}}" <?php if($deputation_list && $deputation_list->zonal_manager_id==$zonal->zonal_manager_id) echo 'selected="selected"'; ?> >{{$zonal->zonal_manager_name}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="deputation_zonal_manager" generated="true" class="error">{{ $errors->first('deputation_zonal_manager') }}</label></div>
                            </div> 
                            
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">Regional Mangers</label>
                                <select id="deputation_regional_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="deputation_regional_manager">
                                    <option value="">--Select Regional Manager--</option>
                                    <?php
                                    foreach($regional_list as $regional) { ?>
                                    <option value="{{$regional->regional_manager_id}}" <?php if($deputation_list && $deputation_list->regional_manager_id==$regional->regional_manager_id) echo 'selected="selected"'; ?> >{{$regional->regional_manager_name}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="deputation_regional_manager" generated="true" class="error">{{ $errors->first('deputation_regional_manager') }}</label></div>
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Sales Regional Mangers</label>
                                <select id="deputation_sales_regional_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="deputation_sales_regional_manager">
                                    <option value="">--Select Regional Sales Manager--</option>
                                    <?php
                                    foreach($sales_regional_list as $sales_regional) { ?>
                                    <option value="{{$sales_regional->sales_regional_manager_id}}" <?php if($deputation_list && $deputation_list->sales_regional_manager_id==$sales_regional->sales_regional_manager_id) echo 'selected="selected"'; ?> >{{$sales_regional->sales_regional_manager_name}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="deputation_sales_regional_manager" generated="true" class="error">{{ $errors->first('deputation_sales_regional_manager') }}</label></div>
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Area Mangers</label>
                                <select id="deputation_area_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="deputation_area_manager">
                                    <option value="">--Select Area Manager--</option>
                                    <?php
                                    foreach($area_list as $area) { ?>
                                    <option value="{{$area->area_manager_id}}" <?php if($deputation_list && $deputation_list->area_manager_id==$area->area_manager_id) echo 'selected="selected"'; ?> >{{$area->area_manager_name}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="deputation_area_manager" generated="true" class="error">{{ $errors->first('deputation_area_manager') }}</label></div>
                            </div>
                            <div class="col-lg-3"> 
                                <label class="formlabel">Sales Area Mangers</label>
                                <select id="deputation_sales_area_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="deputation_sales_area_manager">
                                    <option value="">--Select Area Sales Manager--</option>
                                    <?php
                                    foreach($sales_area_list as $sales_area) { ?>
                                    <option value="{{$sales_area->sales_area_manager_id}}" <?php if($deputation_list && $deputation_list->sales_area_manager_id==$sales_area->sales_area_manager_id) echo 'selected="selected"'; ?> >{{$sales_area->sales_area_manager_name}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="deputation_sales_area_manager" generated="true" class="error">{{ $errors->first('deputation_sales_area_manager') }}</label></div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-3">
                                <div class="input-daterange input-group" id="datepicker" ><label class="formlabel">From date:</label>
                                <input type="text" id="deputation_from_date" class="frm_txt"  name="deputation_from_date" placeholder="" value="<?php if($deputation_list && $deputation_list->deputation_from_date != ''){ echo $deputation_list->deputation_from_date;}?>">
                                </div>
                                <div class="rerror"><label for="deputation_from_date" generated="true" class="error">{{ $errors->first('deputation_from_date') }}</label></div>
                            </div>
                            <div class="col-lg-3"> 
                                <div class="input-daterange input-group" id="datepicker" >
                                <label class="formlabel">To date:</label>
                                <input type="text" id="deputation_to_date" class="frm_txt"  name="deputation_to_date" placeholder="" value="<?php if($deputation_list && $deputation_list->deputation_to_date != ''){ echo $deputation_list->deputation_to_date;}?>">
                                </div>
                                <div class="rerror"><label for="deputation_to_date" generated="true" class="error">{{ $errors->first('deputation_to_date') }}</label></div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">Change Password</label> 
                                <input class="frm_chk" id="change_password" type="checkbox" name="change_password" > 
                                <div class="rerror"></div>
                            </div>
                                                      
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <input type="hidden" name="_bdo" id="_bdo" value="{{ $userdata->id }}" /> 
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Edit BDO" >
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

<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.common-material.min.css" />
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.min.css" />
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.mobile.min.css" />
<script src="https://kendo.cdn.telerik.com/2018.1.221/js/kendo.all.min.js"></script>

<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
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
                                var jsonData1   = JSON.parse(data);
                                if(typeof jsonData1.regional!='undefined') {
                                    var appenddata1 = '';
                                    appenddata1 += "<option value = ''>--Select Regional Manger--</option>";
                                    for (var i = 0; i < jsonData1.regional.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1.regional[i].regional_manager_id + "'>" + jsonData1.regional[i].regional_manager_name + " </option>";
                                    }
                                    $("#regional_manager").append(appenddata1);
                                }
                                else {
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
            url:        "{{url('area/area-sales')}}",
            data:       data,
            success:    function (data) {
                                $("#area_manager").empty();
                                $("#sales_area_manager").empty();
                                var jsonData1   = JSON.parse(data);
                                if(jsonData1.area!='undefined') {
                                    var appenddata1 = '';
                                    appenddata1 += "<option value = ''>--Select Regional Manger--</option>";
                                    for (var i = 0; i < jsonData1.area.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1.area[i].area_manager_id + "'>" + jsonData1.area[i].area_manager_name + " </option>";
                                    }
                                    $("#area_manager").append(appenddata1);
                                }
                                if(jsonData1.sales!='undefined') {
                                    var appenddata2 = '';
                                    appenddata2 += "<option value = ''>--Select Sales Regional Manger--</option>";
                                    for (var i = 0; i < jsonData1.sales.length; i++) {
                                        appenddata2 += "<option value = '" + jsonData1.sales[i].sales_area_manager_id + "'>" + jsonData1.sales[i].sales_area_manager_name + " </option>";
                                    }
                                    $("#sales_area_manager").append(appenddata2);
                                }
                                return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
        
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
    
    
    
    $('#on_deputation').on('click',function(){
        if($('#on_deputation').prop("checked") == false) {
            $('#on_deputation').removeAttr('checked');
        }
        else if($('#on_deputation').prop("checked") == true) {
            $('#on_deputation').attr('checked', 'checked');
        }
        else {
            $('#on_deputation').removeAttr('checked');
        }
    });
    
    $('#deputation_zonal_manager').on('change',function(){

        var zonal_manager   = $('#deputation_zonal_manager').val();
        <?php if(isset($deputation_list->regional_manager_id) && $deputation_list->regional_manager_id != '') {  ?>

        var temp_regional_manager_id = <?php echo $deputation_list->regional_manager_id ?>;
       <?php } else  { ?>
           var temp_regional_manager_id = ''; 
       <?php } ?>

       <?php if(isset($deputation_list->sales_regional_manager_id) && $deputation_list->sales_regional_manager_id != '') {  ?>
            
        var temp_sales_manager_id = <?php echo $deputation_list->sales_regional_manager_id ?>;
       <?php } else  { ?>
           var temp_sales_manager_id = ''; 
       <?php } ?>
       

        


        var data = {
            _token:         $(this).data('token'),
            zonal_manager:  zonal_manager,
            regional_manager : '<?php echo isset($deputation_list->regional_manager_id) ? $deputation_list->regional_manager_id  : ""?>'
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('area/regional-sales')}}",
            data:       data,
            success:    function (data) {
                            if(data) {
                                $("#deputation_regional_manager").empty();
                                $("#deputation_sales_regional_manager").empty();
                                $("#deputation_area_manager").empty();
                                $("#deputation_sales_area_manager").empty();
                                var jsonData1   = JSON.parse(data);

                                if(typeof jsonData1.regional!='undefined') {
                                    var appenddata1 = '';

                                    var reg_sel="";

                                    appenddata1 += "<option value = ''>--Select Regional Manger--</option>";
                                    for (var i = 0; i < jsonData1.regional.length; i++) {
                                        

                                        if(jsonData1.regional[i].regional_manager_id==temp_regional_manager_id){

                                            reg_sel='selected="selected"';
                                        }
                                        appenddata1 += "<option  "+reg_sel+" value = '" + jsonData1.regional[i].regional_manager_id + "'>" + jsonData1.regional[i].regional_manager_name + " </option>";
                                    }
                                    $("#deputation_regional_manager").append(appenddata1);
                                }
                                else {
                                    var appenddata1 = "<option value = ''>--Select Regional Manager--</option>";
                                    $("#deputation_regional_manager").append(appenddata1);
                                }
                                
                                if(typeof jsonData1.sales!='undefined') {
                                    var appenddata2 = '';
                                  
                                     var sal_reg_sel="";


                                    appenddata2 += "<option value = ''>--Select Sales Regional Manger--</option>";


                                    for (var i = 0; i < jsonData1.sales.length; i++) {

                                        if(jsonData1.sales[i].sales_regional_manager_id==temp_sales_manager_id){

                                            sal_reg_sel='selected="selected"';
                                        }

                                        appenddata2 += "<option "+sal_reg_sel+" value = '" + jsonData1.sales[i].sales_regional_manager_id + "'>" + jsonData1.sales[i].sales_regional_manager_name + " </option>";
                                    }
                                    $("#deputation_sales_regional_manager").append(appenddata2);
                                }
                                else {
                                    var appenddata1 = "<option value = ''>--Select Regional Sales Manager--</option>";
                                    $("#deputation_sales_regional_manager").append(appenddata1);
                                }
                                var appenddata3 = "<option value = ''>--Select Area Manager--</option>";
                                $("#deputation_area_manager").append(appenddata3);
                                var appenddata4 = "<option value = ''>--Select Area Sales Manager--</option>";
                                $("#deputation_sales_area_manager").append(appenddata4);
                                return false;
                            }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
    
    $('#deputation_regional_manager').on('change',function(){
        var regional_manager   = $('#deputation_regional_manager').val();
        var data = {
            _token:             $(this).data('token'),
            regional_manager:   regional_manager
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('area/area-sales')}}",
            data:       data,
            success:    function (data) {
                                $("#deputation_area_manager").empty();
                                $("#deputation_sales_area_manager").empty();
                                var jsonData1   = JSON.parse(data);

                                console.log(jsonData1.area);
                              
                                if(jsonData1.area!='undefined' &&  jsonData1.area!=undefined) {
                                    
                                    var appenddata1 = '';
                                    appenddata1 += "<option value = ''>--Select Regional Manger--</option>";


                                    for (var i = 0; i < jsonData1.area.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1.area[i].area_manager_id + "'>" + jsonData1.area[i].area_manager_name + " </option>";
                                    }
                                    $("#deputation_area_manager").append(appenddata1);
                                }
                                if(jsonData1.sales!='undefined' && jsonData1.sales!=undefined) {
                                    var appenddata2 = '';
                                    appenddata2 += "<option value = ''>--Select Sales Regional Manger--</option>";
                                    for (var i = 0; i < jsonData1.sales.length; i++) {
                                        appenddata2 += "<option value = '" + jsonData1.sales[i].sales_area_manager_id + "'>" + jsonData1.sales[i].sales_area_manager_name + " </option>";
                                    }
                                    $("#deputation_sales_area_manager").append(appenddata2);
                                }
                                return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
    

     //$('#deputation_zonal_manager').trigger("change");
    
    jQuery.validator.addMethod("domain", function(value, element) {
        var isSuccess   = false;
        var bdo_code    = $('#bdo_code').val();
        var _bdo        = $('#_bdo').val();
        $.ajax({ 
                url:    "{{url('admin/check-ecn')}}", 
                data:   {'bdo_code':bdo_code, '_bdo': _bdo}, 
                async:  false,
                type:   "POST",
                success: 
                        function(msg) { if(msg === "true"){
                                isSuccess = true;
                                }else if(msg === "truetrue"){
                                    isSuccess = true;
                                }
                                else{

                                    isSuccess = false;
                                } }
                });
         return isSuccess;
    }, "ENC already exists, enter a new ECN.");
    
    jQuery.validator.addMethod("deputation", function(value, element) {
        var isSuccess   = false;
        if($('#on_deputation').prop("checked") == true) {
            var deputation_zonal_manager           = $('#deputation_zonal_manager').val();
            var deputation_regional_manager        = $('#deputation_regional_manager').val();
            var deputation_sales_regional_manager  = $('#deputation_sales_regional_manager').val();
            var deputation_area_manager            = $('#deputation_area_manager').val();
            var deputation_sales_area_manager      = $('#deputation_sales_area_manager').val();  
            if( deputation_zonal_manager!=''           &&
                deputation_regional_manager!=''        &&
                deputation_sales_regional_manager!=''  &&
                deputation_area_manager!=''            &&
                deputation_sales_area_manager!='') {
                alert(deputation_zonal_manager);
                isSuccess = true;
            }
            else {
                isSuccess = false;
            }            
        }
        return isSuccess;
    }, "All deputation field are required.");
    
    $('.step2submit').keypress(function (e) {
        if (e.which == 13) {
            $("#createbdo").click();
        }
    });
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $("#createbdo").validate({
        errorPlacement: function (error, element) {
                            console.log($(element).next());
                            $(element).next('.rerror').html(error);
                            $(element).next().next('.rerror').html(error);
                        },
        ignore: "input[type='text']:hidden",
        rules: {
            zonal_manager: {
                required:   true
            },
            regional_manager: {
                required:   true,
            },
            sales_regional_manager: {
                required:   true,
            },
            area_manager: {
                required:   true,
            },
            sales_area_manager: {
                required:   true,
            },
            state: {
                required:   true,
            },
            district: {
                required:   true,
            },
            language: {
                required:   true,
            },
            department: {
                required:   true,
                minlength:  4,
                maxlength:  50
            },
            designation: {
                required:   true,
                // minlength:  4,
                // maxlength:  50
            },
            bdo_code: {
                required:   true,
                minlength:  4,
                maxlength:  10,
                //number:     true,
                domain:     true
            },
            name: {
                required:   true,
                minlength:  4,
                maxlength:  50
            },
            mobile_no: {
                required:   true,
                minlength:  10,
                maxlength:  10,
                number: true
            },
            bdo_hq: {
                required:   true,
                minlength:  4,
                maxlength:  50
            },
//            deputation_zonal_manager: {
//                deputation: true
//            },
//            deputation_regional_manager: {
//                deputation: true,
//            },
//            deputation_sales_regional_manager: {
//                deputation: true,
//            },
//            deputation_area_manager: {
//                deputation: true,
//            },
//            deputation_sales_area_manager: {
//                deputation: true,
//                required: true,
//            },
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

$("#deputation_from_date, #deputation_to_date").kendoDatePicker({
    // defines the start view
    start: "date",

    // defines when the calendar should return date
    depth: "year",

    // display month and year in the input
    format: "yyyy-MM-dd",

    // specifies that DateInput is used for masking the input element
    dateInput: false,
});
//DISABLE inputs
$("#deputation_from_date, #deputation_to_date").attr("readonly", true);
</script>

<!-- dynamic add dropdown in bdo edit -->


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
                                var jsonData1   = JSON.parse(data);
                                if(typeof jsonData1.regional!='undefined') {
                                    var appenddata1 = '';
                                    appenddata1 += "<option value = ''>--Select Regional Manger--</option>";
                                    for (var i = 0; i < jsonData1.regional.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1.regional[i].regional_manager_id + "'>" + jsonData1.regional[i].regional_manager_name + " </option>";
                                    }
                                    $("#regional_manager").append(appenddata1);
                                }
                                else {
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
            url:        "{{url('area/area-sales')}}",
            data:       data,
            success:    function (data) {
                                $("#area_manager").empty();
                                $("#sales_area_manager").empty();
                                var jsonData1   = JSON.parse(data);
                                if(jsonData1.area!='undefined') {
                                    var appenddata1 = '';
                                    appenddata1 += "<option value = ''>--Select Regional Manger--</option>";
                                    for (var i = 0; i < jsonData1.area.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1.area[i].area_manager_id + "'>" + jsonData1.area[i].area_manager_name + " </option>";
                                    }
                                    $("#area_manager").append(appenddata1);
                                }
                                if(jsonData1.sales!='undefined') {
                                    var appenddata2 = '';
                                    appenddata2 += "<option value = ''>--Select Sales Regional Manger--</option>";
                                    for (var i = 0; i < jsonData1.sales.length; i++) {
                                        appenddata2 += "<option value = '" + jsonData1.sales[i].sales_area_manager_id + "'>" + jsonData1.sales[i].sales_area_manager_name + " </option>";
                                    }
                                    $("#sales_area_manager").append(appenddata2);
                                }
                                return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
        
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

    
    $('.step2submit').keypress(function (e) {
        if (e.which == 13) {
            $("#createbdo").click();
        }
    });
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
  
});
$(document).ready(function () {
    $('#designation').on('change', function () {
        $('.counterpart_fl').css('display','none');
        $("#counterpart_fl").prop('required',false);
        var designation =  $(this).val();
       if(designation == 'BDO-FL')
       {
        $('.counterpart_fl').css('display','block');
        $("#counterpart_fl").prop('required',true);
        $.ajax({
            type:       'GET',
            url:        "{{url('admin/counterpart-fl-list')}}",
            success:    function (data) {
                var data = $.parseJSON(data);
                var newFl = '<option value="">Select COUNTERPART-FL</option>'
                $.each(data, function (key,value) {
                    newFl += '<option value="'+value.id+'">'+value.name+'</option>'
                });
                $('#counterpart_fl').html(newFl);
            }

        })


       }
    })
})
</script>





























































































































































