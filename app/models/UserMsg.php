<?php

use SleepingOwl\Models\SleepingOwlModel;

class UserMsg extends SleepingOwlModel
{

    protected $table = 'user_chats';
    protected $fillable = ['poster_id', 'recipient_id', 'content', 'read'];

    protected $hidden = [
        // 'created_at',
        'updated_at'
    ];

    // public function scopeDefaultSort($query)
    // {
    //     return $query->orderBy('id', 'desc');
    // }

    public function poster()
    {
        return $this->belongsTo('User', 'poster_id');
    }

    public function recipient()
    {
        return $this->belongsTo('User', 'recipient_id');
    }

    public function getCreatedAtAttribute($date)
    {
        // return Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y/m/d');
        return $date;
    }

    public static function summary($user_id)
    {
        $data = [];
        $charts = self::where('poster_id', '=', $user_id)
                    ->orWhere('recipient_id', '=', $user_id)
                    ->groupBy('poster_id')
                    ->groupBy('recipient_id')
                    ->orderBy('created_at', 'desc')
                    ->get();

        foreach ($charts as $chart) {
            $show_user_id = ($chart->poster_id == $user_id) ? $chart->recipient_id : $chart->poster_id;

            $data[] = array(
                'created_at' => $chart->created_at,
                'content' => $chart->content,
                'user' => User::appFind($show_user_id)
            );

        }

        return $data;
    }

    public static function getMsgList($uid)
    {
        $base = array('owner' => Auth::id(), 'uid' => $uid);
        $last_id = intval(Input::get('last_id', 0));

        $charts = self::where(function ($query) use($base){
                    $query->where(function ($query) use($base){
                        $query->where('poster_id', '=', $base['owner'])
                            ->Where('recipient_id', '=', $base['uid']);
                        })
                        ->orWhere(function ($query) use($base){
                            $query->where('poster_id', '=', $base['uid'])
                                    ->Where('recipient_id', '=', $base['owner']);
                        });
                })
                ->where('id', '>', $last_id)
                ->orderBy('created_at', 'desc')
                ->get();

        return $charts;
    }

    public static function postMsg()
    {
        Input::merge(array_map('trim', Input::all()));
        if (!Input::get('recipient_id'))
        {
            return array('status' => 422, 'msg' => '用户不能为空', 'data' => null);
        }

        if (!Input::get('content'))
        {
            return array('status' => 422, 'msg' => '内容不能为空', 'data' => null);
        }

        $new = new UserMsg;
        $new->recipient_id = Input::get('recipient_id');
        $new->poster_id = Auth::id();
        $new->content = Input::get('content');
        $new->save();
        $new->id = '' . $new->id;
        $charts = self::getMsgList(Input::get('recipient_id'));

        return array('status' => 200, 'msg' => 'success', 'data' => $charts);
    }
}