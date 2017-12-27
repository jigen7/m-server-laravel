@extends('cms.layouts.master')
@section('content')
    <h2>Categories</h2>
    @if (!$categories)
        <div class="alert alert-danger">No records found.</div>
    @else
        <table id="category-table" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Name</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->type }}</td>
                    <td>{{ $category->name }}</td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ URL::to('cms/category/view/' . $category->id) }}" class="btn btn-primary"><i class="glyphicon glyphicon-info-sign"> View</i></a>
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ URL::to('cms/category/edit/' . $category->id) }}"><i class="glyphicon glyphicon-pencil"> Edit</i></a></li>
                                <li><a href="#"><i class="glyphicon glyphicon-trash"> Delete</i></a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection