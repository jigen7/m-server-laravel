@extends('cms.layouts.master')
@section('content')
    <h2>Edit Menu - {{ $menu['name'] }}</h2>
    @if (Session::has('errors'))
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (!$menu)
        <div class="alert alert-danger">Menu not found</div>
    @else
        <form class="form-horizontal" action="{{ URL::to('cms/menu/edit/' . $id) }}" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Name <span class="text-danger">*</span></label>
                <div class="col-sm-6">
                    <input class="form-control" id="name" name="name" type="text" placeholder="Name" value="{{ $menu->name }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Category <span class="text-danger">*</span></label>
                <div class="col-sm-6">
                    <input class="form-control" id="category" name="category" type="text" placeholder="Category" value="{{ $menu->category }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Size</label>
                <div class="col-sm-6">
                    <input class="form-control" id="size" name="size" type="text" placeholder="Size" value="{{ $menu->size }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Price <span class="text-danger">*</span></label>
                <div class="col-sm-6">
                    <input class="form-control" id="price" name="price" type="text" placeholder="Price" value="{{ $menu->price }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Description</label>
                <div class="col-sm-6">
                    <input class="form-control" id="description" name="description" type="text" placeholder="Description" value="{{ $menu->description }}" />
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
