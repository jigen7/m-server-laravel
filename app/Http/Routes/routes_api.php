<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// API Routes

Route::get('sample/{id}', 'Api\ActivitiesController@getuser');

// Activities Controller
Route::get('activities/user/{id}'      ,['as' => 'activities_user', 'uses' => 'Api\ActivitiesController@getUserActivitiesAction']);
Route::get('activities/restaurant/{id}',['as' => 'activities_restaurant', 'uses' => 'Api\ActivitiesController@getRestaurantActivitiesAction']);
Route::get('activities/followed/{id}'  ,['as' => 'activities_followed', 'uses' => 'Api\ActivitiesController@getFollowedActivitiesAction']);
Route::get('activities/restaurant/near/{longitude}/{latitude}/{distance}',['as' => 'activities_near', 'uses' => 'Api\ActivitiesController@getNearRestaurantActivitiesAction']);

// Bookmark Controller
Route::get('bookmarks/user/{user_id}'  , ['as' => 'bookmarks_users', 'uses' => 'Api\BookmarkController@userBookmarkListAction']);
Route::post('bookmarks/add'            , ['as' => 'bookmarks_add', 'uses' => 'Api\BookmarkController@addBookmarkAction']);
Route::delete('bookmarks/delete/restaurant/{restaurant_id}/user/{user_id}', ['as' => 'bookmarks_delete', 'uses' => 'Api\BookmarkController@deleteBookmarkAction']);

// Checkin Controller
Route::get('checkins/view/{id}'        , ['as' => 'checkins_view', 'uses' => 'Api\CheckinController@viewAction']);
Route::get('checkins/user/{id}'        , ['as' => 'checkins_users', 'uses' => 'Api\CheckinController@userAction']);
Route::post('checkins/add'             , ['as' => 'checkins_add', 'uses' => 'Api\CheckinController@addAction']);
Route::post('checkins/edit/{id}'       , ['as' => 'checkins_edit', 'uses' => 'Api\CheckinController@editAction']);
Route::delete('checkins/delete/{id}'   , ['as' => 'checkins_delete', 'uses' => 'Api\CheckinController@deleteAction']);

// Comment Controller
Route::get('comments/view/review/{id}' , ['as' => 'comments_view_review', 'uses' => 'Api\CommentController@viewByReviewIdAction']);
Route::get('comments/view/checkin/{id}', ['as' => 'comments_view_checkin', 'uses' => 'Api\CommentController@viewByCheckinAction']);
Route::get('comments/view/photo/{id}'  , ['as' => 'comments_view_photos', 'uses' => 'Api\CommentController@viewByPhotoIdAction']);
Route::post('comments/add'             , ['as' => 'comments_add', 'uses' => 'Api\CommentController@addCommentAction']);
Route::post('comments/edit'            , ['as' => 'comments_edit', 'uses' => 'Api\CommentController@editCommentAction']);
Route::delete('comments/delete/{id}'   , ['as' => 'comments_delete', 'uses' => 'Api\CommentController@deleteCommentAction']);

//Categories
Route::get('categories/cuisines/list' ,  ['as' => 'categories_cuisines_list', 'uses' => 'Api\CategoriesController@cuisineListAction']);

// Follow Controller
Route::get('followers'                 , ['as' => 'followers_list', 'uses' => 'Api\FollowController@followersAction']);
Route::get('following'                 , ['as' => 'following_list', 'uses' => 'Api\FollowController@followingAction']);
Route::post('follow/user'              , ['as' => 'follow_user', 'uses' => 'Api\FollowController@followAction']);
Route::post('follow/users'             , ['as' => 'follow_users', 'uses' => 'Api\FollowController@followManyAction']);
Route::post('follow/fbusers'           , ['as' => 'follow_fbusers', 'uses' => 'Api\FollowController@followFBUsersAction']);
Route::post('follow/twitterusers'      , ['as' => 'follow_twitterusers', 'uses' => 'Api\FollowController@followTwitterUsersAction']);
Route::post('unfollow/user'            , ['as' => 'unfollow_user', 'uses' => 'Api\FollowController@unfollowAction']);

// Like Controller
Route::get('like/list/{type}/{type_id}'                    , ['as' => 'like_list', 'uses' => 'Api\LikeController@likerListAction']);
Route::post('like/add'                                     , ['as' => 'like_add', 'uses' => 'Api\LikeController@addAction']);
Route::delete('like/delete/user/{user_id}/{type}/{type_id}', ['as' => 'like_delete', 'uses' => 'Api\LikeController@deleteAction']);

// LogRecentlyViewed Controller
Route::get('recently_viewed/user/{id}'    , ['as' => 'recently_viewed', 'uses' => 'Api\LogRecentlyViewedController@getAction']);

