<?php

use SleepingOwl\Models\Interfaces\ModelWithImageFieldsInterface;
use SleepingOwl\Models\SleepingOwlModel;
use SleepingOwl\Models\Traits\ModelWithImageOrFileFieldsTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserList extends SleepingOwlModel implements ModelWithImageFieldsInterface
{
	protected $table = 'user';
	use ModelWithImageOrFileFieldsTrait;

	public static $uploadPath = 'contacts/';

	protected $fillable = [
		'cover',
		'email',
		'nickname',
		'gender',
		'city_id'
	];

	protected $hidden = [
		'created_at',
		'updated_at'
	];

	public function getImageFields()
	{
		return [
			'cover' => self::$uploadPath
		];
	}

	public function getNicknameAttribute($value)
	{
		return $value ? $value : $this->username;
	}

	public static function getList()
	{
		return static::lists('email', 'id');
	}

	public function getImageAttribute()
	{
		return Config::get('app.url') . '/images/' . self::$uploadPath . (string)$this->cover;
	}
}