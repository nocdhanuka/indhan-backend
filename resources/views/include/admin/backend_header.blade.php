
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="_token" content="{!! csrf_token() !!}"/>
        <title>Dhanuka Agritech Ltd</title>


        <link rel="shortcut icon" href="{{asset('assets/images/favicon.ico')}}" type="image/x-icon">
        <link rel="icon" href="{{asset('assets/images/favicon.ico')}}" type="image/x-icon">
    
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.5 -->
        <link rel="stylesheet" href="{{asset('assets/admin/bootstrap/css/bootstrap.min.css')}}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{asset('assets/admin/plugins/datatables/dataTables.bootstrap.css')}}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{asset('assets/admin/dist/css/AdminLTE.min.css')}}">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="{{asset('assets/admin/dist/css/skins/_all-skins.min.css')}}">
        <link rel="stylesheet" href="{{asset('assets/css/jquery-ui.min.css')}}">
        <link rel="stylesheet" href="{{asset('assets/css/jquery-ui.theme.min.css')}}">
         <link rel="stylesheet" href="{{asset('assets/css/bootstrap-multiselect.css')}}">
        <link rel="stylesheet" href="{{asset('assets/admin/custom.css')}}">
          <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.10/css/bootstrap-select.css">     
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        
        <style>
            .text-wrap{
                white-space:normal;
                word-wrap:break-word !important;
            }
            .width-200{
                width:200px;
            } 
            .info-box-icon {
                height: 145px !important;
            }
            .info-box {
                min-height: 145px !important;
            }
        </style>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                <?php
                $getimage = \DB::table('settings')->get();
                foreach ($getimage as $image) {
                    $logo = $image->image;
                }
                ?>
                <!-- Logo -->
                <a href="dashboard" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini"><b>A</b>LT</span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg"> <img src="{{asset($logo)}}"/ ></span>

                </a>
                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top left-side_green" role="navigation">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>


                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <?php
                            $role_sess      = \Session::get('login_role_id');
                            $adminid        = \Session::get('adminid');
                            $admin_details  = \DB::table('admin')
                                                ->where('role_id', $role_sess)
                                                ->get();
                            if(count($admin_details) > 0)
                            {
                                foreach ($admin_details as $details) {
                                    $role = $details->username;
                                } 
                            }
                            else
                            {
                                 $admin_details  = \DB::table('branch_admin')
                                                ->where('id', $adminid)
                                                ->get();
                            
                                   foreach ($admin_details as $details) {
                                    $role = $details->user_name;
                                } 
                          
                            }
                            
                            ?>
                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="{{asset('assets/images/default_profile.jpg')}}" class="user-image" alt="User Image">
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header">
                                        <img src="{{asset('assets/images/default_profile.jpg')}}" class="img-circle" alt="User Image">
                                        <p><?php echo $role; ?></p>
                                    </li>
                                    <!-- Menu Body -->

                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="{{URL::to('admin/change-password')}}" class="btn btn-default btn-flat">Change Password</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="{{URL::to('admin/logout')}}" class="btn btn-default btn-flat">Sign out</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!-- Control Sidebar Toggle Button -->

                        </ul>
                    </div>
                </nav>
            </header>
