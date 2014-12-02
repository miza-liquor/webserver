<?php

Admin::model('UserLevel')->title('用户等级')->columns(function ()
{
	Column::string('name', '等级名称');
	Column::string('require', '等级经验要求');
	Column::image('logo', '等级图标')->sortable(false);
})->form(function ()
{
	FormItem::image('logo', '等级图标')->required(true);
	FormItem::text('name', '昵称')->required(true);
	FormItem::text('require', '等级经验要求')->required(true)->validationRule('numeric');
});