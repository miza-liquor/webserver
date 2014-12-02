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
		$reaseans = array();

		if ($this->reason_post)
		{
			array_push($reaseans, '发布最多');
		}
		if ($this->reason_praise)
		{
			array_push($reaseans, '点赞最多');
		}
		if ($this->reason_be_praised)
		{
			array_push($reaseans, '被赞最多');
		}
		if ($this->reason_comment)
		{
			array_push($reaseans, '评论最多');
		}
		if ($this->reason_checkin)
		{
			array_push($reaseans, '签到最多');
		}
		if ($this->reason_be_collected)
		{
			array_push($reaseans, '被收藏最多');
		}
		if ($this->reason_menu_master)
		{
			array_push($reaseans, '酒单大师');
		}

		return implode($reaseans, '<br />');
	}

	public function setRanKingAttribute($ranking)
	{
		$this->changeRanKingIndex($ranking);
	}

	public function user()
	{
		return $this->belongsTo('UserList', 'user_id');
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
		
	}
}