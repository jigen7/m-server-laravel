@extends('cms.layouts.master')
@section('content')
    <h2>Photos</h2>
    @if (count($errors))
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @else
        <div class="row">
            <div class="col-lg-6">
                <table class="table table-striped">
                    <tr>
                        <td><b>ID</b></td>
                        <td>{{ $photo['id'] }}</td>
                    </tr>
                    <tr>
                        <td><b>Type</b></td>
                        <td>{{ $photo['type'] }}</td>
                    </tr>
                    <tr>
                        <td><b>Type ID</b></td>
                        <td>{{ $photo['type_id'] }}</td>
                    </tr>
                    <tr>
                        <td><b>Restaurant ID</b></td>
                        <td>{{ $photo['restaurant_id'] }}</td>
                    </tr>
                    <tr>
                        <td><b>URL</b></td>
                        <td>{{ $photo['url'] }}</td>
                    </tr>
                    <tr>
                        <td><b>Text</b></td>
                        <td>{{ $photo['text'] }}</td>
                    </tr>
                    <tr>
                        <td><b>Status</b></td>
                        <td>{{ $photo['status'] }}</td>
                    </tr>
                    <tr>
                        <td><b>Points</b></td>
                        <td>{{ $photo['points'] }}</td>
                    </tr>
                    <tr>
                        <td><b>User ID</b></td>
                        <td>{{ $photo['user_id']  }}</td>
                    </tr>
                    <tr>
                        <td><b>Data Uploaded</b></td>
                        <td>{{ $photo['date_uploaded'] }}</td>
                    </tr>
                </table>
            </div>
        </div>
    @endif
@stop