@extends('cms.layouts.master')
@section('content')
<h2>Categories</h2>
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
    <div class="alert alert-success">{{ $success }}</div>
@endif
<form id="category-form" action="{{ URL::to('cms/category/new') }}" method="post">
    <div class="form-group">
        <label class="control-label" for="category-type">Type</label>
        <select class="form-control" id="category-type" name="category_type">
            <option value="city">City</option>
            <option value="mall">Mall</option>
            <option value="cuisine">Cuisine</option>
        </select>
    </div>
    <div class="form-group">
        <label class="control-label" for="category-name">Name</label>
        <input class="form-control" id="category-name" type="text" name="category_name" />
    </div>
    <div class="form-group">
        <button class="btn btn-primary" type="submit" value="Submit">Submit</button>
    </div>
</form>
@stop