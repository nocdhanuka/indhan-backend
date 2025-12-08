@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Edit General User</h1>
            
            @if(Session::has('message'))
            <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            </div>
            @endif

            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Edit General User</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row"> 
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <?php
                            $user_role = Session::get('login_role_id');
                            if($user_role=='5') {
                            ?>
                            <form role="form" method="post" class="search-form"  id="createzonalmanager" action="{{url('admin/update-gen')}}"  > 
							                        
                                <div class="col-lg-3">
                                    <label class="formlabel">ECN</label>    
				    <input type="hidden" id="gen_id" class="frm_txt" value="{{$generalUser->general_id}}"  name="gen_id">

                                    <input type="text" disabled id="ECN" class="frm_txt" value="{{$generalUser->ECN}}"  name="ECN" placeholder="ECN Code" maxlength="10">
                                    @if ($errors->has('your_custom_error'))
                                        <div class="error">{{ $errors->first('your_custom_error') }}</div>
                                    @endif
                                    <div class="rerror"><label for="ECN" generated="true" class="error">{{ $errors->first('ECN') }}</label></div>
                                </div>
				
				<div class="col-lg-3"> 
                                <label class="formlabel">State</label>
                                <select id="state" class="frm_txt"  data-token="{{csrf_token()}}"  name="state">
                                    <option value="">Select State</option>
                                    <?php
                                    foreach($state_list as $state) { ?>
				    <option value="{{$state->state_id}}" <?php if($generalUser->state==$state->state_id) echo 'selected="selected"'; ?> >{{$state->state}}</option>
                                    <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="state" generated="true" class="error">{{ $errors->first('state') }}</label></div>
                            </div>
                                <div class="col-lg-3"> 
                                    <label class="formlabel">Name</label>
                                    <input type="text" id="zonal_name" class="frm_txt" name="general_user_name" placeholder="Name" value="{{$generalUser->general_user_name}}">   
                                    <div class="rerror"><label for="zonal_name" generated="true" class="error">{{ $errors->first('zonal_name') }}</label></div>
                                </div>
				<div class="col-lg-3">  
                                    <label class="formlabel">Mobile Number</label>
                                    <input type="text" id="zonal_mobile_number" class="frm_txt" name="general_user_mobile" placeholder="Mobile Number" maxlength="10" value="{{$generalUser->general_user_mobile}}">   
                                    <div class="rerror"><label for="zonal_mobile_number" generated="true" class="error">{{ $errors->first('zonal_mobile_number') }}</label></div>
                                </div>     
 				<div class="clearfix"></div>
                                <div class="col-lg-12"><hr></div>
                                <div class="clearfix"></div>

				<div class="col-lg-3">   
                                <label class="formlabel">Designation</label>
                                <!-- <input type="text" id="designation" class="frm_txt"  name="designation" placeholder="Designation"> -->
                                <select id="designation" class="frm_txt"  name="designation">
                                    <option <?php if($generalUser->designation == 'BDO-FL') { echo 'selected'; } ?> value="BDO-FL">BDO-FL</option>
                                    <option <?php if($generalUser->designation == 'SALES-FL') { echo 'selected'; } ?> value="SALES-FL">SALES-FL</option>
                                    <option <?php if($generalUser->designation == 'COUNTERPART-FL') { echo 'selected'; } ?> value="COUNTERPART-FL">COUNTERPART-FL</option>
				    <option <?php if($generalUser->designation == 'GENERAL-FL') { echo 'selected'; } ?> value="GENERAL-FL">GENERAL-FL</option>
                                </select>
                                <div class="rerror"><label for="designation" generated="true" class="error">{{ $errors->first('designation') }}</label></div>
                            </div>
                            <div class="col-lg-4 counterpart_fl" style="display: none;">  
                                <label class="formlabel">COUNTERPART-FL</label>
                                <!-- <input type="text" id="designation" class="frm_txt"  name="designation" placeholder="Designation"> -->
                                <select id="counterpart_fl" class="frm_txt"  name="counterpart_fl">
                                    <option value="">Select COUNTERPART-FL</option>
                                   
                                </select>
                                <div class="rerror"><label for="designation" generated="true" class="error">{{ $errors->first('designation') }}</label></div>
                            </div>
                                                                                           
                                <div class="clearfix"></div>                                
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                                <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Update General User">
                                
                                <div class="clearfix"></div>
                                <div class="col-lg-12"><hr></div>
                                <div class="clearfix"></div>
                            </form>
                            <?php
                            }
                            ?>
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


<script>
$(document.body).on('click', '#editZonalManager', function () {
    var _ticket =   $(this).data('ticket');
    window.location.assign("{{url('zonal/edit-zonal-manager')}}/"+_ticket);
});

$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});
</script>




