<?php

Route::get('/', function()
{
    return View::make('hello');
});

// no auth checking api
Route::group(array('prefix' => '/v1/app'), function ()
{
    // need to set allow method type to "POST" before go production
    Route::any('login',         array('as' => 'app_login',          'uses' => 'AppController@login'));
    Route::any('register',      array('as' => 'app_register',       'uses' => 'AppController@register'));

    // guest view data
    Route::get('guest',         array('as' => 'guest_view',         'uses' => 'AppController@guestView'));
});

// auth needed api
Route::group(array('prefix' => '/v1/app', 'before' => 'app.auth'), function()
{
    // explore data, including general user info, homepage data, etc.
    Route::get('explore',                   array('as' => 'app_explore',        'uses' => 'AppController@explore'));
    Route::get('topstory',                  array('as' => 'app_top_story',      'uses' => 'AppController@topStory'));
    Route::get('topuser',                   array('as' => 'app_top_user',       'uses' => 'AppController@topUser'));
    Route::get('topbar',                    array('as' => 'app_topbar',         'uses' => 'AppController@topBar'));
    Route::get('winecategory',              array('as' => 'app_winecategory',   'uses' => 'AppController@wineCategory'));
    Route::get('drinked',                   array('as' => 'app_drinked',        'uses' => 'AppController@drinkedList'));
    Route::get('drinking',                  array('as' => 'app_drinking',       'uses' => 'AppController@drinkingList'));
    Route::get('collection',                array('as' => 'app_collection',     'uses' => 'AppController@collection'));
    Route::get('mymenu',                    array('as' => 'app_mymenu',         'uses' => 'AppController@myMenu'));
    Route::get('comments/{category}/{id}',  array('as' => 'app_comment',        'uses' => 'AppController@comments'));
    Route::post('post/comment',             array('as' => 'app_post_comment',   'uses' => 'AppController@postComment'));
});