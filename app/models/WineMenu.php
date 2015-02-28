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

	public function scopeDefaultSort($query)
	{
		return $query->orderBy('menus.updated_at', 'DESC');
	}

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

	public function getAppMenuInfo($getall = false)
	{
		$creator 	= $this->creator;
		$menus_data = array();
		$menu_image = '';

		if ($getall)
		{
			$menu_wines = $this->wines;
			foreach ($menu_wines as $wine)
			{
				$menus_data[] = WineList::appFind($wine->id, $wine);
			}
			$menu_image = count($menus_data) > 0 ? $menus_data[0]->wine_image : '';
		} else {
			$menu_wines = $this->wines->take(5);
			foreach ($menu_wines as $wine)
			{
				$menus_data[] = $wine->wine_image;
			}
			$menu_image = count($menus_data) > 0 ? $menus_data[0] : '';
		}

		$data = array(
			'id' 			=> $this->id,
			'menu_id' 		=> $this->id,
			'menu_name' 	=> $this->name,
			'menu_desc'		=> $this->desc,
			'creator_id' 	=> $creator->id,
			'creator_name' 	=> $creator->nickname,
			'creator_image' => $creator->image,
			'like_num' 		=> $this->likes()->count(),
			'liked' 		=> $this->hasLiked(),
			'wine_num'		=> $this->wines->count(),
			'menus' 		=> $menus_data,
			'menu_image'	=> $menu_image
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

	public static function search($uid, $keyword = null)
	{
		$keyword = trim($keyword);
		$data = array();
		$search;
		$take = 50;

		if ($keyword)
		{
			$search = self::where('name', 'like', "%$keyword%")
						->orWhere('desc', 'like', "%$keyword%")->take($take)->get();
		} else {
			$search = self::all()->take($take);
		}

		foreach ($search as $menu) {
			$data[] = $menu->getAppMenuInfo();
		}

		return $data;
	}

	public static function addMenu()
	{
		Input::merge(array_map('trim', Input::all()));

		if (!Input::get('name'))
		{
			return array('msg' => '酒单名称不能为空', 'data' => null, 'status' => 412);
		}

		$check = self::where('creator_id', '=', Auth::id())
					->where('name', '=', Input::get('name'))
					->count();

		if ($check)
		{
			return array('msg' => '你的酒单已经存在，不需创建', 'data' => array(), 'status' => 421);
		}

		$new = new WineMenu;
		$new->name = Input::get('name');
		$new->desc = Input::get('desc');
		$new->creator_id = Auth::id();
		$new->save();

		return array('msg' => '提交成功', 'data' => $new->getAppMenuInfo(), 'status' => 200);
	}
}