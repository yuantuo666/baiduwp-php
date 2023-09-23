<?php

namespace app\controller;

use app\BaseController;
use app\Request;
use think\facade\Db;

class Message extends BaseController
{
    public function index(Request $request)
    {
        $title = session('msg_title');
        $content = session('msg_content');
        if (!$title || !$content) {
            return redirect('/');
        }
        session('msg_title', null);
        session('msg_content', null);
        return view('index/message', [
            'site_name' => config('baiduwp.site_name'),
            'program_version' => config('baiduwp.program_version'),
            'title' => $title,
            'content' => $content,
        ]);
    }
}