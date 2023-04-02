<?php

/**
 * PanDownload 网页复刻版，PHP 语言版
 * 
 * 解析文件列表
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
class Parse
{
	public static function getList($surl, $pwd, $dir, $sign = "", $timestamp = "")
	{
		if (!$sign || !$timestamp) {
			[$status, $sign, $timestamp] = GetSign($surl);
			if ($status !== 0) {
				$sign = '';
				$timestamp = '1';
			}
		}

		$IsRoot = ($dir == "") ? true : false;
		$Filejson = [];
		$file_list = [];
		$Page = 1;
		// 获取所有文件 fix #86
		while (true) {
			$Filejson = self::getListApi($surl, $dir, $IsRoot, $pwd, $Page, $sign, $timestamp);
			if ($Filejson["errno"] ?? 999 !== 0) {
				return self::handleError($Filejson);
			}
			foreach ($Filejson['data']['list'] as $v) {
				$file_list[] = $v;
			}
			if (count($Filejson['data']["list"]) < 1000) break;
			$Page++;
		}
		$randSk = urlencode(decodeSceKey($Filejson["data"]["seckey"]));
		$shareid = sanitizeContent($Filejson["data"]["shareid"], "number");
		$uk = sanitizeContent($Filejson["data"]["uk"], "number");

		// breadcrumb
		$DirSrc = [];
		if (!$IsRoot) {
			$Dir_list = explode("/", $dir);

			for ($i = 1; $i <= count($Dir_list) - 2; $i++) {
				if ($i == 1 and strstr($Dir_list[$i], "sharelink")) continue;
				$fullsrc = strstr($dir, $Dir_list[$i], true) . $Dir_list[$i];
				$DirSrc[] = array("isactive" => 0, "fullsrc" => $fullsrc, "dirname" => $Dir_list[$i]);
			}
			$DirSrc[] = array("isactive" => 1, "fullsrc" => $dir, "dirname" => $Dir_list[$i]);
		}
		$Filenum = count($file_list);
		$FileData = [];
		$RootData = array(
			"src" => $DirSrc,
			"timestamp" => $timestamp,
			"sign" => $sign,
			"randsk" => $randSk,
			"shareid" => $shareid,
			"surl" => $surl,
			"pwd" => $pwd,
			"uk" => $uk,
		);

		foreach ($file_list as $file) {
			if ($file["isdir"] === 0) {
				//文件
				$FileData[] = array(
					"isdir" => 0,
					"name" => $file["server_filename"],
					"fs_id" => number_format($file["fs_id"], 0, '', ''),
					"size" => $file["size"],
					"uploadtime" => $file["server_ctime"],
					"md5" => $file["md5"],
					"dlink" => $file["dlink"]
				);
			} else {
				//文件夹
				$FileData[] = array(
					"isdir" => 1,
					"name" => $file["server_filename"],
					"path" => $file["path"],
					"size" => $file["size"],
					"uploadtime" => $file["server_ctime"]
				);
			}
		}

		return array("error" => 0, "isroot" => $IsRoot, "dirdata" => $RootData, "filenum" => $Filenum, "filedata" => $FileData);
	}

	private static function getListApi(string $Shorturl, string $Dir, bool $IsRoot, string $Password, int $Page = 1)
	{
		$Url = 'https://pan.baidu.com/share/wxlist?channel=weixin&version=2.2.2&clienttype=25&web=1';

		$Root = ($IsRoot) ? "1" : "0";
		$Dir = urlencode($Dir);
		$Data = "shorturl=$Shorturl&dir=$Dir&root=$Root&pwd=$Password&page=$Page&num=1000&order=time";
		$BDUSS = getSubstr(Cookie, 'BDUSS=', ';');
		$header = ["User-Agent: netdisk", "Cookie: BDUSS=$BDUSS", "Referer: https://pan.baidu.com/disk/home"];
		$result = json_decode(post($Url, $Data, $header), true);
		return $result;
	}

	private static function handleError($Filejson)
	{
		// 解析异常
		$ErrorCode = $Filejson["errtype"] ?? ($Filejson["errno"] ?? 999);
		$ErrorMessage = [
			"mis_105" => "你所解析的文件不存在~",
			"mispw_9" => "提取码错误",
			"mispwd-9" => "提取码错误",
			"mis_2" => "不存在此目录",
			"mis_4" => "不存在此目录",
			5 => "不存在此分享链接或提取码错误",
			3 => "此链接分享内容可能因为涉及侵权、色情、反动、低俗等信息，无法访问！",
			0 => "啊哦，你来晚了，分享的文件已经被删除了，下次要早点哟。",
			10 => "啊哦，来晚了，该分享文件已过期",
			8001 => "普通账号被限制，请检查普通账号状态",
			9013 => "普通账号 Cookie 状态异常，请检查：Cookie 是否设置完整正确；账号是否被限制；Cookie 是否过期",
			9019 => "普通账号 Cookie 状态异常，请检查：Cookie 是否设置完整正确；账号是否被限制；Cookie 是否过期",
			999 => "错误 -> " . json_encode($Filejson)
		];
		if (isset($ErrorMessage[$ErrorCode])) return array("error" => -1, "title" => "[微信API] 解析错误 ($ErrorCode)", "msg" => $ErrorMessage[$ErrorCode]);
		else return array("error" => -1, "title" => "[微信API] 解析错误 ($ErrorCode)", "msg" => "未知错误代码:" . json_encode($Filejson));
	}
}
