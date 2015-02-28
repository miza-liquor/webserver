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
	protected $hidden = array('password', 'remember_token', 'last_checkmail', 'check_code');

	public static function loginAttempt()
	{
		// if login user, logout first
		if (Auth::check())
		{
			Auth::logout();
		}

		// trim all input
		Input::merge(array_map('trim', Input::all()));
		$usernameinput = Input::get('uname');
		$pwd = Input::get('pwd', '');
		if (!$usernameinput || !$pwd) return false;

		$field = filter_var($usernameinput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
		return Auth::attempt(array($field => $usernameinput, 'password' => $pwd), true);
	}

	public static function registerAttempt()
	{
		$respone = false;
		// trim all input
		Input::merge(array_map('trim', Input::all()));

		$data = array(
			'username' 	=> trim(Input::get('username')),
			'email' 	=> trim(Input::get('email')),
			'password' 	=> trim(Input::get('pwd')),
			'cpassword' => trim(Input::get('cpwd'))
		);

		$rules = array(
			"email" 	=> "required|email",
			"username" 	=> "required|between:6,32",
			"password"	=> "required|between:6,32|same:cpassword"
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

			if ($checkUniqueMail == 0 && $checkUniqueUser == 0)
			{
				$user = new User;
				$user->email = $data['email'];
				$user->username = $data['username'];
				$user->password = Hash::make($data['password']);
				$user->save();

				$check_login = Auth::attempt(array('email' => $data['email'], 'password' => $data['password']), true);
				if (!$check_login)
				{
					$respone = array('sys' => '系统错误');
				}
			} else {
				$respone = array();
				if ($checkUniqueMail)
				{
					$respone['email'] = $messagesContent['email.unique'];
				}

				if ($checkUniqueUser)
				{
					$respone['username'] = $messagesContent['username.unique'];
				}
			}
		} else {
			$messages = $validator->messages();
			foreach ($rules as $key => $value)
			{
				if ($messages->has($key))
				{
					$respone[$key] = $messages->first($key);
				}
			}
		}

		return $respone;
	}

	public function likes()
	{
		return $this->belongsToMany('WineMenu', 'menu_likes', 'user_id', 'menu_id');
	}

	public function menus()
	{
		return $this->hasMany('WineMenu', 'creator_id');
	}

	public function wines()
	{
		return $this->hasMany('WineList', 'creator_id');
	}

	public function followers()
	{
		return $this->belongsToMany('User', 'user_followers', 'follower_id', 'user_id');
	}

	public function following()
	{
		return $this->belongsToMany('User', 'user_followers', 'user_id', 'follower_id');
	}

	public function comments()
	{
		return $this->hasMany('Comment', 'user_id');
	}

	public function getNicknameAttribute($value)
	{
		return $value ? $value : $this->username;
	}

	// check if $uid is follower of me
	public function be_followed($uid)
	{
		return DB::table('user_followers')
				->where('follower_id', '=', $uid)
				->where('user_id', '=', Auth::id())
				->first();
	}

	// check if me is follower of $uid
	public function be_follower($uid)
	{
		return DB::table('user_followers')
				->where('user_id', '=', $uid)
				->where('follower_id', '=', Auth::id())
				->first();
	}

	public static function appFind($uid, $user = null)
	{
		$owner_id = Auth::id();
		$user = $user ? $user : self::find($uid);
		$user->followers = $user->followers()->count();
		$user->following = $user->following()->count();
		$user->likes = $user->likes()->count();
		$user->menus = $user->menus()->count();
		$user->wines = $user->wines()->count();
		$user->be_followed = $user->be_followed($uid) ? true : false;
		$user->be_follower = $user->be_follower($uid) ? true : false;

		return $user;
	}

	public static function search($uid, $keyword = null)
	{
		$keyword = trim($keyword);
		$search = self::where('id', '!=', $uid);
		$data = array();

		if ($keyword)
		{
			$search = $search->where('email', 'like', "%$keyword%")
						->orWhere('nickname', 'like', "%$keyword%");
		} else {
			$search = $search->orderBy(DB::raw('RAND()'));
		}

		$search = $search->take(50)->get();
		foreach ($search as $item) {
			$data[] = self::appFind($item->id, $item);
		}

		return $data;
	}

	public static function msgSummary()
	{
		$user = self::find(Auth::id());

		return array(
			'comment_num' => '' . $user->comments()->count(),
			'update_num' => '0',
			'like_num' => '0',
			'chats' => UserMsg::summary($user->id)
		);
	}

	public static function updateRelation()
	{
		$user_id = Input::get('userid');
		$owner = self::find(Auth::id());
		$user = self::find($user_id);

		if (!$user)
		{
			return array('status' => 404, 'msg' => '用户不存在', 'data' => null);
		}

		if ($owner->be_follower($user->id))
		{
			DB::table('user_followers')
				->where('user_id', '=', $user->id)
				->where('follower_id', '=', $owner->id)
				->delete();
		} else {
			DB::table('user_followers')->insert(array(
				'user_id' => $user->id,
				'follower_id' => $owner->id
			));
		}

		return array('status' => 200, 'msg' => 'success', 'data' => self::appFind($user->id, $user));
	}

	public static function mailCheck($mail)
	{
		$user = self::where('email', '=', $mail)->first();
		$now = time();
		$expire_time = 60;// 1 minute

		if (!$user)
		{
			return array('status' => 404, 'msg' => '邮箱地址不存在');
		}

		$duration = $now - $user->last_checkmail;
		if ($duration > $expire_time)
		{
			// send check code to mail
			$str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
			$max = strlen($str_pol)-1;
			$str = '';
			for($i = 0; $i < 6; $i++)
			{
				$str .= $str_pol[rand(0,$max)];
			}

			$user->check_code = strtolower($str);
			$user->last_checkmail = time();
			$user->save();
			return array('status' => 200, 'msg' => "验证码已经发送至邮箱".$mail."，\n请在5分钟之内完成验证");
		} else {
			$duration = $expire_time - $duration;
			return array('status' => 201, 'msg' => '请勿频繁发送，' . $duration . ' 秒后再次发送验证码');
		}
	}

	public static function mailCodeCheck($mail, $code, $reset = false)
	{
		if (!$mail || !$code)
		{
			return array('status' => 404, 'msg' => '验证失败');
		}

		$user = self::where('email', '=', $mail)
				->where('check_code', '=', strtolower($code))
				->first();

		if (!$user)
		{
			return array('status' => 404, 'msg' => '验证失败');
		}

		$expire_time = 60 * 5;// 5 minute
		$duration = time() - $user->last_checkmail;
		if ($duration > $expire_time)
		{
			return array('status' => 401, 'msg' => '验证时间已过，请重新提交邮箱');
		}

		if (!$reset)
		{
			$user->last_checkmail = time();
			$user->save();
		}
		
		return array('status' => 200, 'msg' => '验证成功，请在5分钟之内完成密码重置', 'user' => $user);
	}

	public static function resetForgetPwd($mail, $code, $pwd, $r_pwd)
	{
		$check_code = self::mailCodeCheck($mail, $code, true);
		if ($check_code['status'] !== 200)
		{
			return $check_code;
		}

		if (strlen($pwd) < 6)
		{
			return array('status' => 412, 'msg' => '密码不能少于6个字符');
		}
		if (strlen($pwd) > 32)
		{
			return array('status' => 412, 'msg' => '密码不能多于32个字符');
		}
		if ($pwd != $r_pwd)
		{
			return array('status' => 412, 'msg' => '两次密码不一样');
		}

		$user = $check_code['user'];
		$user->password = Hash::make($pwd);
		$user->save();

		return array('status' => 200, 'msg' => '密码重置成功，请重新登录');
	}

	public function getCoverAttribute($image)
	{
		return Config::get('app.url') . '/images/' . UserList::$uploadPath . $image;
	}
}
