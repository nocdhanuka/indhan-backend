@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>All Product Crop Recommendation List</h1>
            
            @if(Session::has('message'))
            <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            </div>
            @endif

            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Product Crop Recommendation List</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body"> 
                            <form role="form" autocomplete="off" method="post" class="search-form"  id="createcrop" action="{{url('recommendation/create-recommendation')}}"  >                                                          
                                <div class="col-lg-2">
                                    <label class="formlabel">Category Name</label>
                                    <!-- <input type="text" id="category_name" class="frm_txt inputs"  name="category_name" placeholder="Category Name" maxlength="50" value="{{ old('category_name') }}">
                                    <div id="auto"></div>-->
                                    <select id="category_name" class="frm_txt mdb-select inputs"  name="category_name">
                                        <option value="" >Choose Category</option>
                                        <?php foreach($category_detail as $category) { ?>
                                            <option value="{{$category->product_crop_category_id}}">{{$category->category_name}}</option>
                                        <?php } ?>
                                    </select>
                                    <div class="rerror"><label for="category_name" generated="true" class="error">{{ $errors->first('category_name') }}</label></div>
                                </div>
                                <div class="col-lg-3"> 
                                    <label class="formlabel">Product</label>
                                    <select id="product_name" class="frm_txt mdb-select inputs"  name="product_name">
                                        <option value="" >Choose Product</option>
                                        <?php foreach($product_detail as $products) { ?>
                                            <option value="{{$products->product_id}}">{{$products->product}}</option>
                                        <?php } ?>
                                    </select>
                                    <div class="rerror"><label for="product_name" generated="true" class="error">{{ $errors->first('product_name') }}</label></div>
                                </div> 
                                <div class="col-lg-3"> 
                                    <label class="formlabel">Crop</label>
                                    <select id="crop_name" class="frm_txt mdb-select inputs" name="crop_name[]" multiple="multiple">
                                        <option value="" >Choose Crop</option>
                                        <?php foreach($crop_detail as $crops) { ?>
                                            <option value="{{$crops->crop_id}}">{{$crops->crop}}</option>
                                        <?php } ?>
                                    </select>
                                    <div class="rerror"><label for="crop_name" generated="true" class="error">{{ $errors->first('crop_name') }}</label></div>
                                </div>
                                <div class="col-lg-2"> 
                                    <label class="formlabel">Utilization Per Demo</label>
                                    <input type="text" id="utilization_Per_demo" class="frm_txt inputs"  name="utilization_Per_demo" placeholder="Utilization Per Demo" maxlength="50" value="{{ old('utilization_Per_demo') }}">
                                    <div class="rerror"><label for="utilization_Per_demo" generated="true" class="error">{{ $errors->first('utilization_Per_demo') }}</label></div>
                                </div>
                                <div class="col-lg-2">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                                    <input type="submit" name="submit" class="inputs btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Create" >
                                    <input type="button" name="reset" class="btn btn-primary btn-lg gap5040 inputs" id="reset" value="Reset" >
                                </div>
                                
                                <div class="clearfix"></div>
                                <div class="col-lg-12"><hr></div>
                                <div class="clearfix"></div>
                            </form>
                            <div class="success"></div>                          
                            
                            <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Category</th>
                                        <th>Product</th>
                                        <th>Crop</th>
                                        <th>Utilization Per Demo</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>&emsp;&nbsp;<i class="fa fa-wrench" aria-hidden="true"></i></th>
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


<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
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
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

