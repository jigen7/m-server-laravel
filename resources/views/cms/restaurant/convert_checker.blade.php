@extends('cms.layouts.master')
@section('content')
    <h2>Restaurants - Convert Checker</h2>
    @if ($errors)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if ($success)
        <div class="alert alert-success">
            <ul>
                @foreach ($success as $s)
                    <li>{{ $s }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <form action="{{ URL::to('cms/restaurant/convert_checker') }}" enctype="multipart/form-data" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <input class="form-control" type="file" name="convert[]" multiple />
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit" value="Convert">Convert</button>
                </div>
            </form>
        </div>
    </div>
@stop