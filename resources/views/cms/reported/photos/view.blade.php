<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <link rel="stylesheet" href="{{ asset('/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('/css/jqueryui.css') }}" />
    <link rel="stylesheet" href="{{ asset('/css/styles.css') }}" />
</head>
<body>
    <h4>Reported Photo Details</h4>
    <table class="table table-striped">
        @foreach ($photo as $key => $value)
            <tr>
                <td>{{ ucfirst($key) }}</td>
                <td>{{ $value }}</td>
            </tr>
        @endforeach
    </table>

    <script src="{{ asset('/js/jquery.js') }}"></script>
    <script src="{{ asset('/js/jqueryui.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.js') }}"></script>
    <script src="{{ asset('/js/cms-script.js') }}"></script>
    <script src="{{ asset('/js/modernizr.js') }}"></script>
</body>