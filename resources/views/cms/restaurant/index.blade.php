@extends('cms.layouts.master')
@section('content')
    <h2>Restaurants</h2>
    @if (!$restaurants)
        <div class="alert alert-danger">No records found.</div>
    @else
        <table id="restaurant-table" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Rating</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($restaurants as $restaurant)
                <tr>
                    <td>{{ $restaurant->id }}</td>
                    <td>{{ $restaurant->name }}</td>
                    <td>{{ $restaurant->address }}</td>
                    <td>{{ $restaurant->rating }}</td>
                    <td class="col-xs-2">
                        <div class="btn-group">
                            <a href="{{ URL::to('cms/restaurant/view/' . $restaurant->id) }}" class="btn btn-primary"><i class="glyphicon glyphicon-info-sign"> View</i></a>
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ URL::to('cms/restaurant/edit/' . $restaurant->id) }}"><i class="glyphicon glyphicon-pencil"></i> Edit</a></li>
                                <li><a class="restaurant-delete" data-id="{{ $restaurant->id }}" href="#"><i class="glyphicon glyphicon-trash"></i> Delete</a></li>
                                <li><a href="{{ URL::to('cms/menu/view/' . $restaurant->id) }}"><i class="glyphicon glyphicon-list-alt"></i> View Menu</a></li>
                                <li><a href="{{ URL::to('cms/menu/add/' . $restaurant->id) }}"><i class="glyphicon glyphicon-plus-sign"></i> Add Menu</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@stop
