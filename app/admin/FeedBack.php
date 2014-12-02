<?php

Admin::model('FeedBack')->title('投诉反馈')->columns(function ()
{
	Column::string('user_id', '用户昵称');
	Column::string('content', '内容');
	Column::date('created_at', '提交时间')->format('short', 'short');
})
->denyCreating();