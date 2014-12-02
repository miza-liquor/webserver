<?php

Admin::model('UserList')->title('用户列表')->columns(function ()
{
	Column::image('cover', '头像')->sortable(false);
	Column::string('nickname', '昵称');
	Column::string('email', '注册邮箱');
	Column::string('gender', '性别');
	Column::string('city_id', '城市');
})->form(function ()
{
	FormItem::image('cover', '头像')->required(true);
	FormItem::text('email', '注册邮箱')->required(true)->validationRule('email');
	FormItem::text('nickname', '昵称');
	FormItem::select('gender', '性别')->enum(['male', 'female']);
	FormItem::text('city_id', '城市');
});