<?php

use SleepingOwl\Models\SleepingOwlModel;

class WineMenu extends SleepingOwlModel
{
	protected $table = 'menus';

	protected $fillable = [
		'name',
		'creator_id',
		'desc',
		'wines'
	];

	protected $hidden = [
		'created_at',
		'updated_at'
	];

	public static function getList()
	{
		return static::lists('name', 'id');
	}

	public function creator()
	{
		return $this->belongsTo('UserList', 'creator_id');
	}

	public function wines()
	{
		return $this->belongsToMany('WineList', 'wine_menu', 'menu_id', 'wine_id')->orderBy('wine_id', 'desc');
	}

	public function likes()
	{
		return $this->belongsToMany('UserList', 'menu_likes', 'menu_id', 'user_id');
	}

	public function hasLiked()
	{
		if (!Auth::check())
		{
			return 0;
		}

		$mark = DB::table('menu_likes')
					->where('menu_id', '=', $this->id)
					->where('user_id', '=', Auth::id())
					->count();

		return $mark;
	}

	public function setWinesAttribute($wines)
	{
		$this->wines()->detach();
		if ( ! $wines) return;
		if ( ! $this->exists) $this->save();

		$this->wines()->attach($wines);
	}
}