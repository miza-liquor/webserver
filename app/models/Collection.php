<?php

use SleepingOwl\Models\SleepingOwlModel;

class Collection extends SleepingOwlModel
{
    protected $table = 'user_collections';

    protected $fillable = [
        'user_id',
        'wine_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo('UserList', 'user_id');
    }

    public function wine()
    {
        return $this->belongsTo('WineList', 'wine_id');
    }

    public static function appUserCollection($uid)
    {
        $data = self::where('user_id', '=', $uid)->get();
        $response = array();

        foreach ($data as $history)
        {
            $wine = $history->wine;

            $response[] = array(
                'id' => $wine->id,
                'wine_image' => $wine->wine_image
            );
        }

        return $response;
    }
}