<script>
$( document ).on('ready',function() {
    drawTable();
    $('#category_name').on('change',function(){
        var category_name   = $('#category_name').val();
        var product_name    = $('#product_name').val();
        drawTable(category_name, product_name);
    });
    
    $('#product_name').on('change',function(){
        var category_name   = $('#category_name').val();
        var product_name    = $('#product_name').val();
        drawTable(category_name, product_name);
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
function drawTable(category_name = $('#category_name').val(), product_name = $('#product_name').val()) {
    $.fn.dataTable.ext.errMode = 'none';
$.fn.dataTable.moment( 'YYYY-MM-DD, hh:mm A' );
    table   =   $('.datatable').DataTable({
                    "destroy" : true,
                    "language": {
                        searchPlaceholder: ""
                    },
                    "order": [[ 5, "desc" ]],
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
                                    title: 'Crop List'
                                },
                        ],
                    "lengthMenu": [[25, 50, 100, -1], ['25 rows', '50 rows', '100 rows', 'All']],
                    "pageLength": 25,
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                                datatype:   'json',
                                type:       "POST",
                                url:        "{{url('recommendation/allrecommendationlist')}}",
                                data:       { _token: "{{csrf_token()}}", category_name: category_name, product_name: product_name},
                            },
                    "columnDefs": [
                                    { orderable: false, targets: 0 },
                                    { "className": "text-center", orderable: false, targets: [5] },
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
                                { "data": "7" },
                                { 
                                    "render": function(data, type, row, meta){
                                       if(type === 'display'){
                                           data = '<a href="javascript:void(0);" id="editRecommendationStatus" data-token="{{csrf_token()}}" data-ticket="'+row[6]+'">'+row[4]+'</a>';
                                       }
                                       return data;
                                    }
                                },
                                { "data": "5" },                                  
                                { 
                                    "render": function(data, type, row, meta){
                                       if(type === 'display'){
                                           data = '<a href="javascript:void(0);" id="delRecommendation" data-token="{{csrf_token()}}" data-ticket="'+row[6]+'"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                                           data += '&emsp;<a href="javascript:void(0);" id="editRecommendation" data-token="{{csrf_token()}}" data-ticket="'+row[6]+'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
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

$("#createcrop").validate({
    errorPlacement: function (error, element) {
                        console.log($(element).next());
                        $(element).next('.rerror').html(error);
                        $(element).next().next('.rerror').html(error);
                    },
    ignore: "input[type='text']:hidden",
    rules: {
        "category_name": {
            required:   true,
        },
        "product_name": {
            required:   true,
        },
        "crop_name[]": {
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
$.ajaxSetup({
   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});
$(document.body).on('click', '#editRecommendation', function () {
    var _ticket =   $(this).data('ticket');
    window.location.assign("{{url('recommendation/edit-recommendation')}}/"+_ticket);
}); 
$(document.body).on('click', '#delRecommendation', function () {
    var r = confirm("Do you want delete the recommendation?");
    if(r==true){
        $(this).closest('tr').addClass('selected');
        var ele         = $(this);
        var data = {
            _token:     $(this).data('token'),
            _ticket:    $(this).data('ticket')
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('recommendation/recommendation-delete')}}",
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
$(document.body).on('click', '#editRecommendationStatus', function () {
    var r = confirm("Do you want change the recommendation status?");
    if(r==true){
        var ele         = $(this);
        var data = {
            _token:     $(this).data('token'),
            _ticket:    $(this).data('ticket')
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('recommendation/update-recommendation-status')}}",
            data:       data,
            success:    function (data) {
                            if(data=='1') {
                                if(ele.text()=='ACTIVE') {
                                    var cropStatus  = 'INACTIVE';
                                }
                                else {
                                    var cropStatus  = 'ACTIVE';
                                }
                                ele.text(cropStatus);
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

$('#reset').click(function(){
    $('#category_name').val('');
    $('#product_name').val('');
    $('#crop_name').val('');
    drawTable();
});
</script>


<!-- SlimScroll 
<script src="{{asset('assets/admin/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('assets/admin/dist/js/app.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->

<script type="text/javascript">
/*$(document).ready(function(){
    $("#crop_name").on('change', function(e){
        var selectedItems = $(this).val() || [];
        // alert(selectedItems);
    });
    var counter = 2;
    $("#addButton").click(function () {
        if(counter>10){
            alert("Only 10 textboxes allow");
            return false;
        }
        var newTextBoxDiv = $(document.createElement('div')).attr("id", 'TextBoxDiv' + counter);
        newTextBoxDiv.after().html('<label>Textbox #'+ counter + ' : </label>' +
          '<input type="text" name="textbox' + counter + 
          '" id="textbox' + counter + '" value="" >');
        newTextBoxDiv.appendTo("#TextBoxesGroup");
        counter++;
    });

    $("#removeButton").click(function () {
        if(counter==1){
            alert("No more textbox to remove");
            return false;
        }
        counter--;
        $("#TextBoxDiv" + counter).remove();
    });
    $("#getButtonValue").click(function () {
        var msg = '';
        for(i=1; i<counter; i++){
            msg += "\n Textbox #" + i + " : " + $('#textbox' + i).val();
        }
        alert(msg);
    });
});*/
</script>
