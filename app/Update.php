<?php

namespace app;

use app\controller\Index;

class Update
{
    private static function fetch(bool $includePreRelease, array &$info) // 下载
    {
        $header = array(
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 Edg/91.0.864.67"
        );
        if ($includePreRelease) { // 若包括 Pre-Release
            $info[] = "Include-PreRelease";
            $result = Req::GET('https://api.github.com/repos/yuantuo666/baiduwp-php/releases', $header);
            if ($result) {
                $result = json_decode($result, true);
                return $result[0] ?? false; // 返回首个结果
            }
            $info[] = "API-Download-Newest-Error";
        } else { // 若不包括
            $result = Req::GET('https://api.github.com/repos/yuantuo666/baiduwp-php/releases/latest', $header);
            if ($result) {
                return json_decode($result, true);
            }
            $info[] = "API-Download-Latest-Error";
        }
        return false;
    }

    private static function fetchError(array &$info): array // 下载失败
    {
        $info[] = "Download-Error";
        return array("code" => 1, "info" => $info);
    }

    public static function check(bool $includePreRelease = false, bool $enforce = false, array $info = []): array // 检查更新程序
    {
        $filePath = public_path() . "update.json"; // 缓存文件路径
        if ($enforce) { // 是否强制检查更新
            $info[] = "Enforce-Check";
            $result = self::fetch($includePreRelease, $info); // 下载
            if (!$result) { // 若出错则直接 return
                return self::fetchError($info);
            }
        } else { // 不强制检查更新
            $info[] = "Allow-Cached-Check";
            if (file_exists($filePath)) { // 检查更新缓存是否存在
                $lastm = filemtime($filePath); // 获取文件最后修改时间
                if ((!$lastm) || ($lastm + 3600 < time())) { // 获取失败或超时（一小时）则重新获取
                    if (!$lastm) {
                        $info[] = "CacheFile-Get-LastM-Error"; // 获取失败
                    } else {
                        $info[] = "CacheFile-Expired"; // 超时
                    }
                    $result = self::fetch($includePreRelease, $info); // 下载并检查是否出错
                    if (!$result) {
                        return self::fetchError($info);
                    }
                } else {
                    $file = fopen($filePath, "r"); // 打开文件
                    if ($file) { // 若打开成功
                        $result = fread($file, filesize($filePath)); // 读取文件
                        if (!$result) { // 若读取失败
                            $info[] = "Read-CacheFile-Error";
                            $result = self::fetch($includePreRelease, $info); // 下载并检查是否出错
                            if (!$result) {
                                return self::fetchError($info);
                            }
                        } else { // 若读取成功
                            $result = json_decode($result, true); // 解码
                            if (isset($result['prerelease'])) { // 测试是否包含 PreRelease（检查缓存文件是否存在问题）
                                if ($result['prerelease'] && !$includePreRelease) { // 若不检查预发行版本但缓存为预发行
                                    $info[] = "CacheFile-Is-PreRelease-But-Exclude-It";
                                    $result = self::fetch($includePreRelease, $info); // 下载并检查是否出错
                                    if (!$result) {
                                        return self::fetchError($info);
                                    }
                                } else if (!$result['prerelease'] && $includePreRelease) { // 若检查预发行但缓存非预发行（这个只是用来防止万一，所以下载失败了不终止）
                                    $info[] = "CacheFile-Isnot-PreRelease-But-Include-It--Try-To-Get";
                                    $download_result = self::fetch($includePreRelease, $info); // 下载并检查是否出错
                                    if (!$download_result) { // 下载失败的话还使用缓存
                                        $info[] = "Download-Error";
                                        $info[] = "Use-Cache";
                                    } else { // 若下载成功则用新的
                                        $result = $download_result;
                                    }
                                } else { // 没有问题，使用缓存
                                    $info[] = "Use-Cache";
                                }
                            } else { // 缓存文件存在问题
                                $info[] = "Invalid-CacheFile";
                                $result = self::fetch($includePreRelease, $info); // 下载并检查是否出错
                                if (!$result) {
                                    return self::fetchError($info);
                                }
                            }
                        }
                        fclose($file); // 关闭文件
                    } else { // 打开失败
                        $info[] = "Open-CacheFile-Error";
                        $result = self::fetch($includePreRelease, $info); // 下载并检查是否出错
                        if (!$result) {
                            return self::fetchError($info);
                        }
                    }
                }
            } else { // 文件不存在
                $info[] = "No-CacheFile";
                $result = self::fetch($includePreRelease, $info); // 下载并检查是否出错
                if (!$result) {
                    return self::fetchError($info);
                }
            }
        }

        if (!(isset($result['tag_name']) && isset($result['assets']) && isset($result['html_url']))) { // 若缓存文件存在问题
            $info[] = "Invalid-CacheFile";
            $result = self::fetch($includePreRelease, $info); // 下载并检查是否出错
            if (!$result || !(isset($result['tag_name']) && isset($result['assets']) && isset($result['html_url']))) {
                if ($result) { // 若下载成功但数据依旧有问题
                    $info[] = "Download-Data-Error";
                    $info[] = $result;
                }
                return self::fetchError($info);
            }
        }

        $version = substr($result['tag_name'], 1); // 解析数据
        $isPreRelease = $result['prerelease'];
        $url = "";
        $page_url = $result['html_url'];
        foreach ($result['assets'] as $asset) {
            if ($asset['name'] === 'ProgramFiles.zip') {
                $url = $asset['browser_download_url'];
                break;
            }
        }

        $file = fopen($filePath, "w"); // 打开文件
        if ($file) { // 若打开成功
            fwrite($file, json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); // 写入文件
            if (!$result) { // 若写入失败
                $info[] = "Write-NewFile-Error";
            }
            fclose($file); // 关闭文件
        } else { // 打开失败
            $info[] = "Open-NewFile-Error";
        }


        $commonReturn = array(
            "code" => 0, "version" => $version, "PreRelease" => $isPreRelease,
            "file_url" => $url, "page_url" => $page_url, "info" => $info,
            "now_version" => config('baiduwp.program_version') ?? Index::$version
        );
        $compare = version_compare(config('baiduwp.program_version') ?? Index::$version, $version); // 比较版本
        if ($compare === -1 || $compare === 0) { // 更新或相同
            $commonReturn['have_update'] = $compare === -1; // 更新则为 true
            return $commonReturn;
        } else { // 版本存在问题（比最新版还高？）
            if (in_array('Try-Get-Version-Include-PreRelease', $info)) { // 若已尝试获取预发行，则直接返回版本有误提示
                $info[] = "Invalid-Version";
                $commonReturn['code'] = 2;
                $commonReturn['info'] = $info;
                return $commonReturn;
            } else { // 试图强制检查预发行版更新
                $info[] = "Try-Get-Version-Include-PreRelease";
                array_splice($info, array_search('Use-Cache', $info), 1);
                return self::check(true, true, $info);
            }
        }
    }
}
