@include('include.admin.backend_header')
<!-- Left side column. contains the logo and sidebar -->
@include('include.admin.backend_sidebar')

<style>
        .box_dtls_1row{
            border: 1px solid #333;
            text-align: center;
        }
        .secnd_box{
            margin-top: 30px;
        }   
    </style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Summary</h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Activity</li>
        </ol>
    </section>
    <?php //echo "<pre>";print_r($merged);die; ?>
    <section class="content">
        <div class="col-xs-12">
            <div class="filterBtn">
                <button id="filterCallBtn" class="btn btn-info pull-right" data-toggle="modal" data-target="#callFilter" style="background: #228b22;outline: none;border: 0;margin-bottom: 20px;margin-top: 10px;">Filter</button>
            </div>
        </div>
        <div class="ajaxActivity" style="background: #fff;width: 100%;padding: 20px;">
        <div class="row">
            <div class="col-md-3">
                <div class="box_dtls_1row">
                    <h2>{{$merged['countActivity']}}</h2>
                    <p>Activities</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box_dtls_1row">
                    <h2>{{$merged['countNotDoneActivity']}}</h2>
                    <p>Activities not done</p>
                </div>

            </div>
            <div class="col-md-3">
                <div class="box_dtls_1row">
                    <h2>{{$merged['countFarmer']}}</h2>
                    <p>Farmers</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box_dtls_1row">
                    <h2>{{$merged['countValidatedFarmer']}}</h2>
                    <p>Validated Farmers</p>
                </div>
            </div>
            
        </div>
        <div class="row secnd_box">
            <div class="col-md-3">
                <div class="box_dtls_1row">
                    <h2>{{$merged['ExtrapolatedFarmer']}}</h2>
                    <p>Extrapolated validated farmers</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box_dtls_1row">
                    <h2>{{$merged['countDD']}}</h2>
                    <p>DD</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box_dtls_1row">
                    <h2>{{$merged['countDemoRequest']}}</h2>
                    <p>Create Demo</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box_dtls_1row">
                    <h2>{{$merged['countDemoFeedback']}}</h2>
                    <p>Reviewed Create Demo</p>
                </div>

            </div>
        </div><!-- /.row -->
    </div>
</section>
  <div class="modal fade" id="callFilter" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Filter</h4>
        </div>
        <div class="modal-body">
            <form id="callFIlterForm">
         <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>RSM</label>
                    <select class="form-control" name="filter_RSM" id="filter_RSM">
                        <option value="">Select RSM</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>ASM</label>
                    <select class="form-control" name="filter_ASM" id="filter_ASM">
                        <option value="">Select ASM</option>
                    </select>
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>FL</label>
                    <select class="form-control" name="filter_FL" id="filter_FL">
                        <option value="">Select FL</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>DD</label>
                    <select class="form-control" name="filter_DD" id="filter_DD">
                        <option value="">Select DD</option>
                    </select>
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Crop</label>
                    <select class="form-control" name="filter_crop" id="filter_crop">
                        <option value="">Select Crop</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Product</label>
                    <select class="form-control" name="filter_product" id="filter_product">
                        <option value="">Select Product</option>
                    </select>
                </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Start Date</label>
                     <input type="text" class="form-control" name="filter_start" id="filter_start" placeholder="Select Date" readonly="">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>End Date</label>
                    <input type="text" class="form-control" name="filter_end" id="filter_end" placeholder="Select Date" readonly="">
                </div>
            </div>
         </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" id="activitySubmit" class="btn btn-default">Submit</button>
        </div>
      </div>
    </div>
  </div>  
   
<!-- Main content -->

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
<script type="text/javascript" src="{{asset('assets/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
      
        $("#filter_start").datepicker({
           // minDate: 0,
            dateFormat: 'yy-mm-dd',
            onSelect: function(selected_date) {
                $("#filter_end").datepicker("option", "minDate", selected_date);
            }
        });
        $("#filter_end").datepicker({
           // minDate: 0,
            dateFormat: 'yy-mm-dd',
            onSelect: function(selected_date) {
                $("#filter_start").datepicker("option", "maxDate", selected_date);
            }
        });
    });
</script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>

