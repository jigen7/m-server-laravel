<?php

Route::group(['middleware' => ['prefilters','postfilters']], function() {
    require __DIR__.'/Routes/routes_api.php';
});

Route::group(['middleware' => 'auth'], function()
{
    // Only authenticated users may enter...
    require __DIR__.'/Routes/routes_cms.php';
});

//CMS Login with Google OAuth
Route::get('cms/login-google', 'Cms\LoginController@loginWithGoogle');

Route::get('cms/login', 'Cms\LoginController@login');
Route::get('cms/logout', 'Cms\LoginController@logout');