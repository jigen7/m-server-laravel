@extends('ModuleSample::layouts.masterx')
@section('content')
    @if (!empty($errors))
        <div class="alert alert-danger">{{ $errors }}</div>
    @else
        <div class="page-item-detail">
            <section class="sub-header">
                @include('web.layouts.search-bar')
                <div class="breadcrumb-wrapper">
                    <div class="container">
                        <ol class="breadcrumb">
                            <li><a href="{{ URL::to('web') }}">Home</a></li>
                            <li><a href="{{ $restaurant['link'] }}">Restaurant</a></li>
                            <li class="active">Review</li>
                        </ol>
                    </div>
                </div>
            </section>
            <div id="page-content">
                <section class="container">
                    <div class="row">
                        <div class="col-md-9">
                            @if (Session::has('errors'))
                                <div class="alert alert-danger">
                                    @if (is_array(Session::get('errors')))
                                        <ul>
                                            @foreach (Session::get('errors') as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        {{ Session::get('errors') }}
                                    @endif
                                </div>
                            @elseif (Session::has('success'))
                                <div class="alert alert-success">{{ Session::get('success') }}</div>
                            @endif
                            <section class="block" id="main-content">
                                <header class="page-title">
                                    <div class="title">
                                        <h1>{{ $restaurant['name'] }}</h1>
                                        <figure class="rating big pull-left" data-rating="{{ $restaurant['rating'] }}"></figure>
                                    </div>
                                </header>
                                <div class="row">
                                    <aside class="col-md-4 col-sm-4" id="detail-sidebar">
                                        <section>
                                            <header><h3>Telephone Number</h3></header>
                                            <figure>
                                                <div class="info">
                                                    <i class="extrainfo"><img src="{{ asset('assets_web/icons/telephone.png') }}" alt="" /></i>
                                                    <span>{{ $restaurant['telephone'] }}</span>
                                                </div>
                                            </figure>
                                        </section>
                                        <article class="block">
                                            <div id="map-detail"></div>
                                        </article>
                                        <section>
                                            <header><h3>Address</h3></header>
                                            <address>
                                                <div>{{ $restaurant['address'] }}</div>
                                                <figure>
                                                    <div class="info">
                                                        <i><img src="{{ asset('assets_web/icons/budget.png') }}" alt="" /></i>
                                                        <span>{{ $restaurant['budget'] }}</span>
                                                    </div>
                                                    <div class="info">
                                                        <i><img src="{{ asset('assets_web/icons/review.png') }}" alt="" /></i>
                                                        <span>{{ $reviews_count }}</span>
                                                        <span>Reviews</span>
                                                    </div>
                                                    <div class="info">
                                                        <i><img src="{{ asset('assets_web/icons/photo.png') }}" alt="" /></i>
                                                        <span>{{ $photos_count }}</span>
                                                        <span>Photos</span>
                                                    </div>
                                                    <div class="info">
                                                        <i><img src="{{ asset('assets_web/icons/bookmark.png') }}" alt="" /></i>
                                                        <span>{{ $bookmarks_count }}</span>
                                                        <span>Bookmarks</span>
                                                    </div>
                                                    <div class="info">
                                                        <i><img src="{{ asset('assets_web/icons/checkin.png') }}" alt="" /></i>
                                                        <span>{{ $checkins_count }}</span>
                                                        <span>Check-Ins</span>
                                                    </div>
                                                </figure>
                                            </address>
                                        </section>
                                        <section>
                                            <header><h3>Cuisines</h3></header>
                                            <figure>
                                                <div class="info">
                                                    <i class="extrainfo"><img src="{{ asset('assets_web/icons/cuisine.png') }}" alt="" /></i>
                                                    <span>{{ $cuisine }}</span>
                                                </div>
                                            </figure>
                                        </section>
                                        <section>
                                            <header><h3>Operating Time</h3></header>
                                            <figure>
                                                <div class="info">
                                                    <i class="extrainfo"><img src="{{ asset('assets_web/icons/operatingtime.png') }}" alt="" /></i>
                                                    <span>10 AM to 9 PM</span>
                                                </div>
                                            </figure>
                                        </section>
                                        <section>
                                            <header><h3>Delivery</h3></header>
                                            <figure>
                                                <div class="info">
                                                    <i class="extrainfo"><img src="{{ asset('assets_web/icons/delivery.png') }}" alt="" /></i>
                                                    <span>{{ $restaurant['is_delivery'] }}</span>
                                                </div>
                                            </figure>
                                        </section>
                                    </aside>
                                    <div class="col-md-8 col-sm-8">
                                        @if ($photos_count)
                                            <section>
                                                <article class="item-gallery">
                                                    <div class="owl-carousel item-slider">
                                                        <?php $count = 0; ?>
                                                        @foreach ($photos as $photo)
                                                            <?php $count = $count + 1; ?>
                                                            <div class="slide"><img src="{{ asset('uploads/default/' . $photo['url']) }}" data-hash="{{ $count }}" alt="" /></div>
                                                        @endforeach
                                                    </div>
                                                    <div class="thumbnails">
                                                        <span class="expand-content btn framed icon" data-expand="#gallery-thumbnails" >7<i class="fa fa-plus"></i></span>
                                                        <div class="expandable-content height collapsed show-70" id="gallery-thumbnails">
                                                            <div class="content">
                                                                <?php $count = 0; ?>
                                                                @foreach ($photos as $photo)
                                                                    <?php $count = $count + 1; ?>
                                                                    @if ($count == 1)
                                                                        <a href="#{{ $count }}" id="thumbnail-{{ $count }}" class="active"><img src="{{ asset('uploads/default/' . $photo['url']) }}" alt="" /></a>
                                                                    @else
                                                                        <a href="#{{ $count }}" id="thumbnail-{{ $count }}"><img src="{{ asset('uploads/default/' . $photo['url']) }}" alt="" /></a>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </article>
                                            </section>
                                        @endif
                                        <section class="block" id="reviews">
                                            <div class="item list">
                                                <div class="wrapper">
                                                    <figure class="author feed">
                                                        <a href="{{ URL::to('web/user/' . $user['uuid']) }}"><img src="{{ $user['facebook_profile_photo_url'] }}" alt="" /></a>
                                                    </figure>
                                                    <h3>{{ strtoupper($review['title']) }}</h3>
                                                    <div class="info">
                                                        <div class="rating" data-rating="{{ $review['rating'] }}"></div>
                                                        <div class="type">
                                                            <i><img src="{{ asset('assets_web/icons/review.png') }}" alt="" /></i>
                                                            <a href="{{ URL::to('web/user/' . $user['uuid']) }}"><span>{{ $user['full_name'] }}</span></a>
                                                        </div>
                                                        <div class="type">
                                                            <i><img src="{{ asset('assets_web/icons/time.png') }}" alt="" /></i>
                                                            <span class="time">{{ $review['date_created'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="info">
                                                        <div class="type">
                                                            <p>{{ $review['text'] }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="info">
                                                        <div class="type">
                                                            <div class="individual-rating">
                                                                <a href="#">
                                                                    <i class="extrainfo"><img src="{{ asset('assets_web/icons/likes.png') }}" alt="" /></i>
                                                                    <span>0</span>
                                                                    <span>Likes</span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                        <section id="comments">
                                            <header><h2 class="no-border">Comments</h2></header>
                                            @if (!empty($comments))
                                            <ul class="comments">
                                                @foreach ($comments as $comment)
                                                <li class="comment">
                                                    <figure>
                                                        <div class="image">
                                                            <a href={{ $comment['user']['link'] }}><img alt="" src="{{ $comment['user']['facebook_profile_photo'] }}" /></a>
                                                        </div>
                                                    </figure>
                                                    <div class="comment-wrapper">
                                                        <div class="name pull-left"><a href={{ $comment['user']['link'] }}>{{ $comment['user']['full_name'] }}</a></div>
                                                        <div class="info">
                                                            <div class="type">
                                                                <i><img src="{{ asset('assets_web/icons/time.png') }}" alt="" /></i>
                                                                <span class="time">{{ $comment['date_created'] }}</span>
                                                            </div>
                                                        </div>
                                                        <p>{{ $comment['comment'] }}</p>
                                                        <hr />
                                                    </div>
                                                </li>
                                                @endforeach
                                            </ul>
                                            @else
                                                <div class="alert alert-danger">No comments.</div>
                                            @endif
                                        </section>
                                        @if (Session::has('user_id'))
                                        <section id="leave-reply">
                                            <header><h2 class="no-border">Add A Comment</h2></header>
                                            <form role="form" id="form-blog-reply" method="post" action="{{ URL::to('web/comment/new-comment') }}" class="clearfix">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <input type="hidden" name="type" value="{{ \App\Http\Helpers\CONSTANTS::REVIEW }}" />
                                                        <input type="hidden" name="type_id" value="{{ $review['id'] }}" />
                                                        <div class="form-group">
                                                            <textarea class="form-control" id="form-blog-reply-message" rows="5" name="text" required=""></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group clearfix">
                                                    <button type="submit" class="btn pull-right btn-default" id="form-blog-reply-submit">Add A Comment</button>
                                                </div>
                                                <div id="form-rating-status"></div>
                                            </form>
                                        </section>
                                        @else
                                        <div class="alert alert-danger">Please login to comment.</div>
                                        @endif
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="col-md-3">
                            <aside id="sidebar">
                                @include('web.layouts.cuisines')
                            </aside>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
        <script>
            var map_center = new google.maps.LatLng({{ $restaurant['latitude'] }}, {{ $restaurant['longitude'] }});
            var map_styles = [{"featureType":"road","elementType":"labels","stylers":[{"visibility":"simplified"},{"lightness":20}]},{"featureType":"administrative.land_parcel","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape.man_made","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"transit","elementType":"all","stylers":[{"saturation":-100},{"visibility":"on"},{"lightness":10}]},{"featureType":"road.local","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"road.local","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"simplified"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.arterial","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":50}]},{"featureType":"water","elementType":"all","stylers":[{"hue":"#a1cdfc"},{"saturation":30},{"lightness":49}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"hue":"#f49935"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"hue":"#fad959"}]}, {featureType:'road.highway',elementType:'all',stylers:[{hue:'#dddbd7'},{saturation:-92},{lightness:60},{visibility:'on'}]}, {featureType:'landscape.natural',elementType:'all',stylers:[{hue:'#c8c6c3'},{saturation:-71},{lightness:-18},{visibility:'on'}]},  {featureType:'poi',elementType:'all',stylers:[{hue:'#d9d5cd'},{saturation:-70},{lightness:20},{visibility:'on'}]}];
            var map_options = {
                zoom: 14,
                center: map_center,
                disableDefaultUI: true,
                scrollwheel: false,
                styles: map_styles,
                panControl: false,
                zoomControl: false,
                draggable: false
            };
            var map = new google.maps.Map(document.getElementById('map-detail'), map_options);
            var marker = new google.maps.Marker({
                position: map_center,
                map: map,
                title: '{{ $restaurant['name'] }}'
            });
        </script>
    @endif
@stop