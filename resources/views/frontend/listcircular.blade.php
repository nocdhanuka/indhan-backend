<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="_token" content="{!! csrf_token() !!}"/>  
        <title>Testimonial | {{ $circular_name or '' }}</title>
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
        <style type="text/css">
        .not-found {
            height: 100px;
            font-size: 20px;
            padding: 20%;
            color: red;
        }
        .description {
            padding: 40px 0 20px;
            font-size: 18px;
            /*text-align: justify;*/
        }
        /*.testimonial-image {
            padding: 0 15px;
        }*/
        .heading {
            padding-bottom: 30px;
        }
        </style>
    </head>
    <body class="hold-transition login-page" style="background: none !important;">
        <div class="container">
            <div class="row">
                <div class="col-sm-12" style="background-color: #FDF3F0;">
                    <div class="col-sm-2">&nbsp;</div>
                    <div class="col-sm-8">
                        <div class="login-box">
                            <div class="login-logo">
                                <span class="logo-lg"><img src="{{asset('assets/images/logo.png')}}"/ style="width: 100%;"></span>
                            </div><!-- /.login-logo -->
                        </div><!-- /.login-box -->
                    </div>
                    <div class="col-sm-2">&nbsp;</div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-2">&nbsp;</div>
                    <div class="col-sm-8" style="text-align: center;">
                        <?php if($exist == 1) { ?>
                            <div class="heading">
                                <h1 style="text-align: center; color: #18AA5C;">{{$circular_name}}</h1>
                                <span style="color: #EF4B4F; font-weight: bold;">{{$published_date}}</span>
                            </div>
                            <?php
                            foreach ($testimonialList as $key => $testimonial) {
                                $image_video        = $testimonial->image_video;
                                $testimonial_video  = $base_url.$image_video;
                                $testimonialInfo    = pathinfo($testimonial_video);
                                $testimonialExt     = (isset($testimonialInfo['extension'])) ? strtolower($testimonialInfo['extension']) : 'mp4';
                                if(strlen($testimonial->description)>90) {
                                    $style = "justify";
                                } else {
                                    $style = "justify";
                                }
                                ?>
                                <div class="col-sm-12">
                                    <div class="description" style="text-align: {{$style}}">{{$testimonial->description}}</div>
                                    <div class="testimonial-image images">
                                        <?php if(in_array($testimonialExt,$image_arr)) { ?>
                                            <img src="{{$testimonial_video}}" style="width: 100%; height: 50%;"/>
                                        <?php } else if(in_array($testimonialExt, $video_arr)) {
                                            $testimonialExt = ($testimonialExt == 'mov') ? 'mp4' : $testimonialExt;
                                            ?>
                                            <video width="100%" height="50%" controls>
                                                <source src="{{$testimonial_video}}" type="video/{{$testimonialExt}}">
                                                Your browser does not support the video tag.
                                            </video>
                                        <?php } else { ?>
                                            <img src="{{url('assets/settingsimages/logo.png')}}" style="width: 100%; height: 50%;" />
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            <?php
                            }
                            ?>
                        <?php } else { ?>
                            <div class="not-found">
                                Circular waiting for administrator approval.
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-2">&nbsp;</div>
                </div>
            </div>
        </div>
        <!-- jQuery 2.1.4 -->
        <script src="{{asset('assets/admin/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
        <!-- Bootstrap 3.3.5 -->
        <script src="{{asset('assets/admin/bootstrap/js/bootstrap.min.js')}}"></script>
        <!-- iCheck -->
        <script src="{{asset('assets/admin/plugins/iCheck/icheck.min.js')}}"></script>
    </body>
</html>
