<?php

namespace app\controller;

use app\BaseController;
use app\Request;
use app\Parse as GlobalParse;

class Parse extends BaseController
{
    /**
     * 解析链接 - 获取列表
     */
    public function list(Request $request)
    {
        $surl = $request->post('surl', ''); // 获取surl
        $pwd = $request->post('pwd', ''); // 获取密码
        $dir = $request->post('dir', ''); // 获取目录
        $sign = $request->post('sign', '');
        $timestamp = $request->post('timestamp', '');
        $result = GlobalParse::getList($surl, $pwd, $dir, $sign, $timestamp);
        return json($result);
    }

    /**
     * 解析链接 - 获取链接
     */
    public function link(Request $request)
    {
        $fs_id = $request->post('fs_id', '');
        $timestamp = $request->post('timestamp', '');
        $sign = $request->post('sign', '');
        $randsk = $request->post('randsk', '');
        $shareid = $request->post('shareid', '');
        $uk = $request->post('uk', '');

        $result = GlobalParse::download($fs_id, $timestamp, $sign, $randsk, $shareid, $uk);
        return json($result);
    }
}
