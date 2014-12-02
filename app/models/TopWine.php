<?php

use SleepingOwl\Models\SleepingOwlModel;

class TopWine extends SleepingOwlModel
{
	protected $table = 'top_wines';

	protected $fillable = [
		'wine_id',
		'ranking'
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
			DB::update("update top_wines set `ranking` = `ranking` + 1 where `ranking` >= ?", array($ranking));
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

	public function setRanKingAttribute($ranking)
	{
		$this->changeRanKingIndex($ranking);
	}

	public function wine()
	{
		return $this->belongsTo('WineList', 'wine_id');
	}

	public static function getList()
	{
		$count = TopWine::all()->count();
		$list = array();

		for ($i=1; $i <= $count; $i++) { 
			$list[$i] = $i;
		}

		return $list;
	}
}