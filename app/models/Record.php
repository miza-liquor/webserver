<?php

use SleepingOwl\Models\Interfaces\ModelWithImageFieldsInterface;
use SleepingOwl\Models\SleepingOwlModel;
use SleepingOwl\Models\Traits\ModelWithImageOrFileFieldsTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Record extends SleepingOwlModel implements ModelWithImageFieldsInterface
{
    use ModelWithImageOrFileFieldsTrait;

    public static $uploadPath = 'records/';

    protected $fillable = ['creator_id', 'name', 'desc', 'image', 'address', 'location_lon', 'location_lat'];

    protected $hidden = ['created_at', 'updated_at'];

    public function getImageFields()
    {
        return [
            'photo' => self::$uploadPath
        ];
    }

    public function menus()
    {
        return $this->belongsToMany('WineMenu', 'wine_menu', 'record_id', 'menu_id');
    }

    public function getImageUrlAttribute()
    {
        return Config::get('app.url') . '/images/' . self::$uploadPath . $this->image;
    }

    public static function appFind($record_id, $record = null)
    {
        $record = $record ? $record : self::find($record_id);
        $record->image = $record->image_url;
        $record->creator = User::appFind($record->creator_id);

        return $record;
    }

    public static function postNewRecord()
    {
        $record = new Record;
        
        $record->name = trim(Input::get('name'));
        $record->desc = trim(Input::get('desc'));
        $record->creator_id = Auth::id();
        $record->address = trim(Input::get('address'));

        if (!$record->name)
        {
            return array('status' => 500, 'msg' => '记录标题不能为空', 'data' => null);
        }

        $image = Input::file('record_image');
        $menu_id = intval(Input::get('menu_id'));

        $validator = Validator::make(array('image' => $image), array('image' => 'image|required'));
        if ($validator->fails())
        {
            return array('status' => 500, 'msg' => '图片不能为空或者格式不正确', 'data' => null);
        }

        $image_name = time() . '-' . rand(10000, 99999) . '.' . $image->guessClientExtension();
        $uplaod = $image->move('images/' . self::$uploadPath, $image_name);
        if (!$uplaod)
        {
            return array('status' => 500, 'msg' => '图片上传失败，请重新上传', 'data' => null);
        }

        $record->image = $image_name;
        $record->save();

        // add to menu
        $menu_info = WineMenu::find($menu_id);
        if ($menu_info)
        {
            $record->menus()->sync([$menu_id], false);
        }

        return array('status' => 200, 'msg' => 'success', 'data' => self::appFind($record->id));
    }

    public static function search($uid, $keyword = null)
    {
        $keyword = trim($keyword);
        $search = new self;
        $data = array();

        if ($keyword)
        {
            $search = $search->where('name', 'like', "%$keyword%")
                        ->orWhere('desc', 'like', "%$keyword%")
                        ->orWhere('address', 'like', "%$keyword%");
        }

        $search = $search->take(50)->get();
        foreach ($search as $item) {
            $data[] = self::appFind($item->id, $item);
        }

        return $data;
    }
}