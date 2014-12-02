<?php

Admin::model('BarList')->title('酒吧管理')->columns(function ()
{
	Column::string('name', '酒吧名称');
})->form(function ()
{
	FormItem::text('name', '酒吧名称')->required(true);
});