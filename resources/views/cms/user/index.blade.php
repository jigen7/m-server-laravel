@extends('cms.layouts.master')
@section('content')
    <h2>{{ $page_title }}</h2>
    <hr/>
    {!! Form::open(array('method' => 'get')) !!}
    {!! Form::label('from', 'From:') !!}
    {!! Form::text('from', $from, array('class' => 'datefrom form-control non-block')) !!}

    {!! Form::label('to', 'To:') !!}
    {!! Form::text('to', $to, array('class' => 'dateto form-control non-block')) !!}

    {!! Form::submit('Filter', array('class' => 'btn btn-primary')) !!}
    {!! Form::close() !!}
    <hr/>

    @if (!$users->count())
        <div class="alert alert-danger">No records found.</div>
    @else
        <table id="user-table" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Age</th>
                    <th>Nationality</th>
                    <th>Email Address</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->firstname }}</td>
                    <td>{{ $user->lastname }}</td>
                    <td>{{ $user->gender }}</td>
                    <td>{{ $user->age }}</td>
                    <td>{{ $user->nationality }}</td>
                    <td>{{ $user->email }}</td>
                    <td><a href="{{ URL::to('cms/user/view/' . $user->id . '?pw=900&ph=600') }}" class="btn btn-primary default-popup"><i class="glyphicon glyphicon-info-sign"> View</i></a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@stop