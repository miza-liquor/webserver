<?php

/*
 * Describe your menu here.
 *
 * There is some simple examples what you can use:
 *
 * 		Admin::menu()->url('/')->label('Start page')->icon('fa-dashboard')->uses('\AdminController@getIndex');
 * 		Admin::menu(User::class)->icon('fa-user');
 * 		Admin::menu()->label('Menu with subitems')->icon('fa-book')->items(function ()
 * 		{
 * 			Admin::menu(\Foo\Bar::class)->icon('fa-sitemap');
 * 			Admin::menu('\Foo\Baz')->label('Overwrite model title');
 * 			Admin::menu()->url('my-page')->label('My custom page')->uses('\MyController@getMyPage');
 * 		});
 */

Admin::menu()->url('/')->label('首页')->icon('fa-dashboard')->uses('AdminController@index');

Admin::menu()->label('探索页面管理')->icon('fa-user')->items(function(){
	Admin::menu('TopWine')->icon('fa-user');
	Admin::menu('TopImage')->icon('fa-user');
	Admin::menu('TopUser')->icon('fa-user');
	Admin::menu('BarList')->icon('fa-user');
	Admin::menu('TopToday')->icon('fa-user');
	Admin::menu('TopWineMenu')->icon('fa-user');
});

Admin::menu()->label('用户管理')->icon('fa-user')->items(function(){
	Admin::menu('UserList')->icon('fa-user');
	Admin::menu('UserLevel')->icon('fa-user');
});

Admin::menu()->label('酒库管理')->icon('fa-user')->items(function(){
	Admin::menu('WineCategory')->icon('fa-user');
	Admin::menu('WineList')->icon('fa-user');
	Admin::menu('Country')->icon('fa-user');
});

Admin::menu()->label('内容管理')->icon('fa-user')->items(function(){
	Admin::menu('WineMenu')->icon('fa-user');
});

Admin::menu('FeedBack')->icon('fa-user');

Admin::menu()->url('/statistics')->label('数据统计')->icon('fa-user')->uses('AdminController@statistics');

Admin::menu()->label('系统通知')->icon('fa-user')->items(function(){

});

Admin::menu()->label('权限管理')->icon('fa-user')->items(function(){

});