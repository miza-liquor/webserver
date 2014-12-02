<?php

Admin::model('TopWine')->title('酒品排行榜管理')->columns(function ()
{
	Column::string('ranking', '排名')->sortable(false);
	Column::string('wine_id', '酒品编号')->sortable(false);
	Column::string('wine.c_name', '酒品名称')->sortable(false);
	Column::string('wine.score_num', '评分人数')->sortable(false);
	Column::string('wine.score_value', '评分')->sortable(false);
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
	FormItem::select('wine_id', '酒品编号')->list('WineList')->required(true)->unique();
	FormItem::select('ranking', '排序')->list('TopWine')->required(true);
});