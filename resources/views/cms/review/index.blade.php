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

    @if (!$reviews->count())
        <div class="alert alert-danger">No records found.</div>
    @else
        <table id="review-table" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Restaurant Name</th>
                    <th>Rating</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Details</th>
                </tr>
            </thead>

            <tbody>
            @foreach ($reviews as $review)
                <tr>
                    <td>{{ $review->id }}</td>
                    <td>{{ $review->title }}</td>
                    <td>{{ $review->restaurant_name }}</td>
                    <td>{{ $review->rating . ' / 5' }}</td>
                    <td>{{ $review->firstname . ' ' . $review->lastname }}</td>
                    <td>{{ $review->date_created }}</td>
                    <td><a href="{{ URL::to('cms/review/view/' . $review->id . '?pw=900&ph=600') }}" class="btn btn-primary default-popup"><i class="glyphicon glyphicon-info-sign"> View</i></a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@stop