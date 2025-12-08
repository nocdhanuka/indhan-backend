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
        <h1>BDO Request Detail</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">BDO Request Detail</li>
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
                        //echo "<pre>"; print_r($regionalData);die;
                        ?>
                        <form role="form" method="post" class="search-form"  id="createbdo" action=""  >                             
                            <div class="col-lg-3"> 
                                <label class="formlabel">Zonal Mangers</label>
                                <select id="zonal_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="zonal_manager">
                                    <option value="{{$regionalData[0]->zonal_manager_name}}" >{{$regionalData[0]->zonal_manager_name}}</option>
                                </select>
                                
                            </div> 
                            
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">Regional Mangers</label>
                                <select id="regional_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="regional_manager">
                                    <option value="{{$regionalData[0]->regional_manager_name}}" >{{$regionalData[0]->regional_manager_name}}</option>
                                </select>
                               
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Sales Regional Mangers</label>
                                <select id="sales_regional_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="sales_regional_manager">
                                     <option value="{{$regionalData[0]->sales_regional_manager_name}}" >{{$regionalData[0]->sales_regional_manager_name}}</option>
                                </select>
                                
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Area Mangers</label>
                                <select id="area_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="area_manager">
                                     <option value="{{$regionalData[0]->area_manager_name}}" >{{$regionalData[0]->area_manager_name}}</option>
                                </select>
                                
                            </div>
                            <div class="col-lg-3"> 
                                <label class="formlabel">Sales Area Mangers</label>
                                <select id="sales_area_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="sales_area_manager">
                                     <option value="{{$regionalData[0]->sales_area_manager_name}}" >{{$regionalData[0]->sales_area_manager_name}}</option>
                                </select>
                                
                            </div>
                            
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                                
                            <div class="col-lg-3"> 
                                <label class="formlabel">State</label>
                                <select id="state" class="frm_txt"  data-token="{{csrf_token()}}"  name="state">
                                    <option value="{{$regionalData[0]->state}}" >{{$regionalData[0]->state}}</option>
                                </select>
                                
                            </div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">District</label>
                                <select id="district" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="district">
                                    <option value="{{$regionalData[0]->district}}" >{{$regionalData[0]->district}}</option>
                                </select>
                                
                            </div> 

                            <div class="col-lg-3">
                                <label class="formlabel">Language</label>
                                <select id="language" class="frm_txt"  name="language">
                                    <option value="{{$regionalData[0]->language}}" >{{$regionalData[0]->language}}</option>
                                </select>
                                
                            </div>
                            
                            <div class="col-lg-3">  
                                <label class="formlabel">Department</label>
                                <input type="text" id="department" class="frm_txt"  name="department" placeholder="Department" value="{{$regionalData[0]->department}}" readonly>
                                
                            </div>
                            
                            <div class="clearfix"></div>


                            <div class="col-lg-3">  
                                <label class="formlabel">Designation</label>
                                <select id="designation" class="frm_txt"  name="designation">
                                    <option value="{{$regionalData[0]->designation}}" >{{$regionalData[0]->designation}}</option>
                                </select>
                                
                            </div>
                            
                            
                            <div class="col-lg-3">
                                <label class="formlabel">HQ District Code No./ BDO Code</label>
                                <input type="text" id="bdo_code" class="frm_txt"  name="bdo_code" placeholder="BDO Code" value="{{$regionalData[0]->bdo_code}}" readonly>
                                
                            </div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel"> BDO Name</label>
                                <input type="text" id="name" class="frm_txt"  name="name" placeholder="Name" value="{{$regionalData[0]->name}}" readonly>
                                
                            </div>
                             <div class="clearfix"></div>
                            <div class="col-lg-4"> 
                                <label class="formlabel">Mobile No.</label>
                                <input type="text" id="mobile_no" class="frm_txt"  name="mobile_no" placeholder="Mobile No." value="{{$regionalData[0]->bdo_mobile_no}}" readonly>
                                
                            </div>    
                                                        
                            <div class="col-lg-4">  
                                <label class="formlabel">BDO HQ</label>
                                <input type="text" id="bdo_hq" class="frm_txt"  name="bdo_hq" placeholder="BDO HQ" value="{{$regionalData[0]->bdo_hq}}" readonly>
                                
                            </div>
                                                      
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">App Permission To Edit Ticket</label>	
                                <input class="frm_chk" id="app_permission_edit_ticket" type="checkbox" readonly name="app_permission_edit_ticket" <?php if($regionalData[0]->app_permission_edit_ticket=='1') echo 'checked="checked"';?>> 
                                
                            </div>
                            <div class="col-lg-3"> 
                                <label class="formlabel">App Permission To Close Ticket</label>	
                                <input class="frm_chk" id="app_permission_close_ticket" type="checkbox" readonly name="app_permission_close_ticket" <?php if($regionalData[0]->app_permission_close_ticket=='1') echo 'checked="checked"';?>> 
                                
                            </div>
                                
                            <div class="clearfix"></div>

                            <div class="col-lg-3"> 
                                <label class="formlabel">Crop Product Category</label>
                                <select id="product_category" class="frm_txt mdb-select"  name="product_category[]" multiple="multiple">
                                    <?php
                                    if (count($regionalData[0]->products)>0) {
                                    foreach($regionalData[0]->products as $key => $values) { ?>
                                        <option selected="selected" value="{{$regionalData[0]->products[$key]->category_name}}">{{$regionalData[0]->products[$key]->category_name}}</option>
                                    <?php
                                    }
                                    }
                                    ?>
                                </select>
                                
                            </div>
                            
                                                      
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">On Deputation</label>	
                                <input class="frm_chk" id="on_deputation" type="checkbox"  name="on_deputation" <?php if(count($regionalData[0]->Deputation)>0){ echo 'checked="checked"';} ?> > 
                                <div class="rerror"></div>
                            </div>
                                                      
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">Zonal Mangers</label>
                                <select id="deputation_zonal_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="deputation_zonal_manager">
                                    <option value="<?php if(count($regionalData[0]->Deputation)>0){ echo $regionalData[0]->Deputation->zonal_manager_name;} ?>" ><?php if(count($regionalData[0]->Deputation)>0){ echo $regionalData[0]->Deputation->zonal_manager_name;} ?></option>
                                </select>
                                
                            </div> 
                            
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">Regional Mangers</label>
                                <select id="deputation_regional_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="deputation_regional_manager">
                                    <option value="<?php if(count($regionalData[0]->Deputation)>0){ echo $regionalData[0]->Deputation->regional_manager_name;} ?>"  ><?php if(count($regionalData[0]->Deputation)>0){ echo $regionalData[0]->Deputation->regional_manager_name;} ?></option>
                                </select>
                                
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Sales Regional Mangers</label>
                                <select id="deputation_sales_regional_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="deputation_sales_regional_manager">
                                    <option value="<?php if(count($regionalData[0]->Deputation)>0){ echo $regionalData[0]->Deputation->sales_regional_manager_name;} ?>" ><?php if(count($regionalData[0]->Deputation)>0){ echo $regionalData[0]->Deputation->sales_regional_manager_name;} ?></option>
                                </select>
                               
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Area Mangers</label>
                                <select id="deputation_area_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="deputation_area_manager">
                                   <option value="<?php if(count($regionalData[0]->Deputation)>0){ echo $regionalData[0]->Deputation->area_manager_name;} ?>" ><?php if(count($regionalData[0]->Deputation)>0){ echo $regionalData[0]->Deputation->area_manager_name;} ?></option>
                                </select>
                               
                            </div>
                            <div class="col-lg-3"> 
                                <label class="formlabel">Sales Area Mangers</label>
                                <select id="deputation_sales_area_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="deputation_sales_area_manager">
                                   <option value="<?php if(count($regionalData[0]->Deputation)>0){ echo $regionalData[0]->Deputation->sales_area_manager_name;} ?>" ><?php if(count($regionalData[0]->Deputation)>0){ echo $regionalData[0]->Deputation->sales_area_manager_name;} ?></option>
                                </select>
                                
                            </div>
                            
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">Change Password</label> 
                                <input class="frm_chk" id="change_password" type="checkbox" name="change_password" <?php if ($regionalData[0]->changepass == '1'){ ?>
                                    checked
                                <?php } ?>> 
                                
                            </div>
                            <div class="col-lg-3"> 
                                <label class="formlabel">Status</label> 
                                <input class="frm_chk" readonly id="" type="text" name="" <?php if ($regionalData[0]->status == '1'){ ?>
                                    value="active"
                                <?php }else{ ?>

                                    value="inactive"<?php }?>
                                    > 
                                
                            </div>
                                                      
                            <div class="clearfix"></div>
                            <div class="col-lg-12"><hr></div>
                            <div class="clearfix"></div>
                            
                            
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
































































































































































