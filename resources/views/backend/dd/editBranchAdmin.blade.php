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
        <h1>Edit Branch Admin</h1>

        @if(Session::has('message'))
        <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        </div>
        @endif

        <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Edit Branch Admin</li>
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
                       // echo "<pre>"; print_r($branch);
                        ?>
                        <div class="col-md-offset-1 col-md-8 col-xs-12">
                        <form role="form" method="post" class="search-form"  id="updateBranch" action="{{url('admin/update-branch')}}">
                            <div class="row">
                           <div class="col-lg-6"> 
                                <label class="formlabel"> User Name</label>
                                <input type="hidden" name="id" value="{{$branch->id}}">
                                <input type="text" id="user_name" class="user_name form-control"  name="user_name" placeholder="User Name" value="{{$branch->user_name}}">
                                <div class="rerror"><label for="user_name" generated="true" class="error">{{ $errors->first('user_name') }}</label></div>
                            </div>
                            <div class="col-lg-6"> 
                                <label class="formlabel">Branch Name</label>
                                <input type="text" id="branch_name" class="frm_txt form-control"  name="branch_name" placeholder="Branch Name" value="{{$branch->branch_name}}">
                                <div class="rerror"><label for="branch_name" generated="true" class="error">{{ $errors->first('branch_name') }}</label></div>
                            </div>
                            
                            </div>
                            <div class="row">
                            <div class="col-lg-6"> 
                                <label class="formlabel">SelectState</label>
                                     
                                    <?php
                                     $selected = json_decode($branch->state);
                                    
                                    foreach($state_list as $state) { ?>
                                     <input type="checkbox"  id="state" data-token="{{csrf_token()}}"  name="state[]" 
                                      <?php if(in_array($state->state_id,$selected)) {echo "checked"; } ?>  value="{{$state->state_id}}"> {{$state->state}}> 
                                 <?php    
                                    }
                                    ?>
                                </select>
                                <div class="rerror"><label for="state" generated="true" class="error">{{ $errors->first('state') }}</label></div>
                            </div>
                            
                            <div class="col-lg-6"> 
                                <label class="formlabel"> Administrator Name</label>
                                <input type="text" id="administrator_name" class="frm_txt form-control"  name="administrator_name" placeholder="Name" value="{{$branch->administrator_name}}">
                                <div class="rerror"><label for="administrator_name" generated="true" class="error">{{ $errors->first('administrator_name') }}</label></div>
                            </div>
                            </div>

                            
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                            <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Create Branch Admin" >
                        </form>
                    </div>
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
$(document).ready(function () {
    
    $("#updateBranch").validate({
        errorPlacement: function (error, element) {
                            console.log($(element).next());
                            $(element).next('.rerror').html(error);
                            $(element).next().next('.rerror').html(error);
                        },
        ignore: "input[type='text']:hidden",
        rules: {
            user_name: {
                required:   true
            },
            
            state: {
                required:   true,
            },
            administrator_name: {
                required:   true,
            },
            branch_name: {
                required:   true,
            },
            zonal: {
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

    $('#state').on('change',function(){
        var state   = $('#state').val();
        var data = {
            _token: $(this).data('token'),
            state:  state
        }
        $.ajax({
            type:       'POST',
            url:        "{{url('district/state-zonal')}}",
            data:       data,
            success:    function (data) {
                            if(data) {
                                $("#zonal").empty();
                                var jsonData1   = JSON.parse(data);
                                if(jsonData1.zonal!='undefined') {
                                    var appenddata1 = '';
                                    appenddata1 += "<option value = ''>Select Zonal Manager</option>";
                                    for (var i = 0; i < jsonData1.zonal.length; i++) {
                                        appenddata1 += "<option value = '" + jsonData1.zonal[i].zonal_manager_id + "'>" + jsonData1.zonal[i].zonal_manager_name + " </option>";
                                    }
                                    $("#zonal").append(appenddata1);
                                }
                                return false;
                            }
                        },
            error:      function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
        });
    });
});
</script>


