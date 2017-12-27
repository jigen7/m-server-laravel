@extends('cms.layouts.master')
@section('content')
    <h2>Menu - {{ $menu['name'] }}</h2>
    @if (Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    @if (!$menu)
        <div class="alert alert-danger">No records found.</div>
    @else
        <div class="row">
            <div class="col-lg-6">
                <table class="table table-striped">
                    @foreach ($menu as $key => $value)
                        <tr>
                            <td><b>{{ $key }}</b></td>
                            <td>{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>
                <a href="{{ URL::to('cms/menu/edit/' . $menu['id']) }}" class="btn btn-primary btn-block">Edit</a>
            </div>
        </div>
    @endif
@stop
