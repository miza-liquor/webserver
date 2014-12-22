<?php

Admin::model('BarList')->title('酒吧管理')->columns(function ()
{
    Column::image('image')->sortable(false);
	Column::string('name', '酒吧名称');
    Column::string('city', '城市');
    Column::string('address', '街道');
    Column::count('checkins', '签到人数')->append(Column::filter('bar_id')->model('UserList'));
    Column::string('location_lon', '经度');
    Column::string('location_lat', '纬度');
})->form(function ()
{
    FormItem::image('image', '酒吧图片')->required(true);
	FormItem::text('name', '酒吧名称')->required()->unique();
    FormItem::text('city', '所在城市')->required();
    FormItem::text('address', '街道');
    FormItem::text('location_lon', '经度');
    FormItem::text('location_lat', '经度');
});