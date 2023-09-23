<?php

namespace app;

class Tool
{
    public static function getSubstr($str, string $leftStr, string $rightStr): string
    {
        if (empty($str)) return "";
        $left = strpos($str, $leftStr);
        if ($left === false) return "";
        $left += strlen($leftStr);
        $right = strpos($str, $rightStr, $left);
        if ($right === false) return "";
        return substr($str, $left, $right - $left);
    }

    public static function getIP(): string
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = "unknown";
        }
        return htmlspecialchars($ip, ENT_QUOTES); // 防注入 #193
    }
}
