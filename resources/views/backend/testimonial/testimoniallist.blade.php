@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
  <style>
      .fltr_btn{
        border-top: 0!important;
      }
  </style>  
    

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Testimonial</h1>
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Testimonial</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">

                            <div class="row">
                                <div class="col-xs-8 form-inline" style="z-index: 2; float:right; padding:0px 0 0 155px;">
                                    <div class="input-daterange input-group" id="datepicker" style="width: 70%;">
                                        <input type="text" placeholder="Plan Start Date From" class="input-sm form-control" id="startdate"  name="start"   />
                                        <span class="input-group-addon">From</span>
                                        <input type="text" class="input-sm form-control" placeholder="Plan Start Date TO" id="enddate" name="end"   />
                                        <span class="input-group-addon">to</span>
                                        <input type="hidden" class="input-sm form-control" name="token" id="token" value="{{csrf_token()}}"/>
                                    </div>
                                    <button id="clear" class="btn btn-sm ">reset</button>
                                    <button type="button" id="dateSearch" class="btn btn-sm btn-primary" style="margin:0;">Search</button>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-xs-8 form-inline" style="z-index: 2; float: right; padding:0px 0 0 155px;">
                                    <div class="box fltr_btn">
                                        <div class="filterBtn" style="margin-right: 40px;margin-top: 10px;">
                                            <button id="filterCallBtn" class="btn btn-info pull-right" data-toggle="modal" data-target="#callFilter">Filter</button>
                                        </div>
                                    </div>
                                </div> 
                            </div> 
    
<!-- Modal -->
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
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" id="callSubmit" class="btn btn-default">Submit</button>
        </div>
      </div>
    </div>
  </div>

                            <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>ECN</th>
                                        <th>Username</th>
                                        <th>User Role</th>
                                        <th>Testimonial For</th>
                                        <th>Mobile</th>
                                        <th>Description</th>
                                        <th>Product</th>
                                        <th>Crop</th>
                                        <th>Uploaded File</th>
                                        <th>Rating</th>
                                        <th>Evaluated By</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                            </table>

                            <table id="onfilter"  style="display: none;" class="table table-bordered table-striped datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>ECN</th>
                                        <th>Username</th>
                                        <th>User Role</th>
                                        <th>Testimonial For</th>
                                        <th>Mobile</th>
                                        <th>Description</th>
                                        <th>Product</th>
                                        <th>Crop</th>
                                        <th>Uploaded File</th>
                                        <th>Rating</th>
                                        <th>Evaluated By</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                            </table>
                           
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
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->



<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>

