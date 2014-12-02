<?php

Admin::model('TopWineMenu')->title('热门酒单管理')->columns(function ()
{
	Column::string('ranking', '排名')->sortable(false);
	Column::string('menu_id', '酒单编号')->sortable(false);
	Column::string('menu.name', '酒单名称')->sortable(false);
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
	FormItem::select('menu_id', '酒单编号')->list('WineMenu')->required(true)->unique();
	FormItem::select('ranking', '排序')->list('TopWineMenu')->required(true);
});