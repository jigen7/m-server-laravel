@extends('cms.layouts.master')
@section('content')
    <a  class="btn btn-success pull-right" href="{{ URL::to('cms/menu/add/' . $restaurant->id) }}"><i class="glyphicon glyphicon-plus-sign"></i> Add Menu</a>
    <h2>Menu{{ isset($restaurant) ? ' - ' . $restaurant->name : '' }}</h2>
    @if (isset($menu) ? [] : $menu)
        <div class="alert alert-danger">No records found.</div>
    @else
        <table id="menu-table" class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Restaurant ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Size</th>
                <th>Price</th>
                <th>Description</th>
                <th>Details</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($menu as $menu_item)
                <tr>
                    <td>{{ $menu_item->id }}</td>
                    <td>{{ $menu_item->restaurant_id }}</td>
                    <td>{{ $menu_item->name }}</td>
                    <td>{{ $menu_item->category }}</td>
                    <td>{{ $menu_item->size }}</td>
                    <td>{{ $menu_item->price }}</td>
                    <td>{{ $menu_item->description }}</td>
                    <td class="col-xs-2">
                        <div class="btn-group">
                            <a href="{{ URL::to('cms/menu/view/details/' . $menu_item->id) }}" class="btn btn-primary"><i class="glyphicon glyphicon-info-sign"> View</i></a>
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ URL::to('cms/menu/edit/' . $menu_item->id) }}"><i class="glyphicon glyphicon-pencil"></i> Edit</a></li>
                                <li><a class="delete-menu" data-id="{{ $menu_item->id }}" data-restaurant-id="{{ $restaurant->id }}" href="#"><i class="glyphicon glyphicon-trash"></i> Delete</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@stop