<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.common-material.min.css" />
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.min.css" />
<link rel="stylesheet" href="https://kendo.cdn.telerik.com/2018.1.221/styles/kendo.material.mobile.min.css" />
<script src="https://kendo.cdn.telerik.com/2018.1.221/js/kendo.all.min.js"></script>  
<script>
$( document ).on('ready',function() {
    drawTable();
    $('#dateSearch').on('click',function(){ $('#onfilter').hide();
        var startDate   = $('#startdate').val();
        var endDate     = $('#enddate').val();
        drawTable(startDate, endDate);

    });
});
$("#callFilter").on('show.bs.modal', function(){
    $('#callFilter select').prop('selectedIndex',0);
});
$("#startdate, #enddate").kendoDatePicker({
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
$("#startdate, #enddate").attr("readonly", true);

$("#clear").click(function(){
    $("#startdate, #enddate").val('');
    drawTable();
});
</script>


<script>
$( document ).on('ready',function() {
    drawTable();
});


function drawTable(start_date = $('#datepicker').find('input[name="start"]').val(), end_date = $('#datepicker').find('input[name="end"]').val()) {
    // Datatable
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'DD-MM-YYYY, hh:mm A' );
    table   =   $('#example1').DataTable({
                "destroy" : true,
                "language": {
                    searchPlaceholder: "Ticket, BDO, Data Source, State, District, Tehsil, Village, Product, Crop,File Code"
                },
                // "order": [[ 4, "desc" ]],
                "dom": 'lBfrtip',
                "buttons": [
                            {
                                text: 'Export All',
                                className: 'redButton'
                            },
                            {
                                extend: 'colvis',
                                collectionLayout: 'fixed  four-column',
                                columns: ':gt(0)',
                            },
                        ],
                "lengthMenu": [[25, 50, 100], ['25 rows', '50 rows', '100 rows']],
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax": {
                            datatype:   'json',
                            type:       "POST",
                            url:        "{{url('testimonial/allquerylist')}}",
                            data:       { _token: "{{csrf_token()}}", start_date: start_date, end_date: end_date},
                            error:      function (request, status, error) {
                                        },
                            
                        },
                "columnDefs": [
                                //{ orderable: false, targets: 0 },
                                { 
                                    render: function (data, type, full, meta) {
                                        return "<div class='text-wrap width-200'>" + data + "</div>";
                                    },
                                    targets: 6
                                },
                            ],
                "columns": [
                            { 
                                render:   function (data, type, row, meta) {
                                                        return meta.row + meta.settings._iDisplayStart + 1;
                                                    } 
                            },
                            { "data": "1" },
                            { "data": "2" },
                            { "data": "3" },
                            { "data": "4" },              
                            { "data": "5" },             
                            { "data": "6" },
                                         
                            { "data": "7" },
                            { "data": "8" },
                            
                            
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                        str = row[9];
                                         if(str!=undefined && str.includes("assets")) {
                                            var str2 = str.replace('admin/','');
                                            var str3 = row[11]+str2;
                                            data = "<a href='"+str3+"' target='_blank'>view</a>";
                                        }
                                        else {
                                            data = "";
                                        }
                                   }
                                   return data;
                                }
                            },
                             { "data": "13" },   
                             { "data": "14" },                       
                            { "data": "10" },
                           
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

$('.sidebar-menu li').click(function(){
    $(this).find('ul').toggle();
});
</script>

<script>
$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});

