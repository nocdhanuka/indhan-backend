@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Edit Distributor/Advisor</h1>
            @if(Session::has('message'))
            <div class="col-md-12" style="margin-top:20px;">
                <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            </div>
            @endif
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Edit Distributor/Advisor</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row"> 
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            @if(Session::has('message'))
                                <div class="col-md-12" style="margin-top:20px;">
                                    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                                </div>
                             @endif
                            <form role="form" method="post" class="search-form"  id="updatedistributoradvisor" action="{{url('distributoradvisor/update-distributor-advisor')}}"  >
                                <div class="col-lg-3"> 
                                    <label class="formlabel">State </label>
                                    <select id="state_id" class="frm_txt"  data-token="{{csrf_token()}}"  name="state_id">
                                        <option value="">Select State</option>
                                        <?php
                                        foreach($state as $stat) {
                                            if($stat->state_id==$detail->state_id) { ?>
                                                <option selected="selected" value="{{$stat->state_id}}">{{$stat->state}}</option>
                                            <?php } else { ?>
                                                <option value="{{$stat->state_id}}">{{$stat->state}}</option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="state_id" generated="true" class="error">{{ $errors->first('state_id') }}</label></div>
                                </div>
                                <div class="col-lg-3"> 
                                    <label class="formlabel">District</label>
                                    <select id="district_id" class="frm_txt"  data-token="{{csrf_token()}}"  name="district_id">
                                        <option value="">Select District</option>
                                        <?php
                                        foreach($district as $dist) {
                                            if($dist->district_id==$detail->district_id) { ?>
                                                <option selected="selected" value="{{$dist->district_id}}">{{$dist->district}}</option>
                                            <?php } else { ?>
                                                <option value="{{$dist->district_id}}">{{$dist->district}}</option>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="district_id" generated="true" class="error">{{ $errors->first('district_id') }}</label></div>
                                </div>
                                <div class="col-lg-3"> 
                                    <label class="formlabel">Territory</label>
                                    <select id="territory_id" class="frm_txt"  data-token="{{csrf_token()}}"  name="territory_id">
                                        <option value="">--Select Territory--</option>
                                        <?php
                                        foreach($territory as $terr) {
                                            if($terr->territory_id==$detail->territory_id) {
                                            ?>
                                                <option selected="selected" value="{{$terr->territory_id}}">{{$terr->territory}}</option>
                                            <?php } else { ?>
                                                <option value="{{$terr->territory_id}}">{{$terr->territory}}</option>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="territory_id" generated="true" class="error">{{ $errors->first('territory_id') }}</label></div>
                                </div>
                                <div class="col-lg-3"> 
                                    <label class="formlabel">Market</label>
                                    <select id="market_id" class="frm_txt" data-token="{{csrf_token()}}" name="market_id">
                                        <option value="">--Select Market--</option>
                                        <?php
                                        foreach($market as $mark) {
                                            if($mark->market_id==$detail->market_id) { ?>
                                                <option selected="selected" value="{{$mark->market_id}}">{{$mark->market}}</option>
                                            <?php } else { ?>
                                                <option value="{{$mark->market_id}}">{{$mark->market}}</option>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="market_id" generated="true" class="error">{{ $errors->first('market_id') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Distributor Name</label>
                                    <input type="text" id="distributor_name" class="frm_txt"  name="distributor_name" placeholder="Distributor name" value="{{$detail->distributor_name}}">
                                    <div class="rerror">
                                        <label for="distributor_name" generated="true" class="error">{{ $errors->first('distributor_name') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">  
                                    <label class="formlabel">Distributor Firm Name</label>
                                    <input type="text" id="distributor_firm_name" class="frm_txt" name="distributor_firm_name" placeholder="Distributor firm name" value="{{$detail->distributor_firm_name}}">   
                                    <div class="rerror">
                                        <label for="distributor_firm_name" generated="true" class="error">{{ $errors->first('distributor_firm_name') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Mobile Numbmer</label>
                                    <input type="text" id="mobile" class="frm_txt" name="mobile" placeholder="Mobile Number" value="{{ $detail->mobile }}" />
                                    <div class="rerror">
                                        <label for="mobile" generated="true" class="error">{{ $errors->first('mobile') }}</label>
                                    </div>
                                </div>
    			                <div class="col-lg-3">
                                    <label class="formlabel">Alternate Mobile Number</label>
                                    <input type="text" id="mobile2" class="frm_txt" name="mobile2" placeholder="Mobile Number" maxlength="10" value="{{ $detail->mobile2 }}">
                                    <div class="rerror">
                                        <label for="mobile2" generated="true" class="error">{{ $errors->first('mobile2') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Address</label>
                                    <textarea  id="address" class="form-control frm_txt" name="address" placeholder="Address">{{ $detail->address }}</textarea>
                                    <div class="rerror">
                                        <label for="address" generated="true" class="error">{{ $errors->first('address') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">  
                                    <label class="formlabel">Pincode</label>
                                    <input type="text" id="pincode" class="frm_txt" name="pincode" placeholder="Pincode" maxlength="10" value="{{ $detail->pincode }}" />
                                    <div class="rerror">
                                        <label for="pincode" generated="true" class="error">{{ $errors->first('pincode') }}</label>
                                    </div>
                                </div>                                    
                                <div class="col-lg-3">  
                                    <label class="formlabel">Turn over (inr)</label>
                                    <input type="text" id="turnover" class="frm_txt" name="turnover" placeholder="Turn over" maxlength="10" value="{{$detail->turnover}}" />
                                    <div class="rerror">
                                        <label for="turnover" generated="true" class="error">{{ $errors->first('turnover') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3"> 
                                    <label class="formlabel">Working Brands</label>
                                    <select id="brands" class="frm_txt"  data-token="{{csrf_token()}}"  name="brands[]" multiple="multiple" >
                                        <option value="">Select Brands</option>
                                        <?php
                                        $expbrands = explode(",", $detail->brands);
                                        foreach($brands as $brand){
                                            if (($expbrands) && (in_array($brand->c_id,$expbrands))) {
                                            ?>
                                                <option selected="selected" value="{{$brand->c_id}}">{{$brand->name}}</option>
                                            <?php } else { ?>
                                                <option value="{{$brand->c_id}}">{{$brand->name}}</option>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror">
                                        <label for="brands" generated="true" class="error">{{ $errors->first('workingbrands') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Crop Working with</label>
                                    <select id="crops" class="frm_txt"  data-token="{{csrf_token()}}"  name="crops[]" multiple="multiple" >
                                        <option value="">Select Crop</option>
                                        <?php
                                        $expcrops = explode(",", $detail->crope);
                                        foreach($crops as $crope) {
                                            if (($expcrops) && (in_array($crope->crop_id,$expcrops))) {
                                            ?>
                                                <option selected="selected" value="{{$crope->crop_id}}">{{$crope->crop}}</option>
                                            <?php } else { ?>
                                                <option value="{{$crope->crop_id}}">{{$crope->crop}}</option>
                                            <?php
                                            }
                                        }
                                        ?>
                                       </select>
                                    <div class="rerror">
                                        <label for="crops" generated="true" class="error">{{ $errors->first('crops') }}</label>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <input type="hidden" name="distributor_advisor_id" id="distributor_advisor_id" value="{{ $detail->distributor_advisor_id }}" />
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Update Distributor" >
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
<!-- Bootstrap 3.3.5 -->
<script src="{{asset('assets/admin/bootstrap/js/bootstrap.min.js')}}"></script>
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

<!-- SlimScroll -->
<script src="{{asset('assets/admin/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('assets/admin/dist/js/app.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->

<!--Datepicker-->
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.common-material.min.css" />
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.min.css" />
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.mobile.min.css" />
<script src="https://kendo.cdn.telerik.com/2018.1.221/js/kendo.all.min.js"></script>

<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
<script>    
$( document ).on('ready',function() {
    $('#state_id').on('change',function(){
        $("#district_id").empty();
        $("#district_id").append("<option value = ''>--Select District--</option>");
        var state_name   = $('#state_id').val();
        var data = {
            _token:     $(this).data('token'),
            state_name: state_name
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('territory/territory-district')}}",
            data:       data,
            success:    function (data) {
                            var jsonData1   = JSON.parse(data);
                            var appenddata1 = '';
                            $("#district_id").empty();
                            appenddata1 += "<option value = ''>--Select District--</option>";
                            for (var i = 0; i < jsonData1.length; i++) {
                                appenddata1 += "<option value = '" + jsonData1[i].dId + "'>" + jsonData1[i].dName + " </option>";
                            }
                            $("#district_id").append(appenddata1);
                            return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });

    //district
    $('#district_id').on('change',function(){
        $("#territory_id").empty();
        $("#territory_id").append("<option value = ''>--Choose Territory--</option>");
        var district_name   = $('#district_id').val();
        var data = {
            _token:     $(this).data('token'),
            district_name: district_name
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('market/market-district')}}",
            data:       data,
            success:    function (data) {
                            var jsonData1   = JSON.parse(data);
                            var appenddata1 = '';
                            $("#territory_id").empty();
                            appenddata1 += "<option value = ''>--Choose Territory--</option>";
                            for (var i = 0; i < jsonData1.length; i++) {
                                appenddata1 += "<option value = '" + jsonData1[i].tId + "'>" + jsonData1[i].tName + " </option>";
                            }
                            $("#territory_id").append(appenddata1);
                            return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
    //market
    $('#territory_id').on('change',function(){
        $("#market_id").empty();
        $("#market_id").append("<option value = ''>--Choose market--</option>");
        var territory_name   = $('#territory_id').val();
        var data = {
            _token:     $(this).data('token'),
            territory_name: territory_name
        }
        //  console.log(data);
        $.ajax({
            type:       'POST',
            url:        "{{url('market/market-territory')}}",
            data:       data,
            success:    function (data) {
                            var jsonData1   = JSON.parse(data);
                            var appenddata1 = '';
                            $("#market_id").empty();
                            appenddata1 += "<option value = ''>--Choose market--</option>";
                            for (var i = 0; i < jsonData1.length; i++) {
                                appenddata1 += "<option value = '" + jsonData1[i].mId + "'>" + jsonData1[i].mName + " </option>";
                            }
                            $("#market_id").append(appenddata1);
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

$("#updatedistributoradvisor").validate({
    errorPlacement: function (error, element) {
                        console.log($(element).next());
                        $(element).next('.rerror').html(error);
                        $(element).next().next('.rerror').html(error);
                    },
    ignore: "input[type='text']:hidden",
    rules: {
        state_id: {
            required:   true,
        },
        district_id: {
            required:   true,
        },
        territory_id: {
            required:   true,
        },
        market_id: {
            required:   true,
        },
        distributor_name: {
            required:   true,
        },
        distributor_firm_name: {
            required:   true,
        },
        mobile: {
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

$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});
</script>
