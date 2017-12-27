<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <link rel="stylesheet" href="{{ asset('/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('/css/jqueryui.css') }}" />
    <link rel="stylesheet" href="{{ asset('/css/styles.css') }}" />
</head>
<body>
    <h4>Reported Restaurant Details</h4>
    <table class="table table-striped">
        <tr>
            <td>ID</td>
            <td>{{ $restaurant['id'] }}</td>
        </tr>
        <tr>
            <td>Name</td>
            <td>{{ $restaurant['name'] }}</td>
        </tr>
        <tr>
            <td>Address</td>
            <td>{{ $restaurant['address'] }}</td>
        </tr>
        <tr>
            <td>Contact No.</td>
            <td>{{ $restaurant['telephone'] }}</td>
        </tr>
        <tr>
            <td>Budget</td>
            <td>{{ $restaurant['budget'] }}</td>
        </tr>
        <tr>
            <td>Rating</td>
            <td>{{ $restaurant['rating'] . " / 5" }}</td>
        </tr>
        <tr>
            <td>View Count</td>
            <td>{{ $restaurant['view_count'] }}</td>
        </tr>
        <tr>
            <td>Operating Time</td>
            <td>{{ $restaurant['operating_time'] }}</td>
        </tr>
        <tr>
            <td>Latitude</td>
            <td>{{ $restaurant['latitude'] }}</td>
        </tr>
        <tr>
            <td>Longitude</td>
            <td>{{ $restaurant['longitude'] }}</td>
        </tr>
        <tr>
            <td>Thumbnail</td>
            <td>{{ $restaurant['thumbnail'] }}</td>
        </tr>
        <tr>
            <td>Accepts credit card</td>
            <td>{{ $restaurant['credit_card'] }}</td>
        </tr>
        <tr>
            <td>Smoking</td>
            <td>{{ $restaurant['smoking'] }}</td>
        </tr>
        <tr>
            <td>24 Hours Open</td>
            <td>{{ $restaurant['is_24hours'] }}</td>
        </tr>
        <tr>
            <td>Accepts Dine-in</td>
            <td>{{ $restaurant['can_dinein'] }}</td>
        </tr>
        <tr>
            <td>Accepts Take-out</td>
            <td>{{ $restaurant['can_dineout'] }}</td>
        </tr>
        <tr>
            <td>Accepts Delivery</td>
            <td>{{ $restaurant['can_deliver'] }}</td>
        </tr>
        <tr>
            <td>Already Closed</td>
            <td>{{ $restaurant['status_close'] }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>{{ $restaurant['status_verify'] }}</td>
        </tr>
        <tr>
            <td>User ID</td>
            <td>{{ $restaurant['user_id'] }}</td>
        </tr>
    </table>

    <script src="{{ asset('/js/jquery.js') }}"></script>
    <script src="{{ asset('/js/jqueryui.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.js') }}"></script>
    <script src="{{ asset('/js/cms-script.js') }}"></script>
    <script src="{{ asset('/js/modernizr.js') }}"></script>
</body>