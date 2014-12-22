<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	public static function loginAttempt()
	{
		// if login user, logout first
		if (Auth::check())
		{
			Auth::logout();
		}

		// trim all input
		Input::merge(array_map('trim', Input::all()));
		// login input item
		$data = array(
			'email' 	=> Input::get('uname', ''),
			'password' 	=> trim(Input::get('pwd', ''))
		);

		return ($data['email'] && $data['password'] && Auth::attempt($data, true));
	}

	public static function registerAttempt()
	{
		$respone = false;

		$data = array(
			'username' 	=> trim(Input::get('username')),
			'email' 	=> trim(Input::get('email')),
			'password' 	=> trim(Input::get('pwd')),
			'cpassword' => trim(Input::get('cpwd'))
		);

		$rules = array(
			"email" 	=> "required|email",
			"username" 	=> "required|between:8,32",
			"password"	=> "required|between:6,16|same:cpassword"
		);

		$messagesContent = array(
			'email.required' 	=> '邮箱不能为空',
			'email.unique'		=> '邮箱已经存在',
			'email.email' 		=> '邮箱格式不正确',
			'username.required' => '用户名不能为空',
			'username.between' 	=> '用户名字符个数只能在 :min ~ :max 之间',
			'username.unique'	=> '用户民已经存在',
			'password.required' => '密码不能为空',
			'password.between' 	=> '密码字符个数只能在 :min ~ :max 之间',
			'same'				=> '两次密码不相同'
		);

		$validator = Validator::make($data, $rules, $messagesContent);

		if ($validator->passes())
		{
			$checkUniqueMail = User::where('email', '=', $data['email'])->count();
			$checkUniqueUser = User::where('username', '=', $data['username'])->count();
		}

		if ($validator->passes() & $checkUniqueMail == 0 && $checkUniqueUser == 0)
		{
			$user = new User;
			$user->email = $data['email'];
			$user->username = $data['username'];
			$user->password = Hash::make($data['password']);
			$user->save();

			Auth::attempt(array('email' => $data['email'], 'password' => $data['password']), true);
		} else {
			$messages = $validator->messages();
			$respone = array();
			
			foreach ($rules as $key => $value)
			{
				if ($messages->has($key))
				{
					$respone[$key] = $messages->first($key);
				}
			}

			if ($checkUniqueMail)
			{
				$respone['email'] = $messagesContent['email.unique'];
			}

			if ($checkUniqueUser)
			{
				$respone['username'] = $messagesContent['username.unique'];
			}
		}

		return $respone;
	}

	public function likes()
	{
		return $this->belongsToMany('WineMenu', 'menu_likes', 'user_id', 'menu_id');
	}

	public static function appFind($uid)
	{
		$user = self::find($uid);
		$user->followers = DB::table('user_followers')->where('user_id', '=', $uid)->count();
		$user->following = DB::table('user_followers')->where('follower_id', '=', $uid)->count();
		$user->likes = $user->likes()->count();

		return $user;
	}

	public function getCoverAttribute($image)
	{
		return Config::get('app.url') . '/images/' . UserList::$uploadPath . $image;
	}
}
