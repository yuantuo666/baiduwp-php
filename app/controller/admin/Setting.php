<?php

namespace app\controller\admin;

use app\BaseController;
use app\Request;

class Setting extends BaseController
{
    public static $setting = [
        'site_name' => ['网站名称', 'text', '将会显示在网站标题处'],
        'program_version' => ['程序版本', 'readonly', ''],
        'footer' => ['页脚信息', 'textarea', '将会显示在网站底部，支持HTML代码'],

        'admin_password' => ['管理员密码', 'text', '后台管理密码，若为空，则无法进入后台管理，否则输入正确密码才能进入后台管理'],
        'password' => ['首页密码', 'text', '首页解析使用的密码，留空则无密码'],

        'db' => ['是否启用数据库', 'readonly', '若启用，则会将解析记录保存到数据库中，否则将不会被保存，如不启用数据库，也无法使用后台管理、限制次数和流量等功能。'],
        'link_expired_time' => ['链接有效期', 'number', '链接有效期，单位为小时'],
        'times' => ['解析次数', 'number', '解析次数，单IP每日限制解析次数'],
        'flow' => ['解析流量', 'number', '解析流量，单IP每日限制解析流量，单位为GB'],

        'check_speed_limit' => ['限速检测', 'radio', '是否开启限速检测，开启后会在解析时检测限速情况，如超过限速则会标记为限速。只有解析大于 50 MB 的文件同时启用数据库功能才会检测。当数据库所有账号都限速，将自动关闭限速检测并使用下面配置的本地账号解析。'],
        'random_account' => ['随机账号', 'radio', '是否开启随机账号功能，开启后会在账号列表中随机选择一个账号进行解析，如无可用账号则会使用本地账号。如果不开启则会按顺序使用账号列表中的账号进行解析，如无可用账号则会使用本地账号。'],

        'cookie' => ['本地普通账号Cookie', 'textarea', '此处填写普通账号Cookie，可以和SVIP填写一样的Cookie，用于获取百度网盘文件列表，如不填写或填写错误则只能预览，不能生成下载链接。'],
        'svip_cookie' => ['本地SVIP账号Cookie', 'textarea', '此处填写SVIP账号Cookie，如果不是SVIP账号，获取的下载链接将会限速。如果启用数据库，当数据库无可用账号，将会使用此账号。'],
    ];
    public function list(Request $request)
    {
        $data = [];
        foreach (self::$setting as $key => $value) {
            $data[] = [
                'key' => $key,
                'name' => $value[0],
                'value' => config('baiduwp.' . $key),
                'type' => $value[1],
                'description' => $value[2],
            ];
        }
        return json([
            'error' => 0,
            'msg' => 'success',
            'data' => $data,
        ]);
    }
    public function update(Request $request)
    {
        $data = $request->post();
        self::updateConfig($data);
        return json([
            'error' => 0,
            'msg' => '保存成功',
        ]);
    }
    public static function updateConfig($data, $force = false)
    {
        $default = [
            'site_name' => 'PanDownload',
            'program_version' => \app\controller\Index::$version,
            'password' => '',
            'admin_password' => env('ADMIN_PASSWORD'),
            'db' => env('DB', false),
            'link_expired_time' => 8,
            'times' => 20,
            'flow' => 10,
            'check_speed_limit' => true,
            'random_account' => false,
            'cookie' => '',
            'svip_cookie' => '',
            'footer' => '',
        ];

        $config = config('baiduwp');
        if (!$config) {
            $config = $default;
        }
        foreach ($data as $key => $value) {
            if (array_key_exists($key, self::$setting)) {
                if (self::$setting[$key][1] == 'number') {
                    $value = (int)$value;
                }
                if (self::$setting[$key][1] == 'radio') {
                    $value = $value === 'true' ? true : false;
                }
                if (self::$setting[$key][1] == 'text' || self::$setting[$key][1] == 'textarea') {
                    $value = trim($value);
                }
                if (self::$setting[$key][1] == 'readonly') {
                    if (!$force) continue;
                    if ($value === 'true') $value = true;
                    if ($value === 'false') $value = false;
                }
                $config[$key] = $value;
            }
        }
        $config = var_export($config, true);

        // 写入配置文件
        $config = <<<PHP
<?php
// +----------------------------------------------------------------------
// | Baiduwp-php 应用设置
// +----------------------------------------------------------------------
//
// 本文件由程序自动生成，请勿随意修改，以免失效！
return {$config};
PHP;
        file_put_contents('../config/baiduwp.php', $config);
    }
}
