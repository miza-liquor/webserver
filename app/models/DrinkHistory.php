<?php

use SleepingOwl\Models\SleepingOwlModel;

class DrinkHistory extends SleepingOwlModel
{
    protected $table = 'drink_histories';

    protected $fillable = [
        'user_id',
        'wine_id',
        'drinked'
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

    public static function appDrinkHistory($uid, $drinked = 0)
    {
        $drinked = intval($drinked);
        $data = self::where('drinked', '=', $drinked)
                ->where('user_id', '=', $uid)
                ->get();
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