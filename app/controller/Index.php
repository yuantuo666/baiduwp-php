<?php

namespace app\controller;

use app\BaseController;

class Index extends BaseController
{
    static $version = '4.0.3';

    public function index()
    {
        return view('index/index', [
            'site_name' => config('baiduwp.site_name'),
            'program_version' => config('baiduwp.program_version'),
            'footer' => config('baiduwp.footer'),
        ]);
    }
}
