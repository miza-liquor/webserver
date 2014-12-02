<?php

use SleepingOwl\Models\Interfaces\ModelWithImageFieldsInterface;
use SleepingOwl\Models\SleepingOwlModel;
use SleepingOwl\Models\Traits\ModelWithImageOrFileFieldsTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserLevel extends SleepingOwlModel implements ModelWithImageFieldsInterface
{
	protected $table = 'user_levels';
	use ModelWithImageOrFileFieldsTrait;

	protected $fillable = [
		'name',
		'require',
		'logo'
	];

	protected $hidden = [
		'created_at',
		'updated_at'
	];

	public function getImageFields()
	{
		return [
			'logo' => 'contacts/'
		];
	}

}