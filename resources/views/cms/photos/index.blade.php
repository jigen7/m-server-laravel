@extends('cms.layouts.master')
@section('content')
    <h2>Photos</h2>
    @if (count($errors))
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @else
        @if (!$photos)
            <div class="alert alert-danger">No records found.</div>
        @else
            <form class="form-inline" action="{{ URL::to('cms/photos/index') }}" method="get">
                <div class="form-group">
                    <label for="photo-from-date">From</label>
                    <input class="form-control" id="photo-from-date" name="fd" type="text" value="" />
                </div>
                <div class="form-group">
                    <label for="photo-to-date">To</label>
                    <input class="form-control" id="photo-to-date" name="td" type="text" value="" />
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit" value="Filter">Filter</button>
                </div>
            </form>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Type ID</th>
                        <th>Restaurant ID</th>
                        <th>Preview</th>
                        <th>Status</th>
                        <th>User ID</th>
                        <th>Date Uploaded</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($photos as $photo)
                    <tr>
                        <td>{{ $photo->id }}</td>
                        <td>{{ $photo->type }}</td>
                        <td>{{ $photo->type_id }}</td>
                        <td>{{ $photo->restaurant_id }}</td>
                        <td><a class="photo-preview-toggle" href="{{ $photo->url }}">Preview</a></td>
                        <td>{{ $photo->status }}</td>
                        <td>{{ $photo->user_id }}</td>
                        <td>{{ $photo->date_uploaded }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ URL::to('cms/photos/view/' . $photo->id) }}" class="btn btn-primary"><i class="glyphicon glyphicon-info-sign"> View</i></a>
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a class="photo-deactivate" data-id="{{ $photo->id }}" href="#"><i class="glyphicon glyphicon-trash"> Deactivate</i></a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <?php echo $photos->render(); ?>
            <div class="modal fade" id="photo-modal" tabindex="-1" role="dialog" aria-labelledby="photo-modal-label" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <img id="photo-modal-preview" src="" />
                    </div>
                </div>
            </div>
        @endif
    @endif
@stop