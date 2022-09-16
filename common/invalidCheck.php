<?php

/**
 * PanDownload 网页复刻版，PHP 语言版
 * 
 * 检查是否初始化
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
if (!defined('init')) { // 直接访问处理程序
    header('Content-Type: text/plain; charset=utf-8');
    if (!file_exists('config.php')) {
        http_response_code(503);
        header('Content-Type: text/plain; charset=utf-8');
        header('Refresh: 5;url=install.php');
        die("HTTP 503 服务不可用！\r\n暂未安装此程序！\r\n将在五秒内跳转到安装程序！");
    }

    http_response_code(403);
    header('Refresh: 3;url=./');
    define('init', true);
    die("HTTP 403 禁止访问！\r\n此文件是 PanDownload 网页复刻版 PHP 语言版项目的有关文件！\r\n禁止直接访问！");
}
