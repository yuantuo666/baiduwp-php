<?php

/**
 * PanDownload 网页复刻版，PHP 语言版主文件
 *
 * 功能描述：使用百度 SVIP 账号获取真实下载地址，与 Pandownload 原版无关。
 * 本项目是依据 [baiduwp](https://github.com/TkzcM/baiduwp "baiduwp") 的 JavaScript 版本改写而来，仅供大家学习参考。
 *
 * 希望在使用时能够保留导航栏的 Made by Yuan_Tuo 感谢！
 *
 * 请勿随意修改此文件！如需更改相关配置请到 config.php ！
 *
 * 此项目 GitHub 地址：https://github.com/yuantuo666/baiduwp-php
 *
 * @version 1.4.1
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
session_start();
define('init', true);
if (version_compare(PHP_VERSION, '7.0.0', '<')) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://www.php.net/downloads.php');
	die("HTTP 503 服务不可用！\r\nPHP 版本过低！无法正常运行程序！\r\n请安装 7.0.0 或以上版本的 PHP！\r\n将在五秒内跳转到 PHP 官方下载页面！");
}
if (!(file_exists('config.php') && file_exists('functions.php'))) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://github.com/yuantuo666/baiduwp-php');
	die("HTTP 503 服务不可用！\r\n缺少相关配置和定义文件！无法正常运行程序！\r\n请重新 Clone 项目并配置！\r\n将在五秒内跳转到 GitHub 储存库！");
}
// 导入配置和函数
require('config.php');
require('functions.php');
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
	<meta name="version" content="<?php echo programVersion; ?>" />
	<meta name="description" content="PanDownload 网页版，百度网盘分享链接在线解析工具。" />
	<meta name="keywords" content="PanDownload,百度网盘,分享链接,下载,不限速" />
	<title>PanDownload 复刻版</title>
	<link rel="icon" href="resource/favicon.ico" />
	<link rel="stylesheet" href="static/index.css" />
	<link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/4.1.2/css/bootstrap.min.css" />
	<link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/5.8.1/css/all.min.css" />
	<link rel="stylesheet" href="https://cdn.staticfile.org/bootstrap-sweetalert/1.0.1/sweetalert.min.css" />
	<script src="https://cdn.staticfile.org/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://cdn.staticfile.org/popper.js/1.12.5/umd/popper.min.js"></script>
	<script src="https://cdn.staticfile.org/twitter-bootstrap/4.1.2/js/bootstrap.min.js"></script>
	<script src="https://cdn.staticfile.org/bootstrap-sweetalert/1.0.1/sweetalert.min.js"></script>
	<script src="static/functions.js"></script>
	<script defer src="static/ready.js"></script>
</head>

<body>
	<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
		<div class="container">
			<a class="navbar-brand" href="./"><img src="resource/logo.png" class="img-fluid rounded logo-img mr-2" alt="LOGO" />PanDownload</a>
			<button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#collpase-bar"><span class="navbar-toggler-icon"></span></button>
			<div class="collapse navbar-collapse" id="collpase-bar">
				<ul class="navbar-nav">
					<li class="nav-item"><a class="nav-link" href="./">首页</a></li>
					<li class="nav-item"><a class="nav-link" href="?help" target="_blank">下载帮助</a></li>
					<li class="nav-item"><a class="nav-link" href="https://imwcr.cn/" target="_blank">Made by Yuan_Tuo</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container">
		<?php if (isset($_GET["help"])) { // 帮助页 
		?>
			<div class="row justify-content-center">
				<div class="col-md-7 col-sm-8 col-11">
					<div class="alert alert-primary" role="alert">
						<h5 class="alert-heading">提示</h5>
						<hr />
						<p class="card-text">因百度限制，需修改浏览器 User Agent 后下载。<br />
							<div class="page-inner">
								<section class="normal" id="section-">
									<div id="IDM"><a class="anchor" href="#IDM"></a>
										<h4>IDM（推荐）</h4>
									</div>
									<ol>
										<li>选项 -> 下载 -> 手动添加任务时使用的用户代理（UA）-> 填入 <b>LogStatistic</b></li>
										<li><b>右键复制下载链接</b>（直接点击 或 右键调用 IDM 将传入浏览器的 UA，将会导致下载失败），在 IDM 新建任务，粘贴链接即可下载。</li>
									</ol>
									<div id="ADM"><a class="anchor" href="#ADM"></a>
										<h4>ADM Pro（Android推荐）</h4>
										<ol>
											<li>设置 -> 下载中 -> 浏览器标识 -> 自定义 浏览器标识</li>
											<li>填入 <b>LogStatistic</b></li>
											<li>切换到浏览器（ADM留在后台），长按“下载链接”，选择复制链接地址</li>
											<li>然后在ADM里点击开始即可</li>
										</ol>
										<div id="Chrome"><a class="anchor" href="#Chrome"></a>
											<h4>Chrome 浏览器</h4>
										</div>
										<ol>
											<li>安装浏览器扩展程序 <a href="https://chrome.google.com/webstore/detail/user-agent-switcher-for-c/djflhoibgkdhkhhcedjiklpkjnoahfmg" target="_blank">User-Agent Switcher for Chrome</a></li>
											<li>右键点击扩展图标 -> 选项</li>
											<li>New User-Agent name 填入 百度网盘分享下载</li>
											<li>New User-Agent String 填入 <b>LogStatistic</b></li>
											<li>Group 填入 百度网盘</li>
											<li>Append? 选择 Replace</li>
											<li>Indicator Flag 填入 Log，点击 Add 保存</li>
											<li>保存后点击扩展图标，出现“百度网盘”，进入并选择“百度网盘分享下载”。</li>
										</ol>
										<blockquote>
											<p>Chrome 应用商店打不开或者其他 Chromium 内核的浏览器，<a href="resource/UserAgentSwitcher.crx" target="_blank">请点此下载</a></p>
											<p><a href="https://appcenter.browser.qq.com/search/detail?key=User-Agent%20Switcher%20for%20Chrome&amp;id=djflhoibgkdhkhhcedjiklpkjnoahfmg%20&amp;title=User-Agent%20Switcher%20for%20Chrome" target="_blank">QQ浏览器插件下载</a></p>
										</blockquote>
										<div id="Pure"><a class="anchor" href="#Pure"></a>
											<h4>Pure 浏览器（Android）</h4>
										</div>
										<ol>
											<li>设置 –> 浏览设置 -> 浏览器标识(UA)</li>
											<li>添加自定义 UA：<b>LogStatistic</b></li>
										</ol>
										<div id="Alook"><a class="anchor" href="#Alook"></a>
											<h4>Alook 浏览器（IOS）</h4>
										</div>
										<ol>
											<li>设置 -> 通用设置 -> 浏览器标识 -> 移动版浏览器标识 -> 自定义 -><br />填入 <b>LogStatistic</b></li>
										</ol>
										<div id="Copyright"><a class="anchor" href="#Copyright"></a>
											<h4>关于此项目</h4>
										</div>
										<ol>
											<li>本项目与PanDownload无关。</li>
											<li>本项目仅以学习为目的，不得用于其他用途。</li>
											<li>当前项目版本：<?php echo programVersion; ?></li>
											<li><a href="https://github.com/yuantuo666/baiduwp-php" target="_blank">Github仓库</a></li>
											<li><a href="https://imwcr.cn/" target="_blank">Made by Yuan_Tuo</a></li>
										</ol>
								</section>
								<script>
									$('.anchor').attr('target', '_self').prepend(`<svg viewBox="0 0 16 16" version="1.1" width="16" height="16"><path fill-rule="evenodd" d="M7.775 3.275a.75.75 0 001.06 1.06l1.25-1.25a2 2 0 112.83 2.83l-2.5 2.5a2 2 0 01-2.83 0 .75.75 0 00-1.06 1.06 3.5
									3.5 0 004.95 0l2.5-2.5a3.5 3.5 0 00-4.95-4.95l-1.25 1.25zm-4.69 9.64a2 2 0 010-2.83l2.5-2.5a2 2 0 012.83 0 .75.75 0 001.06-1.06 3.5 3.5 0 00-4.95 0l-2.5 2.5a3.5 3.5 0 004.95 4.95l1.25-1.25a.75.75 0 00-1.06-1.06l-1.25 1.25a2 2 0 01-2.83 0z"/></svg>`);
								</script>
							</div>
						</p>
					</div>
				</div>
			</div>
		</div>
			<?php } elseif (isset($_POST["surl"]) && isset($_POST["pwd"])) { // 解析链接页面
			CheckPassword();
			$surl = $_POST["surl"];
			$pwd = $_POST["pwd"];
			if (isset($_POST["dir"])) {
				if ($pwd !== '') $randsk = verifyPwd($surl, $pwd);
				else $randsk = '';
				$shareid = $_POST["share_id"];
				$root = getSign($surl, $randsk);
				if ($root !== 1) {
					$uk = $_POST["uk"];
					$sign = $root["sign"];
					$timestamp = $root["timestamp"];
					$bdstoken = $root["bdstoken"];
					$filejson = GetDir($_POST["dir"], $randsk, $shareid, $uk);
					if ($filejson["errno"] != 0) echo '<div class="row justify-content-center"><div class="col-md-7 col-sm-8 col-11"><div class="alert alert-danger" role="alert">
						<h5 class="alert-heading">文件夹存在问题</h5><hr /><p class="card-text">此文件夹存在问题，无法访问！</p></div></div></div>'; // 鬼知道发生了啥
					else { // 终于正常了
						//面包屑导航
						$filecontent = '<nav aria-label="breadcrumb"><ol class="breadcrumb my-4">
							<li class="breadcrumb-item"><a href="javascript:OpenRoot(\'1' . $surl . '\',\'' . $pwd . '\');">全部文件</a></li>';
						$dir_list = explode("/", $_POST["dir"]);
						for ($i = 1; $i <= count($dir_list) - 2; $i++) {
							$fullsrc = strstr($_POST["dir"], $dir_list[$i], true) . $dir_list[$i];
							$filecontent .= '<li class="breadcrumb-item"><a href="javascript:OpenDir(\'' . $fullsrc . '\',\'' . $pwd . '\',\'' . $shareid . '\',\'' . $uk . '\',\'' . $surl . '\');">' . $dir_list[$i] . '</a></li>';
						}
						$filecontent .= '<li class="breadcrumb-item active">' . $dir_list[$i] . '</li>'
							. '<li class="ml-auto">已全部加载，共' . count($filejson["list"]) . '个</li></ol></nav>';

						$filecontent .= '<div><ul class="list-group">';
						for ($i = 0; $i < count($filejson["list"]); $i++) { //开始输出文件列表
							$file = $filejson["list"][$i];
							if ($file["isdir"] === 0) $filecontent .= '<li class="list-group-item border-muted text-muted py-2"><i class="far fa-file mr-2"></i>
								<a href="javascript:dl(\'' . number_format($file["fs_id"], 0, '', '') . '\',' . $timestamp . ',\'' . $sign . '\',\'' . urlencode($randsk) . '\',\'' . $shareid . '\',\'' . $uk . '\',\'' . $bdstoken . '\',\'' . $file["size"] . '\');">' . $file["server_filename"] . '</a>
								<span class="float-right">' . formatSize($file["size"]) . '</span></li>';
							else $filecontent .= '<li class="list-group-item border-muted text-muted py-2"><i class="far fa-folder mr-2"></i>
								<a href="javascript:OpenDir(\'' . $file["path"] . '\',\'' . $pwd . '\',\'' . $shareid . '\',\'' . $uk . '\',\'' . $surl . '\');">' . $file["server_filename"] . '</a><span class="float-right"></span></li>';
						}
						echo $filecontent . "</ul></div>";
					}
				} else echo '<div class="row justify-content-center"><div class="col-md-7 col-sm-8 col-11"><div class="alert alert-danger" role="alert">
					<h5 class="alert-heading">提示</h5><hr /><p class="card-text">提取码错误或文件失效！</p></div></div></div>';
			} else {
				$surl_1 = substr($surl, 1);
				if ($pwd !== '') $randsk = verifyPwd($surl_1, $pwd);
				else $randsk = '';
				$root = getSign($surl_1, $randsk);
				$filejson = FileList($root);
				if ($filejson !== 1) {
					$sign = $root["sign"];
					$timestamp = $root["timestamp"];
					$shareid = $root["shareid"];
					$uk = $root["uk"];
					$bdstoken = $root["bdstoken"];
					if ($filejson["errno"] != 0) echo '<div class="row justify-content-center"><div class="col-md-7 col-sm-8 col-11"><div class="alert alert-danger" role="alert">
						<h5 class="alert-heading">链接存在问题</h5><hr /><p class="card-text">此链接存在问题，无法访问！</p></div></div></div>'; // 鬼知道发生了啥
					else { // 终于正常了
						$filecontent = '<nav aria-label="breadcrumb">
						<ol class="breadcrumb my-4">
							<li class="breadcrumb-item active" aria-current="page">全部文件</li>
						<li class="ml-auto">已全部加载，共' . count($filejson["list"]) . '个</li>
						</ol>
						</nav>
						<div><ul class="list-group">';
						for ($i = 0; $i < count($filejson["list"]); $i++) {
							$file = $filejson["list"][$i];
							if ($file["isdir"] === 0) $filecontent .= '<li class="list-group-item border-muted text-muted py-2"><i class="far fa-file mr-2"></i>
								<a href="javascript:dl(\'' . number_format($file["fs_id"], 0, '', '') . '\',' . $timestamp . ',\'' . $sign . '\',\'' . urlencode($randsk) . '\',\'' . $shareid . '\',\'' . $uk . '\',\'' . $bdstoken . '\',\'' . $file["size"] . '\');">' . $file["server_filename"] . '</a>
								<span class="float-right">' . formatSize($file["size"]) . '</span></li>';
							else $filecontent .= '<li class="list-group-item border-muted text-muted py-2"><i class="far fa-folder mr-2"></i>
								<a href="javascript:OpenDir(\'' . $file["path"] . '\',\'' . $pwd . '\',\'' . $shareid . '\',\'' . $uk . '\',\'' . $surl_1 . '\');">' . $file["server_filename"] . '</a><span class="float-right"></span></li>';
						}
						echo $filecontent . "</ul></div>";
					}
				} else echo '<div class="row justify-content-center"><div class="col-md-7 col-sm-8 col-11"><div class="alert alert-danger" role="alert">
					<h5 class="alert-heading">提示</h5><hr /><p class="card-text">提取码错误或文件失效！</p></div></div></div>';
			}
		} elseif (isset($_GET["download"])) { // 解析下载地址页面
			if (IsCheckPassword and (!isset($_SESSION["Password"]) or $_SESSION["Password"] != Password)) {
				dl_error("密码错误", "密码错误或超时，请返回首页重新验证密码。"); // 密码错误
			} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if (isset($_POST["fs_id"]) && isset($_POST["time"]) && isset($_POST["sign"]) && isset($_POST["randsk"]) && isset($_POST["share_id"]) && isset($_POST["uk"]) && isset($_POST["bdstoken"]) && isset($_POST["filesize"])) {
					$fs_id = $_POST["fs_id"];
					$timestamp = $_POST["time"];
					$sign = $_POST["sign"];
					$randsk = $_POST["randsk"];
					$share_id = $_POST["share_id"];
					$uk = $_POST["uk"];
					$bdstoken=$_POST["bdstoken"];
					$filesize=$_POST["filesize"];
					$nouarealLink="";//重置
					if((int)$filesize<=52428800){
					    $json5 = getDlink($fs_id, $timestamp, $sign, $randsk, $share_id, $uk ,$bdstoken,true);
					    if ($json5["errno"] == 0) {
					        $nouadlink = $json5["list"][0]["dlink"];
					        //开始获取真实链接
					    	$headerArray = array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36', 'Cookie: BDUSS=' . BDUSS . ';');
					    	$getRealLink = head($nouadlink, $headerArray); // 禁止重定向
					    	$getRealLink = strstr($getRealLink, "Location");
					    	$getRealLink = substr($getRealLink, 10);
					    	$nouarealLink = getSubstr($getRealLink, "http://", "\r\n"); // 删除 http://
					    }
					}
					$json4 = getDlink($fs_id, $timestamp, $sign, $randsk, $share_id, $uk ,$bdstoken,false);
					
					if ($json4["errno"] == 0) {
						$dlink = $json4["list"][0]["dlink"];
						//获取文件相关信息
						$md5 = $json4["list"][0]["md5"];
						$filename = $json4["list"][0]["server_filename"];
						$size = $json4["list"][0]["size"];
						$server_ctime = (int)$json4["list"][0]["server_ctime"] + 28800; // 服务器创建时间 +8:00
						//开始获取真实链接
						$headerArray = array('User-Agent: LogStatistic', 'Cookie: BDUSS=' . BDUSS . ';');
						$getRealLink = head($dlink, $headerArray); // 禁止重定向
						$getRealLink = strstr($getRealLink, "Location");
						$getRealLink = substr($getRealLink, 10);
						$realLink = getSubstr($getRealLink, "http://", "\r\n"); // 删除 http://
						
						// 1. 使用 dlink 下载文件   2. dlink 有效期为8小时   3. 必需要设置 User-Agent 字段   4. dlink 存在 HTTP 302 跳转
						if ($realLink == "") echo '<div class="row justify-content-center"><div class="col-md-7 col-sm-8 col-11"><div class="alert alert-danger" role="alert">
							<h5 class="alert-heading">获取下载链接失败</h5><hr /><p class="card-text">已获取到文件，但未能获取到下载链接！</p><p class="card-text">请检查你是否在 <code>config.php</code> 中配置 SVIP 账号的 BDUSS 和 STOKEN！</p>
							<p class="card-text">未配置或配置了普通账号的均会导致失败！必须要 SVIP 账号！</p>' . FileInfo($filename, $size, $md5, $server_ctime) . '</div></div></div>'; // 未配置 SVIP 账号
						else {
			?>
							<div class="row justify-content-center">
								<div class="col-md-7 col-sm-8 col-11">
									<div class="alert alert-primary" role="alert">
										<h5 class="alert-heading">获取下载链接成功</h5>
										<hr /><?php echo FileInfo($filename, $size, $md5, $server_ctime); ?>
										<?php 
										if($nouarealLink!=""){
											echo '<hr />';
											$type=substr($filename,-4);
											
											if ($type==".jpg" || $type==".png" || $type=="jpeg" || $type==".bmp"){
										    	echo '<img src="https://'.$nouarealLink.'" class="img-fluid rounded">';
										    }elseif($type ==".pdf" || $type =="docx" || $type ==".doc" || $type =="xlsx" || $type ==".xls" || $type =="pptx" || $type ==".ppt" || $type ==".csv" || $type ==".xml" || $type ==".rtf"){
										    	echo '<p class="card-text"><a href="http://view.xdocin.com/xdoc?_xdoc='.urlencode('https://'.$nouarealLink).'" target="_blank">进入在线预览</a></p>';
										    }elseif($type=".mp4"){
										    	echo '<video src="https://'.$nouarealLink.'" controls="controls"></video>';
										    }elseif($type=".mp3"){
										    	echo '<audio src="https://'.$nouarealLink.'" controls="controls"></audio>';
										    }
										    
										    echo '
										    <p class="card-text">
											
											<a href="https://'. $nouarealLink.'" target="_blank" rel="nofollow noopener noreferrer">直链（无需设置UA）</a>
										</p>';
										}
										
										?>
										
										
										<hr />
										<p class="card-text">
											<a id="http" href="http://<?php echo $realLink; ?>" target="_blank" rel="nofollow noopener noreferrer">下载链接（不安全）</a>
											<a id="https" href="https://<?php echo $realLink; ?>" target="_blank" rel="nofollow noopener noreferrer">下载链接（需设置UA，8小时有效）</a>
										</p>
										<p class="card-text">
											<a href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal">推送到Aria2</a>
										</p>
										<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
											<div class="modal-dialog" role="document">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title" id="exampleModalLabel">Send to aria2</h5>
														<button type="button" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
														</button>
													</div>
													<div class="modal-body">
														<div class="form-group">
															<p><label class="control-label">Json-RPC Url</label>
																<input name="url" id="url" class="form-control" placeholder="http://127.0.0.1:6800/jsonrpc"></p>
														</div>
														<div class="form-group">
															<p><label class="control-label">Token</label>
																<input name="token" id="token" class="form-control" placeholder="If none keep empty"></p>
														</div>
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-primary" onclick="addUri()" data-dismiss="modal">Send</button>
														<button type="button" class="btn btn-success" onclick="checkVer()">Check Version</button>
														<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
													</div>
												</div>
											</div>
											<script>
												$(function() {
													if (getCookie('aria2url') != null) {
														$('#url').attr('value', atou(getCookie('aria2url')))
														if (getCookie('aria2token') != null) {
															$('#token').attr('value', atou(getCookie('aria2token')))
														}
													}
												})
											</script>
										</div>
										<p class="card-text"><a href="?help" target="_blank">下载链接使用方法（必读）</a></p>
									</div>
								</div>
							</div>
			<?php }
						// 成功！
					} elseif ($json4["errno"] == 112) dl_error("链接超时", "获取链接超时，每次解析列表后只有5min有效时间，请返回首页重新解析。"); // 链接超时
					else {
					    dl_error("获取下载链接失败", "未知错误！"); // 未知错误
					    }
				} else dl_error("参数有误", "POST 传参出现问题！请不要自行构建表单提交！"); // 参数不齐
			} else dl_error("方法错误", "请不要直接访问此页面或使用 GET 方式访问！"); // 方法错误
		} else { // 首页 
			?>
			<div class="col-lg-6 col-md-9 mx-auto mb-5 input-card">
				<div class="card">
					<div class="card-header bg-dark text-light">百度网盘在线解析 <a class="badge badge-info" href="https://github.com/yuantuo666/baiduwp-php">V<?php echo programVersion; ?></a></div>
					<div class="card-body">
						<form name="form1" method="post" onsubmit="return validateForm()">
							<div class="form-group my-2"><input type="text" class="form-control" name="surl" placeholder="请输入分享链接(完整也可)"></div>
							<div class="form-group my-4"><input type="text" class="form-control" name="pwd" placeholder="请输入提取码(没有留空)"></div>
							<?php
							if (IsCheckPassword) {
								$return = '<div class="form-group my-4"><input type="text" class="form-control" name="Password" placeholder="请输入密码"></div>';
								if (isset($_SESSION["Password"])) {
									if ($_SESSION["Password"] === Password) {
										$return = '<div class="form-group my-4">您的设备在短期内已经验证过，无需再次输入密码。</div>';
									}
								}
								echo $return;
							} // 密码
							?>
							<button type="submit" class="mt-4 mb-3 form-control btn btn-success btn-block">打开</button>
						</form>
					</div>
				</div>
			</div>
		<?php } 
		echo Footer;?>
	</div>
</body>

</html>