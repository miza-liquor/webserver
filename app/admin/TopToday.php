<?php

Admin::model('TopToday')->title('今日热门管理')->columns(function ()
{
	Column::string('ranking', '排名')->sortable(false);
	Column::image('photo')->sortable(false);
	Column::string('title', '标题')->sortable(false);
	Column::string('desc', '描述')->sortable(false);
	Column::string('link', '链接')->sortable(false);
	Column::action('upgrade', '升级')->icon('fa-arrow-up')->style('long')->callback(function ($instance)
	{
		$instance->changeRanKingIndex($instance->ranking - 1, true);
	});
	Column::action('downgrade', '降级')->icon('fa-arrow-down')->style('long')->callback(function ($instance)
	{
		$instance->changeRanKingIndex($instance->ranking + 1, true);
	});
})->form(function ()
{
	FormItem::image('photo', '图片')->required(true);
	FormItem::text('title', '标题')->required(true);
	FormItem::text('link', '链接')->required(true)->validationRule('url');
	FormItem::textarea('desc', '描述')->required(true);
	FormItem::select('ranking', '排序')->list('TopToday')->required(true);
});