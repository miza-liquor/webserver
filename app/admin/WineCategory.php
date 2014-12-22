<?php

Admin::model('WineCategory')->title('酒库分类管理')->with('wines')->columns(function ()
{
    Column::string('id', '编号');
    Column::image('image', '图片')->sortable(false);
	Column::string('name', '分类中文名称');
    Column::string('e_name', '分类英文名称');
	Column::count('wines', '酒品数量')->append(Column::filter('category_id')->model('WineList'));
})->form(function ()
{
    FormItem::image('image', '图片');
	FormItem::text('name', '分类中文名称')->required(true)->unique();
    FormItem::text('e_name', '分类英文名称');
	FormItem::ckeditor('desc', '描述');
});