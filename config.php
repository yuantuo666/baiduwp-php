<?php
/**
 * PanDownload 网页复刻版，PHP 语言版配置文件
 *
 * 务必要保证此文件存在，否则整个服务将会不可使用！
 *
 * 务必要设置 SVIP 账号的 BDUSS 和 STOKEN ，否则将会无法获取链接！
 *
 * 请按提示修改配置！请勿随意修改常量名等不可更改的内容！
 *
 * 若你要向公网开启服务，务必要启用密码验证功能！否则后果自负！
 * 请不要使用弱密码！否则后果自负！
 * 若只在局域网开放，则可根据个人喜好开启或关闭密码。
 *
 * @version 1.3.6
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 * @author LC <lc@lcwebsite.cn>
 * @link https://lcwebsite.cn/
 * @link https://space.bilibili.com/52618445
 */
define('programVersion', '1.3.6');
if (!defined('init')){ // 直接访问处理程序
	http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); header('Refresh: 3;url=./');
	die("HTTP 403 禁止访问！\r\n此文件是 PanDownload 网页复刻版 PHP 语言版项目版本 " . programVersion . " 的配置文件！\r\n禁止直接访问！");
}

define('BDUSS', ''); // 你的 SVIP BDUSS
define('STOKEN', ''); // 你的 SVIP STOKEN
define('IsCheckPassword', true); // 设为 true 则要求密码为变量 Password 的值，否则提示密码错误；设为 false 则不需要密码。
define('Password', '请在这里填写密码啦！ヾ(≧▽≦*)o'); // 在下载器首页需要输入的密码，如果将 IsCheckPassWord 设为 false 则无论设置什么都会失效。
