@extends('cms.layouts.master')
@section('content')
    <h2>Suggested Restaurants</h2>
    <div class="row">
        <div class="dropdown col-md-2">
            <h5 class="text-danger">Status Verify: </h5>
            <button class="btn btn-block btn-success dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                {{ $filter }}
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <li><a href="{{ URL::to('cms/restaurant/suggested/index/unverified') }}">Unverified</a></li>
                <li><a href="{{ URL::to('cms/restaurant/suggested/index/approved') }}">Approved</a></li>
                <li><a href="{{ URL::to('cms/restaurant/suggested/index/rejected') }}">Rejected</a></li>
            </ul>
        </div>
    </div>
    <br />
    @if (Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    @if (!$restaurants)
        <div class="alert alert-danger">No records found.</div>
    @else
        <table id="restaurant-table" class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Details</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($restaurants as $restaurant)
                <tr>
                    <td>{{ $restaurant->id }}</td>
                    <td>{{ $restaurant->name }}</td>
                    <td>{{ $restaurant->address }}</td>
                    <td>{{ $restaurant->latitude }}</td>
                    <td>{{ $restaurant->longitude }}</td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ URL::to('cms/restaurant/suggested/view/' . $restaurant->id) }}" class="btn btn-primary"><i class="glyphicon glyphicon-info-sign"> View</i></a>
                            @if ($restaurant->status_verify == $status_unverified)
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                            @endif
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ URL::to('cms/restaurant/suggested/edit/' . $restaurant->id) }}"><i class="glyphicon glyphicon-pencil"> Edit</i></a></li>
                                <li>
                                    <form action="{{ url('cms/restaurant/suggested/approve') }}" method="post">
                                        <input name="id" value="{{ $restaurant->id }}" type="hidden"/>
                                        <button type="submit" class="btn-link">
                                            <i class="glyphicon glyphicon-ok"> Approve</i>
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ url('cms/restaurant/suggested/reject') }}" method="post">
                                        <input name="id" value="{{ $restaurant->id }}" type="hidden"/>
                                        <button type="submit" class="btn-link">
                                            <i class="glyphicon glyphicon-remove"> Reject</i>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@stop