<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function () {   
    jQuery.validator.addMethod("domain", function(value, element) {
        var isSuccess   = false;
        var bdo_code    = $('#ECN').val();
        $.ajax({ 
                url:    "{{url('zonal/check-ecn')}}", 
                data:   {'bdo_code':bdo_code, '_bdo': '0'}, 
                async:  false,
                type:   "POST",
                success: 
                        function(msg) { isSuccess = msg === "true" ? true : false }
                });
         return isSuccess;
    }, "ENC already exists, enter a new ECN.");

    $('.step2submit').keypress(function (e) {
        if (e.which == 13) {
            $("#createzonalmanager").click();
        }
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#createzonalmanager").validate({
        errorPlacement: function (error, element) {
                            console.log($(element).next());
                            $(element).next('.rerror').html(error);
                            $(element).next().next('.rerror').html(error);
                        },
        ignore: "input[type='text']:hidden",
        rules: {
            zonal_name: {
                required:   true,
                minlength:  4,
                maxlength:  50
            },
            zonal_mobile_number: {
                required:   true,
                minlength:  10,
                maxlength:  10,
                number: true
            },
           ECN: {
                required:   true,
                minlength:  4,
                maxlength:  10,
                //number:     true,
               // domain:     true
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
});




$(document.body).on('click', '#editZonalStatus', function () {
    var r = confirm("Do you want change the zonal manager status?");
    if(r==true){
        var ele         = $(this);
        var data = {
            _token:     $(this).data('token'),
            _ticket:    $(this).data('ticket')
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('zonal/update-zonal-status')}}",
            data:       data,
            success:    function (data) {
                            if(data=='1') {
                                if(ele.text()=='ACTIVE') {
                                    var zonalStatus  = 'INACTIVE';
                                }
                                else {
                                    var zonalStatus  = 'ACTIVE';
                                }
                                ele.text(zonalStatus);
                            }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    }
});

$(document.body).on('click', '#delZonalManager', function () {
    var r = confirm("Do you want delete the zonal manager?");
    if(r==true){
        $(this).closest('tr').addClass('selected');
        var ele         = $(this);
        var data = {
            _token:     $(this).data('token'),
            _ticket:    $(this).data('ticket')
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('zonal/zonal-delete')}}",
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
$(document).ready(function () {
    $('#designation').on('change', function () {
        $('.counterpart_fl').css('display','none');
        $("#counterpart_fl").prop('required',false);
        var designation =  $(this).val();
       if(designation == 'BDO-FL')
       {
        $('.counterpart_fl').css('display','block');
        $("#counterpart_fl").prop('required',true);
        $.ajax({
            type:       'GET',
            url:        "{{url('admin/counterpart-fl-list')}}",
            success:    function (data) {
                var data = $.parseJSON(data);
                var newFl = '<option value="">Select COUNTERPART-FL</option>'
                $.each(data, function (key,value) {
                    newFl += '<option value="'+value.id+'">'+value.name+'</option>'
                });
                $('#counterpart_fl').html(newFl);
            }

        })


       }
    })
})
</script>


































































































