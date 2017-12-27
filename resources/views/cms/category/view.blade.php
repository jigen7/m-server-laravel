@extends('cms.layouts.master')
@section('content')
    <h2>Category {{ isset($page_title) ? $page_title : '' }}</h2>
    @if (count($errors))
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (!$restaurants)
        <div class="alert alert-danger">No records found.</div>
    @else
        <table id="category-table" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Rating</th>
                    <th>Phone Number</th>
                    <th>Budget</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($restaurants as $restaurant)
                <tr>
                    <td>{{ $restaurant['id'] }}</td>
                    <td>{{ $restaurant['name'] }}</td>
                    <td>{{ $restaurant['address'] }}</td>
                    <td>{{ $restaurant['telephone'] }}</td>
                    <td>{{ $restaurant['rating'] }}</td>
                    <td>{{ $restaurant['budget'] }}</td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ URL::to('cms/restaurant/view/' . $restaurant['id']) }}" class="btn btn-primary"><i class="glyphicon glyphicon-info-sign"> View</i></a>
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ URL::to('cms/restaurant/edit/' . $restaurant['id']) }}"><i class="glyphicon glyphicon-pencil"> Edit</i></a></li>
                                <li><a href="#"><i class="glyphicon glyphicon-trash"> Delete</i></a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@stop