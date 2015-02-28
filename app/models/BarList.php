<?php

use SleepingOwl\Models\Interfaces\ModelWithImageFieldsInterface;
use SleepingOwl\Models\SleepingOwlModel;
use SleepingOwl\Models\Traits\ModelWithImageOrFileFieldsTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BarList extends SleepingOwlModel implements ModelWithImageFieldsInterface
{
	use ModelWithImageOrFileFieldsTrait;

	public static $uploadPath = 'bars/';
	protected $table = 'bars';

	protected $fillable = ['name', 'image', 'city', 'address', 'location_lat', 'location_lon'];

	protected $hidden = [
		'created_at',
		'updated_at'
	];

	public function getImageFields()
	{
		return [
			'image' => self::$uploadPath
		];
	}

	public function getImageUrlAttribute()
	{
		return Config::get('app.url') . '/images/' . self::$uploadPath . (string)$this->image;
	}

	public static function getList()
	{
		return static::lists('name', 'id');
	}

	public function checkins()
	{
		return $this->belongsToMany('UserList', 'bar_checkins', 'bar_id', 'user_id');
	}

	public function comments()
	{
		return $this->hasMany('Comment', 'content_id')->where('category', '=', 'bar');
	}

	public function barBasicInfo()
	{
		$checkins = $this->checkins;
		$checkin_users = $checkins->take(6);
		$top_users = array();
		foreach ($checkin_users as $user) {
			$top_users[] = array(
				'image' => $user->image
			);
		}
		return array(
			'id' 		=> $this->id,
			'image'		=> $this->image_url,
			'name' 		=> $this->name,
			'city' 		=> $this->city,
			'address' 	=> $this->address,
			'top_users' => $top_users,
			'lon'		=> $this->location_lon,
			'lat'		=> $this->location_lat,
			'checkin_num' => $checkins->count()
		);
	}

	public static function recommondBars($uid = 0)
	{
		$data = array();
		$top_checkin_bars = DB::table('bar_checkins')
							->select(DB::raw('bar_id, count(*) as checkin_num'))
							->groupBy('bar_id')
							->orderBy('checkin_num', 'desc')
							->take(20)
							->get();

		foreach ($top_checkin_bars as $row)
		{
			$bar_info = self::find($row->bar_id);
			if (!$bar_info) continue;
			$data[] = $bar_info->barBasicInfo();
		}

		return $data;
	}
}