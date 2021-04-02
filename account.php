<?php

/**
 * PanDownload 网页复刻版，PHP 语言版账号状态页面
 *
 * 在此页面显示账号状态
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
    } else {
        require('config.php');
    }
    http_response_code(403);
    header('Refresh: 3;url=./');
    define('init', true);
    require('config.php');
    die("HTTP 403 禁止访问！\r\n此文件是 PanDownload 网页复刻版 PHP 语言版项目的有关文件！\r\n禁止直接访问！");
}

?>
<style>
    .card {
        margin-top: 3rem;
    }
</style>

<div class="card">

    <div class="card-header">账号状态检测</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 col-sm-12 card-item">
                <h5>普通账号状态</h5>
                <p class="card-text">
                    <?php
                    $Status = AccountStatus(BDUSS, STOKEN);
                    if ($Status[0] == 0) {
                        //正常
                        $AccountName = $Status[2];
                        echo "账号名称：$AccountName<br />";
                        $LoginStatus = ($Status[3] == 1) ? "正常" : "异常";
                        echo "登录状态：$LoginStatus<br />";
                        $AccountVIP = ["普通账号", "普通会员", "超级会员"][$Status[1]];
                        echo "会员状态：$AccountVIP<br />";
                        if ($Status[4] != 0) {
                            $AccountTime = time2Units($Status[4]);
                            echo "剩余时间：$AccountTime<br />";
                        }
                    } elseif ($Status[0] == -6) {
                        echo "id为 $id 的SVIP账号已经失效<br />";
                    } else {
                        echo "出现位置错误代码：" . $Status[0] . "<br />";
                    }
                    ?>
                </p>
                <br />
            </div>
            <div class="col-md-6 col-sm-12 card-item">
                <h5>会员账号状态</h5>
                <p class="card-text">
                    <?php
                    // 获取对应BDUSS
                    $DBSVIP = GetDBBDUSS();
                    $SVIP_BDUSS = $DBSVIP[0];
                    $id = $DBSVIP[1];
                    $SVIP_STOKEN = $DBSVIP[2];
                    if ($SVIP_STOKEN == "") {
                        echo "id为 $id 的SVIP账号没有设置对应STOKEN，无法检测<br />";
                    } else {
                        $Status = AccountStatus($SVIP_BDUSS, $SVIP_STOKEN);
                        if ($Status[0] == 0) {
                            //正常
                            $AccountName = $Status[2];
                            echo "账号名称：$AccountName<br />";
                            $LoginStatus = ($Status[3] == 1) ? "正常" : "异常";
                            echo "登录状态：$LoginStatus<br />";
                            $AccountVIP = ["普通账号", "普通会员", "超级会员"][$Status[1]];
                            echo "会员状态：$AccountVIP<br />";
                            if ($Status[4] != 0) {
                                $AccountTime = time2Units($Status[4]);
                                echo "剩余时间：$AccountTime<br />";
                            }
                        } elseif ($Status[0] == -6) {
                            echo "id为 $id 的SVIP账号已经失效<br />";
                        } else {
                            echo "出现位置错误代码：" . $Status[0] . "<br />";
                        }
                    }
                    ?>
                </p>
                <br />
            </div>
        </div>
    </div>
</div>