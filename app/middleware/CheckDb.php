<?php

namespace app\middleware;

use app\Request;

class CheckDb
{
    public function handle(Request $request, \Closure $next)
    {
        if (config('baiduwp.db')) {
            return $next($request);
        } else {
            return json(['error' => 500, 'msg' => '未启用数据库，无法使用此功能']);
        }
    }
}