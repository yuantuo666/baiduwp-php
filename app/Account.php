<?php

/**
 * 百度网盘账号管理
 */

namespace app;

class Account
{

    public static function getBDUSS($cookie)
    {
        $BDUSS = "";
        $STOKEN = "";
        preg_match('/BDUSS=([^;]*)/i', $cookie, $matches);
        if (isset($matches[1])) {
            $BDUSS = $matches[1];
        }
        preg_match('/STOKEN=([^;]*)/i', $cookie, $matches);
        if (isset($matches[1])) {
            $STOKEN = $matches[1];
        }
        return [$BDUSS, $STOKEN];
    }

    /**
     * 用于获取账号状态
     *
     * @return array [errno,会员状态,用户名,登录状态,会员剩余时间]
     */
    public static function checkStatus($cookie)
    {
        // list($BDUSS, $STOKEN) = static::getBDUSS($cookie);
        $Url = "https://pan.baidu.com/api/gettemplatevariable?channel=chunlei&web=1&app_id=250528&clienttype=0";
        $Header = [
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36",
            "Cookie: $cookie"
        ];
        $Data = "fields=[%22username%22,%22loginstate%22,%22is_vip%22,%22is_svip%22,%22is_evip%22]";
        $Result = Req::POST($Url, $Data, $Header);
        $Result = json_decode($Result, true);
        if ($Result["errno"] == 0) {
            // 正常
            $Username = $Result["result"]["username"];
            $LoginStatus = $Result["result"]["loginstate"];
            if ($Result["result"]["is_vip"] == 1) {
                $SVIP = 1; //会员账号
            } elseif ($Result["result"]["is_svip"] == 1 or $Result["result"]["is_evip"] == 1) {
                $SVIP = 2; //超级会员账号
            } else {
                $SVIP = 0; //普通账号
                return array(0, $SVIP, $Username, $LoginStatus, 0);
            }

            $Url = "https://pan.baidu.com/rest/2.0/membership/user?channel=chunlei&web=1&app_id=250528&clienttype=0";
            $Data = "method=query";
            $Result = Req::POST($Url, $Data, $Header);
            $Result = json_decode($Result, true);
            if (isset($Result["reminder"]["svip"])) {
                //存在会员信息
                $LeftSeconds = $Result["reminder"]["svip"]["leftseconds"];
                return array(0, $SVIP, $Username, $LoginStatus, $LeftSeconds);
            }
            return array(-1);
        } elseif ($Result["errno"] == -6) {
            // 账号失效
            return array(-6);
        } else {
            //未知错误
            return array($Result["errno"]);
        }
    }
}
