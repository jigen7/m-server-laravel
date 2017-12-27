<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <link rel="stylesheet" href="{{ asset('/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('/css/jqueryui.css') }}" />
    <link rel="stylesheet" href="{{ asset('/css/styles.css') }}" />
</head>
<body>
    <h4>Review Details</h4>
    <table class="table table-striped">
        <tr>
            <td>Restaurant Name</td>
            <td>{{ $review->restaurant_name }}</td>
        </tr>
        <tr>
            <td>Title</td>
            <td>{{ $review->title }}</td>
        </tr>
        <tr>
            <td>Message</td>
            <td>{{ $review->text }}</td>
        </tr>
        <tr>
            <td>Rating</td>
            <td>{{ $review->rating . " / 5" }}</td>
        </tr>
        <tr>
            <td>Created by</td>
            <td>{{ $review->firstname . " " . $review->lastname }}</td>
        </tr>
        <tr>
            <td>Created at</td>
            <td>{{ $review->date_created }}</td>
        </tr>
    </table>

    <hr/>
    <h4>Comments for this Review</h4>
    @if(!$comments->count())
        <div class="alert alert-danger">This review has no comments yet.</div>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Comment</th>
                    <th>Created by</th>
                    <th>Created at</th>
                </tr>
            </thead>
            @foreach($comments as $comment)
                <tr>
                    <td>{{ $comment->comment }}</td>
                    <td>{{ $comment->firstname . " " . $comment->lastname }}</td>
                    <td>{{ $comment->date_created }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <script src="{{ asset('/js/jquery.js') }}"></script>
    <script src="{{ asset('/js/jqueryui.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.js') }}"></script>
    <script src="{{ asset('/js/cms-script.js') }}"></script>
    <script src="{{ asset('/js/modernizr.js') }}"></script>
</body>