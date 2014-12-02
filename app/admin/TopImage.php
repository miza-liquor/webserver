<?php

Admin::model('TopImage')->title('焦点图管理')->columns(function ()
{
	Column::image('photo')->sortable(false);
	Column::string('link', '链接');
})->form(function ()
{
	FormItem::image('photo', '图片')->required(true);
	FormItem::text('link', '链接')->validationRule('url');
});