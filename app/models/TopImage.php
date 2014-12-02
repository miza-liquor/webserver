<?php

use SleepingOwl\Models\Interfaces\ModelWithImageFieldsInterface;
use SleepingOwl\Models\SleepingOwlModel;
use SleepingOwl\Models\Traits\ModelWithImageOrFileFieldsTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TopImage extends SleepingOwlModel implements ModelWithImageFieldsInterface
{
	use ModelWithImageOrFileFieldsTrait;

	public static $uploadPath = 'contacts/';

	protected $fillable = [
		'photo',
		'link'
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

	public function getImageUrlAttribute()
	{
		return '/images/' . self::$uploadPath . (string)$row->photo;
	}

	public static function appAll()
	{
		$all = self::all();
		foreach ($all as $key => $row) {
			$row->photo = Config::get('app.url') . '/images/' . self::$uploadPath . $row->photo;
		}
		return $all;
	}
}