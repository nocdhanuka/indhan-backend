@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Edit Distributor</h1>
            
            @if(Session::has('message'))
            <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            </div>
            @endif

            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Add Distributor</li>
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

                            <form role="form" method="post" class="search-form"  id="updatedistributer" action="{{url('distributer/update-distributer')}}"  >
                            <div class="col-lg-3"> 
                                <label class="formlabel">State </label>
                                <select id="state_name" class="frm_txt"  data-token="{{csrf_token()}}"  name="state_name">
                                    <option value="">Select State</option>
                                    <?php
                                    foreach($state_detail as $state) {
                                        if($state->state_id==$user_detail->state_id) { ?>
                                            <option selected="selected" value="{{$state->state_id}}">{{$state->state}}</option>
                                        <?php } else { ?>
                                            <option value="{{$state->state_id}}">{{$state->state}}</option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="state_name" generated="true" class="error">{{ $errors->first('state_name') }}</label></div>
                            </div>
                            
                            <div class="col-lg-3"> 
                                <label class="formlabel">District</label>
                                <select id="district_name" class="frm_txt"  data-token="{{csrf_token()}}"  name="district_name">
                                    <option value="">Select District</option>
                                    <?php
                                    foreach($all_districts as $district) {
                                        if($district->district_id==$user_detail->district_id) { ?>
                                            <option selected="selected" value="{{$district->district_id}}">{{$district->district}}</option>
                                        <?php } else { ?>
                                            <option value="{{$district->district_id}}">{{$district->district}}</option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="district_name" generated="true" class="error">{{ $errors->first('district_name') }}</label></div>
                            </div>
                            <div class="col-lg-3"> 
                                <label class="formlabel">Territory</label>
                                <select id="territory_name" class="frm_txt"  data-token="{{csrf_token()}}"  name="territory_name">
                                    <?php if (!empty($territory_detail->territory_id)) { ?>
                                        <option value="{{$territory_detail->territory_id}}">{{$territory_detail->territory}} </option>
                                    <?php } else { ?>
                                        <option value="">--Choose Territory--</option>
                                    <?php } ?>
                                    
                                    
                                </select>
                                <div class="rerror"><label for="territory_name" generated="true" class="error">{{ $errors->first('territory_name') }}</label></div>
                            </div>

                            <div class="col-lg-3"> 
                                <label class="formlabel">Market</label>
                                <select id="market_name" class="frm_txt"  data-token="{{csrf_token()}}"  name="market_name">
                                    <?php if (!empty($market_detail->market_id)) { ?>
                                        <option value="{{$market_detail->market_id}}">{{$market_detail->market}} </option>
                                    <?php } else { ?>
                                        <option value="">--Choose Market--</option>
                                    <?php } ?>
                                </select>
                                <div class="rerror"><label for="market_name" generated="true" class="error">{{ $errors->first('market_name') }}</label></div>
                            </div>

                            <div class="col-lg-3">  
                                    <label class="formlabel">Distributor Firm Name</label>
                                    <input type="text" id="distributerforname" class="frm_txt" name="Distributerfor name" placeholder="Distributor firm name" maxlength="10" value="{{$user_detail->distributerforname}}">   
                                    <div class="rerror"><label for="distributerfirm name" generated="true" class="error">{{ $errors->first('distributerforname') }}</label></div>
                                </div>
                                                        
                                <div class="col-lg-3">
                                    <label class="formlabel">Distributor Name</label>
                                    <input type="text" id="distributername" class="frm_txt"  name="distributername" placeholder="Distributor name" maxlength="50" value="{{$user_detail->distributer_name}}">
                                    @if ($errors->has('your_custom_error'))
                                        <div class="error">{{ $errors->first('your_custom_error') }}</div>
                                    @endif
                                    <div class="rerror"><label for="distributername" generated="true" class="error">{{ $errors->first('distributername') }}</label></div>
                                </div>
				
                                <div class="col-lg-3"> 
                                    <label class="formlabel">Mobile Numbmer 1</label>
                                    <input type="text" id="mobilenumber" class="frm_txt" name="mobilenumbmer" placeholder="Mobile Number" value="{{ $user_detail->mobilenumber }}">   
                                    <div class="rerror"><label for="mobilenumber" generated="true" class="error">{{ $errors->first('mobilenumber') }}</label></div>
                                </div>
				                <div class="col-lg-3">  
                                    <label class="formlabel">Alternate Mobile Number 2</label>
                                    <input type="text" id="mobilenumber" class="frm_txt" name="mobilenumber" placeholder="Mobile Number" maxlength="10" value="{{ $user_detail->mobile2 }}">   
                                    <div class="rerror"><label for="zonalmobilenumber" generated="true" class="error">{{ $errors->first('mobilenumber') }}</label></div>
                                </div>     
                                <div class="col-lg-3">
                                    <label class="formlabel">Address</label>
                                    <textarea  id="address" class="form-control frm_txt" name="address" placeholder="Address">{{ $user_detail->Adress }}</textarea>
                                    <div class="rerror"><label for="address" generated="true" class="error">{{ $errors->first('address') }}</label></div>
                                </div> 
                                    <div class="col-lg-3">  
                                        <label class="formlabel">Pincode</label>
                                        <input type="text" id="pincode" class="frm_txt" name="pincode" placeholder="Pincode" maxlength="10" value="{{ $user_detail->pincode }}">   
                                        <div class="rerror"><label for="pincode" generated="true" class="error">{{ $errors->first('pincode') }}</label></div>
                                    </div>
                                    <!-- <div class="col-lg-3">  
                                        <label class="formlabel">ECN Number</label>
                                        <input type="text" id="ecnnumber" class="frm_txt" name="ecnnumber" placeholder="ENC Number" maxlength="10" value="{{$user_detail->ecnnumber}}">   
                                        <div class="rerror"><label for="ecnnumber" generated="true" class="error">{{ $errors->first('ecnnumber') }}</label></div>
                                    </div> -->
                                    
                                    <div class="col-lg-3">  
                                        <label class="formlabel">Turn over (inr)</label>
                                        <input type="text" id="turnover" class="frm_txt" name="turnover" placeholder="Turn over" maxlength="10" value="{{$user_detail->turnover}}">   
                                        <div class="rerror"><label for="turnover" generated="true" class="error">{{ $errors->first('turnover') }}</label></div>
                                    </div>

                                    <div class="col-lg-3">  
                                        <label class="formlabel">Pesticide Turnover (inr)</label>
                                        <input type="text" id="pesticide_turnover" class="frm_txt" name="pesticide_turnover" placeholder="Turn over" maxlength="10" value="{{$user_detail->pesticide_turnover}}">   
                                        <div class="rerror"><label for="pesticide_turnover" generated="true" class="error">{{ $errors->first('pesticide_turnover') }}</label></div>
                                    </div>
                                    
                                    <div class="col-lg-3"> 
                                    <label class="formlabel">Working Brands</label>
                                    <select id="brand" class="frm_txt"  data-token="{{csrf_token()}}"  name="brand[]" multiple="multiple" >
                                        <option value="">Select Working Brands</option>
                                        <?php
                                        foreach($brands as $brand){ 
                                            $userbrand_id   = json_decode($user_brand->brands);
                                            if (($userbrand_id) && (in_array($brand->c_id,$userbrand_id))) {
                                                ?>
                                                <option selected="selected" value="{{$brand->c_id}}">{{$brand->name}}</option>
                                            <?php    
                                            }
                                            else { ?>
                                                <option value="{{$brand->c_id}}">{{$brand->name}}</option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="workingbrands" generated="true" class="error">{{ $errors->first('workingbrands') }}</label></div>
                                </div>   
                                <div class="col-lg-3"> 
                                    <label class="formlabel">Crop Working with</label>
                                    <select id="crope" class="frm_txt"  data-token="{{csrf_token()}}"  name="crope[]" multiple="multiple" >
                                        <option value="">Select Crop working with</option>
                                        <?php
                                        foreach($crops as $crope){ 
                                            $usercrop_id   = json_decode($user_crop->crope);
                                            if(($usercrop_id) && (in_array($crope->crop_id,$usercrop_id))) {
                                                ?>
                                                <option selected="selected" value="{{$crope->crop_id}}">{{$crope->crop}}</option>
                                            <?php    
                                            }
                                            else { ?>
                                                <option value="{{$crope->crop_id}}">{{$crope->crop}}</option>
                                                <?php
                                            }
                                        }
                                        ?>
                                       </select>
                                    <div class="rerror"><label for="workingbrands" generated="true" class="error">{{ $errors->first('workingbrands') }}</label></div>
                                </div>
                                <div class="col-lg-3">  
                                    <label class="formlabel">Counter Percentage Pesticides</label>
                                    <input type="text" id="counter_percentage_pesticides" class="frm_txt" name="counter_percentage_pesticides" placeholder="Counter Percentage Pesticides" value="{{$user_detail->counter_percentage_pesticides}}">   
                                    <div class="rerror"><label for="counter_percentage_pesticides" generated="true" class="error">{{ $errors->first('counter_percentage_pesticides') }}</label></div>
                                </div>
                                                                                           
                                <div class="clearfix"></div>  
                                <input type="hidden" name="id" id="id" value="{{ $user_detail->id }}" />                              
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
    drawTable();
    $('#state_name').on('change',function(){
        var state_name   = $('#state_name').val();
        // console.log(state_name);die;
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
                                $("#district_name").empty();
                                // consol.log(#)
                                appenddata1 += "<option value = ''>--Select District--</option>";
                                for (var i = 0; i < jsonData1.length; i++) {
                                    appenddata1 += "<option value = '" + jsonData1[i].dId + "'>" + jsonData1[i].dName + " </option>";
                                }
                                $("#district_name").append(appenddata1);
                                drawTable(state_name);
                                return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });

    //district
    $('#district_name').on('change',function(){
        var district_name   = $('#district_name').val();
        // console.log(district_name);
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
                                $("#territory_name").empty();
                                appenddata1 += "<option value = ''>--Choose Territory--</option>";
                                for (var i = 0; i < jsonData1.length; i++) {
                                    appenddata1 += "<option value = '" + jsonData1[i].tId + "'>" + jsonData1[i].tName + " </option>";
                                }
                                $("#territory_name").append(appenddata1);
                                drawTable(state_name);
                                return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
//market
$('#territory_name').on('change',function(){
        var territory_name   = $('#territory_name').val();
    //  console.log(territory_name);
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
                                $("#market_name").empty();
                                appenddata1 += "<option value = ''>--Choose market--</option>";
                                for (var i = 0; i < jsonData1.length; i++) {
                                    appenddata1 += "<option value = '" + jsonData1[i].mId + "'>" + jsonData1[i].mName + " </option>";
                                }
                                $("#market_name").append(appenddata1);
                                drawTable(state_name);
                                return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });


});

// Export All Data In Excel
var oldExportAction = function (self, e, dt, button, config) {
    if (button[0].className.indexOf('buttons-excel') >= 0) {
        if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
            $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
        }
        else {
            $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
        }
    } else if (button[0].className.indexOf('buttons-print') >= 0) {
        $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
    }
};

var newExportAction = function (e, dt, button, config) {
    var self        = this;
    var oldStart    = dt.settings()[0]._iDisplayStart;    
    dt.one('preXhr', function (e, s, data) {
        // Just this once, load all data from the server...
        data.start  = 0;
        data.length = 2147483647;        
        dt.one('preDraw', function (e, settings) {
            // Call the original action function 
            oldExportAction(self, e, dt, button, config);            
            dt.one('preXhr', function (e, s, data) {
                // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                // Set the property to what it was before exporting.
                settings._iDisplayStart = oldStart;
                data.start = oldStart;
            });            
            // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
            setTimeout(dt.ajax.reload, 0);            
            // Prevent rendering of the full data to the DOM
            return false;
        });
    });    
    // Requery the server with the new one-time export settings
    dt.ajax.reload();
};
// Export All Data In Excel

function drawTable(state_name = $('#state_name').val()) {
$.fn.dataTable.ext.errMode = 'none';
$.fn.dataTable.moment( 'YYYY-MM-DD, hh:mm A' );
table   =   $('.datatable').DataTable({
                "destroy" : true,
                "language": {
                    searchPlaceholder: "Market Name"
                },
                "order": [[ 4, "desc" ]],
                "dom": 'lBfrtip',
                "select": true,
                "buttons": [     
                            {
                                extend: 'excel',
                                text: '<span class="fa fa-file-excel-o"></span> Excel (All)',
                                action: newExportAction,
                                exportOptions: {
                                    columns: ':visible'
                                },
                                title: 'DistributerList'
                            },
                    ],
                "lengthMenu": [[25, 50, 100, -1], ['25 rows', '50 rows', '100 rows', 'All']],
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax": {
                            datatype:   'json',
                            type:       "POST",
                            url:        "{{url('distributer/all-distributerlist')}}",
                            data:       { _token: "{{csrf_token()}}", state_name:state_name}
                        },
                "columnDefs": [
                                { orderable: false, targets: 0 },
                                { "className": "text-center", orderable: false, targets: [4] },
                                //{ "targets": 2, "visible": false},
                            ],
                "columns": [                
                            { 
                                render: function (data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                                } 
                            },
                            { "data": "1" },
                            { "data": "2" },
                            { "data": "3" },
                            { "data": "4" },
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                       data = '<a href="javascript:void(0);" id="distributerStatus" data-token="{{csrf_token()}}" data-ticket="'+row[7]+'">'+row[5]+'</a>';
                                   }
                                   return data;
                                }
                            },
                            { "data": "6" },                                  
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                       data = '<a href="javascript:void(0);" id="editdistributer" data-token="{{csrf_token()}}" data-ticket="'+row[1]+'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                                   }
                                   return data;
                                }
                            },
                        ],
                "deferRender":    true,
                "scrollX":        true,
                "scrollY":        350,
                "scrollCollapse": true,
                "scroller":       true,
                "searching":      true,
                "paging":         true,
                "info":           true,

                "orderCellsTop":  true,
                "fixedColumns":   true,
            }); 
}
            
