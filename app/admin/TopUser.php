<?php

Admin::model('TopUser')->title('热门用户推荐管理')->columns(function ()
{
	Column::string('ranking', '排名')->sortable(false);
	Column::string('user_id', '用户编号')->sortable(false);
	Column::string('user.nickname', '用户名称')->sortable(false);
	Column::string('reasons_string', '推荐理由')->sortable(false);
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
	FormItem::select('user_id', '用户')->list('UserList')->required(true)->unique();
	FormItem::select('ranking', '排序')->list('TopUser')->required(true);
	FormItem::checkbox('reason_post', '发布最多');
	FormItem::checkbox('reason_praise', '点赞最多');
	FormItem::checkbox('reason_be_praised', '被赞最多');
	FormItem::checkbox('reason_comment', '评论最多');
	FormItem::checkbox('reason_checkin', '签到最多');
	FormItem::checkbox('reason_be_collected', '被收藏最多');
	FormItem::checkbox('reason_menu_master', '酒单大师');
});