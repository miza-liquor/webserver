<?php

use SleepingOwl\Models\Interfaces\ModelWithImageFieldsInterface;
use SleepingOwl\Models\SleepingOwlModel;
use SleepingOwl\Models\Traits\ModelWithImageOrFileFieldsTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class WineList extends SleepingOwlModel implements ModelWithImageFieldsInterface
{
	protected $table = 'wines';
	public static $uploadPath = 'wines/';
	use ModelWithImageOrFileFieldsTrait;

	protected $fillable = [
		'c_name',
		'e_name',
		'category_id',
		'country_id',
		'creator_id',
		'maker',
		'image',
		'desc',
		'status'
	];

	protected $hidden = [];

	public function scopeDefaultSort($query)
	{
		return $query->orderBy('wines.updated_at', 'DESC');
	}

	public function getImageFields()
	{
		return [
			'image' => self::$uploadPath
		];
	}

	public function category()
	{
		return $this->belongsTo('WineCategory', 'category_id');
	}

	public function country()
	{
		return $this->belongsTo('Country', 'country_id');
	}

	public function creator()
	{
		return $this->belongsTo('User', 'creator_id');
	}

	public function menus()
	{
		return $this->belongsToMany('WineList', 'wine_menu', 'wine_id', 'menu_id');
	}

	public function drinked()
	{
		return $this->belongsToMany('User', 'drink_histories', 'wine_id', 'user_id')->where('drinked', '1');
	}

	public function drinking()
	{
		return $this->belongsToMany('User', 'drink_histories', 'wine_id', 'user_id')->where('drinked', '0');
	}

	public function alldrink()
	{
		return $this->belongsToMany('User', 'drink_histories', 'wine_id', 'user_id');
	}

	public function scopeWithoutCompanies($query)
	{
		$menu_id = intval(Input::get('menu_id', 0));
		if ($menu_id > 0)
		{
			return $query->whereRaw("id in (select `wine_id` from `wine_menu` where wine_id=wines.id and menu_id = $menu_id)");
		}
		return '';
	}

	public static function drink($wine_id, $type)
	{
		$wine = self::find($wine_id);
		$data = array();

		$drinklist = $type == 'drinked' ? $wine->drinked()->get() : $wine->drinking()->get();

		foreach ($drinklist as $user) {
			$data[] = User::appFind($user->id, $user);
		}

		return $data;
	}

	public static function appFind($wine_id, $wine = null)
	{
		$wine = $wine ? $wine : self::find($wine_id);
		$wine->image = $wine->wine_image;
		$wine->drinked = $wine->drinked()->count();
		$wine->drinking = $wine->drinking()->count();
		$wine->menus = $wine->menus()->count();
		$wine->category_name = $wine->category()->first()->name;
		$wine->country_name = $wine->country()->first()->c_name;
		$wine->creator = User::appFind($wine->creator_id);

		return $wine;
	}

	public static function search($uid, $keyword = null)
	{
		$keyword = trim($keyword);
		$category = intval(Input::get('category'));
		$search = new self;
		$data = array();

		$search = $search->where('status', '=', 0);
		if ($category)
		{
			$search = $search->where('category_id', '=', $category);
		}

		if ($keyword)
		{
			$search = $search->where(function($query) use($keyword) {
				$query->where('c_name', 'like', "%$keyword%")
					  	->orWhere('c_name', 'like', "%$keyword%")
					  	->orWhere('e_name', 'like', "%$keyword%")
					  	->orWhere('maker', 'like', "%$keyword%");
			});
		}

		$search = $search->take(50)->get();
		foreach ($search as $item) {
			$wine_info = self::appFind($item->id, $item);
			$wine_info->drink_user = $wine_info->drinked()->get();
			$data[] = $wine_info;
		}

		return $data;
	}

	public static function addToMenu()
	{
		Input::merge(array_map('trim', Input::all()));
		$wine = self::find(Input::get('wine_id'));
		$wine->menus()->sync([Input::get('menu_id')], false);
	}

	public static function postNewWine()
	{
		$wine = new WineList;
		$wine->status = 1;
		$wine->c_name = trim(Input::get('name'));
        $wine->desc = trim(Input::get('desc'));
        $wine->category_id = Auth::id();
        
	}

	public static function getList()
	{
		return static::lists('c_name', 'id');
	}

	public function getWineImageAttribute()
	{
		return Config::get('app.url') . '/images/' . self::$uploadPath . (string) $this->image;
	}
}