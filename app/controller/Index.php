<?php

namespace app\controller;

use app\BaseController;

class Index extends BaseController
{
    public function index()
    {
        return view('index/index', [
            'site_name' => config('baiduwp.site_name'),
            'program_version' => config('baiduwp.program_version'),
            'footer' => config('baiduwp.footer'),
        ]);
    }

    public function hello($name = 'ThinkPHP8')
    {
        return 'hello,' . $name;
    }
}
