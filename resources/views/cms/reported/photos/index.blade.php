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

    @if (!$photos->count())
        <div class="alert alert-danger">No records found.</div>
    @else
        <table id="reported-table" class="table table-striped" data-order='[[ 4, "asc" ]]'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Restaurant Name</th>
                    <th>URL</th>
                    <th>Reason</th>
                    <th>Report Status</th>
                    <th>Reported At</th>
                    <th>Details</th>
                </tr>
            </thead>

            <tbody>
            @foreach ($photos as $photo)
                <tr>
                    <td>{{ $photo['id'] }}</td>
                    <td>{{ $photo['name'] }}</td>
                    <td>{{ $photo['url'] }}</td>
                    <td>{{ $photo['reason'] }}</td>
                    <td>{{ $photo['report_status'] }}</td>
                    <td>{{ $photo['date_created'] }}</td>
                    <td class="col-xs-2">
                        <div class="btn-group">
                            <a href="{{ URL::to('cms/reported/photo/view/' . $photo->photo_id . '?pw=900&ph=600') }}" class="btn btn-primary default-popup"><i class="glyphicon glyphicon-info-sign"> View</i></a>
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <form action="{{ url('cms/reported/photo/change_report_status') }}" method="post">
                                        <input name="page_name" value="photos" type="hidden"/>
                                        <input name="report_status" value="{{ $reported_approved }}" type="hidden"/>
                                        <input name="reported_id" value="{{ $photo['id'] }}" type="hidden"/>
                                        <button type="submit" class="btn-link">
                                            <i class="glyphicon glyphicon-ok"> Approve</i>
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ url('cms/reported/photo/change_report_status') }}" method="post">
                                        <input name="page_name" value="photos" type="hidden"/>
                                        <input name="report_status" value="{{ $reported_rejected }}" type="hidden"/>
                                        <input name="reported_id" value="{{ $photo['id'] }}" type="hidden"/>
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
