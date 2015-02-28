<?php

Admin::model('Wine')->title('酒品管理')->filters(function ()
{
    // ModelItem::filter('category_id')->title()->from('WineCategory', 'name');
    // ModelItem::filter('country_id')->title()->from('Country', 'c_name');
    // ModelItem::filter('creator_id')->title()->from('UserList', 'nickname');
    // ModelItem::filter('menu_id')->scope('withoutCompanies')->title()->from('WineMenu', 'name');
})->columns(function ()
{
    Column::string('id', 'ID');
    Column::image('image', '图片')->sortable(false);
    Column::string('c_name', '酒品名称');
    Column::string('category.name', '分类名称')->append(Column::filter('category_id')->value('category.id'));
    Column::string('country.c_name', '国家或地区')->append(Column::filter('country_id')->value('country.id'));
    Column::string('creator.nickname', '投递者')->append(Column::filter('creator_id')->value('creator.id'));
})->form(function ()
{
    FormItem::image('image', '图片')->required(true);
    FormItem::text('c_name', '中文名称')->required(true);
    FormItem::text('e_name', '英文名称')->required(true);
    FormItem::select('category_id', '分类')->list('\WineCategory')->required(true);
    FormItem::select('country_id', '国家或地区')->list('Country')->required(true);
    FormItem::select('creator_id', '投递者')->list('UserList')->required(true);
    FormItem::text('maker', '酒商')->required(true);
    FormItem::textarea('desc', '描述')->required(true);
});