<?php

namespace app\middleware;

use app\Request;

class CheckInstall
{
    public function handle(Request $request, \Closure $next)
    {
        if (file_exists('./../.env')) {
            $title = '系统已安装';
            $content = '为保护系统安全，禁止直接安装。如需重新安装，请删除根目录下的.env文件后，再次访问 /install 进行安装。';
            if ($request->isJson()) {
                return json([
                    'error' => -1,
                    'msg' => $content,
                ]);
            }
            session('msg_title', $title);
            session('msg_content', $content );
            return redirect('/message');
        }

        return $next($request);
    }
}