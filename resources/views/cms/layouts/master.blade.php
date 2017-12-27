<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="description" content="Masarap CMS {{ isset($page_title) ? '- ' . $page_title : '' }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Masarap CMS {{ isset($page_title) ? '- ' . $page_title : '' }}</title>
    <link rel="stylesheet" href="{{ asset('/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('/css/jqueryui.css') }}" />
    <link rel="stylesheet" href="{{ asset('/css/cms-styles.css') }}" />
    <link rel="stylesheet" href="{{ asset('/css/popup.css') }}" />
    <script src="{{ asset('/js/modernizr.js') }}"></script>
</head>
<body>
    @if (isset($stylesheets))
        @foreach ($stylesheets as $stylesheet)
            <link rel="stylesheet" href="{{ asset('/css/' . $stylesheet . '.css') }}" />
        @endforeach
    @endif
    <div id="header-div">
        @include('cms.layouts.header')
    </div>
    <div class="container-fluid" id="content-div">
        <div class="row">
            <div class="col-sm-2 col-md-2" id="sidebar-div">
                @include('cms.layouts.sidebar')
            </div>
            <div class="col-sm-10 col-sm-offset-3 col-md-10 col-md-offset-2" id="main-cont-div">
                @yield('content')
            </div>
        </div>
    </div>
    <div id="footer-div">
        @include('cms.layouts.footer')
    </div>
    <script src="{{ asset('/js/jquery.js') }}"></script>
    <script src="{{ asset('/js/jqueryui.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.js') }}"></script>
    <script src="{{ asset('/js/cms-script.js') }}"></script>
    <script src="{{ asset('/js/jquery.popup.min.js') }}"></script>
    @if (isset($javascripts))
        @foreach ($javascripts as $javascript)
    <script src="{{ asset('/js/' . $javascript . '.js') }}"></script>
        @endforeach
    @endif
</body>
</html>