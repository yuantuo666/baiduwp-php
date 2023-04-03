<?php

/**
 * PanDownload 网页复刻版，PHP 语言版
 *
 * 首页文件
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
require_once("./common/invalidCheck.php");
?>

<div class="col-lg-6 col-md-9 mx-auto mb-5 input-card page" id="index" style="display: none;">
	<div class="card">
		<div class="card-header bg-dark text-light">
			<text id="parsingtooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" data-bs-title="请稍等，正在连接服务器查询信息"><?php echo Language["IndexTitle"]; ?></text>
			<span style="float: right;" id="sviptooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" data-bs-title="请稍等，正在连接服务器查询SVIP账号状态"><span class="point point-lg" id="svipstate-point"></span><span id="svipstate">Loading...</span></span>
		</div>
		<div class="card-body">
			<div class="form-group my-2"><input type="text" class="form-control" name="surl" placeholder="<?php echo Language["ShareLink"]; ?>" oninput="Getpw()"></div>
			<div class="form-group my-4"><input type="text" class="form-control" name="pwd" placeholder="<?php echo Language["SharePassword"]; ?>"></div>
			<div class="form-group my-4" id="password" style="display: none;"><input type="text" class="form-control" name="password" placeholder="请输入密码"></div>
			<div class="form-group mt-4 mb-3 d-grid">
				<button onclick="SubmitLink()" class="btn btn-primary"><?php echo Language["Submit"]; ?></button>
			</div>
		</div>
	</div>
	<script>
		// 主页部分脚本
		$(document).ready(function() {
			hash = window.location.hash.replace("#", "")
			if (hash === "/list") {
				hash = "/index"
			}
			navigate(hash)

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
					$("#sviptooltip").attr("data-bs-title", data.msg);
					new bootstrap.Tooltip(document.getElementById('sviptooltip'));
				}
			});

			getAPI('ParseCount').then(function(response) {
				if (response.success) {
					$("#parsingtooltip").attr("data-bs-title", response.data.msg);
					new bootstrap.Tooltip(document.getElementById('parsingtooltip'));
				}
			});

			getAPI('Password').then(function(response) {
				switch (response.data.status) {
					case 0:
						// 无密码
						$("#password").hide();
						break;
					case 1:
						// 有密码
						$("#password").show();
						break;
					case 2:
						// 密码正确
						$("#password").html("您的设备在短期内已经验证过，无需再次输入密码。");
						$("#password").show();
						break;
				}
			});
		});
	</script>
</div>

<div id="list" class="page" style="display: none;">
	<nav class="breadcrumb my-4" aria-label="breadcrumb">
		<ol class="breadcrumb my-3" id="dir-list">
			<li class="breadcrumb-item active">正在连接服务器......</li>
		</ol>
	</nav>
	<div>
		<ul class="list-group" id="files-list">
		</ul>
	</div>
</div>

<div class="modal fade" id="downloadpage" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">成功获取下载地址</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div id="downloadlinkdiv">

				</div>
				<h5 class="text-danger" id="limit-tip" style="display: none;">当前 SVIP 账号已被限速，请更换账号。</h5>
				<p class="card-text">
					<a class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#SendToAria2">发送到 Aira2 / Motrix</a>
					<a class="btn btn-outline-primary" href="" id="filecxx" style="display: none;">发送到 文件蜈蚣</a>
				</p>
				<p class="card-text"><a class="btn btn-outline-secondary" href="./#/help" target="_blank">下载帮助（必读）</a></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">关闭</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="SendToAria2" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">发送到 Aria2/Motrix Json-RPC</h5>
				<button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#downloadpage"></button>
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
				<button type="button" class="btn btn-primary" onclick="addUri()" data-bs-toggle="modal" data-bs-target="#downloadpage">发送</button>
				<button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#downloadpage">返回</button>
			</div>
		</div>
	</div>
	<script>
		$(function() {
			if (localStorage.getItem('aria2wsurl') !== null)
				$('#wsurl').attr('value', localStorage.getItem('aria2wsurl'));
			if (localStorage.getItem('aria2token') !== null)
				$('#token').attr('value', localStorage.getItem('aria2token'));
		});
	</script>
</div>

<style>
	.card {
		margin-top: 3rem;
	}

	#Browser-ColorMode,
	#Browser-Language {
		margin-right: 2rem;

	}

	.SaveTime {
		margin-right: 0.8rem;
	}

	.card-item:not(:first-of-type) {
		margin-top: 2rem;
	}

	select {
		margin-top: 0.5rem;
	}
</style>

<div class="card page" id="usersettings" style="display: none;">
	<div class="card-header"><?php echo Language["UserSettings"]; ?></div>
	<div class="card-body">
		<div class="card-item">
			<h3><?php echo Language["ColorMode"]; ?></h3>
			<div id="ColorMode-Setting-View">
				<span class="SaveTime"><?php echo Language["SaveForever"]; ?></span>
				<span><?php echo Language["BrowserSettings"]; ?></span><span id="Browser-ColorMode"></span>
				<span><?php echo Language["CurrentSetting"]; ?></span><span id="Setting-ColorMode"></span>
			</div>
			<select id="ColorMode-Select" class="form-control">
				<option value="auto"><?php echo Language["FollowBrowser"]; ?></option>
				<option value="dark"><?php echo Language["DarkMode"]; ?></option>
				<option value="light"><?php echo Language["LightMode"]; ?></option>
			</select>
		</div>
		<div class="card-item">
			<h3><?php echo Language["LanguageChoose"]; ?></h3>
			<div id="LanguageChoose">
				<span class="SaveTime"><?php echo Language["Save365"]; ?></span>
				<span><?php echo Language["BrowserSettings"]; ?></span><span id="Browser-Language"><?php echo BrowserLanguage; ?></span>
				<span><?php echo Language["CurrentDisplayed"]; ?></span><span id="Displayed-Language"><?php echo Lang; ?></span>
			</div>
			<select id="Language-Select" class="form-control">
				<option value="auto"><?php echo Language["FollowBrowser"]; ?></option>
				<option value="zh-CN">简体中文</option>
				<option value="en">English</option>
			</select>
		</div>
		<div class="card-item">
			<h3><?php echo Language["UpdateTip"]; ?></h3>
			<div id="UpdateTips">
				<?php echo Language["UpdateTips"]; ?>
			</div>
			<select id="UpdateTips-Select" class="form-control">
				<option value="true"><?php echo Language["Enable"]; ?></option>
				<option value="false"><?php echo Language["Disable"]; ?></option>
			</select>
		</div>
	</div>
</div>

<script>
	if (localStorage.getItem('colorMode') === null) { // 判断用户设置的颜色
		$('#Setting-ColorMode').text('<?php echo Language["FollowBrowser"]; ?>'); // 跟随浏览器
		$('#ColorMode-Select option[value=auto]')[0].selected = true;
	} else if (localStorage.getItem('colorMode') === 'dark') { // 深色模式
		$('#Setting-ColorMode').text('<?php echo Language["DarkMode"]; ?>');
		$('#ColorMode-Select option[value=dark]')[0].selected = true;
	} else if (localStorage.getItem('colorMode') === 'light') { // 浅色模式
		$('#Setting-ColorMode').text('<?php echo Language["LightMode"]; ?>');
		$('#ColorMode-Select option[value=light]')[0].selected = true;
	}
	const LanguageSetting = '<?php echo $_COOKIE['Language'] ?? ''; ?>';
	if (LanguageSetting === '') { // 判断用户设置的语言
		$('#Language-Select option[value=auto]').text('<?php echo Language["CurrentSetting"]; ?>' + $('#Language-Select option[value=auto]').text());
		$('#Language-Select option[value=auto]')[0].selected = true;
	} else if (LanguageSetting === 'zh-CN') { // zh-CN
		$('#Language-Select option[value="zh-CN"]').text('<?php echo Language["CurrentSetting"]; ?>' + $('#Language-Select option[value="zh-CN"]').text());
		$('#Language-Select option[value="zh-CN"]')[0].selected = true;
	} else if (LanguageSetting === 'en') { // en
		$('#Language-Select option[value="en"]').text('<?php echo Language["CurrentSetting"]; ?>' + $('#Language-Select option[value="en"]').text());
		$('#Language-Select option[value="en"]')[0].selected = true;
	}

	const dark = window.matchMedia('(prefers-color-scheme: dark)'),
		light = window.matchMedia('(prefers-color-scheme: light)');

	function changeColorMode() { // 更改颜色模式显示
		if (dark.matches) { // 获取浏览器设置
			$('#Browser-ColorMode').text('<?php echo Language["DarkMode"]; ?>'); // 深色模式
		} else if (light.matches) { // 获取浏览器设置
			$('#Browser-ColorMode').text('<?php echo Language["LightMode"]; ?>'); // 浅色模式
		}
	}

	dark.addEventListener('change', changeColorMode); // 当色彩模式改变为深色模式
	light.addEventListener('change', changeColorMode); // 当色彩模式改变为浅色模式
	changeColorMode(); // 初始化

	$('#ColorMode-Select').on('change', function() {
		if (this.value === 'auto') {
			localStorage.removeItem('colorMode');
		} else {
			localStorage.setItem('colorMode', this.value);
		}
		location.reload();
	});

	$('#Language-Select').on('change', function() {
		const expires = (this.value === 'auto') ? 'Thu, 01 Jan 1970 00:00:00 GMT' : new Date(Date.now() + 31536000000);
		document.cookie = `Language=${this.value}; expires=${expires}`;
		location.reload();
	});

	// check if the user set the update tip
	let UpdateTip = localStorage.getItem('UpdateTip') || 'true';
	if (UpdateTip === 'true') {
		$('#UpdateTips-Select option[value=true]')[0].selected = true;
	} else {
		$('#UpdateTips-Select option[value=false]')[0].selected = true;
	}

	$('#UpdateTips-Select').on('change', function() {
		if (this.value === 'true') {
			localStorage.removeItem('UpdateTip');
		} else {
			localStorage.setItem('UpdateTip', 'false');
		}
		location.reload();
	});
</script>

<div id="help" class="page" style="display: none;">
	<?php echo Language["HelpPage"]; ?>
</div>