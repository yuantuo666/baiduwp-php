<?php

/**
 * PanDownload 网页复刻版，PHP 语言版
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
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
				return self::listError($Filejson);
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

	public static function download($fs_id, $timestamp, $sign, $randsk, $share_id, $uk)
	{
		if (!$fs_id || !$timestamp || !$sign || !$randsk || !$share_id || !$uk) {
			EchoInfo(-1, array("msg" => "参数错误"));
			exit;
		}
		$message = [];

		$ip = sanitizeContent(getip());
		$isipwhite = FALSE;
		if (USING_DB) {
			global $conn, $dbtable;
			connectdb();

			// 查询数据库中是否存在已经保存的数据
			$sql = "SELECT * FROM `{$dbtable}_ip` WHERE `ip` LIKE '$ip';";
			$mysql_query = mysqli_query($conn, $sql);
			if ($result = mysqli_fetch_assoc($mysql_query)) {
				// 存在 判断类型
				if ($result["type"] == -1) {
					// 黑名单
					$isipwhite = FALSE;
					return array("error" => -1, "msg" => "当前ip已被加入黑名单，请联系站长解封");
				} elseif ($result["type"] == 0) {
					// 白名单
					$message[] = "当前ip为白名单~";
					$isipwhite = TRUE;
				}
			}
		}

		// check if the timestamp is valid
		if (time() - $timestamp > 300) {
			// try to get the timestamp and sign
			[$_status, $sign, $timestamp] = getSign("", $share_id, $uk);
			if ($_status !== 0) {
				$message[] = "超时，自动获取sign和timestamp失败, $sign";
			} else {
				$message[] = "超时，自动获取sign和timestamp成功: $sign, $timestamp";
			}
		}

		$json4 = self::getDlink($fs_id, $timestamp, $sign, $randsk, $share_id, $uk, APP_ID);
		$errno = $json4["errno"] ?? 999;
		if ($errno !== 0) {
			return self::downloadError($json4, $message);
		}

		$dlink = $json4["list"][0]["dlink"] ?? "";
		// 获取文件相关信息
		$md5 = sanitizeContent($json4["list"][0]["md5"] ?? "");
		$md5 = decryptMd5($md5);
		$filename = $json4["list"][0]["server_filename"] ?? "";
		$size = sanitizeContent($json4["list"][0]["size"] ?? "0", "number");
		$path = $json4["list"][0]["path"] ?? "";
		$server_ctime = (int)$json4["list"][0]["server_ctime"] ?? 0 + 28800; // 服务器创建时间 +8:00

		$FileData = array(
			"filename" => $filename,
			"size" => $size,
			"path" => $path,
			"uploadtime" => $server_ctime,
			"md5" => $md5
		);

		if ($size <= 5242880) { // 5MB
			return array("error" => 0, "filedata" => $FileData, "directlink" => $dlink, "user_agent" => "LogStatistic", "message" => $message);
		}

		if (USING_DB) {
			connectdb();

			$DownloadLinkAvailableTime = (is_int(DownloadLinkAvailableTime)) ? DownloadLinkAvailableTime : 8;
			// 查询数据库中是否存在已经保存的数据
			$sql = "SELECT * FROM `$dbtable` WHERE `md5`='$md5' AND `ptime` > DATE_SUB(NOW(),INTERVAL $DownloadLinkAvailableTime HOUR);";
			$mysql_query = mysqli_query($conn, $sql);
			$result = mysqli_fetch_assoc($mysql_query);
			if ($result) {
				// 存在
				$realLink = $result["realLink"];
				return array("error" => 0, "usingcache" => true,"filedata" => $FileData, "directlink" => "https://" . $realLink, "user_agent" => "LogStatistic", "message" => $message);
			}

			// 判断今天内是否获取过文件
			if (!$isipwhite) { // 白名单跳过
				// 获取解析次数
				$sql = "SELECT count(*) as Num FROM `$dbtable` WHERE `userip`='$ip' AND date(`ptime`)=date(now());";
				$mysql_query = mysqli_query($conn, $sql);
				$result = mysqli_fetch_assoc($mysql_query);
				if ($result["Num"] >= DownloadTimes) {
					// 提示无权继续
					return array("error" => 1, "msg" => "今日解析次数已达上限，请明天再试");
				}
			}
		}
		$DBSVIP = GetDBBDUSS();
		$SVIP_BDUSS = $DBSVIP[0];
		$id = $DBSVIP[1];

		// 开始获取真实链接
		$headerArray = array('User-Agent: LogStatistic', 'Cookie: BDUSS=' . $SVIP_BDUSS . ';');

		$header = head($dlink, $headerArray); // 禁止重定向
		if (!strstr($header, "Location")) {
			// fail
			$message[] = "获取真实链接失败 $header";
		}
		$getRealLink = strstr($header, "Location");
		$getRealLink = substr($getRealLink, 10);
		$realLink = str_replace("https://", "http://", $getRealLink);
		$realLink = getSubstr($getRealLink, "http://", "\n"); // delete http://
		$realLink = trim($realLink); // delete space

		// 1. 使用 dlink 下载文件   2. dlink 有效期为8小时   3. 必需要设置 User-Agent 字段   4. dlink 存在 HTTP 302 跳转
		if (!$realLink || strlen($realLink) < 20 || strstr($realLink, "//qdall01")) {
			if ($id != "-1" && (SVIPSwitchMod === 1 || SVIPSwitchMod === 2)) {
				//限速进行标记 并刷新页面重新解析
				$sql = "UPDATE `{$dbtable}_svip` SET `state`= -1 WHERE `id`=$id";
				$mysql_query = mysqli_query($conn, $sql);
				if ($mysql_query != false) {
					// SVIP账号自动切换成功，对用户界面进行刷新进行重新获取
					return array("error" => -1, "msg" => "SVIP账号自动切换成功，正在刷新页面重新获取下载链接", "refresh" => true);
				} else {
					// SVIP账号自动切换失败
					return array("error" => -1, "msg" => "SVIP账号自动切换失败");
				}
			}

			$body = get($dlink, $headerArray);
			$body_decode = json_decode($body, true);

			$message[] = "获取真实下载链接出错：" . json_encode($body_decode);
			return self::realLinkError($body_decode, $message);
		}

		// 记录下使用者ip，下次进入时提示
		if (USING_DB) {
			$ptime = date("Y-m-d H:i:s");
			$Sqlfilename = htmlspecialchars($filename, ENT_QUOTES); // 防止出现一些刁钻的文件名无法处理
			$Sqlpath = htmlspecialchars($path, ENT_QUOTES);
			$sql = "INSERT INTO `$dbtable`(`userip`, `filename`, `size`, `md5`, `path`, `server_ctime`, `realLink` , `ptime`,`paccount`) VALUES ('$ip','$Sqlfilename','$size','$md5','$Sqlpath','$server_ctime','$realLink','$ptime','$id')";
			$mysql_query = mysqli_query($conn, $sql);
			if ($mysql_query == false) {
				// 保存错误
				return array("error" => -1, "msg" => "数据库保存错误");
			}
		}

		return array("error" => 0, "filedata" => $FileData, "directlink" => "https://" . $realLink, "user_agent" => "LogStatistic", "message" => $message);
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

	private static function getDlink(string $fs_id, string $timestamp, string $sign, string $randsk, string $share_id, string $uk, int $app_id = 250528)
	{ // 获取下载链接
		$url = 'https://pan.baidu.com/api/sharedownload?app_id=' . $app_id . '&channel=chunlei&clienttype=12&sign=' . $sign . '&timestamp=' . $timestamp . '&web=1'; // 获取下载链接

		if (strstr($randsk, "%") != false) $randsk = urldecode($randsk);
		$data = "encrypt=0" . "&extra=" . urlencode('{"sekey":"' . $randsk . '"}') . "&fid_list=[$fs_id]" . "&primaryid=$share_id" . "&uk=$uk" . "&product=share&type=nolimit";
		$header = array(
			"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36 Edg/110.0.1587.69",
			"Cookie: " . Cookie,
			"Referer: https://pan.baidu.com/disk/home"
		);
		$result = json_decode(post($url, $data, $header), true);
		return $result;
		// 没有 referer 就 112, 没有 sekey 参数就 118, -20出现验证码
	}

	private static function listError($Filejson)
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

	private static function downloadError($json4, $message)
	{
		$errno = $json4["errno"] ?? 999;
		$error = [
			999 => ["请求错误", "请求百度网服务器出错，请检查网络连接或重试"],
			-9 => ["文件不存在(-9)", "请返回首页重新解析。"],
			112 => ["链接超时(112)", "获取链接超时，每次解析列表后只有5min有效时间，请返回首页重新解析。"],
			113 => ["传参错误(113)", "获取失败，请检查参数是否正确。"],
			118 => ["服务器错误(118)", "服务器错误，请求百度服务器时，未传入sekey参数或参数错误。"],
			110 => ["服务器错误(110)", "服务器错误，可能服务器IP被百度封禁，请切换 IP 或更换服务器重试。"],
			8001 => ["普通账号错误(8001)", "普通账号被限制，请检查普通账号状态"],
			9013 => ["普通账号错误(9013)", "普通账号被限制，请检查普通账号状态"],
			9019 => ["普通账号错误(9019)", "普通账号 Cookie 状态异常，请检查：Cookie 是否设置完整正确；账号是否被限制；Cookie 是否过期"],
		];

		if (isset($error[$errno])) return ["error" => -1, "title" => $error[$errno][0], "msg" => $error[$errno][1], "message" => $message];
		else return ["error" => -1, "title" => "获取下载链接失败 ($errno)", "msg" => "未知错误！错误：" . json_encode($json4), "message" => $message];
	}

	private static function realLinkError($body_decode, $message)
	{
		$ErrorCode = $body_decode["errno"] ?? 999;
		$ErrorMessage = [
			8001 => "SVIP 账号状态异常，请检查 SVIP 的 BDUSS 和 STOKEN 是否设置正确且有效",
			9013 => "SVIP 账号状态异常，请检查 SVIP 的 BDUSS 和 STOKEN 是否设置正确且有效",
			9019 => "SVIP 账号状态异常，请检查 SVIP 的 BDUSS 和 STOKEN 是否设置正确且有效",
			999 => "错误 -> " . json_encode($body_decode)
		];

		return [
			"error" => -1,
			"title" => "获取下载链接失败 ($ErrorCode)",
			"msg" => $ErrorMessage[$ErrorCode] ?? "未知错误！错误：" . json_encode($body_decode),
			"message" => $message
		];
	}
}
