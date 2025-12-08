<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="_token" content="{!! csrf_token() !!}"/>
        <title>Admin Dhanuka| Log in</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.5 -->
        <link rel="stylesheet" href="{{asset('assets/admin/bootstrap/css/bootstrap.min.css')}}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{asset('assets/admin/dist/css/AdminLTE.min.css')}}">
        <!-- iCheck -->
        <link rel="stylesheet" href="{{asset('assets/admin/plugins/iCheck/square/blue.css')}}">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <?php
        /*if (Cookie::get('name') !== null) {
            $username = Cookie::get('name');
            //echo $username;
        }
        if (Cookie::get('pass') !== null) {
            $password = Cookie::get('pass');
            //echo $password;
        }*/
     ?>
    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
                <span class="logo-lg"> <img src="{{asset('assets/images/logo.png')}}"/ ></span>
            </div><!-- /.login-logo -->
            <div class="login-box-body">
                <p class="login-box-msg">Sign in to start your session</p>

                <form role="form" id="ad_login" method="post" action="" autocomplete="off">
                    <div class="form-group has-feedback">
                        <input type="text" placeholder="Username" id="username" name="username" class="form-control" value="" autocomplete="off" required>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" value="" autocomplete="off" required>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="row">
                        <div class="col-xs-8">
                            <div class="checkbox icheck">
                                <label>
                                    <input name="remember" id="remember" type="checkbox" <?php if (isset($password) && !empty($password)){ echo 'checked';} ?>
                                     value="1"> Remember Me
                                </label>
                            </div>
                        </div><!-- /.col -->
                        <div class="col-xs-4">
                            <!-- <input type="submit" class="btn btn-primary btn-block btn-flat log_admin" value="Sign In"> -->
                            <a type="button" class="btn btn-primary btn-block btn-flat log_admin btnSubmit">Sign In</a>
                        </div><!-- /.col -->
                    </div>
                    <div class="msg_ad"></div>
                </form>

            </div><!-- /.login-box-body -->
        </div><!-- /.login-box -->

        <!-- jQuery 2.1.4 -->
        <script src="{{asset('assets/admin/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
        <!-- Bootstrap 3.3.5 -->
        <script src="{{asset('assets/admin/bootstrap/js/bootstrap.min.js')}}"></script>
        <!-- iCheck -->
        <script src="{{asset('assets/admin/plugins/iCheck/icheck.min.js')}}"></script>



        <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
        </script>

        <script>
            $(document).ready(function () {
                $(".btnSubmit").click(function () {
                    var value = $("#ad_login").serialize();console.log(value);
                    $.ajax({
                        type: 'POST',
                        url: "{{ url('admin/admin-login') }}",
                        data: value,
                        success: function (login) {
                            //console.log(login);
                            $(".msg_ad").show();
                            if (login == 0 || login == '0' ) {
                                $(".msg_ad").html('<div class="alert alert-danger">Login Failed</div>');
                                setTimeout(function () {
                                    $(".msg_ad").hide();
                                }, 3000);
                                return false;
                            } else {
                                $(".msg_ad").hide();
                                window.location = "{{ url('admin/dashboard') }}";
                            }
                        }
                    });
                   
                });


            });

            $(document).on("keypress", function (e) {
                if (e.keyCode == 13) {
                    var value = $("#ad_login").serialize();
                    $.ajax({
                        type: 'POST',
                        url: "{{ url('admin/admin-login') }}",
                        data: value,
                        success: function (login) {
                            //console.log(login);
                            $(".msg_ad").show();
                            if (login == 0 || login == '0' ) {
                                $(".msg_ad").html('<div class="alert alert-danger">Login Failed</div>');
                                setTimeout(function () {
                                    $(".msg_ad").hide();
                                }, 3000);
                                return false;
                            } else {
                                $(".msg_ad").hide();
                                window.location = "{{ url('admin/dashboard') }}";
                            }
                        }
                    });
                    return false;
                }
            });
            /*ajax end here*/
            $.ajaxSetup({
                headers: {'X-CSRF-Token': $('meta[name=_token]').attr('content')}
            });
        </script>

        <script>
            /*$(document).ready(function(){
                $("#username").autocomplete("disable"); 
                $("#password").autocomplete("disable"); 
            });*/
        </script>

    </body>
</html>
