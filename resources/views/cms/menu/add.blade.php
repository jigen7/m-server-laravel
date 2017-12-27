@extends('cms.layouts.master')
@section('content')
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
        {{ $success }}
    </div>
@endif
@if (!$restaurant)
    <h2>Add Menu</h2>
    <div class="alert alert-danger">Restaurant not found</div>
@else
    <h2>Add Menu - {{ $restaurant->name }}</h2>
    <form class="form-horizontal" id="category-form" action="{{ URL::to('cms/menu/add/' . $restaurant->id) }}" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input name="restaurant_id" type="hidden" value="{{ $restaurant->id }}"/>
        <div class="form-group">
            <label for="name" class="col-sm-3 control-label">Name <span class="text-danger">*</span></label>
            <div class="col-sm-8">
                <input class="form-control" id="name" name="name" type="text" placeholder="Name" />
            </div>
        </div>
        <div class="form-group">
            <label for="category" class="col-sm-3 control-label">Category <span class="text-danger">*</span></label>
            <div class="col-sm-8">
                <input class="form-control" id="category" name="category" type="text" placeholder="Category" />
            </div>
        </div>
        <div class="form-group">
            <label for="name" class="col-sm-3 control-label">Size</label>
            <div class="col-sm-8">
                <input class="form-control" id="size" name="size" type="text" placeholder="Size" />
            </div>
        </div>
        <div class="form-group">
            <label for="name" class="col-sm-3 control-label">Price <span class="text-danger">*</span></label>
            <div class="col-sm-8">
                <input class="form-control" id="price" name="price" type="text" placeholder="Price" />
            </div>
        </div>
        <div class="form-group">
            <label for="name" class="col-sm-3 control-label">Description</label>
            <div class="col-sm-8">
                <input class="form-control" id="description" name="description" type="text" placeholder="Description" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">&nbsp;</label>
            <div class="col-sm-8">
                <button class="btn btn-primary btn-block" type="submit" value="Submit">Submit</button>
            </div>
        </div>
    </form>
@endif
@stop
