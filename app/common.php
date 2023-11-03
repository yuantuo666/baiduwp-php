<?php
// 应用公共文件

use think\Response;
use think\facade\Request;
use think\response\Redirect;

/**
 * 获取\think\response\Redirect对象实例
 * 重写redirect方法，解决程序在子目录下的重定向地址问题
 * @param string $url  重定向地址
 * @param int    $code 状态码
 * @return \think\response\Redirect
 */
function redirect(string $url = '', int $code = 302): Redirect
{
    $subUrl = '';
    $baseUrl = Request::baseUrl(); //  获取当前请求URL的路径
    $pathInfo = Request::pathinfo(); //  获取当前请求URL的pathinfo信息

    $len = strlen($pathInfo);
    if ($len) {
        $subBaseUrl = substr($baseUrl, -$len);
        if ($subBaseUrl === $pathInfo) {  //  正常情况下pathinfo信息应该在请求URL的尾部，如果不在尾部，说明用户用了不同寻常的URL重写规则，则不考虑子目录
            $subUrl = substr($baseUrl, 0, -$len); //  移除请求URL尾部的pathinfo信息，作为子目录
        }
    } else {
        $subUrl = $baseUrl; //  请求URL没有pathinfo信息，子目录为请求URL
    }

    $subUrl = rtrim($subUrl, '/');
    $url = ltrim($url, '/');
    $url = $subUrl . '/' . $url;

    return Response::create($url, 'redirect', $code);
}