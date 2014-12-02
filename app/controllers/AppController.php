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
            $this->data['user'] = User::find(Auth::id());
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
            $this->data['user'] = User::find(Auth::id());
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
