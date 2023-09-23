<?php

namespace app;

// 请求库
class Req
{
    public static function setCurl(&$ch, array $header): bool
    { // 批处理 curl
        $a = curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 忽略证书
        $b = curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 不检查证书与域名是否匹配（2为检查）
        $c = curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 以字符串返回结果而非输出
        $d = curl_setopt($ch, CURLOPT_HTTPHEADER, $header); // 请求头
        return ($a && $b && $c && $d);
    }
    public static function POST(string $url, $data, array $header)
    { // POST 发送数据
        $ch = curl_init($url);
        self::setCurl($ch, $header);
        curl_setopt($ch, CURLOPT_POST, true); // POST 方法
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // POST 的数据
        // Set request timeout (in seconds)
        $timeout = 10;
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    public static function GET(string $url, array $header)
    { // GET 请求数据
        $ch = curl_init($url);
        self::setCurl($ch, $header);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    public static function HEAD(string $url, array $header): string
    { // 获取响应头
        $ch = curl_init($url);
        self::setCurl($ch, $header);
        curl_setopt($ch, CURLOPT_HEADER, true); // 返回响应头
        curl_setopt($ch, CURLOPT_NOBODY, true); // 只要响应头
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        $response = curl_exec($ch);
        if ($response === false) {
            // get error msg
            $error = curl_error($ch);
            // close curl
            curl_close($ch);
            // return error msg
            return $error;
        }
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE); // 获得响应头大小
        $result = substr($response, 0, $header_size); // 根据头大小获取头信息
        curl_close($ch);
        return $result;
    }
}
