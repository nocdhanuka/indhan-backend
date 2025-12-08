
      @include('include.admin.backend_header')
      <!-- Left side column. contains the logo and sidebar -->
     
          @include('include.admin.backend_sidebar')
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Change Password
            
          </h1>
          <ol class="breadcrumb">
            <li><a href="{{URL::to('admin/dashboard')}}"><i class="fa fa-dashboard"></i> Home</a></li>
          
            <li class="active">Change Password</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <!-- left column -->
            <div class="col-md-12">
              <!-- general form elements -->
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Form</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <form role="form" id="change_password">
                  <div class="box-body">
                    <div class="form-group">
                      <label>Current Password</label>
                      <input type="password" class="form-control" name="current_password" placeholder="Enter Current Password"  required>
                    </div>
                   
                       <div class="form-group">
                      <label>New Password</label>
                      <input type="password" class="form-control" name="new_password" placeholder="Enter New Password"  required>
                    </div>
                      
                        <div class="form-group">
                      <label>Confirm Password</label>
                      <input type="password" class="form-control" name="confirm_password" placeholder="Enter Confirm Password"  required>
                    </div>
                     
                  </div><!-- /.box-body -->

                  <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </form>
                  <div class="message"></div>
              </div><!-- /.box -->

            </div><!--/.col (left) -->

          </div>   <!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      @include('include.admin.footer')

      <!-- Control Sidebar -->
     <!-- /.control-sidebar -->
      <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
      <div class="control-sidebar-bg"></div>
    </div><!-- ./wrapper -->

    <!-- jQuery 2.1.4 -->
    <script src="{{asset('assets/admin/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="{{asset('assets/admin/bootstrap/js/bootstrap.min.js')}}"></script>
    <!-- FastClick -->
    <script src="{{asset('assets/admin/plugins/fastclick/fastclick.min.js')}}"></script>
    <!-- AdminLTE App -->
    <script src="{{asset('assets/admin/dist/js/app.min.js')}}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{asset('assets/admin/dist/js/demo.js')}}"></script>


 <script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
    <script type="text/javascript">
   
$(document).ready(function(){
						  $('#change_password').validate({	
				rules: {
					
			current_password:"required",
        	new_password:"required",
            confirm_password:"required",
                    
        },
        highlight: function(element) {
            $(element).addClass('red');
        },
        unhighlight: function(element) {
            $(element).removeClass('red');
        },
		submitHandler: function(form) {
  

  		var value =$("#change_password").serialize() ;
 

		$.ajax({
		type:'POST',
        url: "{{ url('admin/password-change') }}",
		data: value,
		success:function (password){
       $(".message").show();
        console.log(password);
												
            if(password==1)
            {
				
        $(".message").html('<div class="alert alert-success">Successfully Change Your Password</div>'); 
          setTimeout(function(){$(".message").hide(); }, 3000);
          

             }
               else if(password==2)
               {
        $(".message").html('<div class="alert alert-danger">newpassword and confirm password are  not correct</div>'); 
          setTimeout(function(){$(".message").hide(); }, 3000);
        }
         else if(password==3)
               {
        $(".message").html('<div class="alert alert-danger">currentpassword not correct</div>'); 
          setTimeout(function(){$(".message").hide(); }, 3000);
        }
          else{
        $(".message").html('<div class="alert alert-danger">error</div>'); 
          setTimeout(function(){$(".message").hide(); }, 3000);
				}
            }
										});
		}
		});
		});
			  /*ajax end here*/
$.ajaxSetup({
   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});
</script>

  </body>
</html>
