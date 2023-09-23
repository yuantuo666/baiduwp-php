<?php

namespace app\controller\admin;

use app\Account;
use app\BaseController;
use app\Request;
use think\facade\Cache;
use think\facade\Db;

class Index extends BaseController
{
    protected $middleware = [
        'app\middleware\CheckAdmin' => ['except' => ['login']],
    ];

    public function index()
    {
        return view('admin/index', [
            'site_name' => config('baiduwp.site_name'),
            'program_version' => config('baiduwp.program_version'),
        ]);
    }

    public function login(Request $request)
    {
        if ($request->isPost()) {
            $password = $request->post('password');
            if ($password == config('baiduwp.admin_password')) {
                session('admin', true);
                return json(['error' => 0, 'msg' => '登录成功']);
            } else {
                return json(['error' => 403, 'msg' => '密码错误']);
            }
        }
        if (session('admin')) {
            return redirect('/admin');
        }
        return view('admin/login', [
            'site_name' => config('baiduwp.site_name'),
            'program_version' => config('baiduwp.program_version'),
        ]);
    }

    public function info()
    {
        $text = '未启用数据库，无法统计';
        $records = [
            'all_times' => $text,
            'all_flow' => 0,
            'today_times' => $text,
            'today_flow' => 0,
        ];
        $account = [
            'all_count' => $text,
            'limit_count' => $text,
        ];
        $ip = [
            'black_count' => $text,
            'white_count' => $text,
        ];

        if (config('baiduwp.db')) {
            // 使用统计
            // 获取累计解析数量和大小
            $data = Db::table('records')->table('records')->field('count(*) as count, sum(size) as size')->select();
            $all_times = 0;
            $all_flow = 0;
            if (count($data)) {
                $all_times = $data[0]['count'] ?? 0;
                $all_flow = $data[0]['size'] ?? 0;
            }

            $data = Db::table('records')->table('records')->field('count(*) as count, sum(size) as size')
                ->where('time', '>=', date('Y-m-d 00:00:00'))->select();
            $today_times = 0;
            $today_flow = 0;
            if (count($data)) {
                $today_times = $data[0]['count'] ?? 0;
                $today_flow = $data[0]['size'] ?? 0;
            }
            $records = [
                'all_times' => $all_times,
                'all_flow' => $all_flow,
                'today_times' => $today_times,
                'today_flow' => $today_flow,
            ];

            // SVIP 账号
            $all_count = Db::table('account')->count();
            $limit_count = Db::table('account')->where('status', -1)->count();
            $account = [
                'all_count' => $all_count,
                'limit_count' => $limit_count,
            ];

            // 黑白名单
            $black_count = Db::table('ip')->where('type', 1)->count();
            $white_count = Db::table('ip')->where('type', 0)->count();
            $ip = [
                'black_count' => $black_count,
                'white_count' => $white_count,
            ];
        }

        // 网站设置 - 网站名称 下载次数限制 下载流量限制 是否开启限速检测 是否开启随机账号
        $setting = [
            'site_name' => config('baiduwp.site_name'),
            'download_times' => config('baiduwp.times'),
            'download_flow' => config('baiduwp.flow'),
            'check_speed_limit' => config('baiduwp.check_speed_limit'),
            'random_account' => config('baiduwp.random_account'),
        ];
        return json(['error' => 0, 'msg' => 'success', 'data' => [
            'records' => $records,
            'account' => $account,
            'ip' => $ip,
            'setting' => $setting,
        ]]);
    }

    public function accountInfo(Request $request)
    {
        if (!$request->get('force')) {
            $cache = Cache::get('account_info');
            if ($cache) {
                return json(['error' => 0, 'msg' => 'success', 'data' => $cache, 'cache' => true]);
            }
        }

        $local_account_cookie = config('baiduwp.cookie');
        $result = Account::checkStatus($local_account_cookie);
        $local_account = [
            'status' => $result[0],
            'svip' => $result[1] ?? '',
            'username' => $result[2] ?? '',
            'login_status' => $result[3] ?? '',
            'expire_time' => $result[4] ?? '',
            'cookie' => env('APP_DEBUG') ? $local_account_cookie ?? '' : 'HIDE',
        ];
        $svip_account_cookie = config('baiduwp.svip_cookie');
        $result = Account::checkStatus($svip_account_cookie);
        $svip_account = [
            'status' => $result[0],
            'svip' => $result[1] ?? '',
            'username' => $result[2] ?? '',
            'login_status' => $result[3] ?? '',
            'expire_time' => $result[4] ?? '',
            'cookie' => env('APP_DEBUG') ? $svip_account_cookie ?? '' : 'HIDE',
        ];
        $data = [
            'local_account' => $local_account,
            'svip_account' => $svip_account,
        ];
        // 将 data 缓存
        Cache::set('account_info', $data, 60);
        return json(['error' => 0, 'msg' => 'success', 'data' => $data]);
    }

    public function account(Request $request)
    {
        if ($request->isGet()) {
            $data = Db::table('account')->select();
            return json(['error' => 0, 'msg' => 'success', 'data' => $data]);
        }

        $id = $request->post('id');
        $status = $request->post('status');
        if ($status == 0) {
            // 删除
            Db::table('account')->where('id', $id)->delete();
        } else {
            // 修改
            $username = $request->post('username');
            $password = $request->post('password');
            $cookie = $request->post('cookie');
            $data = [
                'username' => $username,
                'password' => $password,
                'cookie' => $cookie,
            ];
            Db::table('account')->where('id', $id)->update($data);
        }
        return json(['error' => 0, 'msg' => 'success']);
    }
}
