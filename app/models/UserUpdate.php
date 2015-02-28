<?php

use SleepingOwl\Models\SleepingOwlModel;

class UserUpdate extends SleepingOwlModel
{

    protected $table = 'user_comments';
    protected $fillable = ['user_id', 'content'];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function scopeDefaultSort($query)
    {
        return $query->orderBy('id', 'desc');
    }

    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }
}