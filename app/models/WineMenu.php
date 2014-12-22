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

	public function getAppMenuInfo()
	{
		$creator 	= $this->creator;
		$top_menus 	= $this->wines->take(5);
		$top_menus_data = array();

		foreach ($top_menus as $top_menu) {
			$top_menus_data[] = $top_menu->wine_image;
		}
		$data = array(
			'id' 			=> $this->id,
			'menu_id' 		=> $this->id,
			'menu_name' 	=> $this->name,
			'creator_id' 	=> $creator->id,
			'creator_name' 	=> $creator->nickname,
			'creator_image' => $creator->image,
			'like_num' 		=> $this->likes()->count(),
			'liked' 		=> $this->hasLiked(),
			'wine_num'		=> $this->wines->count(),
			'menus' 		=> $top_menus_data,
			'menu_image'	=> count($top_menus_data) > 0 ? $top_menus_data[0] : ''
		);

		return $data;
	}

	public static function appUserMenus($uid)
	{
		$data = self::where('creator_id', '=', $uid)->get();
		$response = array();

		foreach ($data as $menu) {
			$response[] = $menu->getAppMenuInfo();
		}

		return $response;
	}
}