<?php
/**
* Pandownload复刻版
*
* 功能描述：使用百度SVIP账号获取真实下载地址，与Pandownload原版无关
*
* 使用的时候请保留一下作者信息呀（就是菜单栏的Made by Yuan_Tuo），谢~
*
* 有的注释不是很完整，见谅~
*
* @version 1.1.1
*
* @author Yuan_Tuo <yuantuo666@gmail.com>
* @link https://imwcr.cn/
* @link https://space.bilibili.com/88197958
*
* @author LC <lc@lcwebsite.cn>
* @link https://lcwebsite.cn/
* @link https://space.bilibili.com/52618445
*/
define('init', true);
if (file_exists('config.php')) {
	require('config.php');
} else {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	die('缺少配置文件！无法正常运行程序！
请重新 Clone 项目并配置！');
}
// 相关配置请在 config.php 中查看或修改
function post($url, $data, array $headerArray)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); //忽略ssl
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}
function get($url, array $headerArray)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //忽略ssl
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}
function head($url, array $headerArray)
{
	// curl 获取响应头
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //忽略ssl
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出
	curl_setopt($ch, CURLOPT_HEADER, true); // 返回 response header 默认 false 只会获得响应的正文
	curl_setopt($ch, CURLOPT_NOBODY, true); // 有时候为了节省带宽及时间，只需要响应头
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	$response = curl_exec($ch);
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE); // 获得响应头大小
	$header = substr($response, 0, $header_size); // 根据头大小获取头信息
	curl_close($ch);
	return $header;
}
function getSubstr($str, $leftStr, $rightStr)
{
	$left = strpos($str, $leftStr);
	//echo '左边:'.$left;
	$right = strpos($str, $rightStr, $left);
	//echo '<br>右边:'.$right;
	if ($left < 0 or $right < $left) return '';
	return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
}
//格式化size显示
function formatSize($b, $times = 0)
{
	if ($b > 1024) {
		$temp = $b / 1024;
		return formatSize($temp, $times + 1);
	} else {
		$unit = 'B';
		switch ($times) {
			case '0':
				$unit = 'B';
				break;
			case '1':
				$unit = 'KB';
				break;
			case '2':
				$unit = 'MB';
				break;
			case '3':
				$unit = 'GB';
				break;
			case '4':
				$unit = 'TB';
				break;
			case '5':
				$unit = 'PB';
				break;
			case '6':
				$unit = 'EB';
				break;
			case '7':
				$unit = 'ZB';
				break;
			default:
				$unit = '单位未知';
		}
		return sprintf('%.2f', $b) . $unit;
	}
}
// 检查密码
function CheckPassword(){
	if (IsCheckPassword) {
		global $setpassword;
		if (empty($_POST["password"]) or $_POST["password"] != $setpassword) {
			die('<div class="row justify-content-center">
			<div class="col-md-7 col-sm-8 col-11"><div class="alert alert-danger" role="alert">
			<h5 class="alert-heading">错误</h5>
			<hr>
			<p class="card-text">密码错误</p>
			</div></div></div></div></body></html>');
		} else {
			echo '<script>sweetAlert("重要提示","请勿将密码告诉他人！此项目仅供测试使用！\r\n——Yuan_Tuo","info");</script>';
		}
	}
}
// 通用响应头
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge,chrome=1');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="author" content="Yuan_Tuo" />
	<meta name="author" content="LC" />
	<meta name="description" content="PanDownload 网页版，百度网盘分享链接在线解析工具。" />
	<meta name="keywords" content="PanDownload,百度网盘,分享链接,下载,不限速" />
	<link rel="icon" href="https://pandownload.com/favicon.ico" />
	<link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/4.1.2/css/bootstrap.min.css" />
	<script src="https://cdn.staticfile.org/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://cdn.staticfile.org/popper.js/1.12.5/umd/popper.min.js"></script>
	<script src="https://cdn.staticfile.org/twitter-bootstrap/4.1.2/js/bootstrap.min.js"></script>
	<!-- 可以异步 -->
	<link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/5.8.1/css/all.min.css" />
	<link rel="stylesheet" href="https://cdn.staticfile.org/bootstrap-sweetalert/1.0.1/sweetalert.min.css" />
	<script src="https://cdn.staticfile.org/bootstrap-sweetalert/1.0.1/sweetalert.min.js"></script>
	<title>PanDownload 复刻版</title>
	<style>
		body { background: url("https://pandownload.com/img/baiduwp/bg.png"); }
		.logo-img { width: 1.1em; position: relative; top: -3px; }
		.form-inline input { width: 500px; }
		.input-card { position: relative; top: 7.0em; }
		.card-header { height: 3.2em; font-size: 20px; line-height: 2.0em; }
		form input, form button { height: 3em; }
		.alert { position: relative; top: 5em; }
		.alert-heading { height: 0.8em; }
	</style>
	<script>
		function validateForm() {
			var link = document.forms["form1"]["surl"].value;
			if (link == null || link === "") { document.forms["form1"]["surl"].focus(); return false; }
			var uk = link.match(/uk=(\d+)/), shareid = link.match(/shareid=(\d+)/);
			if (uk != null && shareid != null) {
				document.forms["form1"]["surl"].value = "";
				$("form").append(`<input type="hidden" name="uk" value="${uk[1]}"/><input type="hidden" name="shareid" value="${shareid[1]}"/>`);
				return true;
			}
			var surl = link.match(/surl=([A-Za-z0-9-_]+)/);
			if (surl == null) {
				surl = link.match(/1[A-Za-z0-9-_]+/);
				if (surl == null) {
					document.forms["form1"]["surl"].focus(); return false;
				} else {
					surl = surl[0];
				}
			} else {
				surl = "1" + surl[1];
			}
			document.forms["form1"]["surl"].value = surl;
			return true;
		}
		function dl(fs_id, timestamp, sign, randsk, share_id, uk) {
			var form = $('<form method="post" action="?download" target="_blank"></form>');
			form.append(`<input type="hidden" name="fs_id" value="${fs_id}"/>
				<input type="hidden" name="time" value="${timestamp}"/>
				<input type="hidden" name="sign" value="${sign}"/>
				<input type="hidden" name="randsk" value="${randsk}"/>
				<input type="hidden" name="share_id" value="${share_id}"/>
				<input type="hidden" name="uk" value="${uk}"/>`);
			$(document.body).append(form);
			form.submit();
		}
		function getIconClass(filename) {
			var filetype = {
				file_video: ["wmv", "rmvb", "mpeg4", "mpeg2", "flv", "avi", "3gp", "mpga", "qt", "rm", "wmz", "wmd", "wvx", "wmx", "wm", "mpg", "mp4", "mkv", "mpeg", "mov", "asf", "m4v", "m3u8", "swf"],
				file_audio: ["wma", "wav", "mp3", "aac", "ra", "ram", "mp2", "ogg", "aif", "mpega", "amr", "mid", "midi", "m4a", "flac"],
				file_image: ["jpg", "jpeg", "gif", "bmp", "png", "jpe", "cur", "svg", "svgz", "ico"], // 是否需要增加 webp 格式？
				file_archive: ["rar", "zip", "7z", "iso"],
				windows: ["exe"],
				apple: ["ipa"],
				android: ["apk"],
				file_alt: ["txt", "rtf"],
				file_excel: ["xls", "xlsx"], // xlsm 等以及模板？
				file_word: ["doc", "docx"],
				file_powerpoint: ["ppt", "pptx"],
				file_pdf: ["pdf"],
			};
			var point = filename.lastIndexOf(".");
			var t = filename.substr(point + 1);
			if (t === "") return "";
			t = t.toLowerCase();
			for (var icon in filetype) {
				for (var type in filetype[icon]) {
					if (t === filetype[icon][type]) return "fa-" + icon.replace('_', '-');
				}
			}
			return "";
		}
		$(document).ready(function() {
			$(".fa-file").each(function() {
				var icon = getIconClass($(this).next().text());
				if (icon !== "") {
					if ($.inArray(icon, ['fa-windows', 'fa-android', 'fa-apple']) >= 0) $(this).removeClass("far").addClass("fab");
					$(this).removeClass("fa-file").addClass(icon);
				}
			});
		});
	</script>
</head>
<body>
	<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
		<div class="container">
			<a class="navbar-brand" href=""><img src="https://pandownload.com/img/baiduwp/logo.png" class="img-fluid rounded logo-img mr-2" alt="LOGO"/>PanDownload</a>
			<button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#collpase-bar"><span class="navbar-toggler-icon"></span></button>
			<div class="collapse navbar-collapse" id="collpase-bar">
				<ul class="navbar-nav">
					<li class="nav-item"><a class="nav-link" href="">主页</a></li>
					<li class="nav-item"><a class="nav-link" href="https://pandownload.com/" target="_blank">网盘下载器</a></li>
					<li class="nav-item"><a class="nav-link" href="https://imwcr.cn/" target="_blank">Made by Yuan_Tuo</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container">
		<?php //开始判断
		if (isset($_GET["help"])) { ?>
			<div class="row justify-content-center">
				<div class="col-md-7 col-sm-8 col-11">
					<div class="alert alert-primary" role="alert">
						<h5 class="alert-heading">提示</h5><hr/>
						<p class="card-text">因百度限制，需修改浏览器UA后下载。<br>
							<div class="page-inner">
								<section class="normal" id="section-">
									<div id="IDM"><h4><a class="anchor" href="#IDM"></a>IDM（推荐）</h4></div>
									<ol>
										<li>选项 -> 下载 -> 手动添加任务时使用的用户代理（UA）-> 填入 <b>LogStatistic</b></li>
										<li><b>右键复制下载链接</b>(直接点击 或 右键调用IDM 将传入浏览器的UA，将会导致下载失败)，在 IDM 新建任务，粘贴链接即可下载。</li>
									</ol>
									<div id="Chrome"><h4><a class="anchor" href="#Chrome"></a>Chrome 浏览器</h4></div>
									<ol>
										<li>安装浏览器扩展程序 <a href="https://chrome.google.com/webstore/detail/user-agent-switcher-for-c/djflhoibgkdhkhhcedjiklpkjnoahfmg" target="_blank">User-Agent Switcher for Chrome</a></li>
										<li>右键点击扩展图标 -> 选项</li>
										<li>New User-agent name 填入 百度网盘分享下载</li>
										<li>New User-Agent String 填入 LogStatistic</li>
										<li>Group 填入 百度网盘</li>
										<li>Append? 选择 Replace</li>
										<li>Indicator Flag 填入 Log，点击 Add 保存</li>
										<li>保存后点击扩展图标，出现"百度网盘"，进入并选择"百度网盘分享下载"。</li>
									</ol>
									<blockquote>
										<p>Chrome 应用商店打不开或者其他 Chromium 内核的浏览器，<a href="http://pandownload.com/static/user_agent_switcher_1_0_43_0.crx" target="_blank">请点此下载</a></p>
										<p><a href="https://appcenter.browser.qq.com/search/detail?key=User-Agent%20Switcher%20for%20Chrome&amp;id=djflhoibgkdhkhhcedjiklpkjnoahfmg%20&amp;title=User-Agent%20Switcher%20for%20Chrome" target="_blank">QQ浏览器插件下载</a></p>
									</blockquote>
									<div id="Pure"><h4><a class="anchor" href="#Pure"></a>Pure 浏览器（Android）</h4></div>
									<ol>
										<li>设置 –&gt; 浏览设置 -&gt; 浏览器标识(UA)</li>
										<li>添加自定义UA：LogStatistic</li>
									</ol>
									<div id="Alook"><h4><a class="anchor" href="#$Alook"></a>Alook 浏览器（IOS）</h4></id>
									<ol>
										<li>设置 -&gt; 通用设置 -&gt; 浏览器标识 -&gt; 移动版浏览器标识 -&gt; 自定义 -><br/> 填入 <b>LogStatistic</b></li>
									</ol>
								</section>
								<script>
									$('.anchor').attr('target', '_self').prepend(`<svg viewBox="0 0 16 16" version="1.1" width="16" height="16"><path fill-rule="evenodd" d="M7.775 3.275a.75.75 0 001.06 1.06l1.25-1.25a2 2 0 112.83 2.83l-2.5 2.5a2 2 0 01-2.83 0 .75.75 0 00-1.06 1.06 3.5 3.5 0 004.95
									0l2.5-2.5a3.5 3.5 0 00-4.95-4.95l-1.25 1.25zm-4.69 9.64a2 2 0 010-2.83l2.5-2.5a2 2 0 012.83 0 .75.75 0 001.06-1.06 3.5 3.5 0 00-4.95 0l-2.5 2.5a3.5 3.5 0 004.95 4.95l1.25-1.25a.75.75 0 00-1.06-1.06l-1.25 1.25a2 2 0 01-2.83 0z"/></svg>`);
								</script>
							</div>
						</p>
					</div>
				</div>
			</div>
		<?php } elseif (isset($_POST["surl"])) {
			CheckPassword();
			$surl = $_POST["surl"];
			$pwd = $_POST["pwd"];
			$surl_1 = substr($surl, 1);
			function verifyPwd($surl_1, $pwd)
			{ // 验证密码
				$url = 'https://pan.baidu.com/share/verify?channel=chunlei&clienttype=0&web=1&app_id=250528&surl=' . $surl_1;
				$data = "pwd=$pwd";
				$headerArray = array("user-agent: netdisk", "Referer: https://pan.baidu.com/disk/home");
				$json1 = post($url, $data, $headerArray);
				$json1 = json_decode($json1, true);
				// -12 验证码错误
				if ($json1["errno"] == 0) return $json1["randsk"];
				else return 1;
			}
			function getSign($surl, $randsk)
			{
				if ($randsk == 1) return 1;
				$url = 'https://pan.baidu.com/s/1' . $surl;
				$headerArray = array(
					"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36",
					"Cookie: BDUSS=" . constant("BDUSS") . ";STOKEN=" . constant("STOKEN") . ";BDCLND=" . $randsk . ";"
				);
				$json2 = get($url, $headerArray);
				$re = '/yunData.setData\(({.+)\);/';
				$re = '/yunData.setData\(\{(.*)?\}\);/';
				if (preg_match($re, $json2, $matches)) {
					$json2 = $matches[0];
					$json2 = substr($json2, 16, -2);
					$json2 = json_decode($json2, true);
					return $json2;
				} else return 1;
			}
			function getFileList($shareid, $uk, $randsk)
			{
				$url = 'https://pan.baidu.com/share/list?app_id=250528&channel=chunlei&clienttype=0&desc=0&num=100&order=name&page=1&root=1&shareid=' . $shareid . '&showempty=0&uk=' . $uk . '&web=1';
				$headerArray = array(
					"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36",
					"Cookie: BDUSS=" . constant("BDUSS") . ";STOKEN=" . constant("STOKEN") . ";BDCLND=" . $randsk . ";",
					"Referer: https://pan.baidu.com/disk/home"
				);
				$json3 = get($url, $headerArray);
				$json3 = json_decode($json3, true);
				return $json3;
			}
			if ($pwd != "") $randsk = verifyPwd($surl_1, $pwd);
			else $randsk = "";
			$json2 = getSign($surl_1, $randsk);
			if ($json2 != 1) {
				$sign = $json2["sign"];
				$timestamp = $json2["timestamp"];
				$shareid = $json2["shareid"];
				$uk = $json2["uk"];
				$filejson = getFileList($shareid, $uk, $randsk);
				if ($filejson["errno"] == -21) {
					//链接失效
					echo '<div class="row justify-content-center">
					<div class="col-md-7 col-sm-8 col-11"><div class="alert alert-danger" role="alert">
					<h5 class="alert-heading">链接不存在</h5>
					<hr>
					<p class="card-text">此链接分享内容可能被取消或因涉及侵权、色情、反动、低俗等信息，无法访问！</p>
					</div></div></div>';
				} else if ($filejson["errno"] != 0) {
					// 鬼知道发生了啥，比如说 -7
					echo '<div class="row justify-content-center">
					<div class="col-md-7 col-sm-8 col-11"><div class="alert alert-danger" role="alert">
					<h5 class="alert-heading">链接存在问题</h5>
					<hr>
					<p class="card-text">此链接存在问题，无法访问！</p>
					</div></div></div>';
				} else {
					// 终于正常了
					//var_dump($filejson);
					$filecontent = '<ol class="breadcrumb my-4">
				文件列表(' . count($filejson["list"]) . ') </ol>
				<div>
				<ul class="list-group ">';
					for ($i = 0; $i < count($filejson["list"]); $i++) {
						$file = $filejson["list"][$i];
						if ($file["isdir"] == 0) {
							$filecontent .= '<li class="list-group-item border-muted rounded text-muted py-2">
						<i class="far fa-file mr-2"></i>
						<a href="javascript:dl(\'' . $file["fs_id"] . '\',' . $timestamp . ',\'' . $sign . '\',\'' . $randsk . '\',\'' . $shareid . '\',\'' . $uk . '\');">' . $file["server_filename"] . '</a>
						<span class="float-right">' . formatSize($file["size"]) . '</span>
						</li>';
						} else {
							$filecontent .= '<li class="list-group-item border-muted rounded text-muted py-2">
					<i class="far fa-folder mr-2"></i>
					<a href="javascript:sweetAlert(\'Sorry~\',\'暂不支持文件夹下载\r\n你可以转存到自己网盘、分享后重试\',\'error\');">' . $file["server_filename"] . '</a>
					<span class="float-right"></span>
					</li>';
						}
					}
					$filecontent .= "</ul>";
					echo $filecontent;
				}
			} else {
				echo '<div class="row justify-content-center">
				<div class="col-md-7 col-sm-8 col-11"><div class="alert alert-danger" role="alert">
				<h5 class="alert-heading">提示</h5><hr/>
				<p class="card-text">提取码错误或文件失效</p>
				</div></div></div>';
			}
		} elseif (isset($_GET["download"])) {
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				// 加个 isset ！！！
				$fs_id = $_POST["fs_id"];
				$timestamp = $_POST["time"];
				$sign = $_POST["sign"];
				$randsk = $_POST["randsk"];
				$share_id = $_POST["share_id"];
				$uk = $_POST["uk"];
				function getDlink($fs_id, $timestamp, $sign, $randsk, $share_id, $uk)
				{
					$postdata = "";
					$postdata .= "encrypt=0";
					$postdata .= "&extra=" . urlencode("{\"sekey\":\"" . urldecode($randsk) . "\"}"); //被这个转义坑惨了QAQ
					$postdata .= "&fid_list=[$fs_id]";
					$postdata .= "&primaryid=$share_id";
					$postdata .= "&uk=$uk";
					$postdata .= "&product=share";
					$postdata .= "&type=nolimit";
					$url = 'https://pan.baidu.com/api/sharedownload?app_id=250528&channel=chunlei&clienttype=12&sign=' . $sign . '&timestamp=' . $timestamp . '&web=1';
					$headerArray = array(
						"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36",
						"Cookie: BDUSS=" . constant("BDUSS") . ";STOKEN=" . constant("STOKEN") . ";BDCLND=" . $randsk . ";",
						"Referer: https://pan.baidu.com/disk/home"
					);
					$res3 = post($url, $postdata, $headerArray);
					$res3 = json_decode($res3, true);
					//var_dump($postdata, $res3);
					//没有referer就112，然后没有sekey参数就118  -20？？？
					// 参数	                类型	    描述
					// list	                json array	文件信息列表
					// names	            json	    如果查询共享目录，该字段为共享目录文件上传者的uk和账户名称
					// list[0]["category"]	int	        文件类型
					// list[0]["dlink”]	    string	    文件下载地址
					// list[0]["file_name”]	string	    文件名
					// list[0]["isdir”]	    int	        是否是目录
					// list[0]["server_ctime”]	int	    文件的服务器创建时间
					// list[0]["server_mtime”]	int	    文件的服务修改时间
					// list[0]["size”]	    int	        文件大小
					// list[0]["thumbs”]		        缩略图地址
					// list[0]["height”]	int	        图片高度
					// list[0]["width”]	    int	        图片宽度
					// list[0]["date_taken”]int	        图片拍摄时间
					return $res3;
				}
				$json4 = getDlink($fs_id, $timestamp, $sign, $randsk, $share_id, $uk);
				if ($json4["errno"] == 0) {
					$dlink = $json4["list"][0]["dlink"];
					$md5 = $json4["list"][0]["md5"];
					$filename = $json4["list"][0]["server_filename"];
					$size = $json4["list"][0]["size"];
					$server_ctime = (int)$json4["list"][0]["server_ctime"] + 28800; //服务器创建时间 +8:00
					$headerArray = array(
						'User-Agent: LogStatistic',
						'Cookie: BDUSS=' . constant("BDUSS") . ';'
					);
					$getRealLink = head($dlink, $headerArray); //禁止重定向
					$getRealLink = strstr($getRealLink, "Location");
					$getRealLink = substr($getRealLink, 10);
					$realLink = getSubstr($getRealLink, "http://", "\r\n"); //除掉http://
					// 3. 使用dlink下载文件
					// 4. dlink有效期为8小时
					// 5. 必需要设置User-Agent字段
					// 6. dlink存在302跳转
					echo '<div class="row justify-content-center">
					<div class="col-md-7 col-sm-8 col-11">
					<div class="alert alert-primary" role="alert">
					<h5 class="alert-heading">获取下载链接成功</h5>
					<hr>
					<p class="card-text">文件名: <b>' . $filename . '</b></p>
					<p class="card-text">文件大小: <b>' . formatSize($size) . '</b></p>
					<p class="card-text">文件MD5: <b>' . $md5 . '</b></p>
					<p class="card-text">上传时间: <b>' . date("Y年m月d日 H:i:s", $server_ctime) . '</b></p>
					<p class="card-text"><a href="http://' . $realLink . '" target=_blank>下载链接(http)</a>
					<a href="https://' . $realLink . '" target=_blank>下载链接(https)</a></p>
					<p class="card-text"><a href="?help" target=_blank>下载链接使用方法（必读）</a></p>
					</div></div></div>';
				} else {
					echo '<div class="row justify-content-center">
					<div class="col-md-7 col-sm-8 col-11">
					<div class="alert alert-danger" role="alert">
						<h5 class="alert-heading">获取下载链接失败</h5><hr>
						<p class="card-text">未知错误</p>
						</div></div></div>';
				}
			} else {
				echo '<div class="row justify-content-center">
				<div class="col-md-7 col-sm-8 col-11">
				<div class="alert alert-danger" role="alert">
					<h5 class="alert-heading">方法错误</h5><hr>
					<p class="card-text">请不要直接访问此页面或使用 GET 方式访问！</p>
					</div></div></div>';
			}
		} else { ?>
			<div class="col-lg-6 col-md-9 mx-auto mb-5 input-card">
				<div class="card">
					<div class="card-header bg-dark text-light">分享链接在线解析</div>
					<div class="card-body">
						<form name="form1" method="post" onsubmit="return validateForm()">
							<div class="form-group my-2">
								<input type="text" class="form-control" name="surl" placeholder="分享链接">
							</div>
							<div class="form-group my-4">
								<input type="text" class="form-control" name="pwd" placeholder="提取码">
							</div>
							<?php if (IsCheckPassword) { echo '<div class="form-group my-4"><input type="text" class="form-control" name="password" placeholder="密码"></div>'; } // 密码 ?>
							<button type="submit" class="mt-4 mb-3 form-control btn btn-success btn-block">打开</button>
						</form>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</body>
</html>