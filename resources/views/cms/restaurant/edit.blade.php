@extends('cms.layouts.master')
@section('content')
    <h2>Restaurants - {{ $restaurant['name'] }} Edit</h2>
    @if (Session::has('errors'))
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (!$restaurant)
        <div class="alert alert-danger">Restaurant not found</div>
    @else
        <form class="form-horizontal" action="{{ URL::to('cms/restaurant/edit/' . $id) }}" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
                <label for="restaurant-name" class="col-sm-2 control-label">Restaurant Name</label>
                <div class="col-sm-6">
                    <input class="form-control" id="restaurant-name" name="restaurant_name" type="text" placeholder="Restaurant Name" value="{{ $restaurant->name }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="restaurant-address" class="col-sm-2 control-label">Restaurant Address</label>
                <div class="col-sm-6">
                    <input class="form-control" id="restaurant-address" name="restaurant_address" type="text" placeholder="Restaurant Address" value="{{ $restaurant->address }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="restaurant-telephone" class="col-sm-2 control-label">Phone Number</label>
                <div class="col-sm-6">
                    <input class="form-control" id="restaurant-telephone" name="restaurant_telephone" type="text" placeholder="Restaurant Phone Number" value="{{ $restaurant->telephone }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="restaurant-budget" class="col-sm-2 control-label">Budget</label>
                <div class="col-sm-6">
                    <input id="restaurant-budget" name="restaurant_budget" value="{{ $restaurant->budget }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="restaurant-latitude" class="col-sm-2 control-label">Latitude</label>
                <div class="col-sm-6">
                    <input class="form-control" id="restaurant-latitude" name="restaurant_latitude" type="text" value="{{ $restaurant->latitude }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="restaurant-longitude" class="col-sm-2 control-label">Longitude</label>
                <div class="col-sm-6">
                    <input class="form-control" id="restaurant-longitude" name="restaurant_longitude" type="text" value="{{ $restaurant->longitude }}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">&nbsp;</label>
                <div class="col-sm-6">
                    <button class="btn btn-primary btn-block" type="submit" value="Submit">Submit</button>
                </div>
            </div>
        </form>
    @endif
@stop