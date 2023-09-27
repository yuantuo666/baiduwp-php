<?php

namespace app\controller;

use app\BaseController;
use app\Request;
use app\Update;
use think\facade\Db;

class System extends BaseController
{
    /**
     * 首页显示的系统信息
     */
    public function index()
    {
        // 先检查是否安装
        if (!file_exists('./../.env')) {
            return json([
                'error' => 114514,
                'msg' => '未安装',
            ]);
        }
        // 检查安装版本和代码版本是否一致
        if (config('baiduwp.program_version') != Index::$version) {
            return json([
                'error' => 1919810,
                'msg' => '安装版本和代码版本不一致',
            ]);
        }

        $account = false;
        $count = false;


        if (config('baiduwp.db')) {
            $data = Db::connect()->table('records')->where('size', '>=', 52428800)->order('id', 'desc')->limit(1)->select();
            $account = [
                "last_time" => "无解析记录",
                "limit" => null
            ];
            if (count($data)) {
                $limit = false;
                if (str_contains($data[0]['link'], "//qdall") || !str_contains($data[0]['link'], "tsl=0")) {
                    $limit = true;
                }
                $account = [
                    "last_time" => $data[0]['time'] ?? "",
                    "limit" => $limit
                ];
            }

            $data = Db::connect()->table('records')->field('count(*) as count, sum(size) as size')
                ->where('time', '>=', date('Y-m-d 00:00:00'))->select();
            $today_times = 0;
            $today_flow = 0;
            if (count($data)) {
                $today_times = $data[0]['count'] ?? 0;
                $today_flow = $data[0]['size'] ?? 0;
            }

            $data = Db::connect()->table('records')->field('count(*) as count, sum(size) as size')->select();
            $all_times = 0;
            $all_flow = 0;
            if (count($data)) {
                $all_times = $data[0]['count'] ?? 0;
                $all_flow = $data[0]['size'] ?? 0;
            }

            $count = [
                'today' => [
                    'times' => $today_times,
                    'flow' => $today_flow,
                ],
                'all' => [
                    'times' => $all_times,
                    'flow' => $all_flow,
                ]
            ];
        }

        return json([
            'error' => 0,
            'version' => PHP_VERSION,
            // 返回数据库中上一次解析的时间，及SVIP状态
            'account' => $account,
            // 返回数据库中所有的解析总数和文件总大小
            'count' => $count
        ]);
    }

    /**
     * 更新系统信息
     */
    public function update(Request $request)
    {
        // 定义和获取是否包含预发行，是否强制检查
        $includePreRelease = $request->get('includePreRelease') === 'true';
        $enforce = $request->get('enforce') === 'true';

        $result = Update::check($includePreRelease, $enforce); // 获取结果
        return json($result);
    }
}
