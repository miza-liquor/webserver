<?php

use SleepingOwl\Models\SleepingOwlModel;

class Comment extends SleepingOwlModel
{

    protected $table = 'user_comments';
    protected $fillable = ['category', 'content_id', 'user_id', 'replay_to', 'content'];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function scopeDefaultSort($query)
    {
        return $query->orderBy('id', 'asc');
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y年m月日');
    }

    public function user()
    {
        return $this->belongsTo('UserList', 'user_id');
    }

    public static function appComments($category, $id)
    {
        $comments = self::where('category', '=', $category)
                    ->where('content_id', '=', $id);
        $response = array();
        $data = $comments->get();

        foreach ($data as $comment) {
            $user = $comment->user;
            $response[] = array(
                'id' => $comment->id,
                'user_id' => $comment->user_id,
                'user_image' => $user->image,
                'user_nick' => $user->nickname,
                'content' => $comment->content,
                'created_at' => $comment->created_at
            );
        }

        return array(
            'comments' => $response,
            'count' => $comments->count()
        );
    }

    public static function post()
    {
        $comment = new Comment;
        $comment->user_id = Input::get('user_id');
        $comment->content_id = Input::get('content_id');
        $comment->category = Input::get('category');
        $comment->content = Input::get('content');
        $comment->save();

        return $comment;
    }
}