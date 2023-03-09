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
require_once("./common/invalidCheck.php");
CheckPassword();
$dir = base64_decode($_POST["dir"] ?? ""); // prevent strange characters
$pwd = (!empty($_POST["pwd"])) ? sanitizeContent($_POST["pwd"]) : "";
$IsRoot = ($dir == "") ? true : false;
$surl = sanitizeContent($_POST["surl"]);

// original method costs too much time, so change to Wechat API
$sign = sanitizeContent($_POST["sign"] ?? "");
$timestamp = sanitizeContent($_POST["timestamp"] ?? "");
if (!$sign || !$timestamp) {
	[$status, $sign, $timestamp] = GetSign($surl);
	if ($status !== 0) {
		$sign = '';
		$timestamp = '1';
	}
}

$Filejson = [];
$Page = 1;
// 获取所有文件 fix #86
while (true) {
	$Filejson = GetList($surl, $dir, $IsRoot, $pwd, $Page);
	if ($Filejson["errno"] !== 0) {
		// 解析异常
		$ErrorCode = $Filejson["errtype"] ?? 999;
		$ErrorMessage = [
			"mis_105" => "你所解析的文件不存在~",
			"mispw_9" => "提取码错误",
			"mispwd-9" => "提取码错误",
			"mis_2" => "不存在此目录",
			5 => "不存在此分享链接或提取码错误",
			3 => "此链接分享内容可能因为涉及侵权、色情、反动、低俗等信息，无法访问！",
			0 => "啊哦，你来晚了，分享的文件已经被删除了，下次要早点哟。",
			10 => "啊哦，来晚了，该分享文件已过期",
			999 => "错误 -> " . json_encode($Filejson)
		];
		if (isset($ErrorMessage[$ErrorCode])) dl_error("[微信API] 解析错误", $ErrorMessage[$ErrorCode]);
		else dl_error("[微信API] 解析错误", "未知错误代码:" . $ErrorCode, true);
		exit;
	}

	foreach ($Filejson['data']['list'] as $v) {
		$file_list[] = $v;
	}
	if (count($Filejson['data']["list"]) < 1000) break;
	$Page++;
}

$AllFiles = Language["AllFiles"];
// breadcrumb
if (!$IsRoot) {
	//文件夹页面
	$filecontent = "<nav aria-label=\"breadcrumb\"><ol class=\"breadcrumb my-4\">"
		. "<li class=\"breadcrumb-item\"><a href=\"javascript:OpenRoot('$surl','$pwd');\">$AllFiles</a></li>";
	$dir_list = explode("/", $dir);
	for ($i = 1; $i <= count($dir_list) - 2; $i++) {
		if ($i == 1 and strstr($dir_list[$i], "sharelink")) continue;
		$fullsrc = strstr($dir, $dir_list[$i], true) . $dir_list[$i];
		$fullsrc = base64_encode($fullsrc);
		$filecontent .= "<li class=\"breadcrumb-item\"><a href=\"javascript:OpenDir('$fullsrc','$pwd','','','$surl','','','','');\">{$dir_list[$i]}</a></li>";
	}
	$filecontent .= "<li class=\"breadcrumb-item active\">{$dir_list[$i]}</li>";
} else {
	// 根目录页面
	$filecontent = "<nav aria-label=\"breadcrumb\"><ol class=\"breadcrumb my-4\"><li class=\"breadcrumb-item\">$AllFiles</li>";
}
$filecontent .= "<li class=\"ml-auto\">[微信API] 已全部加载，共" . count($file_list) . "个</li></ol></nav>";

$filecontent .= "<div><ul class=\"list-group\">";
for ($i = 0; $i < count($file_list); $i++) { // 开始输出文件列表
	$file = $file_list[$i];
	$fs_id = number_format($file["fs_id"], 0, '', '');
	$size = $file["size"];
	$char_size = formatSize((float)$file["size"]);
	$filename = htmlspecialchars($file["server_filename"], ENT_QUOTES);
	$path = base64_encode($file["path"]); // prevent strange characters
	$randsk = urlencode(decodeSceKey($Filejson["data"]["seckey"]));
	$shareid = sanitizeContent($Filejson["data"]["shareid"], "number");
	$uk = sanitizeContent($Filejson["data"]["uk"], "number");

	// (path, pwd, share_id, uk, surl, randsk, sign, timestamp)
	if ($file["isdir"] == 0) {
		$dlink = addslashes($file["dlink"]);
		if ($size <=  1024 * 1024 * 3) // 3 MB
			$filecontent .= "<li class=\"list-group-item border-muted text-muted py-2\"><i class=\"far fa-file mr-2\"></i>"
				. "<a href=\"$dlink\" target=\"_blank\">$filename</a>"
				. "<span class=\"float-right\">$char_size</span></li>";
		else
			$filecontent .= "<li class=\"list-group-item border-muted text-muted py-2\"><i class=\"far fa-file mr-2\"></i>"
				. "<a href=\"javascript:confirmdl('$fs_id','$timestamp','$sign','$randsk','$shareid','$uk');\">$filename</a>"
				. "<span class=\"float-right\">$char_size</span></li>";
	} else $filecontent .= "<li class=\"list-group-item border-muted text-muted py-2\"><i class=\"far fa-folder mr-2\"></i>"
		. "<a href=\"javascript:OpenDir('$path','$pwd','$shareid','$uk','$surl','$randsk','$sign','$timestamp');\">$filename</a><span class=\"float-right\"></span></li>";
}
echo $filecontent . "</ul></div>";
