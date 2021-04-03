<?php

/**
 * PanDownload 网页复刻版，PHP 语言版用户设置文件
 *
 * 用户可在此设置
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
if (!defined('init')) { // 直接访问处理程序
	header('Content-Type: text/plain; charset=utf-8');
	if (!file_exists('config.php')) {
		http_response_code(503);
		header('Content-Type: text/plain; charset=utf-8');
		header('Refresh: 5;url=install.php');
		die("HTTP 503 服务不可用！\r\n暂未安装此程序！\r\n将在五秒内跳转到安装程序！");
	} else {
		require('config.php');
	}
	http_response_code(403);
	header('Refresh: 3;url=./');
	define('init', true);
	require('config.php');
	die("HTTP 403 禁止访问！\r\n此文件是 PanDownload 网页复刻版 PHP 语言版项目的有关文件！\r\n禁止直接访问！");
}
?>
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

<div class="card">
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
	const LanguageSetting = '<?php echo $_COOKIE['Language']; ?>';
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
	if (window.matchMedia('(prefers-color-scheme: dark)').matches) { // 获取浏览器设置
		$('#Browser-ColorMode').text('<?php echo Language["DarkMode"]; ?>'); // 深色模式
	} else if (window.matchMedia('(prefers-color-scheme: light)').matches) {
		$('#Browser-ColorMode').text('<?php echo Language["LightMode"]; ?>'); // 浅色模式
	}
	window.matchMedia('(prefers-color-scheme: dark)').addListener(function(e) { // 当色彩模式改变为深色模式
		if (e.matches) {
			$('#Browser-ColorMode').text('<?php echo Language["DarkMode"]; ?>');
		}
	});
	window.matchMedia('(prefers-color-scheme: light)').addListener(function(e) { // 当色彩模式改变为浅色模式
		if (e.matches) {
			$('#Browser-ColorMode').text('<?php echo Language["LightMode"]; ?>');
		}
	});
	$('#ColorMode-Select').on('change', function() {
		if (this.value === 'auto') {
			localStorage.removeItem('colorMode');
			location.reload();
		} else {
			localStorage.setItem('colorMode', this.value);
			location.reload();
		}
	});
	$('#Language-Select').on('change', function() {
		if (this.value === 'auto') {
			document.cookie = 'Language=; expires=Thu, 01 Jan 1970 00:00:00 GMT';
			location.reload();
		} else {
			document.cookie = `Language=${this.value}; expires=${new Date(Date.now() + 31536000000)}`;
			location.reload();
		}
	});
</script>