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
echo '<script>setTimeout(() => Swal.fire("' . Language["TipTitle"] . '","' . Language["TimeoutTip"] . '","info"), 300000);</script>';
CheckPassword();
// $surl = $_POST["surl"]; // 含有1
$pwd = (!empty($_POST["pwd"])) ? sanitizeContent($_POST["pwd"]) : "";
$dir = (!empty($_POST["dir"])) ? sanitizeContent($_POST["dir"]) : "";
$IsRoot = ($dir == "") ? true : false;
$surl = (!empty($dir)) ? "1" . sanitizeContent($_POST["surl"]) : sanitizeContent($_POST["surl"]); // 含有1
$surl_1 = substr($surl, 1); //不含1

if (WECHAT_MOD) {
	$dir = base64_decode($_POST["dir"] ?? "");
	$sign = sanitizeContent($_POST["sign"] ?? "");
	$timestamp = sanitizeContent($_POST["timestamp"] ?? "");
	if ($sign == "" || $timestamp == "") {
		// 计算 sign 和 timestamp
		[$status, $sign, $timestamp] = GetSignCore($surl);
		if ($status != 0) {
			dl_error("链接错误", "无法正常获取文件夹有关信息。\r\n$sign");
			exit;
		}
	}

	$Filejson = [];
	$Page = 1;
	// 获取所有文件 fix #86
	while (true) {
		$Filejson = GetList($surl, $dir, $IsRoot, $pwd, $Page); // 解析子目录时，需添加1
		if ($Filejson["errno"] !== 0) {
			// 解析异常
			$ErrorCode = $Filejson["errtype"] ?? 999;
			$ErrorMessage = [
				"mis_105" => "你所解析的文件不存在~",
				"mispw_9" => "提取码错误",
				"mispwd-9" => "提取码错误",
				"mis_2" => "不存在此目录",
				3 => "此链接分享内容可能因为涉及侵权、色情、反动、低俗等信息，无法访问！",
				0 => "啊哦，你来晚了，分享的文件已经被删除了，下次要早点哟。",
				10 => "啊哦，来晚了，该分享文件已过期"
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
	if (!$IsRoot) {
		//文件夹页面
		$filecontent = "<nav aria-label=\"breadcrumb\"><ol class=\"breadcrumb my-4\">"
			. "<li class=\"breadcrumb-item\"><a href=\"javascript:OpenRoot('$surl','$pwd');\">$AllFiles</a></li>";
		$dir_list = explode("/", $dir);
		for ($i = 1; $i <= count($dir_list) - 2; $i++) {
			if ($i == 1 and strstr($dir_list[$i], "sharelink")) continue;
			$fullsrc = strstr($dir, $dir_list[$i], true) . $dir_list[$i];
			$fullsrc = base64_encode($fullsrc);
			$filecontent .= "<li class=\"breadcrumb-item\"><a href=\"javascript:OpenDir('$fullsrc','$pwd','','','$surl_1','','','','');\">{$dir_list[$i]}</a></li>";
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
		$path = base64_encode($file["path"]);
		$randsk = urlencode(decodeSceKey($Filejson["data"]["seckey"]));
		$shareid = sanitizeContent($Filejson["data"]["shareid"], "number");
		$uk = sanitizeContent($Filejson["data"]["uk"], "number");

		// (path, pwd, share_id, uk, surl, randsk, sign, timestamp)
		if ($file["isdir"] == 0) {
			$dlink = addslashes($file["dlink"]);
			if ($size <= 3000000)
				$filecontent .= "<li class=\"list-group-item border-muted text-muted py-2\"><i class=\"far fa-file mr-2\"></i>"
					. "<a href=\"$dlink\" target=\"_blank\">$filename</a>"
					. "<span class=\"float-right\">$char_size</span></li>";
			else
				$filecontent .= "<li class=\"list-group-item border-muted text-muted py-2\"><i class=\"far fa-file mr-2\"></i>"
					. "<a href=\"javascript:confirmdl('$fs_id','$timestamp','$sign','$randsk','$shareid','$uk');\">$filename</a>"
					. "<span class=\"float-right\">$char_size</span></li>";
		} else $filecontent .= "<li class=\"list-group-item border-muted text-muted py-2\"><i class=\"far fa-folder mr-2\"></i>"
			. "<a href=\"javascript:OpenDir('$path','$pwd','$shareid','$uk','$surl_1','$randsk','$sign','$timestamp');\">$filename</a><span class=\"float-right\"></span></li>";
	}
	echo $filecontent . "</ul></div>";
	// exit;

} else {

	if (isset($_POST["dir"])) {
		// 文件夹页面
		if (isset($_POST["randsk"])) $randsk = sanitizeContent($_POST["randsk"]);
		else $randsk = get_BDCLND($surl, $pwd);
		$shareid = sanitizeContent($_POST["share_id"], 'number');
		$dir = base64_decode($_POST["dir"]); // 含有'等也需要进行提交
		if ($randsk == false) dl_error("解析错误", "解析子文件夹时，提取码错误或文件失效！");
		$encode_randsk = urlencode($randsk);

		$uk = sanitizeContent($_POST["uk"], 'number'); // 分享者信息
		$sign = sanitizeContent($_POST["sign"]);
		$timestamp = sanitizeContent($_POST["timestamp"], 'number');
		$filejson = GetDirRemote($dir, $randsk, $shareid, $uk);
		if ($filejson["errno"] != 0) {
			dl_error("文件夹存在问题", "此文件夹存在问题，无法访问！", true);
			exit;
		} // 鬼知道发生了啥
		// 终于正常了
		// 面包屑导航
		$filecontent = "<nav aria-label=\"breadcrumb\"><ol class=\"breadcrumb my-4\">";
		$filecontent .= "<li class=\"breadcrumb-item\"><a href=\"javascript:OpenRoot('$surl','$pwd');\">${Language["AllFiles"]}</a></li>";
		$dir_list = explode("/", $dir); // potential threat
		for ($i = 1; $i <= count($dir_list) - 2; $i++) {
			if ($i == 1 and strstr($dir_list[$i], "sharelink")) continue;
			$fullsrc = strstr($dir, $dir_list[$i], true) . $dir_list[$i];
			$fullsrc = base64_encode($fullsrc);
			$dirname = htmlspecialchars($dir_list[$i], ENT_QUOTES);
			$filecontent .= "<li class=\"breadcrumb-item\"><a href=\"javascript:OpenDir('$fullsrc','$pwd','$shareid','$uk','$surl_1','$encode_randsk','$sign','$timestamp');\">$dirname</a></li>";
		}
		$dirname = htmlspecialchars($dir_list[$i], ENT_QUOTES);
		$filecontent .= "<li class=\"breadcrumb-item active\">$dirname</li>"
			. "<li class=\"ml-auto\">[网页API] 已加载" . count($filejson["list"]) . "个文件</li></ol></nav>";

		$filecontent .= '<div><ul class="list-group">';
		for ($i = 0; $i < count($filejson["list"]); $i++) { // 开始输出文件列表
			$file = $filejson["list"][$i];
			$fs_id = number_format($file["fs_id"], 0, '', '');
			$size = $file["size"];
			$char_size = formatSize((float)$file["size"]);
			$filename = htmlspecialchars($file["server_filename"], ENT_QUOTES);
			$path = base64_encode($file["path"]);

			if ($file["isdir"] === 0) $filecontent .= "<li class=\"list-group-item border-muted text-muted py-2\"><i class=\"far fa-file mr-2\"></i>"
				. "<a href=\"javascript:confirmdl('$fs_id','$timestamp','$sign','$encode_randsk','$shareid','$uk');\">$filename</a>"
				. "<span class=\"float-right\">$char_size</span></li>";
			else $filecontent .= "<li class=\"list-group-item border-muted text-muted py-2\"><i class=\"far fa-folder mr-2\"></i>"
				. "<a href=\"javascript:OpenDir('$path','$pwd','$shareid','$uk','$surl_1','$encode_randsk','$sign','$timestamp');\">$filename</a><span class=\"float-right\"></span></li>";
		}
		echo $filecontent . "</ul></div>";
	} else {
		// 根页面
		if (!empty($_POST["uk"]) and !empty($_POST["shareid"])) {
			// 使用老版本（估计是2012-2013年左右）分享链接
			// example: https://pan.baidu.com/share/link?shareid=136181&uk=3373607811
			//          https://pan.baidu.com/share/link?shareid=146328&uk=470983691
			$uk = sanitizeContent($_POST["uk"]);
			$shareid = sanitizeContent($_POST["shareid"]);
			$randsk = get_BDCLND("", "", $uk, $shareid);
			$root = getSign("", $randsk, $uk, $shareid);
		} else {
			// 新版本链接
			$randsk = get_BDCLND($surl, $pwd);
			$root = getSign($surl_1, $randsk);
		}
		$filejson = FileList($root);
		if ($filejson !== 1) {
			$shareid = $root["shareid"];
			$bdstoken = $root["bdstoken"];
			$uk = $root["share_uk"];
			$encode_randsk = urlencode($randsk);

			// 为兼容旧版本，此处采用 shareid 和 uk 来获取
			// $url = "https://pan.baidu.com/share/tplconfig?surl=$surl&fields=sign,timestamp&channel=chunlei&web=1&app_id=250528&clienttype=0";
			$url = "https://pan.baidu.com/share/tplconfig?shareid=$shareid&uk=$uk&fields=sign,timestamp&channel=chunlei&web=1&app_id=250528&clienttype=0";
			$header = array(
				"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36",
				"Cookie: BDUSS=" . BDUSS . ";STOKEN=" . STOKEN . ";BDCLND=" . $randsk . ";"
			);
			$result = get($url, $header);
			$result = json_decode($result, true, 512, JSON_BIGINT_AS_STRING);
			if (DEBUG) {
				echo '<pre>【限制版】根目录(sign,timestamp):';
				var_dump($result);
				echo '</pre>';
			}
			$sign = $result["data"]["sign"];
			$timestamp = $result["data"]["timestamp"];

			if ($root["errno"] != 0)
				if ($root["errno"] == 117) dl_error("文件过期(117)", "啊哦，来晚了，该分享文件已过期"); // 文件过期
				else dl_error("链接存在问题", "此链接存在问题，无法访问！", true); // 鬼知道发生了啥
			else { // 终于正常了
				$filecontent = '<nav aria-label="breadcrumb">
						<ol class="breadcrumb my-4">
							<li class="breadcrumb-item" aria-current="page">' . Language["AllFiles"] . '</li>
						<li class="ml-auto">[网页根目录] 已全部加载，共' . count($root["file_list"]) . '个</li>
						</ol>
						</nav>
						<div><ul class="list-group">';
				for ($i = 0; $i < count($root["file_list"]); $i++) {
					$file = $root["file_list"][$i];

					$fs_id = number_format($file["fs_id"], 0, '', '');
					$size = $file["size"];
					$char_size = formatSize((float)$file["size"]);
					$filename = htmlspecialchars($file["server_filename"], ENT_QUOTES);
					$path = base64_encode($file["path"]);

					if ($file["isdir"] === 0) $filecontent .= "<li class=\"list-group-item border-muted text-muted py-2\"><i class=\"far fa-file mr-2\"></i>"
						. "<a href=\"javascript:confirmdl('$fs_id','$timestamp','$sign','$encode_randsk','$shareid','$uk');\">$filename</a>"
						. "<span class=\"float-right\">$char_size</span></li>";
					else $filecontent .= "<li class=\"list-group-item border-muted text-muted py-2\"><i class=\"far fa-folder mr-2\"></i>"
						. "<a href=\"javascript:OpenDir('$path','$pwd','$shareid','$uk','$surl_1','$encode_randsk','$sign','$timestamp');\">$filename</a><span class=\"float-right\"></span></li>";
				}
				echo $filecontent . "</ul></div>";
			}
		} else dl_error("解析错误", "解析根页面时出错！<br />可能原因：①提取码错误 或 文件失效：尝试保存到自己网盘后重新分享解析；<br />②服务器未连接互联网 或 IP被百度封禁：检查网络链接，尝试ping百度网站；<br />③服务器未安装curl（或其php插件）；<br />④网络状况不好：稍后重试。<br /><br />如果以上问题排除后仍无法解决，可能是百度网盘升级了页面，请按下方提示操作：", true);
	}
}
