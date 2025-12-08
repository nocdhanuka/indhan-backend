<!DOCTYPE html>
<html>
    <head>
        <title>Guurso</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=9;IE=10;IE=Edge,chrome=1"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        
        <link rel="shortcut icon" href="{{asset('assets/images/favicon.ico')}}" type="image/x-icon">
        <link rel="icon" href="{{asset('assets/images/favicon.ico')}}" type="image/x-icon">
        <!--[if IE]>
          <script src="js/html5shiv.js"></script>
        <![endif]-->

        <!--[if lte IE 7]>
          <script src="js/IE8.js" type="text/javascript"></script>
        <![endif]-->

        <!--[if lt IE 7]>
          <link rel="stylesheet" type="text/css" media="all" href="stylesheets/ie8.css"/>
        <![endif]-->

        <link rel="stylesheet" type="text/css" href="{{asset('assets/css/bootstrap.min.css')}}"> 
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" 
              integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="{{asset('assets/css/main.css')}}">
<!--        <link rel="stylesheet" type="text/css" href="{{asset('assets/css/main-shyam.css')}}">-->
        <link rel="stylesheet" type="text/css" href="{{asset('assets/css/animate.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('assets/css/layout.css')}}">
                
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="  crossorigin="anonymous"></script>
        <script src="{{ asset('assets/js/jquery.scrollToTop.min.js') }}" type="text/javascript" ></script>
        <script src="{{ asset('assets/js/script.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
        
        <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
        
    </head>

    <?php
    $class = "";
    $headerClass = "";
    $route_name = Route::getCurrentRoute()->getName();
    if ($route_name == "accountdetails" || $route_name == "blogs") {
        $class = "class='blog-page-body'";
        $headerClass = "blog-page-header";
    }
    ?>
    <body <?php echo $class; ?> >
        <header class="header-strip stickyheader<?php echo $headerClass; ?>">
            <section class="row-inner">

                <div class="logo">
                    <a href="{{ url('/') }}">
                        <picture>
                            <source media="(min-width: 736px)" srcset="{{ asset('assets/images/guurso-logo.png') }}">  
                            <img src="{{ asset('assets/images/guurso-logo.png') }}" title="logo">
                        </picture>
                    </a>
                </div>

                <div class="menu-login">
                    <div class="login">
                        <a href="javascript:void(0);"  data-toggle="modal" data-target="#myModalSignIn">
                            <img src="{{ asset('assets/images/sign-in.png') }}"><span>LOG IN</span>
                        </a>
                    </div>
                    <div class="menu a animated burgger-menu">
                        <div class="nav">
                            <?php
                            if (Route::getCurrentRoute()->uri() == '/') { ?>
                            <ul>
                                <li><a href="#howitworks">How it works?</a></li>
                                <li><a href="#pricingplan">Pricing Plan</a></li>
                                <li><a href="#blog">Blog</a></li>                                
                                <li><a href="{{url('about-us')}}">About Us</a></li>
                                <li><a href="{{url('contact-us')}}">Contact Us</a></li>
                            </ul>
                            <?php } else { ?>
                            <ul>
                                <li><a href="{{ url('/') }}">Home</a></li>
                                <li><a href="{{url('about-us')}}">About Us</a></li>
                                <li><a href="{{url('contact-us')}}">Contact Us</a></li>
                            </ul>
                            <?php } ?>
                        </div>
                    </div>

                </div>
                <?php
                if (Route::getCurrentRoute()->uri() == '/') { ?>
                    <a href="javascript:void('0')" class="bugger"><i class="fa fa-bars"></i></a>
                <?php } ?>
            </section> 
        </header>

<script>
    $(document.body).on('click', '.login', function () {
        $('.pop_msg_bkg').css('display','none');
        $('.pop_msg').css('display','none');
    });
</script>






<script type="text/javascript">

//var getLocation = function(href) {
//    var l = document.createElement("a");
//    l.href = href;
//    return l;
//};
//var l           = getLocation(window.location.href);
//var pathArray   = l.pathname.split( '/' );
//
//var blogPage    = 0;
//if(pathArray.includes('blog')) {
//    
//}
//else if(pathArray.includes('blogs')) {
//    
//}
//else {
//    window.onload = function () {
//        if (typeof history.pushState === "function") {
//            history.pushState("jibberish", null, null);
//            window.onpopstate = function () {
//                history.pushState('/', null, null);
//                // Handle the back (or forward) buttons here
//                // Will NOT handle refresh, use onbeforeunload for this.
//            };
//        }
//        else {
//            var ignoreHashChange = true;
//            window.onhashchange = function () {
//                if (!ignoreHashChange) {
//                    ignoreHashChange = true;
//                    window.location.hash = Math.random();
//                    // Detect and redirect change here
//                    // Works in older FF and IE9
//                    // * it does mess with your hash symbol (anchor?) pound sign
//                    // delimiter on the end of the URL
//                }
//                else {
//                    ignoreHashChange = false;
//                }
//            };
//        }
//    };
//}
</script> 
