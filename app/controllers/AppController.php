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
            $this->msg = 'login failed';
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

    public function topbar()
    {
        $this->data = BarList::recommondBars(Auth::id());
        return $this->response();
    }

    public function wineCategory()
    {
        $this->data = WineCategory::appAll();
        return $this->response();
    }

    public function drinkedList()
    {
        $this->data = DrinkHistory::appDrinkHistory(Auth::id(), 1);
        return $this->response();
    }

    public function drinkingList()
    {
        $this->data = DrinkHistory::appDrinkHistory(Auth::id(), 0);
        return $this->response();
    }

    public function collection()
    {
        $this->data = Collection::appUserCollection(Auth::id());
        return $this->response();
    }

    public function myMenu()
    {
        $this->data = WineMenu::appUserMenus(Auth::id());
        return $this->response();
    }

    public function comments($category, $id)
    {
        $this->data = Comment::appComments($category, $id);
        return $this->response();
    }

    public function postComment()
    {
        $post = Comment::post();
        $this->data = Comment::appComments($post->category, $post->content_id);
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
