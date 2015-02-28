<?php

class AppController extends BaseController {

    private $status = 200;
    private $msg    = 'success';
    private $data   = array();

    public function login()
    {
        if (!User::loginAttempt())
        {
            $this->status = 400;
            $this->msg = '登录失败，请检查用户名密码';
        } else {
            $this->data['user'] = User::appFind(Auth::id());
            $this->data['top_image'] = TopImage::appAll();
            $this->data['top_menu'] = TopWineMenu::appAll();
        }

        return $this->response();
    }

    public function register()
    {
        Auth::logout();
        $registerError = User::registerAttempt();
        if ($registerError)
        {
            $this->status = 400;
            $this->msg = '注册失败';
            $this->data = $registerError;
        } else {
            $this->data['user'] = User::appFind(Auth::id());
            $this->data['top_image'] = TopImage::appAll();
            $this->data['top_menu'] = TopWineMenu::appAll();
        }

        return $this->response();
    }

    public function guestView()
    {
        $this->data['top_image'] = TopImage::appAll();
        $this->data['top_menu'] = TopWineMenu::appAll();
        return $this->response(200);
    }

    public function explore()
    {
        return $this->response(201);
    }

    public function topStory()
    {
        $this->data = TopToday::appAll();
        return $this->response();
    }

    public function topUser()
    {
        $this->data = TopUser::appAll();
        return $this->response();
    }

    public function searchUser($keyword = null)
    {
        $this->data = User::search(Auth::id(), $keyword);
        return $this->response();
    }

    public function searchWine($keyword = null)
    {
        $this->data = WineList::search(Auth::id(), $keyword);
        return $this->response();
    }

    public function searchRecord($keyword = null)
    {
        $this->data = Record::search(Auth::id(), $keyword);
        return $this->response();
    }

    public function searchMenu($keyword = null)
    {
        $this->data = WineMenu::search(Auth::id(), $keyword);
        return $this->response();
    }

    public function topbar()
    {
        $this->data = BarList::recommondBars(Auth::id());
        return $this->response();
    }

    public function topWine($category_id = null)
    {
        $this->data = TopWine::appAll($category_id);
        return $this->response();
    }

    public function wineDrinked($wine_id)
    {
        $this->data = WineList::drink($wine_id, 'drinked');
        return $this->response();
    }
    public function wineDrinking($wine_id)
    {
        $this->data = WineList::drink($wine_id, 'drinking');
        return $this->response();
    }

    public function wineCategory()
    {
        $this->data = WineCategory::appAll();
        return $this->response();
    }

    public function drinkedList($uid)
    {
        $this->data = DrinkHistory::appDrinkHistory($uid, 1);
        return $this->response();
    }

    public function drinkingList($uid)
    {
        $this->data = DrinkHistory::appDrinkHistory($uid, 0);
        return $this->response();
    }

    public function collection($uid)
    {
        $this->data = Collection::appUserCollection($uid);
        return $this->response();
    }

    public function myMenu($uid)
    {
        $this->data = WineMenu::appUserMenus($uid);
        return $this->response();
    }

    public function menuInfo($menuid)
    {
        $menu = WineMenu::find($menuid);
        $this->data = $menu->getAppMenuInfo(true);
        return $this->response();
    }

    public function postMenu()
    {
        $response = WineMenu::addMenu();
        $this->data = $response['data'];
        $this->msg = $response['msg'];
        $this->status = $response['status'];
        return $this->response();
    }

    public function follower($uid)
    {
        $followers =  User::find($uid)->followers;
        $data = array();
        foreach ($followers as $follower)
        {
            $data[] = User::appFind($follower->id, $follower);
        }
        $this->data = $data;
        return $this->response();
    }

    public function following($uid)
    {
        $followings =  User::find($uid)->following;
        $data = array();
        foreach ($followings as $following)
        {
            $data[] = User::appFind($following->id, $following);
        }
        $this->data = $data;
        return $this->response();
    }

    public function comments($category, $id)
    {
        $this->data = Comment::appComments($category, $id);
        return $this->response();
    }

    public function msgSummary()
    {
        $this->data = User::msgSummary();
        return $this->response();
    }

    public function msgList($uid)
    {
        $this->data = UserMsg::getMsgList($uid);
        return $this->response();
    }

    public function postMsg()
    {
        $response = UserMsg::postMsg();
        $this->data = $response['data'];
        $this->status = $response['status'];
        $this->msg = $response['msg'];
        return $this->response();
    }

    public function postRecord()
    {
        $response = Record::postNewRecord();
        $this->data = $response['data'];
        $this->status = $response['status'];
        $this->msg = $response['msg'];
        return $this->response();
    }

    public function postWine()
    {
        $response = WineList::postNewWine();
        $this->data = $response['data'];
        $this->status = $response['status'];
        $this->msg = $response['msg'];
        return $this->response();
    }

    public function postComment()
    {
        $post = Comment::post();
        $this->data = Comment::appComments($post->category, $post->content_id);
        return $this->response();
    }

    public function updateRelation()
    {
        $response = User::updateRelation();
        $this->data = $response['data'];
        $this->status = $response['status'];
        $this->msg = $response['msg'];
        return $this->response();
    }

    public function addWineToMenu()
    {
        $response = WineList::addToMenu();
        return $this->response();
    }

    public function checkMail()
    {
        $response = User::mailCheck(Input::get('mail'));
        $this->data = $response['msg'];
        $this->status = $response['status'];
        return $this->response();
    }

    public function checkCode()
    {
        $response = User::mailCodeCheck(Input::get('mail'), Input::get('code'));
        $this->data = $response['msg'];
        $this->status = $response['status'];
        return $this->response();
    }

    public function resetForgetPwd()
    {
        $response = User::resetForgetPwd(Input::get('mail'), Input::get('code'), Input::get('pwd'), Input::get('n_pwd'));
        $this->data = $response['msg'];
        $this->status = $response['status'];
        return $this->response();
    }

    public function getAppVersion($os)
    {
        $version = Config::get('version.' . $os);
        if ($version && is_array($version))
        {
            $this->data = $version[0];
        } else {
            $this->status = 404;
            $this->msg = '未找到';
            $this->data = null;
        }
        
        return $this->response();
    }

    private function response()
    {
        return Response::json(array(
            'status'    => $this->status,
            'msg'       => $this->msg,
            'data'      => $this->data
            )
        );
    }
}
