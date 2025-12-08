@include('include.admin.backend_header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('include.admin.backend_sidebar')
    
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>Add Product Crop Recommendation</h1>
            
            @if(Session::has('message'))
            <div class="col-md-12" style="margin-top:20px;">
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            </div>
            @endif

            <ol class="breadcrumb">
                <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Add Product Crop Recommendation</li>
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
                                <div class="col-lg-3">
                                    <label class="formlabel">Category Name</label>
                                    <!--<input type="text" id="category_name" class="frm_txt inputs"  name="category_name" placeholder="Category Name" maxlength="50" value="{{ old('category_name') }}">
                                    <div id="auto"></div>-->
                                    <select id="category_name" class="frm_txt mdb-select inputs"  name="category_name">
                                        <option value="" >Choose Category</option>
                                        <?php
                                        foreach($category_detail as $category) { ?>
                                        <option value="{{$category->product_crop_category_id}}">{{$category->category_name}}</option>
                                        <?php    
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="category_name" generated="true" class="error">{{ $errors->first('category_name') }}</label></div>
                                </div>
                                <div class="col-lg-3"> 
                                    <label class="formlabel">Product</label>
                                    <select id="product_name" class="frm_txt mdb-select inputs"  name="product_name">
                                        <option value="" >Choose Product</option>
                                        <?php
                                        foreach($product_detail as $products) { ?>
                                        <option value="{{$products->product_id}}">{{$products->product}}</option>
                                        <?php    
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="product_name" generated="true" class="error">{{ $errors->first('product_name') }}</label></div>
                                </div>
                                <div class="col-lg-3"> 
                                    <label class="formlabel">Crop</label>
                                    <select id="crop_name" class="frm_txt mdb-select inputs" name="crop_name[]" multiple="multiple">
                                        <option value="" >Choose Crop</option>
                                        <?php
                                        foreach($crop_detail as $crops) { ?>
                                        <option value="{{$crops->crop_id}}">{{$crops->crop}}</option>
                                        <?php    
                                        }
                                        ?>
                                    </select>
                                    <div class="rerror"><label for="crop_name" generated="true" class="error">{{ $errors->first('crop_name') }}</label></div>
                                </div>
                                <div class="col-lg-3">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" /> 
                                    <input type="submit" name="submit" class="inputs btn btn-primary btn-lg gap5040 step2submit" id="reg" value="Create Relation" >
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
<!-- SlimScroll 
<script src="{{asset('assets/admin/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('assets/admin/dist/js/app.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>
<!-- page script -->







<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/jquery.validate.js" type="text/javascript"></script>
<script>
$.ajaxSetup({
   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});
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


//$('#category_name').keyup(function(e){
//    var input = $(this).val();
//    $.ajax({
//        type:       "post",
//        url:        "{{url('product/all-category')}}",
//        data:       {word: input},
//        async:      true,
//        beforeSend: function(){
//                        $("#category_name").css("background","#FFF url({{asset('assets/images/LoaderIcon.gif')}}) no-repeat 165px");
//                    },
//        success:    function(data){
//                        $("#category_name").css("background","#FFF url({{asset('assets/images/LoaderIcon.gif')}}) no-repeat 165px");
//                        $("#auto").show();
//                        $("#auto").html(data);
//                        $("#category_name").css("background","#FFF");
//                    }
//    });
//});
////To select country name
//function selectCategory(val) {
//    $("#category_name").val(val);
//    $("#auto").hide();
//}


$('.user-image').click(function(){
    $('.dropdown-menu').toggle();
});
</script>
