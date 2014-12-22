<?php

use SleepingOwl\Models\Interfaces\ModelWithImageFieldsInterface;
use SleepingOwl\Models\SleepingOwlModel;
use SleepingOwl\Models\Traits\ModelWithImageOrFileFieldsTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TopToday extends SleepingOwlModel implements ModelWithImageFieldsInterface
{
	use ModelWithImageOrFileFieldsTrait;

	public static $uploadPath = 'contacts/';
	protected $table = 'top_todays';

	protected $fillable = [
		'photo',
		'title',
		'link',
		'desc',
		'ranking'
	];

	protected $hidden = [
		'created_at',
		'updated_at'
	];

	public function getImageFields()
	{
		return [
			'photo' => self::$uploadPath
		];
	}

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
			DB::update("update top_todays set `ranking` = `ranking` + 1 where `ranking` >= ?", array($ranking));
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

	public static function getList()
	{
		$count = self::all()->count();
		$list = array();

		for ($i=1; $i <= $count; $i++) { 
			$list[$i] = $i;
		}

		return $count > 0 ? $list : array('1' => 1);
	}

	public function getImageAttribute()
	{
		return Config::get('app.url') . '/images/' . self::$uploadPath . (string)$this->photo;
	}

	public function getCreatedAtAttribute($date)
	{
    	return Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y年m月日');
	}

	public static function appAll()
	{
		$all = self::all();
		$data = array();

		foreach ($all as $key => $row)
		{
			$data[] = array(
				'id' => $row->id,
				'title' => $row->title,
				'photo' => $row->image,
				'date' => $row->created_at,
				'link' => $row->link
			);
		}

		return $data;
	}
}