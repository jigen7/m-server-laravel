<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets_web/fonts/font-awesome.css') }}" />
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Lato:400,700" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets_web/bootstrap/css/bootstrap.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets_web/css/bootstrap-select.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets_web/css/owl.carousel.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets_web/css/jquery.nouislider.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets_web/css/style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets_web/css/user.style.css') }}" />

    @if (isset($stylesheets))
        @foreach ($stylesheets as $stylesheet)
            <link rel="stylesheet" href="{{ asset('assets_web/css/' . $stylesheets . '.css') }}" type="text/css" />
        @endforeach
    @endif

    <link rel="shortcut icon" href="{{ asset('assets_web/favicon/favicon.ico') }}" />
    <link rel="icon" sizes="16x16 32x32 64x64" href="{{ asset('assets_web/favicon/favicon.ico') }}" />
    <link rel="icon" type="image/png" sizes="196x196" href="{{ asset('assets_web/favicon/favicon-192.png') }}" />
    <link rel="icon" type="image/png" sizes="160x160" href="{{ asset('assets_web/favicon/favicon-160.png') }}" />
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets_web/favicon/favicon-96.png') }}" />
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('assets_web/favicon/favicon-64.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets_web/favicon/favicon-32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets_web/favicon/favicon-16.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('assets_web/favicon/favicon-57.png') }}" />
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets_web/favicon/favicon-114.png') }}" />
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets_web/favicon/favicon-72.png') }}" />
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets_web/favicon/favicon-144.png') }}" />
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('assets_web/favicon/favicon-60.png') }}" />
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets_web/favicon/favicon-120.png') }}" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets_web/favicon/favicon-76.png') }}/favicon-76.png" />
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets_web/favicon/favicon-152.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets_web/favicon/favicon-180.png') }}" />
    <meta name="msapplication-TileColor" content="#FFFFFF" />
    <meta name="msapplication-TileImage" content="{{ asset('assets_web/favicon/favicon-144.png') }}" />
    <meta name="msapplication-config" content="/browserconfig.xml" />

    @yield('head_content')

    <title>Masarap - The Delicious App</title>
</head>

<body onunload="" class="map-fullscreen page-homepage" id="page-top">
    <div id="fb-root"></div>

    <script>
        window.fbAsyncInit = function() {
            FB.init({
                appId: '{{ env('FACEBOOK_APP_ID') }}',
                cookie: true,
                xfbml: true,
                version: 'v2.3'
            });
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        } (document, 'script', 'facebook-jssdk'));
    </script>

    <div id="outer-wrapper">
        <div id="inner-wrapper">
            <div class="header">
                <div class="wrapper">
                    <div class="brand">
                        <a href="{{ URL::to('web') }}"><img src="{{ asset('assets_web/img/logo.png') }}" alt="Masarap" /></a>
                    </div>
                    @if (!Session::has('user_id'))
                    <nav class="navigation-items">
                        <div class="wrapper">
                            <ul class="user-area">
                                <li><a id="facebook-login" href="#"><strong>Log In With Facebook</strong></a></li>
                            </ul>
                        </div>
                    </nav>
                    @else
                    <div id="cssmenu">
                        <ul><li id="responsive-tab"><a href="#"><img src="{{ asset('assets_web/img/logo.png') }}" alt="logo"></a></li>
                            <li class="has-sub"><a class="username" href="{{ URL::to('web/user/' . Session::get('user_uuid')) }}"><span><img src="{{ Session::get('user_facebook_profile_photo') }}"><strong>{{ Session::get('user_fullname') }}</strong></span></a>
                                <ul>
                                    <li><a href="{{ URL::to('web/feed/following') }}"><span><img src="{{ asset('assets_web/icons/feed.png') }}" alt="" />Activity Feed</span></a></li>
                                    <li><a href="{{ URL::to('web/user/recently-viewed') }}"><span><img src="{{ asset('assets_web/icons/feed.png') }}" alt="" />Recently Viewed</span></a></li>
                                    <!--<li><a href="{{ URL::to('web/settings') }}"><span><img src="{{ asset('assets_web/icons/settings.png') }}" alt="" />Settings</span></a></li>-->
                                    <li class="last"><a href="{{ URL::to('web/logout') }}"><span><img src="{{ asset('assets_web/icons/logout.png') }}" alt="" />Logout</span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <ul id="notify-nav">
                            <li id="notification_li">
                                <span id="notification_count"></span>
                                <a href="#" id="notificationLink"><i><img src="{{ asset('assets_web/icons/notify.png') }}" alt=""></i></a>
                                <div id="notificationContainer">
                                    <div id="notificationTitle">Notifications</div>
                                    <div id="notificationsBody" class="notifications"></div>
                                    <div id="notificationFooter" class="hide-display"><a class="notify-link" href="{{ URL::to('web/notifications') }}">See All</a></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
            <div id="page-canvas">
                @yield('content')
            </div>
            <footer id="page-footer">
                <div class="inner">
                    <div class="footer-bottom">
                        <div class="container">
                            <span class="left">
                                <img src="{{ asset('assets_web/img/logo_footer.png') }}" alt="Masarap">
                                <span class="copyright-text">© 2015 Masarap, All Rights Reserved</span>
                                <a href="{{ URL::to('web/help') }}" class="footer-link">Help</a>
                                <a href="{{ URL::to('web/privacy_policy') }}" class="footer-link">Privacy Policy</a>
                            </span>
                            <span class="right">
                                <a href="#page-top" class="to-top roll"><i class="fa fa-angle-up"></i></a>
                            </span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script type="text/javascript" src="{{ asset('assets_web/js/jquery-2.1.0.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets_web/js/jquery-migrate-1.2.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets_web/bootstrap/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets_web/js/custom.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets_web/js/maps.js') }}"></script>

    <!--[if lte IE 9]>
    <script type="text/javascript" src="{{ asset('assets_web/js/ie-scripts.js') }}"></script>
    <![endif]-->

    @if (isset($javascripts))
        @foreach ($javascripts as $javascript)
            <script type="text/javascript" src="{{ asset('assets_web/js/' . $javascript . '.js') }}"></script>
        @endforeach
    @endif

    <script>
        $(window).load(function () {
            var rtl = false;
            initializeOwl(rtl);
        });
    </script>

    <div id="loading-overlay">
        <p>LOADING</p>
    </div>
</body>
</html>