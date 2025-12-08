@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>All Godown List</h1>
            
            @if(Session::has('message'))
            <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            </div>
            @endif

            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Godown List</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <form role="form" method="post" class="search-form"  id="creategodown" action="{{url('godown/create-godown')}}">
                                <div class="col-lg-3">
                                    <label class="formlabel">State Name</label>
                                    <select id="state_name" class="frm_txt mdb-select inputs"  name="state_name">
                                        <option value="" >Choose State</option>
                                        <?php
                                        foreach($state_detail as $state) {
                                            if($state->state_id==old('state_name')) { ?>
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
                                    <label class="formlabel">District Name</label>
                                    <select id="district_name" class="frm_txt mdb-select inputs"  name="district_name">
                                        <option value="" >Choose District</option>
                                        <?php
                                        if(old('district_list')!='') {
                                            foreach(old('district_list') as $district) {
                                                if($district['dId']==old('district_name')) {
                                                ?>
                                                    <option selected="selected" value="{{$district['dId']}}">{{$district['dName']}}</option>
                                                <?php } else { ?>
                                                    <option value="{{$district['dId']}}">{{$district['dName']}}</option>
                                                <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="district_name" generated="true" class="error">{{ $errors->first('district_name') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Godown Name</label>
                                    <input type="text" id="godown_name" class="frm_txt inputs"  name="godown_name" placeholder="Godown Name" maxlength="50" value="{{ old('godown_name') }}">
                                    <div class="rerror"><label for="godown_name" generated="true" class="error">{{ $errors->first('godown_name') }}</label></div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Contact Person</label>
                                    <input type="text" id="contact_person" class="frm_txt inputs"  name="contact_person" placeholder="Contact Person" value="{{ old('contact_person') }}">
                                    <div class="rerror"><label for="contact_person" generated="true" class="error">{{ $errors->first('contact_person') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Contact Number</label>
                                    <input type="text" id="contact_number" class="frm_txt inputs"  name="contact_number" placeholder="Contact Number" value="{{ old('contact_number') }}">
                                    <div class="rerror"><label for="contact_number" generated="true" class="error">{{ $errors->first('contact_number') }}</label></div>
                                </div>
                                <!-- <div class="clearfix"></div> -->
                                <div class="col-lg-3">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                                    <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit inputs" id="reg" value="Create Godown" >
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-lg-12"><hr></div>
                                <div class="clearfix"></div>
                            </form>
                            <!-- <form role="form" method="post" class="search-form"  id="importgodown" action="{{url('godown/import-godowns')}}" enctype="multipart/form-data">
                                <div class="col-lg-3">
                                    <label class="formlabel">Upload file</label>
                                    <input type="file" name="godowndata" id="godowndata">
                                    <div class="rerror"><label for="godowndata" generated="true" class="error">{{ $errors->first('godowndata') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                    <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit inputs" id="reg" value="Import godowns" >
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-lg-12"><hr></div>
                                <div class="clearfix"></div>
                            </form> -->
                            
                            <table id="example1" class="table table-bordered table-striped datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>State</th>
                                        <th>District</th>
                                        <th>Godown</th>
                                        <th>Contact Person</th>
                                        <th>Contact Number</th>
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

<!-- ./wrapper --><!-- jQuery 2.1.4 -->
<script src="{{asset('assets/admin/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>

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

<!-- FastClick -->
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('assets/admin/dist/js/app.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->

<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
<script>    
$( document ).on('ready',function() {
    drawTable();
    $('#state_name').on('change',function(){
        var state_name   = $('#state_name').val();
        var data = {
            _token:     $(this).data('token'),
            state_name: state_name
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('tehsil/tehsil-district')}}",
            data:       data,
            success:    function (data) {
                            var jsonData1   = JSON.parse(data);
                            var appenddata1 = '';
                            $("#district_name").empty();
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
                        searchPlaceholder: "Godown Name"
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
                                    title: 'Godown List'
                                },
                        ],
                    "lengthMenu": [[25, 50, 100, -1], ['25 rows', '50 rows', '100 rows', 'All']],
                    "pageLength": 25,
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                                datatype:   'json',
                                type:       "POST",
                                url:        "{{url('godown/all-godownlist')}}",
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
                                { "data": "5" },
                                { 
                                    "render": function(data, type, row, meta){
                                       if(type === 'display'){
                                           data = '<a href="javascript:void(0);" id="editgodownStatus" data-token="{{csrf_token()}}" data-ticket="'+row[8]+'">'+row[6]+'</a>';
                                       }
                                       return data;
                                    }
                                },
                                { "data": "7" },
                                { 
                                    "render": function(data, type, row, meta){
                                       if(type === 'display'){
                                           data = '<a href="javascript:void(0);" id="editgodown" data-token="{{csrf_token()}}" data-ticket="'+row[8]+'"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
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

$("#creategodown").validate({
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
        godown: {
            required:   true,
        },
        contact_person: {
            required:   true,
        },
        contact_number: {
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

$("#importgodown").validate({
    errorPlacement: function (error, element) {
                        console.log($(element).next());
                        $(element).next('.rerror').html(error);
                        $(element).next().next('.rerror').html(error);
                    },
    ignore: "input[type='text']:hidden",
    rules: {
        godowndata: {
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

$(document.body).on('click', '#editgodown', function () {
    var _ticket =   $(this).data('ticket');
    window.location.assign("{{url('godown/edit-godown')}}/"+_ticket);
}); 

$(document.body).on('click', '#delgodown', function () {
    var r = confirm("Do you want delete the godown?");
    if(r==true){
        $(this).closest('tr').addClass('selected');
        var ele         = $(this);
        var data = {
            _token:     $(this).data('token'),
            _ticket:    $(this).data('ticket')
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('godown/godown-delete')}}",
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

$(document.body).on('click', '#editgodownStatus', function () {
    var r = confirm("Do you want change the Godown status?");
    if(r==true){
        var ele         = $(this);
        var data = {
            _token:     $(this).data('token'),
            _ticket:    $(this).data('ticket')
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('godown/update-godown-status')}}",
            data:       data,
            success:    function (data) {
                            if(data=='1') {
                                if(ele.text()=='ACTIVE') {
                                    var godownStatus  = 'INACTIVE';
                                }
                                else {
                                    var godownStatus  = 'ACTIVE';
                                }
                                ele.text(godownStatus);
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

