<?php

use SleepingOwl\Models\SleepingOwlModel;

class TopWineMenu extends SleepingOwlModel
{
	protected $table = 'top_menus';

	protected $fillable = [
		'menu_id',
		'ranking'
	];

	protected $hidden = [
		'created_at',
		'updated_at'
	];

	public function scopeDefaultSort($query)
	{
		return $query->orderBy('ranking', 'asc');
	}

	public function menu()
	{
		return $this->belongsTo('WineMenu', 'menu_id');
	}

	public function changeRanKingIndex($ranking, $autoupdate=false)
	{
		if ($ranking < 1)
		{
			return;
		}

		if (!$this->id)
		{
			DB::update("update top_menus set `ranking` = `ranking` + 1 where `ranking` >= ?", array($ranking));
		} else {
			$swap = self::where('ranking', '=', $ranking);
			if ($swap->first())
			{
				$swap->update(array('ranking' => $this->ranking));
			} else {
				$ranking = self::all()->count();
			}
		}

		$this->attributes['ranking'] = $ranking;
		if ($autoupdate)
		{
			$this->save();
		}
	}

	public function setRanKingAttribute($ranking)
	{
		$this->changeRanKingIndex($ranking);
	}

	public static function getList()
	{
		$count = self::all()->count();
		$list = array();

		for ($i=1; $i <= $count; $i++) { 
			$list[$i] = $i;
		}

		return $count > 0 ? $list : array('1' => 1);
	}

	public static function appAll()
	{
		$all = self::all()->take(10);
		$data = array();
		foreach ($all as $key => $row) {
			$top_menus = $row->menu->wines->take(5);
			$top_menus_data = array();
			$menu = $row->menu;
			$creator = $menu->creator;

			foreach ($top_menus as $top_menu) {
				$top_menus_data[] = $top_menu->wine_image;
			}

			$data[] = array(
				'id' 			=> $row->id,
				'menu_id' 		=> $row->menu_id,
				'menu_name' 	=> $menu->name,
				'creator_id' 	=> $creator->id,
				'creator_name' 	=> $creator->nickname,
				'creator_image' => $creator->image,
				'like_num' 		=> $menu->likes()->count(),
				'liked' 		=> $menu->hasLiked(),
				'menus' 		=> $top_menus_data
			);
		}
		return $data;
	}
}