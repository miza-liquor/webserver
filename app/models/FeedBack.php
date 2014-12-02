<?php

use SleepingOwl\Models\SleepingOwlModel;

class FeedBack extends SleepingOwlModel
{
	protected $table = 'user_feedbacks';

	protected $fillable = [
		'user_id',
		'content'
	];

	protected $hidden = [
		'created_at',
		'updated_at'
	];
}