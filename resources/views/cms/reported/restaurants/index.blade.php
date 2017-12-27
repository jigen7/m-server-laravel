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

    @if (!$restaurants->count())
        <div class="alert alert-danger">No records found.</div>
    @else
        <table id="reported-table" class="table table-striped" data-order='[[ 4, "asc" ]]'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Reason</th>
                    <th>Report Status</th>
                    <th>Reported At</th>
                    <th>Details</th>
                </tr>
            </thead>

            <tbody>
            @foreach ($restaurants as $restaurant)
                <tr>
                    <td>{{ $restaurant['id'] }}</td>
                    <td>{{ $restaurant['name'] }}</td>
                    <td>{{ $restaurant['address'] }}</td>
                    <td>{{ $restaurant['reason'] }}</td>
                    <td>{{ $restaurant['report_status'] }}</td>
                    <td>{{ $restaurant['date_created'] }}</td>
                    <td class="col-xs-2">
                        <div class="btn-group">
                            <a href="{{ URL::to('cms/reported/restaurant/view/' . $restaurant->restaurant_id . '?pw=900&ph=600') }}" class="btn btn-primary default-popup"><i class="glyphicon glyphicon-info-sign"> View</i></a>
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <form action="{{ url('cms/reported/restaurant/change_report_status') }}" method="post">
                                        <input name="page_name" value="restaurants" type="hidden"/>
                                        <input name="report_status" value="{{ $reported_approved }}" type="hidden"/>
                                        <input name="reported_id" value="{{ $restaurant['id'] }}" type="hidden"/>
                                        <button type="submit" class="btn-link">
                                            <i class="glyphicon glyphicon-ok"> Approve</i>
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ url('cms/reported/restaurant/change_report_status') }}" method="post">
                                        <input name="page_name" value="restaurants" type="hidden"/>
                                        <input name="report_status" value="{{ $reported_rejected }}" type="hidden"/>
                                        <input name="reported_id" value="{{ $restaurant['id'] }}" type="hidden"/>
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
