<?php

/**
 * PanDownload 网页复刻版，PHP 语言版安装程序
 *
 * 功能描述：安装PanDownload 网页复刻版
 *
 * 此项目 GitHub 地址：https://github.com/yuantuo666/baiduwp-php
 *
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
define('init', true);
if (version_compare(PHP_VERSION, '7.0.0', '<')) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://www.php.net/downloads.php');
	die("HTTP 503 服务不可用！\r\nPHP 版本过低！无法正常运行程序！\r\n请安装 7.0.0 或以上版本的 PHP！\r\n将在五秒内跳转到 PHP 官方下载页面！");
}
if (!(file_exists('functions.php') && file_exists('language.php'))) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://github.com/yuantuo666/baiduwp-php');
	die("HTTP 503 服务不可用！\r\n缺少相关配置和定义文件！无法正常运行程序！\r\n请重新 Clone 项目并进入此页面安装！\r\n将在五秒内跳转到 GitHub 储存库！");
}
if (file_exists('config.php')) {
	// 如果已经安装过一次，必须管理员登录
	session_start();
	$is_login = (empty($_SESSION["admin_login"])) ? false : $_SESSION["admin_login"];
	if (!$is_login and !empty($_POST["setting_password"])) {
		require_once('config.php');
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
}
// 通用响应头
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge,chrome=1');
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="referrer" content="same-origin" />
	<meta name="author" content="Yuan_Tuo" />
	<meta name="author" content="LC" />
	<title>PanDownload 复刻版 - 安装程序</title>
	<link rel="icon" href="favicon.ico" />
	<link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/4.1.2/css/bootstrap.min.css" />
	<link rel="stylesheet" disabled id="ColorMode-Dark" href="https://cdn.jsdelivr.net/gh/vinorodrigues/bootstrap-dark@0.0.9/dist/bootstrap-nightfall.css" />
	<link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/5.8.1/css/all.min.css" />
	<link rel="stylesheet" disabled id="Swal2-Dark" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4.0.2/dark.min.css" />
	<link rel="stylesheet" disabled id="Swal2-Light" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-default@4.0.2/default.min.css" />
	<script src="https://cdn.staticfile.org/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://cdn.staticfile.org/popper.js/1.12.5/umd/popper.min.js"></script>
	<script src="https://cdn.staticfile.org/twitter-bootstrap/4.1.2/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.14.0/dist/sweetalert2.min.js"></script>
	<script src="static/color.js"></script>
</head>

<body>
	<div class="container">
		<nav>
			<ol class="breadcrumb my-4">
				<li class="breadcrumb-item"><a href="./">baiduwp-php</a></li>
				<li class="breadcrumb-item"><a href="install.php">安装程序</a></li>
				<li class="breadcrumb-item">设置页面</li>
			</ol>
		</nav>

		<?php
		if (file_exists('config.php') and !$is_login) { ?>
			<!-- 登录 -->
			<div class="col-lg-6 col-md-9 mx-auto mb-5 input-card">
				<div class="card">
					<div class="card-header bg-dark text-light">Pandownload复刻版 - 管理员登录</div>
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
		<?php } elseif (!isset($_POST["Sitename"])) {
			// 如果已经安装过一次，读取相关基本设置
			if (file_exists('config.php')) {
				require_once('config.php');
				echo "<script>Swal.fire('提示','检测到你已安装过本程序<br />现已自动填入config.php中设置的数据','info');</script>";
			}
			function getConfig(&$var, string $name, $default = '')
			{
				$var = defined($name) ? constant($name) : $default;
			}
			getConfig($Sitename, 'Sitename');
			getConfig($IsCheckPassword, 'IsCheckPassword', true);
			getConfig($Password, 'Password');
			getConfig($ADMIN_PASSWORD, 'ADMIN_PASSWORD');
			getConfig($DownloadTimes, 'DownloadTimes', '5');
			getConfig($DownloadLinkAvailableTime, 'DownloadLinkAvailableTime', '8');
			getConfig($IsConfirmDownload, 'IsConfirmDownload', true);
			getConfig($Footer, 'Footer');

			getConfig($BDUSS, 'BDUSS');
			getConfig($STOKEN, 'STOKEN');
			getConfig($SVIP_BDUSS, 'SVIP_BDUSS');
			getConfig($SVIP_STOKEN, 'SVIP_STOKEN');
			getConfig($SVIPSwitchMod, 'SVIPSwitchMod', '0'); // 有bug隐患 如果未开启数据库，必须为0

			getConfig($USING_DB, 'USING_DB', true);
			if (defined('DbConfig')) {
				function getDbConfig(&$var, string $key)
				{
					$var = isset(DbConfig[$key]) ? DbConfig[$key] : '';
				}
				getDbConfig($servername, 'servername');
				getDbConfig($username, 'username');
				getDbConfig($DBPassword, 'DBPassword');
				getDbConfig($dbname, 'dbname');
				getDbConfig($dbtable, 'dbtable');
			} else {
				//未处理默认情况 #76
				$servername = "127.0.0.1";
				$username = "";
				$DBPassword = "";
				$dbname = "";
				$dbtable = "bdwp";
			}
		?>
			<!-- 设置页面 -->
			<div class="card">
				<div class="card-header">
					设置页面
				</div>
				<div class="card-body">
					<form action="install.php" method="post" id="SettingForm">
						<h5 class="card-title">站点设置</h5>
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">站点名称</label>
							<div class="col-sm-10">
								<input class="form-control" value="Pandownload 复刻版" name="Sitename" value="<?php echo $Sitename; ?>">
								<small class="form-text">设置你的站点名称，将在首页标题处显示。</small>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">是否开启解析密码</label>
							<div class="col-sm-10">
								<div class="form-check  form-check-inline">
									<input class="form-check-input" type="radio" name="IsCheckPassword" id="IsCheckPassword1" value="true" <?php if ($IsCheckPassword) echo "checked"; ?>>
									<label class="form-check-label" for="IsCheckPassword1">
										是
									</label>
								</div>
								<div class="form-check  form-check-inline">
									<input class="form-check-input" type="radio" name="IsCheckPassword" id="IsCheckPassword2" value="false" <?php if (!$IsCheckPassword) echo "checked"; ?>>
									<label class="form-check-label" for="IsCheckPassword2">
										否
									</label>
								</div>
								<small class="form-text">若开启，则在使用解析前必须输入设置的密码；若关闭（一般用于局域网搭建），则无需输入密码即可解析。</small>
							</div>
						</div>
						<div class="form-group row" id="Password" <?php if (!$IsCheckPassword) echo "style=\"display: none;\""; ?>>
							<label class="col-sm-2 col-form-label">解析密码设置</label>
							<div class="col-sm-10">
								<input class="form-control" name="Password" value="<?php echo $Password; ?>">
								<small class="form-text">在首页需要输入的密码，至少需要6位字符。</small>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">管理员密码设置</label>
							<div class="col-sm-10">
								<input class="form-control" name="ADMIN_PASSWORD" value="<?php echo $ADMIN_PASSWORD; ?>">
								<small class="form-text">用于登录管理后台(setting.php)的密码。</small>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">下载次数限制修改</label>
							<div class="col-sm-10">
								<input class="form-control" name="DownloadTimes" value="<?php echo $DownloadTimes; ?>">
								<small class="form-text">设置每一个IP的下载次数。（仅开启数据库有效）</small>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">下载链接有效时间</label>
							<div class="col-sm-10">
								<input class="form-control" name="DownloadLinkAvailableTime" value="<?php echo $DownloadLinkAvailableTime; ?>">
								<small class="form-text">设置解析出来的下载链接有效时间，超出对应时间则重新获取。（仅开启数据库有效，默认及最大为8小时，单位小时）</small>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">是否开启下载次数提示</label>
							<div class="col-sm-10">
								<div class="form-check  form-check-inline">
									<input class="form-check-input" type="radio" name="IsConfirmDownload" id="IsConfirmDownload1" value="true" <?php if ($IsConfirmDownload) echo "checked"; ?>>
									<label class="form-check-label" for="IsConfirmDownload1">
										是
									</label>
								</div>
								<div class="form-check  form-check-inline">
									<input class="form-check-input" type="radio" name="IsConfirmDownload" id="IsConfirmDownload2" value="false" <?php if (!$IsConfirmDownload) echo "checked"; ?>>
									<label class="form-check-label" for="IsConfirmDownload2">
										否
									</label>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">页脚设置</label>
							<div class="col-sm-10">
								<textarea class="form-control" name="Footer" rows="3"><?php echo $Footer; ?></textarea>
								<small class="form-text">通常用于设置隐藏的统计代码。</small>
							</div>
						</div>
						<hr />
						<h5 class="card-title">解析账号设置</h5>
						<p>需要在此设置账号的 cookie ，获取 cookie 方法见 <a href="https://pandownload.com/faq/cookie.html">PD官网</a></p>
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">普通账号BDUSS</label>
							<div class="col-sm-10">
								<input class="form-control" name="BDUSS" placeholder="例：liMlp3bFN1NWpVM**********PYjItRlJhNFNTSn5rNW5vQ0FrVzRYRTkyWHBiQVFBQUFBJCQAAAAAAAAAAA……" value="<?php echo $BDUSS; ?>">
								<small class="form-text">用来获取文件列表及信息，不需要SVIP也可。</small>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">普通账号STOKEN</label>
							<div class="col-sm-10">
								<input class="form-control" name="STOKEN" placeholder="例：0c27e6ebdb50252b**********a8b44f4ba448d0d62bc0527eead328d491a613" value="<?php echo $STOKEN; ?>">
								<small class="form-text">此信息必须与上一信息使用同一账号数据。用来获取文件列表及信息，不需要SVIP也可。</small>
							</div>
						</div>
						<br />
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">超级会员账号BDUSS</label>
							<div class="col-sm-10">
								<input class="form-control" name="SVIP_BDUSS" placeholder="例：W4tanVHelU2VGpxb**********0ZTZlUm1saEVtYnpTWjByfmxheWwxRFRtNlphQVFBQUFBJCQAAAAAAAAAAA……" value="<?php echo $SVIP_BDUSS; ?>">
								<small class="form-text">用来获取文件告诉下载地址，必须为SVIP账号，否则将获取到限速地址。</small>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">超级会员账号STOKEN</label>
							<div class="col-sm-10">
								<input class="form-control" name="SVIP_STOKEN" placeholder="例：0c27e6ebdb50252b**********a8b44f4ba448d0d62bc0527eead328d491a613" value="<?php echo $SVIP_STOKEN; ?>">
								<small class="form-text">此信息必须与上一信息使用同一账号数据。可以留空，仅为检测账号状态使用。</small>
							</div>
						</div>
						<hr />
						<h5 class="card-title">MySQL数据库设置</h5>
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">是否启用数据库</label>
							<div class="col-sm-10">
								<div class="form-check  form-check-inline">
									<input class="form-check-input" type="radio" name="USING_DB" id="USING_DB1" value="true" <?php if ($USING_DB) echo "checked"; ?>>
									<label class="form-check-label" for="USING_DB1">
										是
									</label>
								</div>
								<div class="form-check  form-check-inline">
									<input class="form-check-input" type="radio" name="USING_DB" id="USING_DB2" value="false" <?php if (!$USING_DB) echo "checked"; ?>>
									<label class="form-check-label" for="USING_DB2">
										否
									</label>
								</div>
								<small class="form-text">如需使用记录解析数据、设置黑\白名单、自动切换限速SVIP账号等功能，需开启数据库。</small>
							</div>
						</div>
						<div id="DbConfig" <?php if (!$USING_DB) echo "style=\"display: none;\""; ?>>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">数据库地址</label>
								<div class="col-sm-10">
									<input class="form-control" name="DbConfig_servername" value="<?php echo $servername; ?>">
									<small class="form-text">填入MySQL数据库的地址。</small>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">数据库用户名</label>
								<div class="col-sm-10">
									<input class="form-control" name="DbConfig_username" value="<?php echo $username; ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">数据库密码</label>
								<div class="col-sm-10">
									<input class="form-control" name="DbConfig_DBPassword" value="<?php echo $DBPassword; ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">数据库名</label>
								<div class="col-sm-10">
									<input class="form-control" name="DbConfig_dbname" value="<?php echo $dbname; ?>">
									<small class="form-text">如果此数据库不存在将会在检查连接时自动创建。</small>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">数据库表名前缀</label>
								<div class="col-sm-10">
									<input class="form-control" name="DbConfig_dbtable" value="<?php echo $dbtable; ?>">
									<small class="form-text">一般情况无需修改</small>
								</div>
							</div>
							<!-- 选择会员账号切换模式 -->
							<div class="form-group row">
								<label for="SVIPSwitchMod" class="col-sm-2 col-form-label">会员账号切换模式</label>
								<div class="col-sm-10">
									<select class="form-control" id="SVIPSwitchMod" name="SVIPSwitchMod">
										<option value="0" <?php if ($SVIPSwitchMod == "0") echo "selected=\"selected\""; ?>>本地模式</option>
										<option value="1" <?php if ($SVIPSwitchMod == "1") echo "selected=\"selected\""; ?>>顺序模式</option>
										<option value="2" <?php if ($SVIPSwitchMod == "2") echo "selected=\"selected\""; ?>>会员账号轮换模式</option>
										<option value="4" <?php if ($SVIPSwitchMod == "4") echo "selected=\"selected\""; ?>>所有账号轮换模式</option>
										<option value="3" <?php if ($SVIPSwitchMod == "3") echo "selected=\"selected\""; ?>>手动模式</option>
									</select>
									<small class="form-text">
										本地模式：不管是否限速，一直使用本地账号解析。<br />
										顺序模式：一直使用设置的账号解析，用到会员账号失效切换下一账号；当数据库中会员账号失效后，会使用本地账号解析。<br />
										会员账号轮换模式：解析一次就切换一次账号，只使用会员账号；当数据库中会员账号失效后，会使用本地账号解析。<br />
										所有账号轮换模式：解析一次就切换一次账号，无视是否限速。<br />
										手动模式：不管是否限速，一直使用数据库中设置的账号。
									</small>
								</div>
							</div>
							<!-- 提供不清空数据选项 -->
							<div class="form-group form-check">
								<input type="checkbox" class="form-check-input" id="ReserveDBData" name="ReserveDBData" value="true">
								<label class="form-check-label" for="ReserveDBData">保留以前的数据库数据</label>
								<small class="form-text">如果勾选此选项，将不会执行导入数据库操作，但请保证安装的新版本数据结构与旧版本一致，否则可能出现不可预料的错误。</small>
							</div>
							<a href="javascript:CheckMySQLConnect();" class="btn btn-primary">检查数据库连接</a>
						</div>
						<hr />
						<div class="form-group form-check">
							<input type="checkbox" class="form-check-input" id="AgreeCheck">
							<label class="form-check-label" for="AgreeCheck">
								<p class="text-danger">我同意在首页及其他页面<strong>保留作者版权信息</strong></p>
							</label>
						</div>
						<!-- 已经读取了配置，没必要确认 -->
						<a href="javascript:CheckForm();" class="btn btn-primary">提交</a>
						<small class="form-text">TIPS：1. 由于新版本可能更新了css和js文件，如果你的网站有缓存，请在清理后访问首页（一般CDN会提供此功能）；如果浏览器存在缓存，请按下Ctrl+F5强制刷新，或进入设置页面删除缓存，否则可能遇到无法使用的问题。</small>
						<small class="form-text">2. 你可以手动在当前目录下新建一个 notice.html 文件，当加载首页时会自动引用该文件。</small>
						<small class="form-text">3. 如果点击此页面任何按钮都没有反应，可能是相关的JavaScript文件加载失败，刷新页面即可。</small>
						<br><br>


						<script>
							async function postAPI(method, body) { // 获取 API 数据
								try {
									const response = await fetch(`api.php?m=${method}`, { // fetch API
										credentials: 'same-origin', // 发送验证信息 (cookies)
										method: 'POST',
										headers: {
											"Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
										},
										body: body,
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

							$("input[name='IsCheckPassword']").on('click', function() {
								item = $(this).val(); // 这里获取的是你点击的那个radio的值，而不是设置的值。（虽然效果是一样的
								if (item == "false") {
									$("div#Password").slideUp();
								} else {
									$("div#Password").slideDown();
								}
							});
							$("input[name='USING_DB']").on('click', function() {
								item = $(this).val();
								if (item == "false") {
									$("div#DbConfig").slideUp();
									$("select#SVIPSwitchMod").val("0");
								} else {
									$("div#DbConfig").slideDown();
								}
							});
							$("#AgreeCheck").on('click', function() {
								item = $(this).prop("checked");
								if (item == true) {
									// 提示
									Swal.fire({
										title: "同意保留版权",
										html: "保留版权对站长没有坏处，只是给那些想要学习 PHP 语言的人一个机会。<br/>保留原作者版权是MIT协议所规定的，这是对作者的一种尊重，让作者有继续开发的动力，而不是每天都在发邮件处理版权问题。<hr/>此项目在 GitHub 上开放源代码，所有历史版本及当前版本源代码均公开可查。",
										icon: "warning",
										showCancelButton: true,
										confirmButtonText: "我同意",
										footer: '<a href="https://github.com/yuantuo666/baiduwp-php" target="_blank">GitHub 上的源代码仓库</a>'
									}).then(function(e) {
										if (e.isConfirmed) {
											$("#AgreeCheck").prop("checked", true);
										} else {
											$("#AgreeCheck").prop("checked", false);
										}
									});
								}
							});
							$("#ReserveDBData").on('click', function() {
								item = $(this).prop("checked");
								if (item == true) {
									// 提示
									Swal.fire({
										title: "保留数据库数据",
										html: "如果勾选此选项，将不会执行导入数据库操作，但请保证安装的新版本数据结构与旧版本一致，否则可能出现不可预料的错误。",
										icon: "warning",
										showCancelButton: true,
										confirmButtonText: "确认数据库一致"
									}).then(function(e) {
										if (e.isConfirmed) {
											$("#ReserveDBData").prop("checked", true);
										} else {
											$("#ReserveDBData").prop("checked", false);
										}
									});
								}
							});

							var SQLConnect = false;

							function CheckMySQLConnect() {
								Swal.fire("正在连接数据库，请稍等");
								Swal.showLoading();
								servername = $("input[name='DbConfig_servername']").val();
								username = $("input[name='DbConfig_username']").val();
								DBPassword = $("input[name='DbConfig_DBPassword']").val();
								dbname = $("input[name='DbConfig_dbname']").val();
								dbtable = $("input[name='DbConfig_dbtable']").val();

								if(dbtable==""){
									Swal.fire("数据库表名前缀设置错误", "请检查你的数据库设置，数据库表名前缀不能为空！<br />你可以设置为bdwp或其他有效字符串。", "error");
									return;
								}

								body = `servername=${servername}&username=${username}&DBPassword=${DBPassword}&dbname=${dbname}&dbtable=${dbtable}`;

								postAPI('CheckMySQLConnect', body).then(function(response) {
									if (response.success) {
										const data = response.data;
										if (data.error == 0) {
											// 连接成功
											Swal.fire("数据库连接成功", "请完成其他信息填写并提交。<br />详细信息：" + data.msg, "success");
											$("input[name='DbConfig_servername']").attr("readonly", true); // 禁用修改，防止提交后出错
											$("input[name='DbConfig_username']").attr("readonly", true);
											$("input[name='DbConfig_DBPassword']").attr("readonly", true);
											$("input[name='DbConfig_dbname']").attr("readonly", true);
											SQLConnect = true;
										} else {
											// 连接失败
											Swal.fire("数据库连接错误", "请检查你的数据库设置，并重新提交。<br />详细信息：" + data.msg, "error");
										}
									}
								});

							}

							function CheckForm() {
								Swal.fire("正在安装，请稍等……");
								Swal.showLoading();
								USING_DB = $("input[name='USING_DB']:checked").val();
								ADMIN_PASSWORDLength = $("input[name='ADMIN_PASSWORD']").val().length;

								if (ADMIN_PASSWORDLength < 6) {
									// 密码过短
									Swal.fire("密码过短", "请检查你设置的密码，为保证站点安全，管理员密码必须为6位或6位以上。", "warning");
									return 0;
								}
								if (USING_DB == "true") {
									if (!SQLConnect) {
										// 暂未连接数据库
										Swal.fire("暂未连接数据库", "请先点击检查数据库连接按钮，再提交数据。", "warning");
										return 0;
									}
								}
								AgreeCheck = $("#AgreeCheck").prop("checked");
								if (AgreeCheck == false) {
									Swal.fire("请同意保留版权信息", "请同意保留版权信息，再点击提交。", "warning");
									return 0;
								}
								$("#SettingForm").submit(); // 提交表格
							}
						</script>
				</div>
			</div>

		<?php
		} else {
		?>
			<div class="card">
				<div class="card-header">
					设置页面
				</div>
				<div class="card-body">
					安装结果：
				<?php
				// 已经获取到所需信息，先导入数据库，再写配置到config.php

				// 处理post数据
				$Sitename = (!empty($_POST["Sitename"])) ? $_POST["Sitename"] : "";
				$IsCheckPassword = (!empty($_POST["IsCheckPassword"])) ? $_POST["IsCheckPassword"] : "true";
				$Password = (!empty($_POST["Password"])) ? $_POST["Password"] : "";
				$ADMIN_PASSWORD = (!empty($_POST["ADMIN_PASSWORD"])) ? $_POST["ADMIN_PASSWORD"] : "";
				$DownloadTimes = (!empty($_POST["DownloadTimes"])) ? $_POST["DownloadTimes"] : "";
				$DownloadLinkAvailableTime = (!empty($_POST["DownloadLinkAvailableTime"])) ? $_POST["DownloadLinkAvailableTime"] : "8";
				$IsConfirmDownload = (!empty($_POST["IsConfirmDownload"])) ? $_POST["IsConfirmDownload"] : "true";
				$Footer = (!empty($_POST["Footer"])) ? $_POST["Footer"] : "";

				$BDUSS = (!empty($_POST["BDUSS"])) ? $_POST["BDUSS"] : "";
				$STOKEN = (!empty($_POST["STOKEN"])) ? $_POST["STOKEN"] : "";
				$SVIP_BDUSS = (!empty($_POST["SVIP_BDUSS"])) ? $_POST["SVIP_BDUSS"] : "";
				$SVIP_STOKEN = (!empty($_POST["SVIP_STOKEN"])) ? $_POST["SVIP_STOKEN"] : "";

				$USING_DB = (!empty($_POST["USING_DB"])) ? $_POST["USING_DB"] : "false";
				$servername = (!empty($_POST["DbConfig_servername"])) ? $_POST["DbConfig_servername"] : "";
				$username = (!empty($_POST["DbConfig_username"])) ? $_POST["DbConfig_username"] : "";
				$DBPassword = (!empty($_POST["DbConfig_DBPassword"])) ? $_POST["DbConfig_DBPassword"] : "";
				$dbname = (!empty($_POST["DbConfig_dbname"])) ? $_POST["DbConfig_dbname"] : "";
				$dbtable = (!empty($_POST["DbConfig_dbtable"])) ? $_POST["DbConfig_dbtable"] : "";
				$ReserveDBData = (!empty($_POST["ReserveDBData"])) ? $_POST["ReserveDBData"] : "false"; // 是否保存以前数据库数据 未选中不会提交
				$SVIPSwitchMod = (!empty($_POST["SVIPSwitchMod"])) ? $_POST["SVIPSwitchMod"] : "0";

				if ($USING_DB == "true") { //注意判断要用string类型进行
					// 连接数据库
					$conn = mysqli_connect($servername, $username, $DBPassword, $dbname);
					// Check connection
					if (!$conn) {
						die("数据库连接错误，详细信息：" . mysqli_connect_error());
					}
					if ($ReserveDBData == "true") {
						echo "保存以前数据库数据<br />";
					} else {
						// 打开sql文件
						$SQLfile = file_get_contents("./install/bdwp.sql");
						if ($SQLfile == false) die("无法打开bdwp.sql文件");

						$SQLfile = str_replace('<dbtable>', $dbtable, $SQLfile);

						$sccess_result = 0;
						if (mysqli_multi_query($conn, $SQLfile)) {
							do {
								$sccess_result = $sccess_result + 1;
							} while (mysqli_more_results($conn) && mysqli_next_result($conn));
						}

						$affect_row = mysqli_affected_rows($conn);
						if ($affect_row == -1) {
							die("数据库导入出错，错误在" . $sccess_result . "行");
						} else {
							echo "数据库导入成功，成功导入" . $sccess_result . "条数据<br />";
						}
					}
				} else {
					echo "不启用数据库<br />";
				}
				// 修改文件
				$raw_config = file_get_contents("./install/config_raw");
				if ($raw_config == false) die("无法打开config_raw文件");


				$update_config = $raw_config;

				$update_config = str_replace('<Sitename>', $Sitename, $update_config);
				$update_config = str_replace('<IsCheckPassword>', $IsCheckPassword, $update_config);
				$update_config = str_replace('<Password>', $Password, $update_config);
				$update_config = str_replace('<ADMIN_PASSWORD>', $ADMIN_PASSWORD, $update_config);
				$update_config = str_replace('<DownloadTimes>', $DownloadTimes, $update_config);
				$update_config = str_replace('<DownloadLinkAvailableTime>', $DownloadLinkAvailableTime, $update_config);
				$update_config = str_replace('<IsConfirmDownload>', $IsConfirmDownload, $update_config);
				$update_config = str_replace('<Footer>', $Footer, $update_config);

				$update_config = str_replace('<BDUSS>', $BDUSS, $update_config);
				$update_config = str_replace('<STOKEN>', $STOKEN, $update_config);
				$update_config = str_replace('<SVIP_BDUSS>', $SVIP_BDUSS, $update_config);
				$update_config = str_replace('<SVIP_STOKEN>', $SVIP_STOKEN, $update_config);

				$update_config = str_replace('<USING_DB>', $USING_DB, $update_config);
				$update_config = str_replace('<servername>', $servername, $update_config);
				$update_config = str_replace('<username>', $username, $update_config);
				$update_config = str_replace('<DBPassword>', $DBPassword, $update_config);
				$update_config = str_replace('<dbname>', $dbname, $update_config);
				$update_config = str_replace('<dbtable>', $dbtable, $update_config);
				$update_config = str_replace('<SVIPSwitchMod>', $SVIPSwitchMod, $update_config);

				$len = file_put_contents('config.php', $update_config);

				if ($len != false) {
					echo "成功！成功写入 config.php 共 $len 个字符。<br />";
				} else {
					die("写入 config.php 文件失败，请检查 config.php 文件状态及当前用户权限。");
				}
				header('Refresh: 5;url=./');
				echo "恭喜你！安装成功了~<br />浏览器将会在5s内自动跳转，若没有跳转可<a href='./'>点此链接</a>前往主页查看。";
			}
				?>
				</div>
			</div>

	</div>

</body>

</html>