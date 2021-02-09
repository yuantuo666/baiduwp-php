<?php


session_start();
define('init', true);
if (version_compare(PHP_VERSION, '7.0.0', '<')) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://www.php.net/downloads.php');
	die("HTTP 503 服务不可用！\r\nPHP 版本过低！无法正常运行程序！\r\n请安装 7.0.0 或以上版本的 PHP！\r\n将在五秒内跳转到 PHP 官方下载页面！");
}
if (!(file_exists('config.php') && file_exists('functions.php') && file_exists('language.php'))) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://github.com/yuantuo666/baiduwp-php');
	die("HTTP 503 服务不可用！\r\n缺少相关配置和定义文件！无法正常运行程序！\r\n请重新 Clone 项目并配置！\r\n将在五秒内跳转到 GitHub 储存库！");
}
// 导入配置和函数
require('config.php');
require('language.php');
// 通用响应头
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge,chrome=1');
if (DEBUG) {
	error_reporting(E_ALL);
} else {
	error_reporting(0); //关闭错误报告
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="referrer" content="same-origin" />
	<meta name="author" content="Yuan_Tuo" />
	<meta name="version" content="<?php echo programVersion; ?>" />
	<title>用户设置</title>
	<link rel="icon" href="favicon.ico" />
	<link rel="stylesheet" href="static/index.css" />
	<link rel="stylesheet" disabled id="ColorMode-Auto" href="static/colorMode/auto.css" />
	<link rel="stylesheet" disabled id="ColorMode-Dark" href="static/colorMode/dark.css" />
	<link rel="stylesheet" disabled id="ColorMode-Light" href="static/colorMode/light.css" />
	<link rel="stylesheet" href="static/colorMode/index.css" />
	<link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/4.1.2/css/bootstrap.min.css" />
	<link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/5.8.1/css/all.min.css" />
	<link rel="stylesheet" disabled id="Swal2-Dark" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4.0.2/dark.min.css" />
	<link rel="stylesheet" disabled id="Swal2-Light" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-default@4.0.2/default.min.css" />
	<script src="https://cdn.jsdelivr.net/gh/jquery/jquery@3.5.1/dist/jquery.min.js"></script>
	<script src="https://cdn.staticfile.org/twitter-bootstrap/4.1.2/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.14.0/dist/sweetalert2.min.js"></script>
	<script src="static/colorMode/index.js"></script>
	<style>
		.card {
			margin-top: 3rem;
		}

		@media (prefers-color-scheme: dark) {
			#ColorMode-Auto-Button {
				background: #424242 !important;
				color: white !important;
			}
		}

		@media (prefers-color-scheme: light) {
			#ColorMode-Auto-Button {
				background: #f1f1f1 !important;
				color: black !important;
			}
		}



		#ColorMode-Dark-Button {
			background: #424242 !important;
			color: white !important;
		}

		#ColorMode-Light-Button {
			background: #f1f1f1 !important;
			color: black !important;
		}

		#Browser-ColorMode {
			margin-right: 2rem;

		}

		#ColorMode-Setting-View {
			margin-bottom: 0.6rem;
		}
	</style>
</head>

<body>
	<nav class="navbar navbar-expand-sm navbar-dark">
		<div class="container">
			<a class="navbar-brand" href="./"><img src="resource/logo.png" class="img-fluid rounded logo-img mr-2" alt="LOGO" />PanDownload</a>
			<button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#collpase-bar"><span class="navbar-toggler-icon"></span></button>
			<div class="collapse navbar-collapse" id="collpase-bar">
				<ul class="navbar-nav">
					<li class="nav-item"><a class="nav-link" href="./"><?php echo Language["IndexButton"]; ?></a></li>
					<li class="nav-item"><a class="nav-link" href="?help" target="_blank"><?php echo Language["HelpButton"]; ?></a></li>
					<li class="nav-item"><a class="nav-link" href="usersettings.php" target="_blank"><?php echo Language["UserSettings"]; ?></a></li>
					<li class="nav-item"><a class="nav-link" href="https://imwcr.cn/" target="_blank">Made by Yuan_Tuo</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="container">
		<div class="card">
			<div class="card-header"><?php echo Language["UserSettings"]; ?></div>
			<div class="card-body">
				<div>
					<h3><?php echo Language["ColorMode"]; ?></h3>
					<div id="ColorMode-Setting-View">
						<span><?php echo Language["BrowserSettings"]; ?></span><span id="Browser-ColorMode"></span>
						<span><?php echo Language["CurrentSetting"]; ?></span><span id="Setting-ColorMode"></span>
					</div>
					<div><select id="ColorMode-Select" class="form-control">
							<option value="auto"><button class="btn ColorMode-Button" data-colorMode="auto" id="ColorMode-Auto-Button"><?php echo Language["FollowBrowser"]; ?></button></option>
							<option value="dark"><button class="btn ColorMode-Button" data-colorMode="dark" id="ColorMode-Dark-Button"><?php echo Language["DarkMode"]; ?></button></option>
							<option value="light"><button class="btn ColorMode-Button" data-colorMode="light" id="ColorMode-Light-Button"><?php echo Language["LightMode"]; ?></button></option>
						</select></div>
				</div>
			</div>
		</div>
	</div>
	<script>
		if (localStorage.getItem('colorMode') === null) { // 判断用户设置的颜色
			$('#Setting-ColorMode').text('<?php echo Language["FollowBrowser"]; ?>'); // 跟随浏览器
			$('option[value=auto]')[0].selected = true;
		} else if (localStorage.getItem('colorMode') === 'dark') { // 深色模式
			$('#Setting-ColorMode').text('<?php echo Language["DarkMode"]; ?>');
			$('option[value=dark]')[0].selected = true;
		} else if (localStorage.getItem('colorMode') === 'light') { // 浅色模式
			$('#Setting-ColorMode').text('<?php echo Language["LightMode"]; ?>');
			$('option[value=light]')[0].selected = true;
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
		// $('.ColorMode-Button').each(function() { // 更改颜色模式的按钮的事件
		// 	if (this.dataset.colormode === 'auto') {
		// 		this.addEventListener('click', function() {
		// 			localStorage.removeItem('colorMode');
		// 			location.reload();
		// 		});
		// 	} else {
		// 		this.addEventListener('click', function() {
		// 			localStorage.setItem('colorMode', this.dataset.colormode);
		// 			location.reload();
		// 		});
		// 	}
		// });
	</script>
</body>

</html>