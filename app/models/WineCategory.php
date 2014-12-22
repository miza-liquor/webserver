<?php

use SleepingOwl\Models\Interfaces\ModelWithImageFieldsInterface;
use SleepingOwl\Models\SleepingOwlModel;
use SleepingOwl\Models\Traits\ModelWithImageOrFileFieldsTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class WineCategory extends SleepingOwlModel implements ModelWithImageFieldsInterface
{
	use ModelWithImageOrFileFieldsTrait;

	public static $uploadPath = 'winecategory/';
	protected $table = 'wine_categories';
	protected $fillable = [
		'image',
		'name',
		'e_name',
		'desc'
	];

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

	public function wines()
	{
		return $this->hasMany('WineList', 'category_id');
	}

	public function getImageUrlAttribute()
	{
		return Config::get('app.url') . '/images/' . self::$uploadPath . $this->image;
	}

	public static function getList()
	{
		return static::lists('name', 'id');
	}

	public static function appAll()
	{
		$all = self::all();
		$data = array();

		foreach ($all as $category)
		{
			$data[] = array(
				'id' => $category->id,
				'image' => $category->image_url,
				'name' => $category->name,
				'e_name' => $category->e_name
			);
		}

		return $data;
	}
}