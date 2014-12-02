<?php

use SleepingOwl\Models\SleepingOwlModel;

class Country extends SleepingOwlModel
{

	protected $table = 'countries';
	protected $fillable = ['e_name', 'c_name', 'e_full_name', 'code_abc_2', 'code_abc_3', 'code_number', 'comment'];

	protected $hidden = [
		'created_at',
		'updated_at'
	];

	public function scopeDefaultSort($query)
	{
		return $query->orderBy('id', 'asc');
	}

	public function wines()
	{
		return $this->hasMany('WineList', 'country_id');
	}

	public static function getList()
	{
		return static::lists('c_name', 'id');
	}

}