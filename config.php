<?php
/**
* Pandownload PHP 复刻版配置文件
*
* 务必要保证此文件存在，否则整个服务将会不可使用！
*
* 若你要向公网开启服务，务必要启用密码验证功能！！！否则后果自负！！！
* 若只在局域网开放，则可根据个人喜好开启或关闭密码。
*
* @version 1.1.1
*
* @author Yuan_Tuo <yuantuo666@gmail.com>
* @link https://imwcr.cn/
* @link https://space.bilibili.com/88197958
*
* @author LC <lc@lcwebsite.cn>
* @link https://lcwebsite.cn/
* @link https://space.bilibili.com/52618445
*/
define("BDUSS", ""); // 你的 SVIP BDUSS
define("STOKEN", ""); // 你的 SVIP STOKEN
define('IsCheckPassword', true); // 设为 true 则要求密码为变量 $setpassword 的值，否则提示密码错误；设为 false 则不需要密码。
$setpassword='请在这里填写密码啦ヾ(≧▽≦*)o'; // 在下载器首页需要输入的密码，如果将 IsCheckPassWord 设为 false 则无论设置什么都会失效。

if (!defined('init')){ http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); die('想啥呢？访问这个文件？'); } // 直接访问处理程序