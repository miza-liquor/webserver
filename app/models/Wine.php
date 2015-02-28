<?php

use SleepingOwl\Models\Interfaces\ModelWithImageFieldsInterface;
use SleepingOwl\Models\SleepingOwlModel;
use SleepingOwl\Models\Traits\ModelWithImageOrFileFieldsTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Wine extends SleepingOwlModel implements ModelWithImageFieldsInterface
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
        'desc'
    ];

    protected $hidden = [];

    // public function scopeDefaultSort($query)
    // {
    //  return $query->orderBy('id', 'DESC');
    // }

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

    public function drinked()
    {
        return $this->belongsToMany('User', 'drink_histories', 'wine_id', 'user_id')->withPivot('drinked', 1);
    }

    public function drinking()
    {
        return $this->belongsToMany('User', 'drink_histories', 'wine_id', 'user_id')->withPivot('drinked', 0);
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

    public static function appFind($wine_id, $wine = null)
    {
        $wine = $wine ? $wine : self::find($wine_id);
        $wine->image = $wine->wine_image;
        $wine->drinked = $wine->drinked()->count();
        $wine->category_name = $wine->category->name;
        $wine->country_name = $wine->country->c_name;
        $wine->creator = User::appFind($wine->creator_id);

        return $wine;
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