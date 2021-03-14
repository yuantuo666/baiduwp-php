<?php

/**
 * PanDownload 网页复刻版，PHP 语言版Settings文件
 *
 * 设置及后台功能
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
$programVersion_Settings = '2.1.3';
session_start();
define('init', true);
if (version_compare(PHP_VERSION, '7.0.0', '<')) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://www.php.net/downloads.php');
	die("HTTP 503 服务不可用！\r\nPHP 版本过低！无法正常运行程序！\r\n请安装 7.0.0 或以上版本的 PHP！\r\n将在五秒内跳转到 PHP 官方下载页面！");
}
if (!file_exists('functions.php')) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://github.com/yuantuo666/baiduwp-php');
	die("HTTP 503 服务不可用！\r\n缺少相关配置和定义文件！无法正常运行程序！\r\n请重新 Clone 项目并配置！\r\n将在五秒内跳转到 GitHub 储存库！");
}
// 导入配置和函数
if (!file_exists('config.php')) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=install.php');
	die("HTTP 503 服务不可用！\r\n暂未安装此程序！\r\n将在五秒内跳转到安装程序！");
} else {
	require('config.php');
	if ($programVersion_Settings !== programVersion) {
		http_response_code(503);
		header('Content-Type: text/plain; charset=utf-8');
		header('Refresh: 5;url=install.php');
		die("HTTP 503 服务不可用！\r\n配置文件版本异常！\r\n将在五秒内跳转到安装程序！\r\n若重新安装无法解决问题，请重新 Clone 项目并配置！");
	}
}
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
$method = (!empty($_GET["m"])) ? $_GET["m"] : "";
$is_login = (empty($_SESSION["admin_login"])) ? false : $_SESSION["admin_login"];
if (!$is_login and !empty($_POST["setting_password"])) {
	// 开始验证密码
	if ($_POST["setting_password"] === ADMIN_PASSWORD) {
		// 密码正确
		$_SESSION["admin_login"] = true;
		$is_login = true;
	} else {
		// 密码错误
		$_SESSION["admin_login"] = false;
		$PasswordError = true;
	}
}
if ($is_login) connectdb();
?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="referrer" content="same-origin" />
	<meta name="author" content="Yuan_Tuo" />
	<meta name="version" content="<?php echo programVersion; ?>" />
	<title><?php echo Sitename; ?> - Settings</title>
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
	<script src="https://cdn.jsdelivr.net/npm/jquery-form@4.3.0/dist/jquery.form.min.js"></script>
	<script src="static/color.js"></script>
</head>

<body>
	<div class="container">
		<div class="row justify-content-center">
			<?php


			if (!$is_login) { ?>
				<!-- 登录 -->
				<div class="col-lg-6 col-md-9 mx-auto mb-5 input-card">
					<div class="card">
						<div class="card-header bg-dark text-light">Pandownload复刻版 - 后台登录</div>
						<div class="card-body">
							<form id="form1" method="post">
								<div class="form-group my-2">
									<input type="text" class="form-control" name="setting_password" placeholder="Password">
									<small class="form-text text-right">忘记密码可进入<b>config.php</b>中查看~</small>
								</div>
								<button onclick="Sumbitform()" class="mt-4 mb-3 form-control btn btn-success btn-block">登录</button>
							</form>
							<script>
								<?php if (isset($PasswordError) and $PasswordError) echo "Swal.fire('管理员密码错误','如果忘记管理员密码请进入 config.php 查看','error');"; ?>

								function Sumbitform() {
									Swal.fire("正在登录，请稍等");
									Swal.showLoading();
									$("#form1").submit();
								}
							</script>
						</div>
					</div>
				</div>
			<?php } else {
				// 登录后操作
			?>
				<div class="col-md-12 col-sm-12 col-12">
					<?php if ($method == "analyse") { ?>
						<nav>
							<ol class="breadcrumb my-4">
								<li class="breadcrumb-item"><a href="index.php">baiduwp-php</a></li>
								<li class="breadcrumb-item"><a href="settings.php">后台管理</a></li>
								<li class="breadcrumb-item">数据分析</li>
							</ol>
						</nav>
						<div class="card">
							<div class="card-header">
								数据分析
							</div>
							<div class="card-body">
								<h5 class="card-title">最近解析</h5>
								<div style="overflow:scroll;">
									<table id="AnalyseTable" class="table table-striped" style="min-width:1400px;" page=1>
										<thead>
											<tr>
												<th scope="col">#</th>
												<th scope="col">操作</th>
												<th scope="col">用户ip</th>
												<th scope="col">文件名</th>
												<th scope="col">文件大小</th>
												<!-- <th scope="col">文件效验码</th> -->
												<th scope="col">文件路径</th>
												<!-- <th scope="col">文件创建时间</th> -->
												<th scope="col">文件下载地址</th>
												<th scope="col">解析时间</th>
												<th scope="col">解析账号</th>
												<!-- 新增解析账号：ALTER TABLE `bdwp` ADD `paccount` INT NOT NULL COMMENT '解析账号id' AFTER `ptime`; -->
											</tr>
										</thead>
										<tbody>
											<?php
											echo GetAnalyseTablePage(1);
											?>

										</tbody>
									</table>
								</div>
								<br />
								<a href="javascript:AnalyseLoadmore();" class="btn btn-primary">加载更多</a>
								<script>
									function AnalyseLoadmore() {
										Swal.fire("正在加载，请稍等");
										Swal.showLoading();
										newpage = Number($("#AnalyseTable").attr("page")) + 1;
										$.get(`api.php?m=ADMINAPI&act=AnalyseGetTable&page=${newpage}`, function(data, status) {
											if (status == "success") {
												$("#AnalyseTable").append(data);
												$("#AnalyseTable").attr("page", newpage);
												Swal.close();
											} else {
												Swal.fire("请求错误，请检查网络是否正常");
											}
										});
									}
								</script>
							</div>
						</div>
					<?php } elseif ($method == "svip") { ?>
						<nav>
							<ol class="breadcrumb my-4">
								<li class="breadcrumb-item"><a href="index.php">baiduwp-php</a></li>
								<li class="breadcrumb-item"><a href="settings.php">后台管理</a></li>
								<li class="breadcrumb-item">会员账号</li>
							</ol>
						</nav>
						<!-- SVIP -->
						<div class="card">
							<div class="card-header">
								SVIP账号管理
							</div>
							<div class="card-body">
								<h5 class="card-title">默认账号</h5>
								<?php
								$sql = "SELECT * FROM `" . $dbtable . "_svip` WHERE `state`!=-1 ORDER BY `is_using` DESC LIMIT 0,1"; // 时间倒序输出第一项未被限速账号
								$Result = mysqli_query($conn, $sql);

								if ($Result =  mysqli_fetch_assoc($Result)) {
									$id = $Result["id"];
									$name = $Result["name"];
									$add_time = $Result["add_time"];
									$is_using = $Result["is_using"];
									$state = ($Result["state"] == -1) ? "限速" : "正常";

									$sql = "SELECT count(`id`) as AllCount,sum(`size`) as AllSize FROM `$dbtable` WHERE `paccount`=$id"; // 时间倒序输出第一项未被限速账号
									$Result = mysqli_query($conn, $sql);

									if ($Result = mysqli_fetch_assoc($Result)) {
										$AllCount = $Result["AllCount"];
										$AllSize = ($AllCount == "0") ? "无数据" : formatSize((float)$Result["AllSize"]); // 格式化获取到的文件大小
										$ParseCountMsg =  "累计解析次数：$AllCount 个<br />累计解析大小：$AllSize";
									}

									echo "<p>ID：$id<br />";
									echo "名称：$name<br />";
									echo "启用时间：$add_time<br />";
									echo "是否使用(最新时间将被用于解析)：$is_using<br />";
									echo "状态：$state<br />";
									echo "$ParseCountMsg</p>";
								} else echo "<p>Error!当前没有可用账户，正使用本地解析。</p>";
								?>
								<br>
								<h5 class="card-title">所有账号</h5>
								<div style="overflow:scroll;">
									<table id="SvipTable" class="table table-striped" style="min-width:1400px;" page=1>
										<thead>
											<tr>
												<th scope="col">#</th>
												<th scope="col">操作</th>
												<th scope="col">是否正在使用</th>
												<th scope="col">账号名称</th>
												<th scope="col">会员状态</th>
												<th scope="col">添加时间</th>
												<th scope="col">会员bduss</th>
												<th scope="col">会员stoken</th>
										</thead>
										<tbody>
											<?php
											echo GetSvipTablePage(1);
											?>

										</tbody>
									</table>
								</div>
								<br />
								<a href="javascript:SvipLoadmore();" class="btn btn-primary">加载更多</a>
								<script>
									function SvipLoadmore() {
										Swal.fire("正在加载，请稍等");
										Swal.showLoading();
										newpage = Number($("#SvipTable").attr("page")) + 1;
										$.get(`api.php?m=ADMINAPI&act=SvipGetTable&page=${newpage}`, function(data, status) {
											if (status == "success") {
												$("#SvipTable").append(data);
												$("#SvipTable").attr("page", newpage);
												Swal.close();
											} else {
												Swal.fire("请求错误，请检查网络是否正常");
											}
										});
									}

									function SettingFirstAccount(id) {
										Swal.fire("正在设置，请稍等");
										Swal.showLoading();
										$.get(`api.php?m=ADMINAPI&act=SvipSettingFirstAccount&id=${id}`, function(data, status) {
											if (status == "success") {
												var json = JSON.parse(data);
												Swal.fire(json.msg);
												if (json.refresh == true) setTimeout("location.reload();", 3000);
											} else {
												Swal.fire("请求错误，请检查网络是否正常");
											}
										});
									}

									function SettingNormalAccount(id) {
										Swal.fire("正在设置，请稍等");
										Swal.showLoading();
										$.get(`api.php?m=ADMINAPI&act=SvipSettingNormalAccount&id=${id}`, function(data, status) {
											if (status == "success") {
												var json = JSON.parse(data);
												Swal.fire(json.msg);
												if (json.refresh == true) setTimeout("location.reload();", 3000);
											} else {
												Swal.fire("请求错误，请检查网络是否正常");
											}
										});
									}
								</script>
								<br><br><br>
								<!-- 新增会员数据 -->
								<h5 class="card-title">新增会员数据</h5>
								<form class="ajaxform" action="api.php?m=ADMINAPI&act=singleBDUSS">
									<div class="form-group">
										<label>账号名称</label>
										<input type="text" class="form-control form-control-sm" name="name">
									</div>
									<div class="form-group">
										<label>BDUSS</label>
										<input type="text" class="form-control form-control-sm" name="BDUSS">
									</div>
									<div class="form-group">
										<label>STOKEN</label>
										<input type="text" class="form-control form-control-sm" name="STOKEN">
									</div>
									<button type="submit" class="btn btn-primary">提交</button>
								</form>
								<!-- 新增会员数据 -->
								<br><br>
								<h5 class="card-title">批量导入svip</h5>
								<form class="ajaxform" action="api.php?m=ADMINAPI&act=multiBDUSS">
									<div class="form-group">
										<label>账号名称</label>
										<input type="text" class="form-control form-control-sm" name="name">
									</div>
									<div class="form-group">
										<label>MULTI BDUSS（每行一个）</label>
										<textarea type="text" class="form-control form-control-sm" name="MULTI_BDUSS" style="height: 200px;"></textarea>
									</div>
									<button type="submit" class="btn btn-primary">提交</button>
								</form>
							</div>
						</div>
					<?php } elseif ($method == "iplist") { ?>
						<nav>
							<ol class="breadcrumb my-4">
								<li class="breadcrumb-item"><a href="index.php">baiduwp-php</a></li>
								<li class="breadcrumb-item"><a href="settings.php">后台管理</a></li>
								<li class="breadcrumb-item">IP黑/白名单</li>
							</ol>
						</nav>
						<!-- IP地址 -->
						<div class="card">
							<div class="card-header">
								黑/白名单管理
							</div>
							<div class="card-body">
								<h5 class="card-title">所有IP</h5>
								<div style="overflow:scroll;">
									<table id="IPTable" class="table table-striped" style="min-width:600px;" page=1>
										<thead>
											<tr>
												<th scope="col">#</th>
												<th scope="col">操作</th>
												<th scope="col">IP</th>
												<th scope="col">账号状态</th>
												<th scope="col">备注</th>
												<th scope="col">添加时间</th>
										</thead>
										<tbody>
											<?php
											echo GetIPTablePage(1);
											?>

										</tbody>
									</table>
								</div>
								<br />
								<a href="javascript:IPLoadmore();" class="btn btn-primary">加载更多</a>
								<script>
									function IPLoadmore() {
										Swal.fire("正在加载，请稍等");
										Swal.showLoading();
										newpage = Number($("#IPTable").attr("page")) + 1;
										$.get(`api.php?m=ADMINAPI&act=IPGetTable&page=${newpage}`, function(data, status) {
											if (status == "success") {
												$("#IPTable").append(data);
												$("#IPTable").attr("page", newpage);
												Swal.close();
											} else {
												Swal.fire("请求错误，请检查网络是否正常");
											}
										});
									}
								</script>
								<br><br><br>
								<!-- 新增IP -->
								<h5 class="card-title">新增IP</h5>
								<form class="ajaxform" action="api.php?m=ADMINAPI&act=NewIp">
									<div class="form-group">
										<label>IP地址</label>
										<input type="text" class="form-control form-control-sm" name="ip">
									</div>
									<div class="form-group">
										<label>备注</label>
										<input type="text" class="form-control form-control-sm" name="remark">
									</div>
									<div class="form-group">
										<label>账号种类</label>
										<select class="form-control form-control-sm" name="type">
											<option value="0">白名单</option>
											<option value="-1">黑名单</option>
										</select>
									</div>
									<button type="submit" class="btn btn-primary">提交</button>
								</form>
							</div>
						</div>
					<?php } elseif ($method == "DownloadTimes") { ?>
						<nav>
							<ol class="breadcrumb my-4">
								<li class="breadcrumb-item"><a href="index.php">baiduwp-php</a></li>
								<li class="breadcrumb-item"><a href="settings.php">后台管理</a></li>
								<li class="breadcrumb-item">下载次数限制修改</li>
							</ol>
						</nav>
						<!-- 下载次数限制修改 -->
						<div class="card">
							<div class="card-header">
								下载次数限制修改
							</div>
							<div class="card-body">
								<h5 class="card-title">下载次数限制修改</h5>
								<?php
								echo "当前设置的下载次数：" . DownloadTimes;
								?>
								<br>
								注意！此功能需要修改config.php的信息，请小心使用。
								<br><br><br>
								<!-- 修改下载次数 -->
								<h5 class="card-title">修改下载次数</h5>
								<form class="ajaxform" action="api.php?m=ADMINAPI&act=setDownloadTimes">
									<div class="form-group">
										<label>下载次数</label>
										<input type="text" class="form-control form-control-sm" name="DownloadTimes">
									</div>
									<button type="submit" class="btn btn-primary">提交</button>
								</form>

							</div>
						</div>
					<?php } elseif ($method == "SVIPSwitchMod") { ?>
						<nav>
							<ol class="breadcrumb my-4">
								<li class="breadcrumb-item"><a href="index.php">baiduwp-php</a></li>
								<li class="breadcrumb-item"><a href="settings.php">后台管理</a></li>
								<li class="breadcrumb-item">会员账号切换模式</li>
							</ol>
						</nav>
						<!-- 会员账号切换模式 -->
						<div class="card">
							<div class="card-header">
								会员账号切换模式
							</div>
							<div class="card-body">
								<h5 class="card-title">会员账号切换模式</h5>
								<?php
								switch (SVIPSwitchMod) {
									case '0':
										$Mod = "本地模式";
										break;
									case '1':
										$Mod = "顺序模式";
										break;
									case '2':
										$Mod = "轮换模式";
										break;
									case '3':
										$Mod = "手动模式";
										break;
									default:
										$Mod = "未知模式";
										break;
								}
								echo "当前设置的会员账号切换模式：$Mod <br />";
								?>
								本地模式：不管是否限速，一直使用本地账号解析。<br />
								顺序模式：一直使用设置的账号解析，用到会员账号失效切换下一账号；当数据库中会员账号失效后，会使用本地账号解析。<br />
								轮换模式：解析一次就切换一次账号，只使用会员账号；当数据库中会员账号失效后，会使用本地账号解析。<br />
								手动模式：不管是否限速，一直使用数据库中设置的账号。
								<br>
								注意！此功能需要修改config.php的信息，请小心使用。
								<br><br>
								<!-- 修改会员账号切换模式 -->
								<h5 class="card-title">修改会员账号切换模式</h5>
								<form class="ajaxform" action="api.php?m=ADMINAPI&act=setSVIPSwitchMod">
									<div class="form-group">
										<label>会员账号切换模式</label>
										<select class="form-control" id="SVIPSwitchMod" name="SVIPSwitchMod">
											<option value="0" <?php if (SVIPSwitchMod == "0") echo "selected=\"selected\""; ?>>本地模式</option>
											<option value="1" <?php if (SVIPSwitchMod == "1") echo "selected=\"selected\""; ?>>顺序模式</option>
											<option value="2" <?php if (SVIPSwitchMod == "2") echo "selected=\"selected\""; ?>>轮换模式</option>
											<option value="3" <?php if (SVIPSwitchMod == "3") echo "selected=\"selected\""; ?>>手动模式</option>
										</select>
									</div>
									<button type="submit" class="btn btn-primary">提交</button>
								</form>

							</div>
						</div>
					<?php } else { ?>
						<nav>
							<ol class="breadcrumb my-4">
								<li class="breadcrumb-item"><a href="index.php">baiduwp-php</a></li>
								<li class="breadcrumb-item"><a href="settings.php">后台管理</a></li>
								<li class="breadcrumb-item">概览</li>
							</ol>
						</nav>
						<!-- 概览 -->
						<div class="card">
							<div class="card-header">
								概览
							</div>
							<div class="card-body">
								<div class="row">

									<div class="col-md-6 col-sm-12">
										<h5 class="card-title">使用统计</h5>
										<p class="card-text">
											<?php
											$sql = "SELECT count(`id`) as AllCount,sum(`size`) as AllSize FROM `$dbtable`";
											$Result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
											// 存在数据
											$AllCount = $Result["AllCount"];
											$AllSize = formatSize((float)$Result["AllSize"]); // 格式化获取到的文件大小
											echo "累计解析 $AllCount 个，共 $AllSize";
											?>
											<br />
											<?php
											$sql = "SELECT count(`id`) as AllCount,sum(`size`) as AllSize FROM `$dbtable` WHERE date(`ptime`)=date(now());"; // 获取今天的解析量
											$Result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
											// 存在数据
											$AllCount = $Result["AllCount"];
											$AllSize = formatSize((float)$Result["AllSize"]); // 格式化获取到的文件大小
											echo "今日解析 $AllCount 个，共 $AllSize";
											?>
										</p>
										<a href="?m=analyse" class="btn btn-primary">查看详细情况</a>
										<br><br>
									</div>
									<div class="col-md-6 col-sm-12">
										<h5 class="card-title">SVIP账号</h5>
										<p class="card-text">
											<?php
											$sql = "SELECT count(`id`) as AllCount FROM `" . $dbtable . "_svip`";
											$Result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
											$AllCount = $Result["AllCount"];
											$SvipCountMsg =  "数据库中共 $AllCount 个账号";
											$sql = "SELECT count(`id`) as AllCount FROM `" . $dbtable . "_svip` WHERE `state`=-1";
											$Result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
											$AllCount = $Result["AllCount"];
											$SvipFailCountMsg = "有 $AllCount 个账号已被限速";
											echo $SvipCountMsg . "<br />" . $SvipFailCountMsg;
											?>
										</p>
										<a href="?m=svip" class="btn btn-primary">查看详细情况</a>
										<br><br>
									</div>
								</div>
								<div class="row">

									<div class="col-md-6 col-sm-12">
										<h5 class="card-title">黑/白名单</h5>
										<p class="card-text">
											<?php
											$sql = "SELECT count(`id`) as AllCount FROM `" . $dbtable . "_ip`";
											$Result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
											$AllCount = $Result["AllCount"];
											$SvipCountMsg =  "数据库中共 $AllCount 个ip";
											if ($AllCount != 0) {
												$sql = "SELECT count(`id`) as AllCount FROM `" . $dbtable . "_ip` WHERE `type`=-1";
												$Result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
												$AllCount = $Result["AllCount"];
												$SvipFailCountMsg = "有 $AllCount 个黑名单";
												$sql = "SELECT count(`id`) as AllCount FROM `" . $dbtable . "_ip` WHERE `type`=0";
												$Result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
												$AllCount = $Result["AllCount"];
												$SvipSuccCountMsg = "有 $AllCount 个白名单";
											} else $SvipFailCountMsg = $SvipSuccCountMsg = "";
											echo $SvipCountMsg . "<br />" . $SvipFailCountMsg . "<br />" . $SvipSuccCountMsg;
											?>
										</p>
										<a href="?m=iplist" class="btn btn-primary">查看详细情况</a>
										<br><br>
									</div>
									<div class="col-md-6 col-sm-12">
										<h5 class="card-title">下载次数限制修改</h5>
										<p class="card-text">
											<?php
											echo "当前设置的下载次数：" . DownloadTimes;
											?>
										</p>
										<a href="?m=DownloadTimes" class="btn btn-primary">查看详细情况</a>
										<br><br>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6 col-sm-12">
										<h5 class="card-title">会员账号切换模式</h5>
										<p class="card-text">
											<?php
											switch (SVIPSwitchMod) {
												case '0':
													$Mod = "本地模式";
													break;
												case '1':
													$Mod = "顺序模式";
													break;
												case '2':
													$Mod = "轮换模式";
													break;
												case '3':
													$Mod = "手动模式";
													break;
												default:
													$Mod = "未知模式";
													break;
											}
											echo "当前设置的会员账号切换模式：$Mod";
											?>
										</p>
										<a href="?m=SVIPSwitchMod" class="btn btn-primary">查看详细情况</a>
										<br><br>
									</div>
								</div>

							</div>



						</div>
				</div>
			<?php } ?>



		</div>
	<?php
			} ?>
	</div>
	<script>
		function DeleteById(Type, Id) {
			Swal.fire("正在提交删除请求，请稍等");
			Swal.showLoading();
			$.get(`api.php?m=ADMINAPI&act=DeleteById&id=${Id}&type=${Type}`, function(data, status) {
				if (status == "success") {
					var json = JSON.parse(data);
					Swal.fire(json.msg);
					if (json.refresh == true) setTimeout("location.reload();", 3000);
				} else {
					Swal.fire("请求错误，请检查网络是否正常");
				}
			});

		}
		$('form.ajaxform').ajaxForm({
			type: 'post',
			dataType: 'json',
			beforeSubmit: function() {
				Swal.fire("正在提交数据，请稍等");
				Swal.showLoading();
			},
			success: function(data, success, xhr, $form) {
				if (data.error === 0) {
					Swal.fire(`${data.msg}`, data.detail, 'success');
					if (data.refresh == true) setTimeout("location.reload();", 3000);
				} else {
					Swal.fire(`${data.msg}`, data.detail, 'error');
				}
			},
			error: function(xhr, error, text, $form) {
				console.log(xhr);
				console.log(`${error}: ${text}`);
				console.log($form);
				Swal.fire('添加失败', '发生错误，添加失败！<br />详细信息请见控制台！', 'error');
			}
		});
	</script>
</body>

</html>