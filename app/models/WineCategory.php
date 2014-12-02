<?php

use SleepingOwl\Models\SleepingOwlModel;

class WineCategory extends SleepingOwlModel
{
	protected $table = 'wine_categories';

	protected $fillable = [
		'name',
		'e_name',
		'desc'
	];

	protected $hidden = [
		'created_at',
		'updated_at'
	];

	public function wines()
	{
		return $this->hasMany('WineList', 'category_id');
	}

	public static function getList()
	{
		return static::lists('name', 'id');
	}
}