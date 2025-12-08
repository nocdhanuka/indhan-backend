<?php //dd($zonal_manager_list); ?>
@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')
<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Regional Manager Request Detail</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Regional Manager Request Detail</li>
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
                        //echo "<pre>"; print_r($regionalData); exit;
                        ?>
                        <form role="form" method="post" class="search-form"  id="createregionalmanager" action=""  >                             
                            <div class="col-lg-12"> 
                                <label class="formlabel">Zonal Manager</label>
                                <select id="zonal_manager" class="frm_txt mdb-select"  name="zonal_manager">
                                    <option value="{{$regionalData[0]->zonal_manager_name}}" >{{$regionalData[0]->zonal_manager_name}}</option>
                                </select>
                            </div> 
                            <div class="col-lg-12"> 
                                <label class="formlabel">HQ Code</label>
                                <input type="text" id="regional_manager_hq_code" class="frm_txt" name="regional_manager_hq_code" placeholder="HQ Code" maxlength="10" value="{{$regionalData[0]->regional_manager_hq_code}}" readonly>   
                            </div> 
                            <div class="col-lg-3"> 
                                <label class="formlabel">Name</label>
                                <input type="text" id="regional_name" class="frm_txt" name="regional_name" placeholder="Name" value="{{$regionalData[0]->regional_manager_name }}" readonly>   
                                
                            </div>
                            <div class="col-lg-3">
                                <label class="formlabel">Mobile Number</label>
                                <input type="text" id="regional_mobile_number" class="frm_txt" name="regional_mobile_number" placeholder="Mobile Number" maxlength="10" value="{{$regionalData[0]->regional_manager_mobile }}" readonly>   
                                
                            </div>                                
                            <div class="col-lg-3"> 
                                <label class="formlabel">State</label>
                                <select id="regional_state" class="frm_txt mdb-select"  name="regional_state[]" multiple="multiple">
                                    <?php 
                                    if (count($regionalData[0]->states)>0) {
                                        
                                    foreach($regionalData[0]->states as $key => $values) {?>
                                        <option selected="selected" value="{{$regionalData[0]->states[$key]->state}}">{{$regionalData[0]->states[$key]->state}}</option>
                                    <?php } } ?>
                                </select>
                            </div>                                
                            <div class="col-lg-3">
                                <label class="formlabel">Pincode</label>
                                <input type="text" id="regional_pincode" class="frm_txt"  name="regional_pincode" placeholder="Pincode" maxlength="6" value="{{$regionalData[0]->regional_manager_pincode }}" readonly>
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
                                <label class="formlabel">Change Password</label> 
                                <input class="frm_chk" id="change_password" type="checkbox" name="change_password" <?php if ($regionalData[0]->changepass == '1'){ ?>
                                    checked
                                <?php } ?> > 
                            </div>
                            <div class="col-lg-3"> 
                                <label class="formlabel">Status</label> 
                                <input class="frm_chk" readonly type="text" name="" <?php if ($regionalData[0]->status == '1'){ ?>
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











