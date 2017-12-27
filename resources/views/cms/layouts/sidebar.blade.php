<ul class="nav nav-sidebar">
    <li><a href="{{ URL::to('cms/restaurant/index') }}"><b>Restaurants</b></a></li>
        <ul>
            <li><a href="#">New</a></li>
            <li><a href="{{ URL::to('cms/restaurant/convert') }}">Convert</a></li>
            <li><a href="{{ URL::to('cms/restaurant/convert_checker') }}">Convert Checker</a></li>
            <li><a href="#">Compute Rating</a></li>
            <li><a href="{{URL::to('cms/restaurant/suggested/index')}}">Suggested</a></li>
        </ul>
    <li><hr /></li>
    <li><a href="#"><b>Menu</b></a></li>
    <ul>
        <li><a href="{{ URL::to('cms/menu/convert/') }}">Convert</a></li>
    </ul>
    <li><a href="{{ URL::to('cms/category/index') }}"><b>Category</b></a></li>
        <ul>
            <li><a href="{{ URL::to('cms/category/new') }}">New</a></li>
        </ul>
    <li><hr /></li>
    <li><a href="{{ URL::to('cms/photos/index') }}"><b>Photos</b></a></li>
    <li><hr /></li>
    <li><a href="{{ URL::to('cms/reviews/index') }}"><b>Reviews</b></a></li>
    <li><hr /></li>
    <li><span>Reported</span></li>
        <ul>
            <li><a href="{{ URL::to('cms/reported/photos/index') }}"><b>Photos</b></a></li>
            <li><a href="{{ URL::to('cms/reported/restaurants/index') }}"><b>Restaurants</b></a></li>
        </ul>
    <li><hr /></li>
    <li><a href="{{ URL::to('cms/users/index') }}"><b>Users</b></a></li>
</ul>
