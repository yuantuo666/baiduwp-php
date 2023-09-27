<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use app\Request;
use think\facade\Route;

Route::get('/', 'Index/index');

Route::get('auth/status', 'Auth/status');

Route::get('message', 'Message/index');

Route::group('parse', function () {
    Route::post('list', 'Parse/list');
    Route::post('link', 'Parse/link');
})->middleware(\app\middleware\CheckPassword::class);

Route::group('system', function () {
    Route::get('/', 'System/index');
    Route::get('update', 'System/update');
});

Route::group('install', function () {
    Route::get('/', 'Install/index');
    Route::post('test_db_connect', 'Install/testDbConnect');
    Route::post('install', 'Install/install');
})->middleware(\app\middleware\CheckInstall::class);
Route::group('install', function () {
    Route::get('upgrade', 'Install/upgrade');
    Route::post('upgrade', 'Install/upgrade');
});


Route::group('admin', function () {
    Route::get('/', 'admin.Index/index');
    Route::get('info', 'admin.Index/info');
    Route::get('account_info', 'admin.Index/accountInfo');
    Route::any('login', 'admin.Index/login');
    Route::group('record', function () {
        Route::get('list/:page', 'admin.Record/list');
        Route::post('delete/:id', 'admin.Record/delete');
        Route::post('clear_all', 'admin.Record/clearAll');
    })->middleware(\app\middleware\CheckDb::class);
    Route::group('account', function () {
        Route::get('list/:page', 'admin.Account/list');
        Route::post('delete/:id', 'admin.Account/delete');
        Route::post('add', 'admin.Account/add');
        Route::post('reset/:id', 'admin.Account/reset');
    })->middleware(\app\middleware\CheckDb::class);
    Route::group('ip', function () {
        Route::get('list/:page', 'admin.Ip/list');
        Route::post('delete/:id', 'admin.Ip/delete');
        Route::post('add', 'admin.Ip/add');
    })->middleware(\app\middleware\CheckDb::class);
    Route::group('setting', function () {
        Route::get('list', 'admin.Setting/list');
        Route::post('update', 'admin.Setting/update');
    });
})->middleware(\app\middleware\CheckAdmin::class);

Route::miss(function (Request $request) {
    if ($request->isJson()) {
        return json(['error' => 404, 'msg' => '当前路由不存在']);
    }
    session('msg_title', '404 Not Found');
    session('msg_content', '当前路由不存在，可能是您访问了不存在的链接。');
    return redirect('/message');
});