<script type="text/javascript">
    $(document.body).on('click', '#filterCallBtn', function () {
            $.ajax({
                type:       'GET',
                url:        "{{url('call/call-filter')}}",
                success:    function (data) {
                    var data = $.parseJSON(data);
                    var filter_RSM = '<option value="">Select RSM</option>';
                    var filter_crop = '<option value="">Select Crop</option>';
                    $.each(data.rmsList, function(key,value)
                    {
                        filter_RSM += '<option value="'+value.sales_regional_manager_id+'">' + value.sales_regional_manager_name + '</option>';
                        
                    });
                     $.each(data.cropList, function(key,value)
                    {
                        filter_crop += '<option value="'+value.crop_id+'">' + value.crop + '</option>';
                        
                    });               
                    $('#filter_RSM').html(filter_RSM);
                    $('#filter_crop').html(filter_crop);
                    $('#filter_product').html('<option value="">Select Product</option>');
                 
                },
                error:      function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });

    });
    $(document.body).on('click', '#filter_RSM', function () {
        var option = $(this).val();
        var type = 'ASM';
            $.ajax({
                type:       'POST',
                url:        "{{url('call/call-filter-ajax')}}",
                 data:       { _token: "{{csrf_token()}}",option:option,type:type},
                success:    function (data) {
                    var data = $.parseJSON(data);
                    var filter_ASM = '<option value="">Select ASM</option>';
                    $.each(data, function(key,value)
                    {
                        filter_ASM += '<option value="'+value.sales_area_manager_id+'">' + value.sales_area_manager_name + '</option>';
                        
                    });                  
                    $('#filter_ASM').html(filter_ASM);
                    $('#filter_FL').html('<option value="">Select FL</option>');
                    $('#filter_DD').html('<option value="">Select DD</option>');
                },
                error:      function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
    });

    $(document.body).on('click', '#filter_ASM', function () {
        var option = $(this).val();
        var type = 'FL';
            $.ajax({
                type:       'POST',
                url:        "{{url('call/call-filter-ajax')}}",
                 data:       { _token: "{{csrf_token()}}",option:option,type:type},
                success:    function (data) {
                    var data = $.parseJSON(data);
                    var filter_FL = '<option value="">Select FL</option>';
                    $.each(data, function(key,value)
                    {
                        filter_FL += '<option value="'+value.id+'">' + value.name + '</option>';
                        
                    });                  
                    $('#filter_FL').html(filter_FL);
                    $('#filter_DD').html('<option value="">Select DD</option>');
                },
                error:      function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
    });
    $(document.body).on('click', '#filter_FL', function () {
        var option = $(this).val();
        var type = 'DD';
            $.ajax({
                type:       'POST',
                url:        "{{url('call/call-filter-ajax')}}",
                 data:       { _token: "{{csrf_token()}}",option:option,type:type},
                success:    function (data) {
                    var data = $.parseJSON(data);
                    var filter_DD = '<option value="">Select DD</option>';
                    $.each(data, function(key,value)
                    {
                        filter_DD += '<option value="'+value.dd_id+'">' + value.name + '</option>';
                        
                    });                  
                    $('#filter_DD').html(filter_DD);

                },
                error:      function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
    });

    $(document.body).on('click', '#filter_crop', function () {
        var option = $(this).val();
        var type = 'crop';
            $.ajax({
                type:       'POST',
                url:        "{{url('call/call-filter-ajax')}}",
                 data:       { _token: "{{csrf_token()}}",option:option,type:type},
                success:    function (data) {
                    var data = $.parseJSON(data);
                    var filter_product = '<option value="">Select Product</option>';
                    $.each(data, function(key,value)
                    {
                        filter_product += '<option value="'+value.product_id+'">' + value.product + '</option>';
                        
                    });                  
                    $('#filter_product').html(filter_product);
                },
                error:      function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
    });

    $(document.body).on('click', '#activitySubmit', function (e) {
      var filter_RSM = $('#filter_RSM').val();
      var filter_ASM = $('#filter_ASM').val();
      var filter_FL = $('#filter_FL').val();
      var filter_DD = $('#filter_DD').val();
      var filter_crop = $('#filter_crop').val();
      var filter_product = $('#filter_product').val();
      var filter_start = $('#filter_start').val();
      var filter_end = $('#filter_end').val();
      // debugger;
      if(filter_start != '' || filter_end != '')
      {
        if(!( /\d{2}-\d{2}-\d{2}/.test($('#filter_start').val()) && /\d{2}-\d{2}-\d{2}/.test($('#filter_end').val() ) ) )
        {
            e.preventDefault(); //stop form being submitted!
            alert('please select start and end date');
            return false;
        }
        else
        {

            $.ajax({
            dataType:   "json",
            type:       "POST",
            url:        "{{url('admin/activity-filter')}}",
            data:       { _token: "{{csrf_token()}}",filter_RSM:filter_RSM,filter_ASM:filter_ASM,filter_FL:filter_FL,filter_DD:filter_DD,filter_crop:filter_crop,filter_product:filter_product,filter_start:filter_start,filter_end:filter_end},
            success:function (data) {
                //console.log('here',data);
                $('#callFilter').modal('hide');
                $('#callFIlterForm')[0].reset();
                $('.dataTables_info,.dataTables_paginate').hide();
                //var data = $.parseJSON(data);
                //console.log(data);
                //console.log(data.length)
                var filter='';
                // if(data.length > 0){
                    filter += '<div class="row"><div class="col-md-3"><div class="box_dtls_1row"><h2>'+data.countActivity+'</h2><p>Activities</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+data.countNotDoneActivity+'</h2><p>Activities not done</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+data.countFarmer+'</h2><p>Farmers</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+data.countValidatedFarmer+'</h2><p>Validated Farmers</p></div></div></div><div class="row secnd_box"><div class="col-md-3"><div class="box_dtls_1row"><h2>'+data.ExtrapolatedFarmer+'</h2><p>Extrapolated validated farmers</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+data.countDD+'</h2><p>DD</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+data.countDemoRequest+'</h2><p>Create Demo</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+data.countDemoFeedback+'</h2><p>Reviewed Create Demo</p></div></div></div>';
                // }
                // else
                // {
                //     filter += '<div class="row"><div class="col-md-3"><div class="box_dtls_1row"><h2>'+0+'</h2><p>Activities</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+0+'</h2><p>Activities not done</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+0+'</h2><p>Farmers</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+0+'</h2><p>Validated Farmers</p></div></div></div><div class="row secnd_box"><div class="col-md-3"><div class="box_dtls_1row"><h2>'+0+'</h2><p>Extrapolated validated farmers</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+0+'</h2><p>DD</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+0+'</h2><p>Create Demo</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+0+'</h2><p>Reviewed Create Demo</p></div></div></div>';
                     
                // }   
                // // console.log(filter);
                $('.ajaxActivity').html(filter);
                 
                },
                error:      function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });

            }
        }
        else
        {
            $.ajax({
            dataType:   "json",
            type:       "POST",
            url:        "{{url('admin/activity-filter')}}",
            data:       { _token: "{{csrf_token()}}",filter_RSM:filter_RSM,filter_ASM:filter_ASM,filter_FL:filter_FL,filter_DD:filter_DD,filter_crop:filter_crop,filter_product:filter_product,filter_start:filter_start,filter_end:filter_end},
            success:    function (data) {
                //console.log('here',data);
                  $('#callFilter').modal('hide');
                   $('#callFIlterForm')[0].reset();
                   $('.dataTables_info,.dataTables_paginate').hide();
                //console.log(data);
                
               var filter='';
               //if(data.length > 0){
                    filter += '<div class="row"><div class="col-md-3"><div class="box_dtls_1row"><h2>'+data.countActivity+'</h2><p>Activities</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+data.countNotDoneActivity+'</h2><p>Activities not done</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+data.countFarmer+'</h2><p>Farmers</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+data.countValidatedFarmer+'</h2><p>Validated Farmers</p></div></div></div><div class="row secnd_box"><div class="col-md-3"><div class="box_dtls_1row"><h2>'+data.ExtrapolatedFarmer+'</h2><p>Extrapolated validated farmers</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+data.countDD+'</h2><p>DD</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+data.countDemoRequest+'</h2><p>Create Demo</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+data.countDemoFeedback+'</h2><p>Reviewed Create Demo</p></div></div></div>';
                // }
                // else
                // {
                //     filter += '<div class="row"><div class="col-md-3"><div class="box_dtls_1row"><h2>'+0+'</h2><p>Activities</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+0+'</h2><p>Activities not done</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+0+'</h2><p>Farmers</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+0+'</h2><p>Validated Farmers</p></div></div></div><div class="row secnd_box"><div class="col-md-3"><div class="box_dtls_1row"><h2>'+0+'</h2><p>Extrapolated validated farmers</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+0+'</h2><p>DD</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+0+'</h2><p>Create Demo</p></div></div><div class="col-md-3"><div class="box_dtls_1row"><h2>'+0+'</h2><p>Reviewed Create Demo</p></div></div></div>';
                     
                // }   
                // // console.log(filter);
                $('.ajaxActivity').html(filter);
               
                 
                },
                error:      function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
        });

        }

     });


    

</script>




















