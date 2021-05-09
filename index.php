<?php

/**
 * PanDownload 网页复刻版，PHP 语言版主文件
 *
 * 功能描述：使用百度 SVIP 账号获取真实下载地址，与 Pandownload 原版无关。
 *
 * 希望在使用时能够保留导航栏的 Made by Yuan_Tuo 感谢！
 *
 * 此项目 GitHub 地址：https://github.com/yuantuo666/baiduwp-php
 *
 * @version 2.1.7
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
$programVersion_Index = "2.1.7";
session_start();
define('init', true);
if (version_compare(PHP_VERSION, '7.0.0', '<')) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://www.php.net/downloads.php');
	die("HTTP 503 服务不可用！\r\nPHP 版本过低！无法正常运行程序！\r\n请安装 7.0.0 或以上版本的 PHP！\r\n将在五秒内跳转到 PHP 官方下载页面！");
}
if (!file_exists('config.php')) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=install.php');
	die("HTTP 503 服务不可用！\r\n暂未安装此程序！\r\n将在五秒内跳转到安装程序！");
} else {
	require('config.php');
	if ($programVersion_Index !== programVersion) {
		http_response_code(503);
		header('Content-Type: text/plain; charset=utf-8');
		header('Refresh: 5;url=install.php');
		die("HTTP 503 服务不可用！\r\n配置文件版本异常！\r\n将在五秒内跳转到安装程序！\r\n若重新安装无法解决问题，请重新 Clone 项目并配置！");
	}
}
if (!(file_exists('functions.php') && file_exists('language.php'))) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://github.com/yuantuo666/baiduwp-php');
	die("HTTP 503 服务不可用！\r\n缺少相关配置和定义文件！无法正常运行程序！\r\n请重新 Clone 项目并配置！\r\n将在五秒内跳转到 GitHub 储存库！");
}
// 确认会员账号模式是否正常
if (USING_DB == false and SVIPSwitchMod != 0) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://github.com/yuantuo666/baiduwp-php');
	die("HTTP 503 服务不可用！\r\n配置错误，未启用数据库无法使用其他会员模式！无法正常运行程序！\r\n请重新 Clone 项目并配置！\r\n将在五秒内跳转到 GitHub 储存库！");
}
// 保存启动时间
$system_start_time = microtime(true);
// 导入配置和函数

require('language.php');
require('functions.php');
// 通用响应头
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge,chrome=1');
// 隐藏错误代码，保护信息安全
if (DEBUG) {
	error_reporting(E_ALL);
} else {
	error_reporting(0); // 关闭错误报告
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="referrer" content="same-origin" />
	<meta name="author" content="Yuan_Tuo" />
	<meta name="author" content="LC" />
	<meta name="version" content="<?php echo programVersion; ?>" />
	<meta name="description" content="PanDownload 网页版，百度网盘分享链接在线解析工具。" />
	<meta name="keywords" content="PanDownload,百度网盘,分享链接,下载,不限速" />
	<title><?php echo Sitename; ?></title>
	<link rel="icon" href="favicon.ico" />
	<link rel="stylesheet" href="static/index.css" />
	<link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/4.1.2/css/bootstrap.min.css" />
	<link rel="stylesheet" disabled id="ColorMode-Dark" href="https://cdn.jsdelivr.net/gh/vinorodrigues/bootstrap-dark@0.0.9/dist/bootstrap-nightfall.css" />
	<link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/5.8.1/css/all.min.css" />
	<link rel="stylesheet" disabled id="Swal2-Dark" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4.0.2/dark.min.css" />
	<link rel="stylesheet" disabled id="Swal2-Light" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-default@4.0.2/default.min.css" />
	<script src="https://cdn.staticfile.org/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://cdn.staticfile.org/popper.js/1.12.5/umd/popper.min.js"></script>
	<script src="https://cdn.staticfile.org/twitter-bootstrap/4.1.2/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.14.0/dist/sweetalert2.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@keeex/qrcodejs-kx"></script>
	<script src="static/color.js"></script>
	<script src="static/functions.js"></script>
	<script defer src="static/ready.js"></script>
	<?php
	if (isset($_POST["surl"])) {
		echo '<script>';
		if (USING_DB and IsConfirmDownload) {
			$Language = Language;
			$JSCode['echo'](
				<<<Function
function confirmdl(fs_id, timestamp, sign, randsk, share_id, uk, bdstoken, filesize) {
	Swal.fire({
		title: "{$Language["ConfirmTitle"]}",
		html: "{$Language["ConfirmText"]}",
		icon: "warning",
		showCancelButton: true,
		confirmButtonText: "{$Language["ConfirmmButtonText"]}",
		reverseButtons: true
	}).then(function(e) {
		if (e.isConfirmed) {
			dl(fs_id, timestamp, sign, randsk, share_id, uk, bdstoken, filesize);
		}
	});
}
Function
			);
		} else {
			echo 'let confirmdl = dl;';
		}
		echo '</script>';
	}
	?>
</head>

<body>
	<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
		<div class="container">
			<a class="navbar-brand" href="./"><img src="resource/logo.png" class="img-fluid rounded logo-img mr-2" alt="LOGO" />PanDownload</a>
			<button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#collpase-bar"><span class="navbar-toggler-icon"></span></button>
			<div class="collapse navbar-collapse" id="collpase-bar">
				<ul class="navbar-nav">
					<li class="nav-item"><a class="nav-link" href="./"><?php echo Language["IndexButton"]; ?></a></li>
					<li class="nav-item"><a class="nav-link" href="?help" target="_blank"><?php echo Language["HelpButton"]; ?></a></li>
					<li class="nav-item"><a class="nav-link" href="?usersettings"><?php echo Language["UserSettings"]; ?></a></li>
					<li class="nav-item"><a class="nav-link" href="https://github.com/yuantuo666/baiduwp-php" target="_blank">Github</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container">
		<?php
		if (DEBUG) {
			echo '<pre>$_GET:';
			var_dump($_GET);
			echo '$_POST:';
			var_dump($_POST);
			echo '</pre>';
		}
		if (isset($_GET["help"])) { // 帮助页
			echo Language["HelpPage"];
		} elseif (isset($_GET["usersettings"])) { // 用户设置页面
			require("usersettings.php");
		} elseif (isset($_POST["surl"])) { // 解析链接页面
			echo '<script>setTimeout(() => Swal.fire("' . Language["TipTitle"] . '","' . Language["TimeoutTip"] . '","info"), 300000);</script>';
			CheckPassword();
			// $surl = $_POST["surl"]; // 含有1
			$pwd = (!empty($_POST["pwd"])) ? $_POST["pwd"] : "";
			$dir = (!empty($_POST["dir"])) ? $_POST["dir"] : "";
			$IsRoot = ($dir == "") ? true : false;
			$surl = (!empty($dir)) ? "1" . $_POST["surl"] : $_POST["surl"]; // 含有1
			$surl_1 = substr($surl, 1); //不含1

			if (WECHAT_MOD) {
				$Filejson = GetList($surl, $dir, $IsRoot, $pwd); // 解析子目录时，需添加1
				if ($Filejson["errno"] == 0) { // 一种新的解析方法，暂未完工
					// 解析正常
					if (!$IsRoot) {
						//文件夹页面
						$filecontent = '<nav aria-label="breadcrumb"><ol class="breadcrumb my-4">
						<li class="breadcrumb-item"><a href="javascript:OpenRoot(\'' . $surl . '\',\'' . $pwd . '\');">' . Language["AllFiles"] . '</a></li>';
						$dir_list = explode("/", $dir);
						for ($i = 1; $i <= count($dir_list) - 2; $i++) {
							if ($i == 1 and strstr($dir_list[$i], "sharelink")) continue;
							$fullsrc = strstr($dir, $dir_list[$i], true) . $dir_list[$i];
							$filecontent .= '<li class="breadcrumb-item"><a href="javascript:OpenDir(\'' . $fullsrc . '\',\'' . $pwd . '\',\'\',\'\',\'' . $surl_1 . '\',\'\',\'\',\'\',\'\');">' . $dir_list[$i] . '</a></li>';
						}
						$filecontent .= '<li class="breadcrumb-item active">' . $dir_list[$i] . '</li>';
					} else {
						$filecontent = '<nav aria-label="breadcrumb"><ol class="breadcrumb my-4">
						<li class="breadcrumb-item">' . Language["AllFiles"] . '</li>';
					}
					$filecontent .= '<li class="ml-auto">[微信API] 已全部加载，共' . count($Filejson["data"]["list"]) . '个</li></ol></nav>';

					$filecontent .= '<div><ul class="list-group">';
					for ($i = 0; $i < count($Filejson["data"]["list"]); $i++) { // 开始输出文件列表
						$file = $Filejson["data"]["list"][$i];
						if ($file["isdir"] == 0) $filecontent .= '<li class="list-group-item border-muted text-muted py-2"><i class="far fa-file mr-2"></i>
								<a href="' . $file["dlink"] . '" target="_blank">' . $file["server_filename"] . '</a>
								<span class="float-right">' . formatSize((float)$file["size"]) . '</span></li>';
						else $filecontent .= '<li class="list-group-item border-muted text-muted py-2"><i class="far fa-folder mr-2"></i>
							<a href="javascript:OpenDir(\'' . $file["path"] . '\',\'' . $pwd . '\',\'\',\'\',\'' . $surl_1 . '\',\'\',\'\',\'\',\'\');">' . $file["server_filename"] . '</a><span class="float-right"></span></li>';
					}
					echo $filecontent . "</ul></div>";

					// exit;
				} else {
					// 解析异常
					$ErrorCode = $Filejson["errtype"];
					$ErrorMessage = [
						"mis_105" => "你所解析的文件不存在~",
						"mispw_9" => "验证码错误",
						"mis_2" => "不存在此目录",
						3 => "此链接分享内容可能因为涉及侵权、色情、反动、低俗等信息，无法访问！",
						0 => "啊哦，你来晚了，分享的文件已经被删除了，下次要早点哟。",
						10 => "啊哦，来晚了，该分享文件已过期"
					];
					if (isset($ErrorMessage[$ErrorCode])) dl_error("[微信API] 解析错误", $ErrorMessage[$ErrorCode]);
					else dl_error("[微信API] 解析错误", "未知错误代码:" . $ErrorCode, true);
					// exit;
				}
			} else {

				if (isset($_POST["dir"])) {
					// 文件夹页面
					if (isset($_POST["randsk"])) $randsk = $_POST["randsk"];
					else $randsk = get_BDCLND($surl, $pwd);
					$shareid = $_POST["share_id"];
					//$root = getSign($surl, $randsk);第二次不需要再次获取
					if ($randsk !== 1) {
						$uk = $_POST["uk"]; // 分享者信息
						$sign = $_POST["sign"];
						$timestamp = $_POST["timestamp"];
						$bdstoken = $_POST["bdstoken"];
						$filejson = GetDirRemote($_POST["dir"], $randsk, $shareid, $uk);
						if ($filejson["errno"] != 0) dl_error("文件夹存在问题", "此文件夹存在问题，无法访问！", true); // 鬼知道发生了啥
						else { // 终于正常了
							// 面包屑导航
							$filecontent = '<nav aria-label="breadcrumb"><ol class="breadcrumb my-4">
						<li class="breadcrumb-item"><a href="javascript:OpenRoot(\'' . $surl . '\',\'' . $pwd . '\');">' . Language["AllFiles"] . '</a></li>';
							$dir_list = explode("/", $_POST["dir"]);
							for ($i = 1; $i <= count($dir_list) - 2; $i++) {
								if ($i == 1 and strstr($dir_list[$i], "sharelink")) continue;
								$fullsrc = strstr($_POST["dir"], $dir_list[$i], true) . $dir_list[$i];
								$filecontent .= '<li class="breadcrumb-item"><a href="javascript:OpenDir(\'' . $fullsrc . '\',\'' . $pwd . '\',\'' . $shareid . '\',\'' . $uk . '\',\'' . $surl_1 . '\',\'' . urlencode($randsk) . '\',\'' . $sign . '\',\'' . $timestamp . '\',\'' . $bdstoken . '\');">' . $dir_list[$i] . '</a></li>';
							}
							$filecontent .= '<li class="breadcrumb-item active">' . $dir_list[$i] . '</li>'
								. '<li class="ml-auto">[网页API] 已加载' . count($filejson["list"]) . '个文件</li></ol></nav>';

							$filecontent .= '<div><ul class="list-group">';
							for ($i = 0; $i < count($filejson["list"]); $i++) { // 开始输出文件列表
								$file = $filejson["list"][$i];
								if ($file["isdir"] === 0) $filecontent .= '<li class="list-group-item border-muted text-muted py-2"><i class="far fa-file mr-2"></i>
								<a href="javascript:confirmdl(\'' . number_format($file["fs_id"], 0, '', '') . '\',' . $timestamp . ',\'' . $sign . '\',\'' . urlencode($randsk) . '\',\'' . $shareid . '\',\'' . $uk . '\',\'' . $bdstoken . '\',\'' . $file["size"] . '\');">' . $file["server_filename"] . '</a>
								<span class="float-right">' . formatSize((float)$file["size"]) . '</span></li>';
								else $filecontent .= '<li class="list-group-item border-muted text-muted py-2"><i class="far fa-folder mr-2"></i>
							<a href="javascript:OpenDir(\'' . $file["path"] . '\',\'' . $pwd . '\',\'' . $shareid . '\',\'' . $uk . '\',\'' . $surl_1 . '\',\'' . urlencode($randsk) . '\',\'' . $sign . '\',\'' . $timestamp . '\',\'' . $bdstoken . '\');">' . $file["server_filename"] . '</a><span class="float-right"></span></li>';
							}
							echo $filecontent . "</ul></div>";
						}
					} else dl_error("解析错误", "解析子文件夹时，提取码错误或文件失效！");
				} else {
					// 根页面
					if (isset($_POST["randsk"])) $randsk = $_POST["randsk"];
					else $randsk = get_BDCLND($surl, $pwd);
					$root = getSign($surl_1, $randsk);
					$filejson = FileList($root);
					if ($filejson !== 1) {
						$url = "https://pan.baidu.com/share/tplconfig?surl=$surl&fields=sign,timestamp&channel=chunlei&web=1&app_id=250528&clienttype=0";
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
						$uk = $root["share_uk"];

						$shareid = $root["shareid"];
						$bdstoken = $root["bdstoken"];
						if ($root["errno"] != 0) if ($root["errno"] == 117) dl_error("文件过期(117)", "啊哦，来晚了，该分享文件已过期"); // 文件过期
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
								if ($file["isdir"] === 0) $filecontent .= '<li class="list-group-item border-muted text-muted py-2"><i class="far fa-file mr-2"></i>
								<a href="javascript:confirmdl(\'' . number_format($file["fs_id"], 0, '', '') . '\',' . $timestamp . ',\'' . $sign . '\',\'' . urlencode($randsk) . '\',\'' . $shareid . '\',\'' . $uk . '\',\'' . $bdstoken . '\',\'' . $file["size"] . '\');">' . $file["server_filename"] . '</a>
								<span class="float-right">' . formatSize((float)$file["size"]) . '</span></li>';
								else $filecontent .= '<li class="list-group-item border-muted text-muted py-2"><i class="far fa-folder mr-2"></i>
							<a href="javascript:OpenDir(\'' . $file["path"] . '\',\'' . $pwd . '\',\'' . $shareid . '\',\'' . $uk . '\',\'' . $surl_1 . '\',\'' . urlencode($randsk) . '\',\'' . $sign . '\',\'' . $timestamp . '\',\'' . $bdstoken . '\');">' . $file["server_filename"] . '</a><span class="float-right"></span></li>';
							}
							echo $filecontent . "</ul></div>";
						}
					} else dl_error("解析错误", "解析根页面时出错！<br />可能原因：①提取码错误 或 文件失效：尝试保存到自己网盘后重新分享解析；<br />②服务器未连接互联网 或 IP被百度封禁：检查网络链接，尝试ping百度网站；<br />③服务器未安装curl（或其php插件）；<br />④网络状况不好：稍后重试。<br /><br />如果以上问题排除后仍无法解决，可能是百度网盘升级了页面，请按下方提示操作：", true);
				}
			}
		} elseif (isset($_GET["download"])) { // 解析下载地址页面
			if (!CheckPassword(true)) {
				dl_error(Language["PasswordError"], "密码错误或超时，请返回首页重新验证密码。"); // 密码错误
			} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST["fs_id"]) && isset($_POST["time"]) && isset($_POST["sign"]) && isset($_POST["randsk"]) && isset($_POST["share_id"]) && isset($_POST["uk"]) && isset($_POST["bdstoken"]) && isset($_POST["filesize"])) {
					function getip()
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
						return $ip;
					}
					$ip = getip();
					$isipwhite = FALSE; //初始化 防止报错
					if (USING_DB) {
						connectdb();

						// 查询数据库中是否存在已经保存的数据
						$sql = "SELECT * FROM `" . $dbtable . "_ip` WHERE `ip`='$ip';";
						$mysql_query = mysqli_query($conn, $sql);
						if ($result = mysqli_fetch_assoc($mysql_query)) {
							// 存在 判断类型
							if ($result["type"] == -1) {
								// 黑名单
								$isipwhite = FALSE;
								dl_error(Language["AccountError"], "当前ip已被加入黑名单，请联系站长解封");
								exit;
							} elseif ($result["type"] == 0) {
								// 白名单
								echo "<script>console.log('当前IP为白名单~');</script>";
								$isipwhite = TRUE;
							}
						}
					}

					$fs_id = $_POST["fs_id"];
					$timestamp = $_POST["time"];
					$sign = $_POST["sign"];
					$randsk = $_POST["randsk"];
					$share_id = $_POST["share_id"];
					$uk = $_POST["uk"];
					$bdstoken = $_POST["bdstoken"];
					$filesize = $_POST["filesize"];
					$smallfile = ((int)$filesize < 52428800) ? true : false; // 如果是小文件 那么可以不需要传入SVIP的BDUSS 仅需普通用户的即可
					$smallfile = false; // 小文件竟然也会限速，醉了，现在先不搞这个
					// 文件小于50MB可以使用这种方法获取：
					// $nouarealLink="";// 重置
					// if((int)$filesize<=52428800){
					//     $json5 = getDlink($fs_id, $timestamp, $sign, $randsk, $share_id, $uk ,$bdstoken,true);
					//     if ($json5["errno"] == 0) {
					//         $nouadlink = $json5["list"][0]["dlink"];
					//         // 开始获取真实链接
					//     	$headerArray = array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36', 'Cookie: BDUSS=' . BDUSS . ';');
					//     	$getRealLink = head($nouadlink, $headerArray); // 禁止重定向
					//     	$getRealLink = strstr($getRealLink, "Location");
					//     	$getRealLink = substr($getRealLink, 10);
					//     	$nouarealLink = getSubstr($getRealLink, "https://", "\r\n"); // 删除 https://
					//     }
					// }
					$json4 = getDlink($fs_id, $timestamp, $sign, $randsk, $share_id, $uk, $bdstoken, false, APP_ID);
					if ($json4["errno"] == 0) {
						$dlink = $json4["list"][0]["dlink"];
						// 获取文件相关信息
						$md5 = $json4["list"][0]["md5"];
						$filename = $json4["list"][0]["server_filename"];
						$size = $json4["list"][0]["size"];
						$path = $json4["list"][0]["path"];
						$server_ctime = (int)$json4["list"][0]["server_ctime"] + 28800; // 服务器创建时间 +8:00

						if (USING_DB) {
							connectdb();

							$DownloadLinkAvailableTime = (is_int(DownloadLinkAvailableTime)) ? DownloadLinkAvailableTime : 8;
							// 查询数据库中是否存在已经保存的数据
							$sql = "SELECT * FROM `$dbtable` WHERE `md5`='$md5' AND `ptime` > DATE_SUB(NOW(),INTERVAL $DownloadLinkAvailableTime HOUR);";
							$mysql_query = mysqli_query($conn, $sql);
						}
						if (USING_DB and $result = mysqli_fetch_assoc($mysql_query)) {
							$realLink = $result["realLink"];
							$usingcache = true;
						} else {

							// 判断今天内是否获取过文件
							if (USING_DB and !$isipwhite and !$smallfile) { // 白名单和小文件跳过
								// 获取解析次数
								$sql = "SELECT count(*) as Num FROM `$dbtable` WHERE `userip`='$ip' AND `size`>=52428800 AND date(`ptime`)=date(now());";
								$mysql_query = mysqli_query($conn, $sql);
								$result = mysqli_fetch_assoc($mysql_query);
								if ($result["Num"] >= DownloadTimes) {
									// 提示无权继续
									dl_error(Language["NoChance"], "<p class='card-text'>数据库中无此文件解析记录。</p><p class='card-text'>剩余解析次数为零，请明天再试。</p><hr />" . FileInfo($filename, $size, $md5, $server_ctime));
									exit;
								}
							}

							$DBSVIP = GetDBBDUSS();
							$SVIP_BDUSS = $DBSVIP[0];
							$id = $DBSVIP[1];

							// 开始获取真实链接
							if ($smallfile) $headerArray = array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36', 'Cookie: BDUSS=' . BDUSS . ';');
							else $headerArray = array('User-Agent: LogStatistic', 'Cookie: BDUSS=' . $SVIP_BDUSS . ';'); // 仅此处用到SVIPBDUSS

							$getRealLink = head($dlink, $headerArray); // 禁止重定向
							$getRealLink = strstr($getRealLink, "Location");
							$getRealLink = substr($getRealLink, 10);
							if ($smallfile) $realLink = getSubstr($getRealLink, "https://", "\r\n"); // 注意，这里小文件是https
							else $realLink = getSubstr($getRealLink, "http://", "\r\n"); // 删除 http://
							$usingcache = false;

							switch (SVIPSwitchMod) {
								case 1:
									//模式1：用到废为止
								case 2:
									//模式2：轮番上
									if ($id != "-1" and (strstr('https://' . $realLink, "//qdall") or $realLink == "")) {
										//限速进行标记 并刷新页面重新解析
										$sql = "UPDATE `" . $dbtable . "_svip` SET `state`= -1 WHERE `id`=$id";
										$mysql_query = mysqli_query($conn, $sql);
										if ($mysql_query != false) {
											// SVIP账号自动切换成功，对用户界面进行刷新进行重新获取
											$Language = Language;
											echo <<<SWITCHTIP
												<div class="row justify-content-center">
													<div class="col-md-7 col-sm-8 col-11">
														<div class="alert alert-danger" role="alert">
															<h5 class="alert-heading">{$Language["SwitchWait"]}</h5>
															<hr />
															<p class="card-text">当前SVIP账号已经被限速</p>
															<p class="card-text">正在自动切换新账号中</p>
															<p class="card-text">预计需要2~3秒，请耐心等待</p>
															</p>
														</div>
													</div>
												</div>
												<script>
													setTimeout(() => location.reload(), 2000);
												</script>
SWITCHTIP;
											exit;
										} else {
											// SVIP账号自动切换失败
											dl_error("SVIP账号切换失败", "数据库出现问题，无法切换SVIP账号，请联系站长修复", true);
											exit;
										}
									}
									break;
								case 3:
									//模式3：手动切换，不管限速
								case 4:
									//模式4：轮番上(无视限速)
								case 0:
									//模式0：使用本地解析
								default:
									break;
							}
						}

						// 1. 使用 dlink 下载文件   2. dlink 有效期为8小时   3. 必需要设置 User-Agent 字段   4. dlink 存在 HTTP 302 跳转
						if ($realLink == "") echo '<div class="row justify-content-center"><div class="col-md-7 col-sm-8 col-11"><div class="alert alert-danger" role="alert">
						<h5 class="alert-heading">' . Language["DownloadLinkError"] . '</h5><hr /><p class="card-text">已获取到文件，但未能获取到下载链接！</p><p class="card-text">请检查你是否在 <code>config.php</code> 中配置 SVIP 账号的 BDUSS 和 STOKEN！</p>
						<p class="card-text">未配置或配置了普通账号的均会导致失败！必须要 SVIP 账号！</p>' . FileInfo($filename, $size, $md5, $server_ctime) . '</div></div></div>'; // 未配置 SVIP 账号
						else {

							// 记录下使用者ip，下次进入时提示
							if (USING_DB and !$usingcache) {
								$ptime = date("Y-m-d H:i:s");
								$Sqlfilename = htmlspecialchars($filename); // 防止出现一些刁钻的文件名无法处理
								$Sqlpath = htmlspecialchars($path);
								$sql = "INSERT INTO `$dbtable`(`userip`, `filename`, `size`, `md5`, `path`, `server_ctime`, `realLink` , `ptime`,`paccount`) VALUES ('$ip','$Sqlfilename','$size','$md5','$Sqlpath','$server_ctime','$realLink','$ptime','$id')";
								$mysql_query = mysqli_query($conn, $sql);
								if ($mysql_query == false) {
									// 保存错误
									dl_error(Language["DatabaseError"], "数据库错误，请联系站长修护");
									exit;
								}
								echo "<script>var d=new Date();d.setDate(d.getDate()+1);d.setHours(0);d.setMinutes(0);d.setSeconds(0);document.cookie='SESSID=Nbef-cz-Zvbo_Uvp;expires='+d.toGMTString();</script>";
								// 为了防止一些换ip调用，这里写一个cookie
							}

		?>
							<div class="row justify-content-center">
								<div class="col-md-7 col-sm-8 col-11">
									<div class="alert alert-primary" role="alert">
										<h5 class="alert-heading"><?php echo Language["DownloadLinkSuccess"]; ?></h5>
										<hr />
										<?php
										if (USING_DB) {
											if ($usingcache) echo "<p class=\"card-text\">下载链接从数据库中提取，不消耗免费次数。</p>";
											elseif ($smallfile) echo "<p class=\"card-text\"><span style=\"color:red;\">此文件很小，不消耗解析次数。</span></p>";
											else echo "<p class=\"card-text\">服务器将保存下载地址8小时，时限内再次解析不消耗免费次数。</p>";
										}
										echo FileInfo($filename, $size, $md5, $server_ctime);

										echo '<hr><p class="card-text">' . Language["Rreview"] . '</p>';
										if ($_SERVER['HTTP_USER_AGENT'] == "LogStatistic" or $smallfile) {

											$type = substr($filename, -4);
											if ($type == ".jpg" || $type == ".png" || $type == "jpeg" || $type == ".bmp" || $type == ".gif") {
												echo '<img src="https://' . $realLink . '" class="img-fluid rounded" style="width: 100%;">';
											} elseif ($type == ".mp4") {
												echo '<video src="https://' . $realLink . '" controls="controls" style="width: 100%;">浏览器不支持</video>';
											} elseif ($type == ".mp3" || $type == ".wav") {
												echo '<audio src="https://' . $realLink . '" controls="controls" style="width: 100%;">浏览器不支持</audio>';
											} else {
												echo '<p class="card-text">' . Language["NotSupportWithUA"] . '</p>';
											}
										} else {
											echo '<p class="card-text">' . Language["NotSupportWithoutUA"] . '</p>';
										}
										echo '<hr />';
										if (strstr('https://' . $realLink, "//qdall")) echo '<h5 class="text-danger">当前SVIP账号已被限速，请联系站长更换账号。</h5>';
										echo '
								<p class="card-text">
									<a id="http" href="http://' . $realLink . '" style="display: none;">' . Language["DownloadLink"] . '（不安全）</a>';
										if ($smallfile) {
											echo '<a id="https" href="https://' . $realLink . '" target="_blank" rel="nofollow noopener noreferrer">' . Language["DownloadLink"] . '（无需设置UA，8小时有效）</a>';
										} else {
											echo '<a id="https" href="https://' . $realLink . '" target="_blank" rel="nofollow noopener noreferrer">' . Language["DownloadLink"] . '（需设置UA，8小时有效）</a>';
										}
										echo '</p>';
										?>
										<p class="card-text">
											<a href="javascript:void(0)" data-toggle="modal" data-target="#SendToAria2"><?php echo Language["SendToAria2"]; ?>(Motrix)</a>
										</p>
										<p class="card-text"><a href="?help" target="_blank"><?php echo Language["DownloadLink"] . Language["HelpButton"]; ?>（必读）</a></p>
										<p class="card-text"><?php echo Language["DownloadTip"]; ?></p>

										<div class="modal fade" id="SendToAria2" tabindex="-1" role="dialog" aria-hidden="true">
											<div class="modal-dialog" role="document">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title"><?php echo Language["SendToAria2"]; ?> Json-RPC</h5>
														<button type="button" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
														</button>
													</div>
													<div class="modal-body">
														<div class="form-group">
															<p><label class="control-label">RPC地址</label>
																<input id="wsurl" class="form-control" value="ws://localhost:6800/jsonrpc">
															</p>
															<small>推送aria2默认配置:<b>ws://localhost:6800/jsonrpc</b><br />推送Motrix默认配置:<b>ws://localhost:16800/jsonrpc</b></small>
														</div>
														<div class="form-group">
															<p><label class="control-label">Token</label>
																<input id="token" class="form-control" placeholder="没有请留空">
															</p>
														</div>
														<small>填写的信息在推送成功后将会被自动保存。</small>
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-primary" onclick="addUri()" data-dismiss="modal"><?php echo Language["Send"]; ?></button>
														<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo Language["Close"]; ?></button>
													</div>
												</div>
											</div>
											<script>
												$(function() {
													if (localStorage.getItem('aria2wsurl') != null)
														$('#wsurl').attr('value', localStorage.getItem('aria2wsurl'));
													if (localStorage.getItem('aria2token') != null)
														$('#token').attr('value', localStorage.getItem('aria2token'));
												})
											</script>
										</div>
									</div>
								</div>
							</div>
			<?php }
						// 成功！
					} elseif ($json4["errno"] == -9) dl_error("文件不存在(-9)", "请返回首页重新解析。");
					elseif ($json4["errno"] == 112) dl_error("链接超时(112)", "获取链接超时，每次解析列表后只有5min有效时间，请返回首页重新解析。"); // 链接超时
					elseif ($json4["errno"] == 113) dl_error("传参错误(113)", "获取失败，请检查参数是否正确。");
					elseif ($json4["errno"] == 118) dl_error("服务器错误(118)", "服务器错误，请求百度服务器时，未传入sekey参数或参数错误。", true);
					elseif ($json4["errno"] == 110) dl_error("服务器错误(110)", "服务器错误，可能服务器IP被百度封禁，请切换账号或更换服务器重试。"); // 服务器IP被ban
					else dl_error("获取下载链接失败", "未知错误！<br>错误号：" . $json4["errno"], true); // 未知错误
				} else dl_error("参数有误", "POST 传参出现问题！请不要自行构建表单提交！"); // 参数不齐
			} else dl_error("方法错误", "请不要直接访问此页面或使用 GET 方式访问！"); // 方法错误
		} else { // 首页
			?>
			<div class="col-lg-6 col-md-9 mx-auto mb-5 input-card">
				<div class="card">
					<div class="card-header bg-dark text-light">
						<?php if (USING_DB) { ?>
							<text id="parsingtooltip" data-placement="top" data-html="true" title="请稍等，正在连接服务器查询信息"><?php echo Language["IndexTitle"]; ?></text>
							<span style="float: right;" id="sviptooltip" data-placement="top" data-html="true" title="请稍等，正在连接服务器查询SVIP账号状态"><span class="point point-lg" id="svipstate-point"></span><span id="svipstate">Loading...</span></span>
						<?php } else echo Language["IndexTitle"]; ?>
					</div>
					<div class="card-body">
						<form name="form1" method="post" onsubmit="return validateForm()">
							<div class="form-group my-2"><input type="text" class="form-control" name="surl" placeholder="<?php echo Language["ShareLink"]; ?>" oninput="Getpw()"></div>
							<div class="form-group my-4"><input type="text" class="form-control" name="pwd" placeholder="<?php echo Language["SharePassword"]; ?>"></div>
							<?php
							if (IsCheckPassword) {
								$return = '<div class="form-group my-4"><input type="text" class="form-control" name="Password" placeholder="' . Language["PassWord"] . '"></div>';
								if (isset($_SESSION["Password"])) {
									if ($_SESSION["Password"] === Password) {
										$return = '<div>' . Language["PassWordVerified"] . '</div>';
									}
								}
								echo $return;
							} // 密码
							?>
							<button type="submit" class="mt-4 mb-3 btn btn-success btn-block"><?php echo Language["Submit"]; ?></button>
						</form>
						<?php if (file_exists("notice.html")) echo file_get_contents("notice.html"); ?>
					</div>
				</div>
				<?php if (USING_DB) { ?>
					<script>
						// 主页部分脚本
						$(document).ready(function() {

							$("#sviptooltip").tooltip(); // 初始化
							$("#parsingtooltip").tooltip(); // 初始化

							async function getAPI(method) { // 获取 API 数据
								try {
									const response = await fetch(`api.php?m=${method}`, { // fetch API
										credentials: 'same-origin' // 发送验证信息 (cookies)
									});
									if (response.ok) { // 判断是否出现 HTTP 异常
										return {
											success: true,
											data: await response.json() // 如果正常，则获取 JSON 数据
										}
									} else { // 若不正常，返回异常信息
										return {
											success: false,
											msg: `服务器返回异常 HTTP 状态码：HTTP ${response.status} ${response.statusText}.`
										};
									}
								} catch (reason) { // 若与服务器连接异常
									return {
										success: false,
										msg: '连接服务器过程中出现异常，消息：' + reason.message
									};
								}
							}

							getAPI('LastParse').then(function(response) {
								if (response.success) {
									const data = response.data;
									if (data.error == 0) {
										// 请求成功
										if (data.svipstate == 1) {
											$("#svipstate-point").addClass("point-success");
										} else {
											$("#svipstate-point").addClass("point-danger");
										}
									}
									$("#svipstate").text(data.sviptips);
									$("#sviptooltip").attr("data-original-title", data.msg);
								}
							});

							getAPI('ParseCount').then(function(response) {
								if (response.success) {
									$("#parsingtooltip").attr("data-original-title", response.data.msg);
								}
							});

						});
					</script>
				<?php } ?>
			</div>
		<?php
		}
		echo Footer; ?>
	</div>

	<?php
	$system_end_time = microtime(true);
	$system_runningtime = $system_end_time - $system_start_time;
	echo '<script>console.log("后端计算时间：' . $system_runningtime . '秒");</script>';
	?>
</body>

</html>