$.ajaxSetup({
   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});

$("#createmarket").validate({
    errorPlacement: function (error, element) {
                        console.log($(element).next());
                        $(element).next('.rerror').html(error);
                        $(element).next().next('.rerror').html(error);
                    },
    ignore: "input[type='text']:hidden",
    rules: {
        state_name: {
            required:   true,
        },
        district_name: {
            required:   true,
        },
        territory_name: {
            required:   true,
        },
        market_name: {
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

$("#importmarket").validate({
    errorPlacement: function (error, element) {
                        console.log($(element).next());
                        $(element).next('.rerror').html(error);
                        $(element).next().next('.rerror').html(error);
                    },
    ignore: "input[type='text']:hidden",
    rules: {
        marketdata: {
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

$(document.body).on('click', '#editdistributer', function () {
    var _ticket =   $(this).data('ticket');
    window.location.assign("{{url('distributer/edit-distributer')}}/"+_ticket);
}); 

$(document.body).on('click', '#delVillage', function () {
    var r = confirm("Do you want delete the village?");
    if(r==true){
        $(this).closest('tr').addClass('selected');
        var ele         = $(this);
        var data = {
            _token:     $(this).data('token'),
            _ticket:    $(this).data('ticket')
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('village/village-delete')}}",
            data:       data,
            success:    function (data) {
                            if(data=='1') {
                                table.row('.selected').remove().draw(false);
                            }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    }
    else {
        $(this).closest('tr').removeClass('selected');
    }
});

$(document.body).on('click', '#distributerStatus', function () {
    var r = confirm("Do you want change the market status?");
    if(r==true){
        var ele         = $(this);
        var data = {
            _token:     $(this).data('token'),
            _ticket:    $(this).data('ticket')
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('distributer/distributer-status')}}",
            data:       data,
            success:    function (data) {
                            if(data=='1') {
                                if(ele.text()=='ACTIVE') {
                                    var marketStatus  = 'INACTIVE';
                                }
                                else {
                                    var marketStatus  = 'ACTIVE';
                                }
                                ele.text(marketStatus);
                            }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    }
});

$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});
</script>


