@extends('cms.layouts.master')
@section('content')
    <h2>
        Suggested Restaurant
        @if (isset($restaurant) ? $restaurant : [])
            - {{ $restaurant['name'] }}
        @endif
    </h2>
    @if (Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    @if (isset($error) ? $error : '')
        <div class="alert alert-danger">{{ $error }}</div>
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
                @if ($restaurant['status_verify'] == $status_unverified)
                    <a href="{{ URL::to('cms/restaurant/suggested/edit/' . $restaurant['id']) }}" class="btn btn-primary btn-block">Edit</a>
                @endif
                </div>
            </div>
        </div>
    @endif
@stop
