<?php

use SleepingOwl\Models\SleepingOwlModel;

class TopUser extends SleepingOwlModel
{
	protected $table = 'top_users';

	protected $fillable = [
		'user_id',
		'ranking',
		'reason_post',
		'reason_praise',
		'reason_be_praised',
		'reason_comment',
		'reason_checkin',
		'reason_be_collected',
		'reason_menu_master'
	];

	protected $hidden = [
		'created_at',
		'updated_at'
	];

	public function scopeDefaultSort($query)
	{
		return $query->orderBy('ranking', 'asc');
	}

	public function changeRanKingIndex($ranking, $autoupdate=false)
	{
		if ($ranking < 1)
		{
			return;
		}

		if (!$this->id)
		{
			DB::update("update top_users set `ranking` = `ranking` + 1 where `ranking` >= ?", array($ranking));
		} else {
			$swap = self::where('ranking', '=', $ranking);
			if ($swap->first())
			{
				$swap->update(array('ranking' => $this->ranking));
			} else {
				$ranking = self::all()->count();
			}
		}

		$this->attributes['ranking'] = $ranking;
		if ($autoupdate)
		{
			$this->save();
		}
	}

	public function getReasonsAttribute()
	{
		$reasons = array();

		if ($this->reason_post)
		{
			array_push($reasons, '发布最多');
		}
		if ($this->reason_praise)
		{
			array_push($reasons, '点赞最多');
		}
		if ($this->reason_be_praised)
		{
			array_push($reasons, '被赞最多');
		}
		if ($this->reason_comment)
		{
			array_push($reasons, '评论最多');
		}
		if ($this->reason_checkin)
		{
			array_push($reasons, '签到最多');
		}
		if ($this->reason_be_collected)
		{
			array_push($reasons, '被收藏最多');
		}
		if ($this->reason_menu_master)
		{
			array_push($reasons, '酒单大师');
		}

		return $reasons;
	}

	public function getReasonsStringAttribute()
	{
		return implode($this->reasons, '<br />');
	}

	public function setRanKingAttribute($ranking)
	{
		$this->changeRanKingIndex($ranking);
	}

	public function user()
	{
		return $this->belongsTo('User', 'user_id');
	}

	public static function getList()
	{
		$count = self::all()->count();
		$list = array();

		for ($i=1; $i <= $count; $i++) { 
			$list[$i] = $i;
		}

		return $count > 0 ? $list : array('1' => 1);
	}

	public static function appAll()
	{
		$all = self::where('id', '!=', Auth::id())->take(10)->get();
		$list = array();

		foreach ($all as $top_user) {
			$user_info = User::appFind($top_user->user_id);
			$reasons = $top_user->reasons;
			$user_info->reasons = implode($reasons, '、');
			$list[] = $user_info;
		}

		return array(
			'本周最热10名用户推荐' => $list,
			'历史排名前20酒神' => $list
		);
	}
}