<?php

Route::get('/', function()
{
    return View::make('hello');
});

Route::get('/404', function()
{
    return View::make('404');
});

// no auth checking api
Route::group(array('prefix' => '/v1/app'), function ()
{
    // need to set allow method type to "POST" before go production
    Route::any('login',             array('as' => 'app_login',              'uses' => 'AppController@login'));
    Route::any('register',          array('as' => 'app_register',           'uses' => 'AppController@register'));

    // guest view data
    Route::get('version/{os}',      array('as' => 'app_version',            'uses' => 'AppController@getAppVersion'));
    Route::get('guest',             array('as' => 'guest_view',             'uses' => 'AppController@guestView'));
    Route::any('pwd/mailcheck',     array('as' => 'app_mail_check',         'uses' => 'AppController@checkMail'));
    Route::any('pwd/codecheck',     array('as' => 'app_code_check',         'uses' => 'AppController@checkCode'));
    Route::any('pwd/resetpwd',      array('as' => 'app_reset_forget_pwd',   'uses' => 'AppController@resetForgetPwd'));
});

// auth needed api
Route::group(array('prefix' => '/v1/app', 'before' => 'app.auth'), function()
{
    // explore data, including general user info, homepage data, etc.
    Route::get('explore',                   array('as' => 'app_explore',        'uses' => 'AppController@explore'));
    Route::get('topstory',                  array('as' => 'app_top_story',      'uses' => 'AppController@topStory'));
    Route::get('topuser',                   array('as' => 'app_top_user',       'uses' => 'AppController@topUser'));
    Route::get('search/user/{keyword?}',    array('as' => 'app_search_user',    'uses' => 'AppController@searchUser'));
    Route::get('search/wine/{keyword?}',    array('as' => 'app_search_wine',    'uses' => 'AppController@searchWine'));
    Route::get('search/record/{keyword?}',  array('as' => 'app_search_record',  'uses' => 'AppController@searchRecord'));
    Route::get('search/menu/{keyword?}',    array('as' => 'app_search_menu',    'uses' => 'AppController@searchMenu'));
    Route::get('topbar',                    array('as' => 'app_topbar',         'uses' => 'AppController@topBar'));
    Route::get('topwine/{category?}',       array('as' => 'app_topwine',        'uses' => 'AppController@topWine'));
    Route::get('wine/drinked/{wineid}',     array('as' => 'app_winedrinked',    'uses' => 'AppController@wineDrinked'));
    Route::get('wine/drinking/{wineid}',    array('as' => 'app_winedrinking',   'uses' => 'AppController@wineDrinking'));
    Route::get('winecategory',              array('as' => 'app_winecategory',   'uses' => 'AppController@wineCategory'));
    Route::get('drinked/{uid}',             array('as' => 'app_drinked',        'uses' => 'AppController@drinkedList'));
    Route::get('drinking/{uid}',            array('as' => 'app_drinking',       'uses' => 'AppController@drinkingList'));
    Route::get('collection/{uid}',          array('as' => 'app_collection',     'uses' => 'AppController@collection'));
    Route::get('mymenu/{uid}',              array('as' => 'app_mymenu',         'uses' => 'AppController@myMenu'));
    Route::get('menuinfo/{menuid}',         array('as' => 'app_menuinfo',       'uses' => 'AppController@menuInfo'));
    Route::get('follower/{uid}',            array('as' => 'app_follower',       'uses' => 'AppController@follower'));
    Route::get('following/{uid}',           array('as' => 'app_following',      'uses' => 'AppController@following'));
    Route::get('comments/{category}/{id}',  array('as' => 'app_comment',        'uses' => 'AppController@comments'));
    Route::get('msg/summary',               array('as' => 'app_msg_summary',    'uses' => 'AppController@msgSummary'));
    Route::get('msg/list/{uid}',            array('as' => 'app_msg_list',       'uses' => 'AppController@msgList'));

    Route::any('post/comment',              array('as' => 'app_post_comment',   'uses' => 'AppController@postComment'));
    Route::any('post/menu',                 array('as' => 'app_post_menu',      'uses' => 'AppController@postMenu'));
    Route::any('add/wine/menu',             array('as' => 'app_add_wine_menu',  'uses' => 'AppController@addWineToMenu'));
    Route::any('post/msg',                  array('as' => 'app_post_msg',       'uses' => 'AppController@postMsg'));
    Route::any('post/record',               array('as' => 'app_post_record',    'uses' => 'AppController@postRecord'));
    Route::any('post/wine',                 array('as' => 'app_post_wine',      'uses' => 'AppController@postWine'));

    Route::any('update/relation',           array('as' => 'app_update_relation','uses' => 'AppController@updateRelation'));
});