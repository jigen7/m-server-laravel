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

// CMS Routes
Route::group(['prefix' => 'cms'], function()
{
// Category Controller
    Route::get('category', 'Cms\CategoryCmsController@indexAction');
    Route::get('category/index', 'Cms\CategoryCmsController@indexAction');
    Route::get('category/view/{id}', 'Cms\CategoryCmsController@viewAction');
    Route::match(['get', 'post'], 'category/new', 'Cms\CategoryCmsController@newAction');

// Main Controller
    Route::get('/', 'Cms\MainCmsController@indexAction');
    Route::get('main/', 'Cms\MainCmsController@indexAction');
    Route::get('main/index', 'Cms\MainCmsController@indexAction');

// Photos Controller
    Route::get('photos', 'Cms\PhotosCmsController@indexAction');
    Route::get('photos/index', 'Cms\PhotosCmsController@indexAction');
    Route::get('photos/view/{id}', 'Cms\PhotosCmsController@viewAction');

// Reported Controller
    Route::any('reported/photos/index', 'Cms\ReportedCmsController@indexPhotosAction');
    Route::any('reported/photo/view/{photo_id}', 'Cms\ReportedCmsController@viewPhotoAction');
    Route::any('reported/restaurants/index', 'Cms\ReportedCmsController@indexRestaurantsAction');
    Route::get('reported/restaurant/view/{restaurant_id}', 'Cms\ReportedCmsController@viewRestaurantAction');
    Route::post('reported/photo/change_report_status', 'Cms\ReportedCmsController@changeReportStatusAction');
    Route::post('reported/restaurant/change_report_status', 'Cms\ReportedCmsController@changeReportStatusAction'   );

// Restaurants Controller
    Route::get('restaurant/', 'Cms\RestaurantCmsController@indexAction');
    Route::get('restaurant/index', 'Cms\RestaurantCmsController@indexAction');
    Route::get('restaurant/view/{id}', 'Cms\RestaurantCmsController@viewAction');
    Route::match(['get', 'post'], 'restaurant/edit/{id}', 'Cms\RestaurantCmsController@editAction');
    Route::match(['get', 'post'], 'restaurant/convert', 'Cms\RestaurantCmsController@convertAction');
    Route::match(['get', 'post'], 'restaurant/convert_checker', 'Cms\RestaurantCmsController@convertCheckerAction');
    Route::post('restaurant/delete/', 'Cms\RestaurantCmsController@deleteAction');
    Route::get('restaurant/suggested/index', 'Cms\RestaurantCmsController@indexSuggestedAction');
    Route::get('restaurant/suggested/index/{filter}', 'Cms\RestaurantCmsController@indexSuggestedFilterAction');
    Route::get('restaurant/suggested/view/{id}', 'Cms\RestaurantCmsController@viewSuggestedAction');
    Route::post('restaurant/suggested/approve', 'Cms\RestaurantCmsController@approveSuggestedAction');
    Route::post('restaurant/suggested/reject', 'Cms\RestaurantCmsController@rejectSuggestedAction');
    Route::match(['get', 'post'], 'restaurant/suggested/edit/{id}', 'Cms\RestaurantCmsController@editSuggestedAction');

// Reviews Controller
    Route::get('review/view/{review_id}', 'Cms\ReviewCmsController@viewAction');
    Route::any('reviews/index', 'Cms\ReviewCmsController@indexAction');

// Users Controller
    Route::get('user/view/{user_id}', 'Cms\UserCmsController@viewAction');
    Route::any('users/index', 'Cms\UserCmsController@indexAction');

// Menu Controller
   Route::match(['get', 'post'], 'menu/add/{restaurant_id}', 'Cms\MenuCmsController@addAction');
   Route::get('menu/view/{restaurant_id}', 'Cms\MenuCmsController@viewAction');
   Route::get('menu/view/details/{id}', 'Cms\MenuCmsController@viewDetailsAction');
   Route::match(['get', 'post'], 'menu/edit/{id}', 'Cms\MenuCmsController@editAction');
   Route::post('menu/delete', 'Cms\MenuCmsController@deleteAction');
   Route::match(['get', 'post'], 'menu/convert', 'Cms\MenuCmsController@convertAction');
});

