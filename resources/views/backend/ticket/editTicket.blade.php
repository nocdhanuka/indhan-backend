<?php //dd($zonal_manager_list); ?>
@include('include.admin.backend_header')
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
        <h1>Edit Ticket</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Edit Ticket</li>
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
                        //echo "<pre>"; 
                       //print_r($ticketData);die;
                        ?>
                        <form role="form" method="post" class="search-form"  id="editmarketingsupervisor" action="{{url('admin/update-ticket')}}"  >    
                            <div class="col-lg-12"> 
                                <label class="formlabel">Data Source</label>
                                <select id="data_source_id" class="frm_txt mdb-select"  name="data_source_id">
                                    <option unselect value="" >Choose Data Source</option>
                                    <?php
                                    foreach($dataSourceList as $dataSourceList) 
                                    {
                                        if($dataSourceList->data_source_id == $ticketData->data_source_id) {
                                        ?>
                                            <option selected="selected" value="{{ $dataSourceList->data_source_id }}">{{$dataSourceList->data_source}}</option>
                                        <?php    
                                        }
                                        else 
                                        { 
                                        ?>
                                            <option value="{{ $dataSourceList->data_source_id }}">{{$dataSourceList->data_source}}</option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="data_source_id" generated="true" class="error">{{ $errors->first('data_source_id') }}</label></div>
                            </div>  

                            <div class="col-lg-12"> 
                                <label class="formlabel">Language</label>
                                <select id="language_id" class="frm_txt mdb-select"  name="language_id">
                                    <option unselect value="" >Choose Language</option>
                                    <?php
                                    foreach($languageList as $language) 
                                    {
                                        if($language->id == $ticketData->language_id) {
                                        ?>
                                            <option selected="selected" value="{{ $language->id }}">{{$language->language}}</option>
                                        <?php    
                                        }
                                        else 
                                        { 
                                        ?>
                                            <option value="{{ $language->id }}">{{$language->language}}</option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="language_id" generated="true" class="error">{{ $errors->first('language_id') }}</label></div>
                            </div>

                            <div class="col-lg-6"> 
                                <label class="formlabel">Crop 1</label>
                                <select id="crop_1_id" class="frm_txt mdb-select"  name="crop_1_id">
                                    <option unselect value="" >Choose Crop 1</option>
                                    <?php
                                    foreach($cropList as $crop) 
                                    {
                                        if($crop->crop_id == $ticketData->crop_1_id) {
                                        ?>
                                            <option selected="selected" value="{{ $crop->crop_id }}">{{$crop->crop}}</option>
                                        <?php    
                                        }
                                        else 
                                        { 
                                        ?>
                                            <option value="{{ $crop->crop_id }}">{{$crop->crop}}</option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="crop_1_id" generated="true" class="error">{{ $errors->first('crop_1_id') }}</label></div>
                            </div>

                            <div class="col-lg-6"> 
                                <label class="formlabel">Product 1</label>
                                <select id="product_1_id" class="frm_txt mdb-select"  name="product_1_id">
                                    <option unselect value="" >Choose Product 1</option>
                                    <?php
                                    foreach($productList as $product) 
                                    {
                                        if($product->product_id == $ticketData->product_1_id) {
                                        ?>
                                            <option selected="selected" value="{{ $product->product_id }}">{{$product->product}}</option>
                                        <?php    
                                        }
                                        else 
                                        { 
                                        ?>
                                            <option value="{{ $product->product_id }}">{{$product->product}}</option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="product_1_id" generated="true" class="error">{{ $errors->first('product_1_id') }}</label></div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="col-lg-6"> 
                                <label class="formlabel">Crop 2</label>
                                <select id="crop_2_id" class="frm_txt mdb-select"  name="crop_2_id">
                                    <option unselect value="" >Choose Crop 2</option>
                                    <?php
                                    foreach($cropList as $crop) 
                                    {
                                        if($crop->crop_id == $ticketData->crop_2_id) {
                                        ?>
                                            <option selected="selected" value="{{ $crop->crop_id }}">{{$crop->crop}}</option>
                                        <?php    
                                        }
                                        else 
                                        { 
                                        ?>
                                            <option value="{{ $crop->crop_id }}">{{$crop->crop}}</option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="crop_2_id" generated="true" class="error">{{ $errors->first('crop_2_id') }}</label></div>
                            </div>

                            <div class="col-lg-6"> 
                                <label class="formlabel">Product 2</label>
                                <select id="product_2_id" class="frm_txt mdb-select"  name="product_2_id">
                                    <option unselect value="" >Choose Product 2</option>
                                    <?php
                                    foreach($productList as $product) 
                                    {
                                        if($product->product_id == $ticketData->product_2_id) {
                                        ?>
                                            <option selected="selected" value="{{ $product->product_id }}">{{$product->product}}</option>
                                        <?php    
                                        }
                                        else 
                                        { 
                                        ?>
                                            <option value="{{ $product->product_id }}">{{$product->product}}</option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="product_2_id" generated="true" class="error">{{ $errors->first('product_2_id') }}</label></div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="col-lg-6"> 
                                <label class="formlabel">Crop 3</label>
                                <select id="crop_3_id" class="frm_txt mdb-select"  name="crop_3_id">
                                    <option unselect value="" >Choose Crop 3</option>
                                    <?php
                                    foreach($cropList as $crop) 
                                    {
                                        if($crop->crop_id == $ticketData->crop_3_id) {
                                        ?>
                                            <option selected="selected" value="{{ $crop->crop_id }}">{{$crop->crop}}</option>
                                        <?php    
                                        }
                                        else 
                                        { 
                                        ?>
                                            <option value="{{ $crop->crop_id }}">{{$crop->crop}}</option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="crop_3_id" generated="true" class="error">{{ $errors->first('crop_3_id') }}</label></div>
                            </div>
                            <div class="col-lg-6"> 
                                <label class="formlabel">Product 3</label>
                                <select id="product_3_id" class="frm_txt mdb-select"  name="product_3_id">
                                    <option unselect value="" >Choose Product 2</option>
                                    <?php
                                    foreach($productList as $product) 
                                    {
                                        if($product->product_id == $ticketData->product_3_id) {
                                        ?>
                                            <option selected="selected" value="{{ $product->product_id }}">{{$product->product}}</option>
                                        <?php    
                                        }
                                        else 
                                        { 
                                        ?>
                                            <option value="{{ $product->product_id }}">{{$product->product}}</option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="product_3_id" generated="true" class="error">{{ $errors->first('product_3_id') }}</label></div>
                            </div>
                            <div class="clearfix"></div>

                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="hidden" name="ticket_id" value="{{$ticketData->ticket_id}}" />
                            <input type="hidden" name="ticket_no" value="{{$ticketData->ticket_no}}" /> 
                            <input type="hidden" name="plan_atp_id" value="{{$ticketData->plan_atp_id}}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Edit Ticket" >
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

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.10/js/bootstrap-select.js"></script>  
<script type="text/javascript">
  
</script>






