@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Edit Target List</h1>
            @if(Session::has('message'))
            <div class="col-md-12" style="margin-top:20px;">
                <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            </div>
            @endif
            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Edit Target</li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <form role="form" method="post" class="search-form"  id="updatetarget" action="{{url('influencer/update-target')}}">
                                <div class="col-lg-3">
                                    <label class="formlabel">User Type</label>
                                    <select name="user_type" id="user_type" class="frm_txt mdb-select inputs" disabled="disabled">
                                        <option value="{{$targetdetail->user_type}}">{{$user_types[$targetdetail->user_type]}}</option>
                                    </select>
                                    <div class="rerror">
                                        <label for="user_type" generated="true" class="error">{{ $errors->first('user_type') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">User</label>
                                    <select id="user_name" class="frm_txt mdb-select inputs" name="user_name" disabled="disabled">
                                        <option value="{{$targetdetail->user_ecn}}">{{$targetdetail->user_name}}</option>
                                    </select>
                                    <div class="rerror">
                                        <label for="user_name" generated="true" class="error">{{ $errors->first('user_name') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Month</label>
                                    <select id="month" class="frm_txt mdb-select inputs"  name="month">
                                        <option selected="selected" value="{{$targetdetail->target_month}}">{{$targetdetail->target_month}}</option>
                                    </select>
                                    <div class="rerror">
                                        <label for="month" generated="true" class="error">{{ $errors->first('month') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Year</label>
                                    <select id="year" class="frm_txt mdb-select inputs"  name="year">
                                        <option selected="selected" value="{{$targetdetail->target_year}}">{{$targetdetail->target_year}}</option>
                                    </select>
                                    <div class="rerror">
                                        <label for="year" generated="true" class="error">{{ $errors->first('year') }}</label>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Category A Target</label>
                                    <input type="text" id="category_a_target" class="frm_txt inputs"  name="category_a_target" placeholder="A Target" value="{{ $targetdetail->category_a_target }}">
                                    <div class="rerror">
                                        <label for="category_a_target" generated="true" class="error">{{ $errors->first('category_a_target') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Category B Target</label>
                                    <input type="text" id="category_b_target" class="frm_txt inputs"  name="category_b_target" placeholder="B Target" value="{{ $targetdetail->category_b_target }}">
                                    <div class="rerror">
                                        <label for="category_b_target" generated="true" class="error">{{ $errors->first('category_b_target') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Category C Target</label>
                                    <input type="text" id="category_c_target" class="frm_txt inputs"  name="category_c_target" placeholder="C Target" value="{{ $targetdetail->category_c_target }}">
                                    <div class="rerror">
                                        <label for="category_c_target" generated="true" class="error">{{ $errors->first('category_c_target') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="formlabel">Office Type {{$targetdetail->office_type}}</label>
                                    <select name="office_type" id="office_type" class="frm_txt mdb-select inputs">
                                        <option value="1" <?php if($targetdetail->office_type ==1) { echo "selected='selected'"; } ?>>Head Office</option>
                                        <option value="2" <?php if($targetdetail->office_type ==2) { echo "selected='selected'"; } ?>>Branch Office</option>
                                    </select>
                                    <div class="rerror"><label for="years" generated="true" class="error">{{ $errors->first('years') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <input type="hidden" name="target_id" id="target_id" value="{{ $targetdetail->target_id }}" />
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                                    <input type="submit" name="submit" class="btn btn-primary btn-lg gap5040 step2submit inputs" id="reg" value="Update" />
                                    <input type="button" name="reset" class="btn btn-primary btn-lg gap5040 inputs" id="reset" value="Reset" />
                                </div>
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
$.ajaxSetup({
   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});
$("#updatetarget").validate({
    errorPlacement: function (error, element) {
                        console.log($(element).next());
                        $(element).next('.rerror').html(error);
                        $(element).next().next('.rerror').html(error);
                    },
    ignore: "input[type='text']:hidden",
    rules: {
        category_a_target: {
            required:   true
        },
        category_b_target: {
            required:   true
        },
        category_c_target: {
            required:   true
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
