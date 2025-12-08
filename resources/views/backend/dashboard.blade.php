    <?php 
//dd($total_validate_farmer); 

$regional_manager           = (!empty($regional_sales['regional'])) ? $regional_sales['regional'] : array();
$sales_regional_manager     = (!empty($regional_sales['sales'])) ? $regional_sales['sales'] : array();
$area_manager               = (!empty($area_sales['area'])) ? $area_sales['area'] : array();
$sales_area_manager         = (!empty($area_sales['sales'])) ? $area_sales['area'] : array();
$bdos                       = (!empty($bdo)) ? $bdo : array();
//dd($bdo); 
?>
<?php 
//dd($total_no_of_activities);
?>
<script type = "text/javascript" src = "https://www.gstatic.com/charts/loader.js"></script>
<script type = "text/javascript">
    google.charts.load('current', {packages: ['corechart', 'line']});
</script>
@include('include.admin.backend_header')
@include('include.admin.backend_sidebar')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Dashboard</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>
    
    <section class="content" style="min-height: 5px !important;">
        <div class="row">   
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <div class="col-xs-12">
                            <form role="form" method="post" class="search-form"  id="createbdo" action="{{url('admin/dashboard')}}"  > 
                                <div class="col-lg-3"> 
                                    <label class="formlabel">S3</label>
                                    <select id="zonal_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="zonal_manager" style="min-width: 217px;">
                                        <option value="">--Select S3--</option>
                                        <?php
                                        foreach($zonal_list as $zonal) { ?>
                                    
                                        <option value="{{$zonal->zonal_manager_id}}" <?php echo (isset($my_data['zonal_manager']) && $my_data['zonal_manager']==$zonal->zonal_manager_id?'selected="selected"':''); ?>>{{$zonal->zonal_manager_name}}</option>
                                        <?php    
                                        }
                                        ?>
                                    </select>
                                </div> 
                                <div class="col-lg-3"> 
                                    <label class="formlabel">RDBM</label>
                                    <select id="regional_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="regional_manager" style="min-width: 217px;">
                                        <option value="">--Select RDBM--</option>
                                        <?php
                                        if(count($regional_manager)>0) {
                                            foreach($regional_manager as $regional) { ?>
                                                <option value="{{$regional['regional_manager_id']}}" <?php echo (isset($my_data['regional_manager']) && $my_data['regional_manager']==$regional['regional_manager_id']?'selected="selected"':''); ?>>{{$regional['regional_manager_name']}}</option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div> 
                                <div class="col-lg-3"> 
                                    <label class="formlabel">BD S1</label>
                                    <select id="area_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="area_manager" style="min-width: 217px;">
                                        <option value="">--Select BD S1--</option>
                                        <?php
                                        if(count($area_manager)>0) {
                                            foreach($area_manager as $area) { ?>
                                                <option value="{{$area['area_manager_id']}}" <?php echo (isset($my_data['area_manager']) && $my_data['area_manager']==$area['area_manager_id']?'selected="selected"':''); ?>>{{$area['area_manager_name']}}</option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-lg-3"> 
                                    <label class="formlabel">BDO</label>
                                    <select id="bdo" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="bdo" style="min-width: 217px;">
                                        <option value="">--Select BDO--</option>
                                        <?php
                                        if(count($bdos)>0) {
                                            foreach($bdos as $bdo) { ?>
                                                <option value="{{$bdo['id']}}" <?php echo (isset($my_data['bdo']) && $my_data['bdo']==$bdo['id']?'selected="selected"':''); ?>>{{$bdo['name']}}</option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                
                                <div class="clearfix"></div>
                                <div class="col-lg-12"></div>
                                <div class="clearfix"></div>
                                
                                <div class="col-lg-3 col-md-push-3"> 
                                    <label class="formlabel">SME<!-- Sales Regional Mangers --></label>
                                    <select id="sales_regional_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="sales_regional_manager" style="min-width: 217px;">
                                        <option value="">--Select SME--</option>
                                        <?php
                                        if(count($sales_regional_manager)>0) {
                                            foreach($sales_regional_manager as $sales_regional) { ?>
                                                <option value="{{$sales_regional['sales_regional_manager_id']}}" <?php echo (isset($my_data['sales_regional_manager']) && $my_data['sales_regional_manager']==$sales_regional['sales_regional_manager_id']?'selected="selected"':''); ?>>{{$sales_regional['sales_regional_manager_name']}}</option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div> 
                                <div class="col-lg-3 col-md-push-3"> 
                                    <label class="formlabel">Sales S1<!-- Sales Area Mangers --></label>
                                    <select id="sales_area_manager" class="frm_txt mdb-select"  data-token="{{csrf_token()}}"  name="sales_area_manager" style="min-width: 217px;">
                                        <option value="">--Select S1--</option>
                                        <?php
                                        if(count($sales_area_manager)>0) {
                                            foreach($sales_area_manager as $sales_area) { ?>
                                                <option value="{{$sales_area['sales_area_manager_id']}}" <?php echo (isset($my_data['sales_area_manager']) && $my_data['sales_area_manager']==$sales_area['sales_area_manager_id']?'selected="selected"':''); ?>>{{$sales_area['sales_area_manager_name']}}</option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                
                                
                                <div class="clearfix"></div>
                                <div class="col-lg-12"></div>
                                <div class="clearfix"></div>
                                
                                <div class="col-lg-6 col-md-push-3"> 
                                    <label class="formlabel">Select Month</label>
                                    <div class="input-daterange input-group" id="datepicker">
                                        <input type="text" class="input-sm form-control" id="startdate"  name="start" value="<?php echo (isset($my_data['start']) && $my_data['start']!=''?$my_data['start']:''); ?>"   />
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="input-sm form-control" id="enddate" name="end" value="<?php echo (isset($my_data['end']) && $my_data['end']!=''?$my_data['end']:''); ?>"   />
                                        <input type="hidden" class="input-sm form-control" name="token" value="{{csrf_token()}}"/>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-md-push-3"> 
                                    <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040" id="reg" value="Search" >
                                </div>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
                    </div><!-- /.row -->
    </section>
    
    
    <section class="content">
        <!-- Tabbing start -->
        <div class="my-tabbing">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#tabbing1">Performance Graph</a></li>
                <li><a data-toggle="tab" href="#tabbing2">Efficiency</a></li>
            </ul>
            <div class="tab-content">
                <div id="tabbing1" class="tab-pane fade in active">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-body">
                                    <div><a href="{{ url('cron/hard-ticket-qualified-status') }}">Refresh</a></div>   
                                    <div id = "container1"></div>
                                
                                    <div class="clearfix"></div>
                                    <div class="col-lg-12"><hr/></div>
                                    <div class="clearfix"></div>
                                    
                                    <div id = "container2"></div>
                                
                                    <div class="clearfix"></div>
                                    <div class="col-lg-12"><hr/></div>
                                    <div class="clearfix"></div>
                                    
                                    <div id = "container3"></div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.row -->
                </div>
                <div id="tabbing2" class="tab-pane fade">   
                    <div class="">
                        <div class="row report_ratio">
                            <div class="row total-farmer-row">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 ">
                                    <div><a href="{{ url('cron/hard-calculate-efficiency') }}">Refresh</a></div> 
                                    <div class="circle-dotted text-center img-responsive">
                                        <button id="next_month" name="next_month" data-month="next">Next Month >></button>
                                        <input type="hidden" name="month" id="month" value="{{$month}}" />
                                        <img src="{{asset('assets/images/efficiency.png')}}" alt="img1">
                                        <div class="data"><span id="efficiency_percent"><?php echo (array_key_exists(0, $efficiency) && $efficiency[0]['efficiency']?$efficiency[0]['efficiency']:0);?>%</span>Efficiency<p id="month_string"><?php echo date('M Y'); ?></p></div>
                                        <button id="previous_month" name="previous_month" data-month="previous"><< Previous Month </button>
                                    </div>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 ">
                                    <div class="farmer-activity-middle-sec">
                                        <!-- First-row -->
                                        <div class="row activites_row">
                                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 activity-col">
                                                <div class="farmer_activity_heading">Total No. of Activities</div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 activity-col text-right">
                                                <div class="farmer_activity_number"><?php echo (isset($total_no_of_activities) && $total_no_of_activities!=''?$total_no_of_activities:0);?></div>
                                            </div>
                                        </div>
                                        <!-- second-row -->
                                        <div class="row farmer-cal">
                                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 total-farmer-col">
                                                <div><h4><?php echo (isset($total_farmer) && $total_farmer!=''?$total_farmer:0);?></h4></div>
                                                <span>Total Farmers</span>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 total-farmer-col">
                                                <div><h4><?php echo (isset($total_validate_farmer) && $total_validate_farmer!=''?$total_validate_farmer:0);?></h4></div>
                                                <span>Total Validated Farmers</span>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 total-farmer-col">
                                                <div><h4><?php echo (isset($average_farmer) && $average_farmer!=''?$average_farmer:0);?></h4></div>
                                                <span>Average Farmers</span>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 total-farmer-col">
                                                <div><h4><?php echo (isset($average_validated_farmer) && $average_validated_farmer!=''?$average_validated_farmer:0);?></h4></div>
                                                <span>Average Validated Farmers</span>
                                            </div>
                                        </div>
                                        <!-- Third-row -->
                                        <div class="row testimonial_row">
                                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 activity-col">
                                                <div class="farmer_activity_heading">Testimonials</div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-xs-4 col-sm-4 activity-col text-right">
                                                <div class="farmer_activity_number"><?php echo (isset($testimonial) && $testimonial!=''?$testimonial:0);?></div>
                                            </div>
                                        </div>
                                        <!-- fourth-row -->
                                        <div class="row customer_care_row">
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 customer_care_col">
                                                <div class="farmer_activity_heading">Customer Care</div>
                                            </div>
                                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 total-farmer-col customer_care_col">
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 cc-inner-col">
                                                        <h4><?php echo (isset($customer_care_closed) && $customer_care_closed!=''?$customer_care_closed:0);?></h4>
                                                        <span>Closed</span>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 cc-inner-col customer-care-sub-col">
                                                        <h4><?php echo (isset($customer_care_follow_up) && $customer_care_follow_up!=''?$customer_care_follow_up:0);?></h4>
                                                        <span>Follow up</span>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 cc-inner-col ">
                                                        <h4><?php echo (isset($customer_care_open) && $customer_care_open!=''?$customer_care_open:0);?></h4>
                                                        <span>Open</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- fifth-row -->
                                        <div class="row customer_care_row">
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 customer_care_col">
                                                <div class="farmer_activity_heading">Sales Queries</div>
                                            </div>
                                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 total-farmer-col customer_care_col">
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 cc-inner-col">
                                                        <h4><?php echo (isset($customer_care_sale_closed) && $customer_care_sale_closed!=''?$customer_care_sale_closed:0);?></h4>
                                                        <span>Closed</span>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 cc-inner-col customer-care-sub-col">
                                                        <h4><?php echo (isset($customer_care_sale_follow_up) && $customer_care_sale_follow_up!=''?$customer_care_sale_follow_up:0);?></h4>
                                                        <span>Follow up</span>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 cc-inner-col ">
                                                        <h4><?php echo (isset($customer_care_sale_open) && $customer_care_sale_open!=''?$customer_care_sale_open:0);?></h4>
                                                        <span>Open</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br><br><hr><br><br><br><br>
        </div>
        <!-- Tabbing end -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@include('include.admin.footer')

<!-- Control Sidebar -->
<!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>

</div><!-- ./wrapper -->

<!-- ./wrapper --><!-- jQuery 2.1.4 -->
<script src="{{asset('assets/admin/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>

<!--Datepicker-->
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.common-material.min.css" />
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.min.css" />
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.mobile.min.css" />
<script src="https://kendo.cdn.telerik.com/2018.1.221/js/kendo.all.min.js"></script>  
<script>
$("#startdate, #enddate").kendoDatePicker({
    // defines the start view
    start: "year",

    // defines when the calendar should return date
    depth: "year",

    // display month and year in the input
    format: "MMMM yyyy",

    // specifies that DateInput is used for masking the input element
    dateInput: false,
});
//DISABLE inputs
$("#startdate, #enddate").attr("readonly", true);
</script>
<!--Datepicker-->

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
                                $("#bdo").empty();
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
                                $("#bdo").empty();
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
    
    $('#area_manager').on('change',function(){
        var area_manager   = $('#area_manager').val();
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
                                var jsonData1   = JSON.parse(data);
                                if(jsonData1!='undefined') {
                                    var appenddata1 = '';
                                    appenddata1 += "<option value = ''>--Select BDO--</option>";
                                    for (var i = 0; i < jsonData1.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1[i].id + "'>" + jsonData1[i].name + " </option>";
                                    }
                                    $("#bdo").append(appenddata1);
                                }
                                return false;
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
});
</script>


<script language = "JavaScript">
    function drawChart1() {
        // Define the chart to be drawn.
        var data = new google.visualization.DataTable();
        data.addColumn('date', 'Month');
        data.addColumn('number', 'Activities');
        data.addColumn('number', 'Registered Farmers');
        data.addColumn('number', 'Validated Farmers');
        data.addRows([
            <?php
            foreach($activities as $row) { ?>
                [new Date(<?php echo date('Y, n - 1, 01', strtotime($row['y_m_d']));?>), <?=$row['activities'];?>, <?=$row['registered_farmers'];?>, <?=$row['validated_farmers'];?>],
                <?php
            } 
            ?>
        ]);
        // sort by start and end dates
        data.sort([{column: 0}]);                
        // Set chart options
        var options = {'title': 'Activity Graph',
            hAxis: {
                title: 'Month'
            },
            vAxis: {
                title: 'Numbers'
            },
            'width': 1000,
            'height': 400,
            'pointSize' : 5,
            'lineWidth' : 2,
//            explorer: {
//              axis: 'horizontal',
//              keepInBounds: true,
//              maxZoomIn: 100
//            },
        };

        // Instantiate and draw the chart.
        var chart = new google.visualization.LineChart(document.getElementById('container1'));
        chart.draw(data, options);
    }
    google.charts.setOnLoadCallback(drawChart1);

    function drawChart2() {
        // Define the chart to be drawn.
        var data = new google.visualization.DataTable();
        data.addColumn('date', 'Month');
        data.addColumn('number', 'Participation');
        data.addRows([
            <?php
            foreach($self_participations as $row) { ?>
                [new Date(<?php echo date('Y, n - 1, 01', strtotime($row['y_m_d']));?>), <?=$row['self_participations'];?>],
                <?php
            } 
            ?>
        ]);
        // sort by start and end dates
        data.sort([{column: 0}]);                
        // Set chart options
        var options = {'title': 'Self Participation Graph',
            hAxis: {
                title: 'Month'
            },
            vAxis: {
                title: 'Numbers'
            },
            'width': 1000,
            'height': 400,
            'pointSize' : 5,
            'lineWidth' : 2,
//            explorer: {
//              axis: 'horizontal',
//              keepInBounds: true,
//              maxZoomIn: 100
//            },
        };

        // Instantiate and draw the chart.
        var chart = new google.visualization.LineChart(document.getElementById('container2'));
        chart.draw(data, options);
    }
    google.charts.setOnLoadCallback(drawChart2);

</script>






<script type="text/javascript">
$(document).ready(function () {
     var current_month = $('#month').val();
     efficiency(parseInt(current_month) + 1);
    $('#next_month, #previous_month').on('click',function(){
          var month = $('#month').val();
          var month_type = $(this).data('month');
          efficiency(month,month_type);
    });
});

function efficiency(month=0,month_type=0)
{
        var data = {
            _token:                 $(this).data('token'),
            zonal_manager:          $('#zonal_manager').val(),
            regional_manager:       $('#regional_manager').val(),
            sales_regional_manager: $('#sales_regional_manager').val(),
            area_manager:           $('#area_manager').val(),
            sales_area_manager:     $('#sales_area_manager').val(),
            bdo:                    $('#bdo').val(),
            month:                   month,
            month_type:              month_type
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('admin/efficiency')}}",
            data:       data,
            success:    function (data) {
                            if(data) {
                                var jsonData1   = JSON.parse(data);
                                if(typeof jsonData1.efficiency!='undefined') {
                                    $('#efficiency_percent').html(jsonData1.efficiency+'%');
                                }
                                if(typeof jsonData1.month!='undefined') {
                                    $('#month').val(jsonData1.month);
                                }  
                                if(typeof jsonData1.month_string!='undefined') {
                                    $('#month_string').html(jsonData1.month_string);
                                }                               
                            }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });



}
</script>