<?php

Admin::model('WineMenu')->title('酒单')->columns(function ()
{
	Column::string('id', '编号');
	Column::string('name', '酒单名称');
	Column::string('creator.nickname', '发布者')->append(Column::filter('creator_id')->value('creator.id'));
	Column::count('wines', '酒品数量')->append(Column::filter('menu_id')->model('WineList'));
	Column::string('desc', '描述');
	// Column::date('created_at', '发布时间')->format('short', 'short');
})->form(function ()
{
	FormItem::text('name', '酒单名称')->required(true);
	FormItem::select('creator_id', '发布者')->list('UserList')->required(true);
	FormItem::multiSelect('wines', '酒品')->list('WineList')->value('wines.wine_id');
	FormItem::text('desc', '酒单描述');
});