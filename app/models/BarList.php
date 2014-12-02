<?php

use SleepingOwl\Models\SleepingOwlModel;

class BarList extends SleepingOwlModel
{
	protected $table = 'bars';

	protected $fillable = [
		'name'
	];

	protected $hidden = [
		'created_at',
		'updated_at'
	];

	public static function getList()
	{
		return static::lists('name', 'id');
	}
}