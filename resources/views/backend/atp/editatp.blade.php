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
        <h1>Edit ATP</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif
        

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Edit ATP</li>
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
                        // echo "<pre>";
                        // print_r($planData);
                        // print_r($productList);
                        ?>
                        <form role="form" method="post" class="search-form"  id="editplanAtp" action="{{url('atp/updateatp')}}"  >    
                            <div class="col-lg-12"> 
                                <div class="col-lg-3">
                                    <label class="formlabel">Data Source</label>
                                    <select id="data_source_id" class="frm_txt mdb-select"  name="data_source_id" disabled>
                                        <option unselect value="" >Choose Data Source</option>
                                        <?php
                                        foreach($dataSourceList as $dataSourceList) 
                                        {
                                            if($dataSourceList->data_source_id == $planData->data_source_id) {
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
                                <div class="col-lg-3">
                                    <label class="formlabel">Language</label>
                                    <select id="language_id" class="frm_txt mdb-select"  name="language_id" disabled>
                                        <option unselect value="" >Choose Language</option>
                                        <?php
                                        foreach($languageList as $language) 
                                        {
                                            if($language->id == $planData->language_id) {
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
                                <div class="col-lg-3">
                                    <label class="formlabel">Crop</label>
                                    <select id="crop_id" class="frm_txt mdb-select"  name="crop_id" disabled>
                                        <option unselect value="" >Choose Crop</option>
                                        <?php
                                        foreach($cropList as $crop) 
                                        {
                                            if($crop->crop_id == $planData->crop_id) {
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
                                    <div class="rerror"><label for="crop_id" generated="true" class="error">{{ $errors->first('crop_id') }}</label></div>
                                </div> 
                            </div>
                            <div class="clearfix"></div>

                            <div class="col-lg-12"> 
                                <div class="col-lg-3"> 
                                    <label class="formlabel">Product 1</label>
                                    <select id="product_id" class="frm_txt mdb-select"  name="product_id" disabled>
                                        <option unselect value="" >Choose Product 1</option>
                                        <?php
                                        foreach($productList as $product) 
                                        {
                                            if($product->product_id == $planData->product_id) {
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
                                    <div class="rerror"><label for="product_id" generated="true" class="error">{{ $errors->first('product_id') }}</label></div>
                                </div>
                                <div class="col-lg-3"> 
                                    <label class="formlabel">Product 2</label>
                                    <select id="product_id_2" class="frm_txt mdb-select"  name="product_id_2" disabled>
                                        <option unselect value="" >Choose Product 2</option>
                                        <?php
                                        foreach($productList as $product) 
                                        {
                                            if($product->product_id == $planData->product_id_2) {
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
                                    <div class="rerror"><label for="product_id_2" generated="true" class="error">{{ $errors->first('product_id_2') }}</label></div>
                                </div>
                                <div class="col-lg-6"> 
                                    <label class="formlabel">Product 3</label>
                                    <select id="product_id_3" class="frm_txt mdb-select"  name="product_id_3" disabled>
                                        <option unselect value="" >Choose Product 2</option>
                                        <?php
                                        foreach($productList as $product) 
                                        {
                                            if($product->product_id == $planData->product_id_3) {
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
                                    <div class="rerror"><label for="product_id_3" generated="true" class="error">{{ $errors->first('product_id_3') }}</label></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="col-lg-12"> 
                                <div class="col-lg-3"> 
                                    <label class="formlabel">State</label>
                                    <select id="state_id" class="frm_txt mdb-select"  name="state_id" disabled>
                                        <option unselect value="" >Choose State</option>
                                        <?php
                                        foreach($state as $statelist) 
                                        {
                                            if($statelist->state_id == $planData->state_id) {
                                            ?>
                                                <option selected="selected" value="{{ $statelist->state_id }}">{{$statelist->state}}</option>
                                            <?php    
                                            }
                                            else 
                                            { 
                                            ?>
                                                <option value="{{ $statelist->state_id }}">{{$statelist->state}}</option>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="state_id" generated="true" class="error">{{ $errors->first('state_id') }}</label></div>
                                </div>
                                <div class="col-lg-3"> 
                                    <label class="formlabel">District</label>
                                    <select id="district_id" class="frm_txt mdb-select"  name="district_id" disabled>
                                        <option unselect value="" >Choose District</option>
                                        <?php
                                        foreach($district as $districtlist) 
                                        {
                                            if($districtlist->district_id == $planData->district_id) {
                                            ?>
                                                <option selected="selected" value="{{ $districtlist->district_id }}">{{$districtlist->district}}</option>
                                            <?php    
                                            }
                                            else 
                                            { 
                                            ?>
                                                <option value="{{ $districtlist->district_id }}">{{$districtlist->district}}</option>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="district_id" generated="true" class="error">{{ $errors->first('district_id') }}</label></div>
                                </div>
                                <div class="col-lg-3"> 
                                    <label class="formlabel">Tehsil</label>
                                    <select id="tehsil_id" class="frm_txt mdb-select"  name="tehsil_id" disabled>
                                        <option unselect value="" >Choose Tehsil</option>
                                        <?php
                                        foreach($tehsil as $tehsilList) 
                                        {
                                            if($tehsilList->tehsil_id == $planData->tehsil_id) {
                                            ?>
                                                <option selected="selected" value="{{ $tehsilList->tehsil_id }}">{{$tehsilList->tehsil}}</option>
                                            <?php    
                                            }
                                            else 
                                            { 
                                            ?>
                                                <option value="{{ $tehsilList->tehsil_id }}">{{$tehsilList->tehsil}}</option>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="tehsil_id" generated="true" class="error">{{ $errors->first('tehsil_id') }}</label></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div class="col-lg-12"> 
                                <div class="col-lg-3">
                                    <input type="hidden" name="village" value="{{ $planData->village }}">
                                    <label class="formlabel">Village</label>
                                    <select id="village_id" class="frm_txt mdb-select"  name="village_id" disabled>
                                        <option unselect value="" >Choose State</option>
                                        <?php
                                        foreach($village as $villagelist) 
                                        {
                                            if($villagelist->village_id == $planData->village_id) {
                                            ?>
                                                <option selected="selected" value="{{ $villagelist->village_id }}">{{$villagelist->village}}</option>
                                            <?php    
                                            }
                                            else 
                                            { 
                                            ?>
                                                <option value="{{ $villagelist->village_id }}">{{$villagelist->village}}</option>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="village_id" generated="true" class="error">{{ $errors->first('village_id') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Plan Date</label>
                                    <input type="datetime-local" name="plan_date" value="{{ strftime('%Y-%m-%dT%H:%M:%S', strtotime($planData->plan_date))}}" disabled>
                                    <div class="rerror"><label for="plan_date" generated="true" class="error">{{ $errors->first('plan_date') }}</label></div>
                                </div>
                                <div class="col-lg-3">&nbsp;</div>
                            </div>
                            <div class="clearfix"></div>
                            <?php
                            /*echo "<pre>";
                            print_r($parent_user);
                            exit();*/
                            // echo "=>".$planData->presenter_id."--".$planData->presenter_type;
                            ?>
                            <div class="col-lg-12"> 
                                <div class="col-lg-3">
                                    <label class="formlabel">Presenter</label>
                                    <input type="hidden" name="presenter_type" id="presenter_type" value="{{$planData->presenter_type}}">
                                    <input type="hidden" name="presenter_name" id="presenter_name" value="{{$planData->presenter_name}}">
                                    <select id="presenter_id" class="frm_txt mdb-select"  name="presenter_id">
                                        <option unselect value="" >Select Presenter</option>
                                        <?php
                                        foreach ($parent_user as $user) {
                                            if(($user->id==$planData->presenter_id) && ($user->user_type==$planData->presenter_type)) {
                                            ?>
                                                <option value="{{$user->id}}" data-type="{{$user->user_type}}" selected="selected">{{$user->name}}</option>
                                            <?php } else { ?>
                                                <option value="{{$user->id}}" data-type="{{$user->user_type}}">{{$user->name}}</option>
                                        <?php } } ?>
                                    </select>
                                    <div class="rerror"><label for="presenter_id" generated="true" class="error">{{ $errors->first('presenter_id') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Monitor</label>
                                    <input type="hidden" name="monitor_type" id="monitor_type" value="{{$planData->monitor_type}}">
                                    <input type="hidden" name="monitor_name" id="monitor_name" value="{{$planData->monitor_name}}">
                                    <select id="monitor_id" class="frm_txt mdb-select"  name="monitor_id">
                                        <option unselect value="">Select Monitor</option>
                                        <?php
                                        foreach ($parent_user as $user) {
                                            if(($user->id==$planData->monitor_id) && ($user->user_type==$planData->monitor_type)) {
                                            ?>
                                                <option value="{{$user->id}}" data-type="{{$user->user_type}}" selected="selected">{{$user->name}}</option>
                                            <?php } else { ?>
                                                <option value="{{$user->id}}" data-type="{{$user->user_type}}">{{$user->name}}</option>
                                        <?php } } ?>
                                    </select>
                                    <div class="rerror"><label for="monitor_id" generated="true" class="error">{{ $errors->first('monitor_id') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Meeting Date</label>
                                    <input type="datetime-local" name="meeting_date" value="{{ ($planData->meeting_date) ? strftime('%Y-%m-%dT%H:%M:%S', strtotime($planData->meeting_date)) : ''}}">
                                    <div class="rerror"><label for="meeting_date" generated="true" class="error">{{ $errors->first('meeting_date') }}</label></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-12">
                                <div class="col-lg-6">
                                    <label class="formlabel">Meeting Details</label>
                                    <textarea name="meeting_detail" id="meeting_detail" placeholder="Please enter the meeting detail" rows="3" cols="60">{{$planData->meeting_detail}}</textarea>
                                    <div class="rerror"><label for="meeting_detail" generated="true" class="error">{{ $errors->first('meeting_detail') }}</label></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="hidden" name="plan_atp_id" value="{{$planData->plan_atp_id}}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Edit ATP" >
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

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.10/js/bootstrap-select.js"></script>  
<script type="text/javascript">
$("#editplanAtp").validate({
    errorPlacement: function (error, element) {
        console.log($(element).next());
        $(element).next('.rerror').html(error);
        $(element).next().next('.rerror').html(error);
    },
    ignore: "input[type='text']:hidden",
    rules: {
        presenter_id: {
            required:   true,
        },
        presenter_type: {
            required:   true,
        },
        monitor_id: {
            required:   true,
        },
        monitor_type: {
            required:   true,
        },
        meeting_date: {
            required:   true,
        },
        meeting_detail: {
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
$('#presenter_id').on('change', function() {
    var presenter_type = $(this).find(':selected').data('type');
    var presenter_name = $("#presenter_id option:selected").text();
    $('#presenter_type').val(presenter_type);
    $('#presenter_name').val(presenter_name);
});
$('#monitor_id').on('change', function() {
    var monitor_type = $(this).find(':selected').data('type');
    var monitor_name = $("#presenter_id option:selected").text();
    $('#monitor_type').val(monitor_type);
    $('#monitor_name').val(monitor_name);
});
</script>