// Photos
Route::post('photos/delete'        , ['as' => 'photos_delete', 'uses' => 'Api\PhotosController@photoDeleteAction']);
Route::get('photos/{type}/{type_id}'   , ['as' => 'photos_type_view', 'uses' => 'Api\PhotosController@viewPhotosByTypeAction']);
Route::post('photos/upload/restaurant/', ['as' => 'photos_upload', 'uses' => 'Api\PhotosController@photoUploadRestaurantAction']);

// Reported Controller
Route::post('reported/add'             , ['as' => 'report_add', 'uses' => 'Api\ReportedController@addAction']);

// Review
Route::post('reviews/add'              , ['as' => 'reviews_add', 'uses' => 'Api\ReviewController@addAction']);
Route::post('reviews/edit/{id}'        , ['as' => 'reviews_edit', 'uses' => 'Api\ReviewController@editAction']);
Route::delete('reviews/delete/{id}'    , ['as' => 'reviews_delete', 'uses' => 'Api\ReviewController@deleteAction']);
Route::get('reviews/view/{id}'         , ['as' => 'reviews_view', 'uses' => 'Api\ReviewController@viewAction']);
Route::get('reviews/user/{id}'         , ['as' => 'reviews_user', 'uses' => 'Api\ReviewController@userAction']);
Route::get('reviews/restaurant/{id}'   , ['as' => 'reviews_restaurant', 'uses' => 'Api\ReviewController@restaurantAction']);

// Restaurant Controller
Route::get('restaurants/view/{id}/'                                                             , ['as' => 'restaurant_view', 'uses' => 'Api\RestaurantController@viewAction']);
Route::get('restaurants/search'                                                                 , ['as' => 'restaurant_search', 'uses' => 'Api\RestaurantController@searchAction']);
Route::get('restaurants/near/{longitude}/{latitude}/{distance}'                                 , ['as' => 'restaurant_near', 'uses' => 'Api\RestaurantController@nearAction']);
Route::get('restaurants/nearby-cuisines/{longitude}/{latitude}/{distance}'                      , ['as' => 'restaurant_nearby', 'uses' => 'Api\RestaurantController@nearbyCuisineAction']);
Route::get('restaurants/nearby-restaurant-cuisines/{longitude}/{latitude}/{distance}/{cuisine}' , ['as' => 'restaurant_nearby_cuisine', 'uses' => 'Api\RestaurantController@nearbyRestaurantsCuisineAction']);
Route::get('restaurants/auto-complete/{search_key}'                                             , ['as' => 'restaurant_autocomplete', 'uses' => 'Api\RestaurantController@restaurantsAutoCompleteAction']);
Route::get('restaurants/recent-activity/{user_id}/{search_key?}'                                , ['as' => 'restaurant_recent_activity', 'uses' => 'Api\RestaurantController@recentActivitySearchAction']);
Route::post('restaurants/suggest'                                                               , ['as' => 'restaurants_suggest', 'uses' => 'Api\RestaurantController@suggestAction']);
Route::get('restaurants/tag_list'                                                               , ['as' => 'restaurant_tagl_ist', 'uses' => 'Api\RestaurantController@getTagListAction']);


//Users Controller
Route::get('users/view'                 , ['as' => 'users_view', 'uses' => 'Api\UserController@viewAction']);
Route::post('users/add'                 , ['as' => 'users_add', 'uses' => 'Api\UserController@addAction']);
Route::post('users/edit/{user_id}'      , ['as' => 'users_edit', 'uses' => 'Api\UserController@editAction']);
Route::get('users/search'               , ['as' => 'users_search', 'uses' => 'Api\UserController@searchAction']);
Route::post('users/enable-notification' , ['as' => 'users_enable_notification', 'uses' => 'Api\UserController@enableNotificationAction']);
Route::post('users/disable-notification', ['as' => 'users_disable_notification', 'uses' => 'Api\UserController@disableNotificationAction']);
Route::get('users/viewstats/{user_id}'  , ['as' => 'users_view_stats', 'uses' =>  'Api\UserController@viewStatisticsAction']);
Route::get('users/featured/{user_id}'   , ['as' => 'users_featured', 'uses' =>  'Api\UserController@viewFeaturedUsersAction']);

//Notifications Controller
Route::get('notifications/view'         ,['as' => 'notification_view', 'uses' => 'Api\NotificationsController@viewAction']);
Route::post('notifications/read'          , ['as' => 'notifications_read', 'uses' => 'Api\NotificationsController@readAction']);

//Symfony Conroller
Route::any('/web/app.php/{all?}'              ,['as' => 'symfony_access', 'uses' => 'Api\ApiController@symfonyAction'])->where('all', '.*');