$(document.body).on('click', '.redButton', function () {
    $('.redButton').html('please wait, it might take few minutes...');
    start_date = $('#datepicker').find('input[name="start"]').val();
    end_date = $('#datepicker').find('input[name="end"]').val();
    var data = {
        _token:         $('#token').val(),
        search:         $('.dataTables_filter input').val(),
        start_date: start_date, 
        end_date: end_date
    }
    $.ajax({
        type: 'POST',
        url: "{{route('postexportalltestimonial')}}",
        data: data,        
        success: function (response, textStatus, request) {
                    var a       = document.createElement("a");
                    a.href      = response.file; 
                    a.download  = response.name;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    $('.redButton').html('Export All');
                },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
});

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

$(document.body).on('click', '#callSubmit', function (e) {
    var filter_RSM = $('#filter_RSM').val();
    var filter_ASM = $('#filter_ASM').val();
    var filter_FL = $('#filter_FL').val();
    var filter_DD = $('#filter_DD').val();
    var filter_crop = $('#filter_crop').val();
    var filter_product = $('#filter_product').val();
    // debugger;
    //=========================================   
    $('#callFilter').modal('hide');
    $('#example1_wrapper').hide(); 
     $('#onfilter').show();
    $.fn.dataTable.ext.errMode = 'none';
    $.fn.dataTable.moment( 'DD-MM-YYYY, hh:mm A' );
    table =   $('#onfilter').DataTable({
                "destroy" : true,
                "language": {
                    searchPlaceholder: "Ticket, BDO, Data Source, State, District, Tehsil, Village, Product, Crop,File Code"
                },
                // "order": [[ 4, "desc" ]],
                "dom": 'lBfrtip',
                "buttons": [
                            {
                                text: 'Export All',
                                className: 'redButton'
                            },
                            {
                                extend: 'colvis',
                                collectionLayout: 'fixed  four-column',
                                columns: ':gt(0)',
                            },
                        ],
                "lengthMenu": [[25, 50, 100], ['25 rows', '50 rows', '100 rows']],
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "ajax": {
                            datatype:   'json',
                            type:       "POST",
                            url:        "{{url('testimonial/filtertestimonial')}}",
                            data:       { _token: "{{csrf_token()}}",filter_RSM:filter_RSM,filter_ASM:filter_ASM,filter_FL:filter_FL,filter_DD:filter_DD,filter_crop:filter_crop,filter_product:filter_product},
                            error:      function (request, status, error) {
                                        }
                            
                        },
                "columnDefs": [
                                //{ orderable: false, targets: 0 },
                                { 
                                    render: function (data, type, full, meta) {
                                        return "<div class='text-wrap width-200'>" + data + "</div>";
                                    },
                                    targets: 6
                                },
                            ],
                "columns": [
                            { 
                                render:   function (data, type, row, meta) {
                                                        return meta.row + meta.settings._iDisplayStart + 1;
                                                    } 
                            },
                            { "data": "1" },
                            { "data": "2" },
                            { "data": "3" },
                            { "data": "4" },              
                            { "data": "5" },             
                            { "data": "6" },
                                         
                            { "data": "7" },
                            { "data": "8" },
                            
                            
                            { 
                                "render": function(data, type, row, meta){
                                   if(type === 'display'){
                                        str = row[9];
                                         if(str!=undefined && str.includes("assets")) {
                                            var str2 = str.replace('admin/','');
                                            var str3 = row[11]+str2;
                                            data = "<a href='"+str3+"' target='_blank'>view</a>";
                                        }
                                        else {
                                            data = "";
                                        }
                                   }
                                   return data;
                                }
                            },
                             { "data": "13" },   
                             { "data": "14" },                       
                            { "data": "10" },
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
//     $('#callFilter').modal('hide');
// $('#callFilter select').prop('selected',true);
//             $("#callFIlterForm")[0].reset();
    //==========================================

    // $.ajax({
    // dataType:   "json",
    // type:       "POST",
    // url:        "{{url('call/call-list-filter')}}",
    // data:       { _token: "{{csrf_token()}}",filter_RSM:filter_RSM,filter_ASM:filter_ASM,filter_FL:filter_FL,filter_DD:filter_DD,filter_crop:filter_crop,filter_product:filter_product},
    // success:    function (data) {
    //     $('#callFilter').modal('hide');
    //     $('#callFIlterForm')[0].reset();
    //     $('.dataTables_info,.dataTables_paginate').hide();
    //     // var data = $.parseJSON(data);
    //     // console.log(data);
    //     var filter='';
    //     if(data.length > 0){
    //         var i = 1;
    //         $.each(data, function(key,value)
    //         {
    //             filter += '<tr><td>'+i+'</td><td>'+value.username+'</td><td>'+value.ECN+'</td><td>'+value.ticket_no+'</td><td>'+value.leadSource+'</td><td>'+value.farmer_name+'</td><td>';
    //             if(typeof(value.mobile) != "undefined" && value.mobile !== null)
    //             {
    //                 filter += value.mobile; 
    //             }
    //              else  if(typeof(value.contact_number) != "undefined" && value.contact_number !== null)
    //             {
    //                filter += value.contact_number;  
    //             }
    //             else
    //             {
    //                filter += 'NA';  
    //             }
    //             filter += '</td><td>'+value.updated_at+'</td></tr>';
    //             i++;
    //         });
    //     }
    //     else
    //     {
    //         filter += '<tr><td></td><td></td><td>No Record Found</td><td></td></tr>';
    //     }
    //     // console.log(filter);
    //     $('.ajaxCallList').html(filter);
    //     },
    //     error:      function (jqXHR, textStatus, errorThrown) {
    //         console.log(textStatus, errorThrown);
    //     }
    // });
    
});
</script>