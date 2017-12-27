@extends('cms.layouts.master')
@section('content')
    <h2>Restaurants - {{ $restaurant['name'] }}</h2>
    @if (Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    @if (!$restaurant)
        <div class="alert alert-danger">No records found.</div>
    @else
        <div class="row">
            <div class="col-lg-6">
                <table class="table table-striped">
                    @foreach ($restaurant as $key => $value)
                        <tr>
                            <td><b>{{ $key }}</b></td>
                            <td>{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    @endif
@stop