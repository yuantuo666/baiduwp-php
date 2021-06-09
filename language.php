<?php

/**
 * PanDownload 网页复刻版，语言文件
 *
 * 功能描述：为一些页面添加必要的语言翻译
 *
 * 此项目 GitHub 地址：https://github.com/yuantuo666/baiduwp-php
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */

$lang = [
	"zh-CN" => [
		"LanguageName" => "Chinese",
		"ConfirmTitle" => "继续解析？",
		"ConfirmText" => "为保证服务稳定，每个IP每天有" . DownloadTimes . "次免费解析次数，是否继续？",
		"ConfirmmButtonText" => "确定",
		"IndexButton" => "首页",
		"HelpButton" => "使用帮助",
		"TipTitle" => "提示",
		"TimeoutTip" => "当前页面已失效，请刷新重新获取。",
		"AllFiles" => "全部文件",
		"PasswordError" => "密码错误",
		"AccountError" => "账户错误",
		"NoChance" => "免费次数不足",
		"SwitchWait" => "请稍后，正在切换账号中~",
		"DownloadLinkError" => "获取下载链接失败",
		"DatabaseError" => "数据库错误",
		"DownloadLinkSuccess" => "获取下载链接成功",
		"Rreview" => "在线预览：",
		"NotSupportWithUA" => "暂不支持当前文件。",
		"NotSupportWithoutUA" => "目前只支持 <b>50MB以下文件</b> 或 <b>设置UA</b> 后使用在线预览功能。",
		"DownloadLink" => "下载链接",
		"DownloadTip" => "Tips: 电脑端右键即可复制下载链接，手机端长按可复制下载链接。推荐使用 Aria2、Motrix 下载，速度更快，使用方法请访问帮助页面。",
		"SendToAria2" => "发送到 Aria2",
		"Send" => "发送",
		"Close" => "关闭",
		"IndexTitle" => "百度网盘在线解析",
		"ShareLink" => "请输入分享链接(可输入带提取码链接)",
		"SharePassword" => "请输入提取码(没有留空)",
		"PassWord" => "请输入密码",
		"PassWordVerified" => "您的设备在短期内已经验证过，无需再次输入密码。",
		"Submit" => "提交",
		"UserSettings" => "用户设置",
		"ColorMode" => "色彩模式",
		"BrowserSettings" => "浏览器设置：",
		"CurrentSetting" => "当前设置：",
		"FollowBrowser" => "跟随浏览器（默认）",
		"DarkMode" => "深色模式",
		"LightMode" => "浅色模式",
		"LanguageChoose" => "选择语言",
		"SaveForever" => '将会永久保存。',
		"Save365" => '将会保存 365 天，每次访问此项目会自动续期。',
		"CurrentDisplayed" => "当前显示：",
		"HelpPage" => '
	<div class="row justify-content-center">
		<div class="col-md-7 col-sm-8 col-11">
			<div class="alert alert-primary" role="alert">
				<h5 class="alert-heading">下载提示</h5>
				<hr />
				<p class="card-text">因百度限制，需修改浏览器 User Agent 后下载。你可以在下方选择你喜欢的方式进行下载。<br />
					<div class="page-inner">
						<section class="normal" id="section-">
							<div id="Motrix"><a class="anchor" href="#Motrix"></a>
								<h4>Motrix（推荐）</h4>
							</div>
							<ol>
								<li>前往 <a href="https://motrix.app/" target="_blank">Motrix官网</a> 下载 <b>Motrix</b> 对应版本</li>
								<li>安装后运行Motrix。</li>
								<li>打开解析下载页面，点击 <b>推送到 Aria2(Motrix)</b></li>
								<li>在“RPC地址”中输入 <b>ws://localhost:16800/jsonrpc</b> 并 点击发送。</li>
							</ol>
							<div id="aria2-windows"><a class="anchor" href="#aria2-windows"></a>
								<h4>Aria2（Windows）</h4>
							</div>
							<ol>
								<li><a href="./resource/aria2.zip">点击此处</a> 下载 <b>aria2.zip</b></li>
								<li>解压 <b>aria2.zip</b> 文件并运行其中的 <b>点此启动.bat</b></li>
								<li>打开解析下载页面，点击 <b>推送到 Aria2(Motrix)</b></li>
							</ol>
							<div id="aria2-android"><a class="anchor" href="#aria2-android"></a>
								<h4>Aria2（安卓）</h4>
							</div>
							<ol>
								<li><a data-qrcode-attr="href" href="https://github.com/devgianlu/Aria2Android/releases/download/v2.6.1/app-foss-release.apk">点击此处</a> 下载 <b>Aria2Android.apk</b></li>
								<li>安装并运行 <b>Aria2Android.apk</b></li>
								<li>将 RPC -> RPC令牌(token) 设置好后，点击右下角启动 aria2</li>
								<li>打开解析下载页面，将上一步设置的 <b>token</b> 输入框中，点击 <b>推送到 Aria2(Motrix)</b></li>
							</ol>
							<div id="IDM"><a class="anchor" href="#IDM"></a>
								<h4>IDM</h4>
							</div>
							<ol>
								<li>选项 -> 下载 -> 手动添加任务时使用的用户代理（UA）-> 填入 <b>LogStatistic</b></li>
								<li><b>右键复制下载链接</b>（如果 直接点击 或 右键调用IDM 将传入浏览器的 UA 导致下载失败），在 IDM 新建任务，粘贴链接即可下载。</li>
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
								<li>当前项目版本：' . programVersion . '</li>
								<li><a data-qrcode-attr="href" href="https://github.com/yuantuo666/baiduwp-php" target="_blank">Github仓库</a></li>
								<li>项目作者：<a data-qrcode-attr="href" href="https://imwcr.cn/" target="_blank">Yuan_Tuo</a></li>
								<li>项目协作者：<a data-qrcode-attr="href" href="https://lcwebsite.cn/" target="_blank">LC</a></li>
							</ol>
						</section>
						<script>
							$(".anchor").attr("target", "_self").prepend(`<svg viewBox="0 0 16 16" version="1.1" width="16" height="16"><path fill-rule="evenodd" d="M7.775 3.275a.75.75 0 001.06 1.06l1.25-1.25a2 2 0 112.83 2.83l-2.5 2.5a2 2 0 01-2.83 0 .75.75 0 00-1.06 1.06 3.5
							3.5 0 004.95 0l2.5-2.5a3.5 3.5 0 00-4.95-4.95l-1.25 1.25zm-4.69 9.64a2 2 0 010-2.83l2.5-2.5a2 2 0 012.83 0 .75.75 0 001.06-1.06 3.5 3.5 0 00-4.95 0l-2.5 2.5a3.5 3.5 0 004.95 4.95l1.25-1.25a.75.75 0 00-1.06-1.06l-1.25 1.25a2 2 0 01-2.83 0z"/></svg>`);
						</script>
					</div>
				</p>
			</div>
		</div>
	</div>'
	],
	"en" => [
		"LanguageName" => "English",
		"ConfirmTitle" => "Continue?",
		"ConfirmText" => "You have " . DownloadTimes . " download times, continue?",
		"ConfirmmButtonText" => "Yes",
		"IndexButton" => "Home",
		"HelpButton" => "Using Help",
		"TipTitle" => "Tip",
		"TimeoutTip" => "It\'s timeout now, please refresh the page!", // 转义！！
		"AllFiles" => "All File(s)",
		"PasswordError" => "Password is error",
		"AccountError" => "You are banned",
		"NoChance" => "You have no download times.",
		"SwitchWait" => "Please wait",
		"DownloadLinkError" => "An error happened when try to get download link",
		"DatabaseError" => "An error happened when try to connect to database",
		"DownloadLinkSuccess" => "Succeed",
		"Rreview" => "Preview:",
		"NotSupportWithUA" => "The type is not support",
		"NotSupportWithoutUA" => "The type is not support",
		"DownloadLink" => "Download Link",
		"DownloadTip" => "Tips: Copy the download link and put it in your download apps.",
		"SendToAria2" => "Send to aria2",
		"Send" => "Send",
		"Close" => "Close",
		"IndexTitle" => "Get Download Link of pan.baidu.com",
		"ShareLink" => "Please enter the share link",
		"SharePassword" => "Please enter the share link password",
		"PassWord" => "Please enter the site password",
		"PassWordVerified" => "Your device has been verified in a short period of time, and there is no need to enter the password again.",
		"Submit" => "Submit",
		"UserSettings" => "User settings",
		"ColorMode" => "Color mode",
		"BrowserSettings" => "Browser settings: ",
		"CurrentSetting" => "Current setting: ",
		"FollowBrowser" => "Follow browser (default)",
		"DarkMode" => "Dark mode",
		"LightMode" => "Light mode",
		"LanguageChoose" => "Choose a language",
		"SaveForever" => 'Will be saved forever.',
		"Save365" => 'It will be saved for 365 days and will be automatically renewed every time you visit this item.',
		"CurrentDisplayed" => "Current displayed: ",
		"HelpPage" => '
	<div class="row justify-content-center">
		<div class="col-md-7 col-sm-8 col-11">
			<div class="alert alert-primary" role="alert">
				<h5 class="alert-heading">Using help</h5>
				<hr />
				<p class="card-text">Due to Baidu restrictions, you need to modify the browser "User Agent" before downloading. <br />
					<div class="page-inner">
						<section class="normal" id="section-">
							<div id="Motrix"><a class="anchor" href="#Motrix"></a>
								<h4>Motrix(Recommend)</h4>
							</div>
							<ol>
								<li>Download <b>Motrix</b> from <a href="https://motrix.app/" target="_blank">https://motrix.app/</a>.</li>
								<li>Install and run Motrix.</li>
								<li>Open the file download page and click <b>Send to aria2(Motrix)</b></li>
								<li>Type <b>ws://localhost:16800/jsonrpc</b> in the "RPC地址".Click send button.</li>
							</ol>
							<div id="aria2-windows"><a class="anchor" href="#aria2-windows"></a>
								<h4>Aria2(Windows)</h4>
							</div>
							<ol>
								<li><a href="./resource/aria2.zip">Click here</a> to download <b>aria2.zip</b></li>
								<li>Unzip <b>aria2.zip</b> and run <b>点此启动.bat</b></li>
								<li>Open the file download page and click <b>Send to aria2(Motrix)</b></li>
							</ol>
							<div id="aria2-android"><a class="anchor" href="#aria2-android"></a>
								<h4>Aria2(Android)</h4>
							</div>
							<ol>
								<li><a data-qrcode-attr="href" href="https://github.com/devgianlu/Aria2Android/releases/download/v2.6.1/app-foss-release.apk">Click here</a>  to download  <b>Aria2Android.apk</b></li>
								<li>Install <b>Aria2Android.apk</b> and run the APP</li>
								<li>Set your RPC token and then click the bottom button to run aria2</li>
								<li>Open the file download page and put your <b>token</b> in the box, then click <b>Send to aria2(Motrix)</b></li>
							</ol>
							<div id="IDM"><a class="anchor" href="#IDM"></a>
								<h4>Internet Download Manager</h4>
							</div>
							<ol>
								<li>Options -> Downloads -> User-Angent for manually added downloads -> Type in <b>LogStatistic</b></li>
								<li><b>Copy the download link.</b> -> Add URL (in IDM) -> Paste the link -> OK </li>
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
								<li>当前项目版本：' . programVersion . '</li>
								<li><a data-qrcode-attr="href" href="https://github.com/yuantuo666/baiduwp-php" target="_blank">Github仓库</a></li>
								<li>项目作者：<a data-qrcode-attr="href" href="https://imwcr.cn/" target="_blank">Yuan_Tuo</a></li>
								<li>项目协作者：<a data-qrcode-attr="href" href="https://lcwebsite.cn/" target="_blank">LC</a></li>
							</ol>
						</section>
						<script>
							$(".anchor").attr("target", "_self").prepend(`<svg viewBox="0 0 16 16" version="1.1" width="16" height="16"><path fill-rule="evenodd" d="M7.775 3.275a.75.75 0 001.06 1.06l1.25-1.25a2 2 0 112.83 2.83l-2.5 2.5a2 2 0 01-2.83 0 .75.75 0 00-1.06 1.06 3.5
							3.5 0 004.95 0l2.5-2.5a3.5 3.5 0 00-4.95-4.95l-1.25 1.25zm-4.69 9.64a2 2 0 010-2.83l2.5-2.5a2 2 0 012.83 0 .75.75 0 001.06-1.06 3.5 3.5 0 00-4.95 0l-2.5 2.5a3.5 3.5 0 004.95 4.95l1.25-1.25a.75.75 0 00-1.06-1.06l-1.25 1.25a2 2 0 01-2.83 0z"/></svg>`);
						</script>
					</div>
				</p>
			</div>
		</div>
	</div>'
	],
];

$lang['zh'] = $lang['zh-CN']; // 将 zh 的值设为和 zh-CN 相同

define('BrowserLanguage', $_SERVER["HTTP_ACCEPT_LANGUAGE"]); // 浏览器传入的语言（Accept-Language）（一个字符串）

function setLanguage()
{
	global $lang; // 支持的语言列表
	$languages = []; // 排序后的浏览器语言列表
	$qs = []; // 临时变量

	define('BrowserLanguages', explode(",", BrowserLanguage)); // 浏览器传入的语言列表（Accept-Language）一个 Array
	foreach (BrowserLanguages as &$value) { // 遍历浏览器语言列表
		if (preg_match('#([A-Za-z0-9\-]{1,8});q=(\d(.\d{1,3})?)#', $value, $matches)) { // 判断是否有优先级（;q=x.x）
			$qs[$matches[2]] = $matches[1]; // 如果有，加入临时变量 qs
		} else {
			array_push($languages, $value); // 如果没有，直接加入排序后语言列表
		}
	}
	krsort($qs); // 排序 qs
	foreach (array_values($qs) as &$value) { // 遍历 qs
		array_push($languages, $value); // 将 qs 的值一个个加入语言列表
	}
	unset($qs); // 删除 qs

	foreach ($languages as &$value) { // 遍历排序后的浏览器支持语言列表
		if (array_key_exists($value, $lang)) { // 当发现第一个支持的
			define('Lang', $value); // 定义 Lang 为选择的语言
			break; // 停止遍历
		}
	}
}

if (isset($_COOKIE['Language'])) { // 判断用户是否设置语言
	if (array_key_exists($_COOKIE['Language'], $lang)) { // 如果用户设置的语言存在
		define('Lang', $_COOKIE['Language']); // 定义 Lang 为选择的语言
		setcookie('Language', $_COOKIE['Language'], time() + 31536000); // 自动延长 Cookie 保存时长
	} else { // 若语言配置错误
		setcookie('Language', '', time() - 31536000); // 删除 Cookie
		setLanguage(); // 按照未设置语言来自动决定语言
		echo "<div>There was a problem with your language configuration and it has been reset for you. <a href=\"?usersettings\" target=\"_blank\">Click here to select language.</div>"; // 输出配置错误提示
	}
} else { // 若未设置
	setLanguage(); // 自动决定语言
}


if (!defined('Lang')) { // 如果没有支持的语言
	define('Lang', 'en'); // 设为英语
	echo "<div>This project is not available in your language, the following is the English version. <a href=\"?usersettings\" target=\"_blank\">Click here to select language.</div>"; // 输出没有支持的语言提示
}

define("Language", $lang[Lang]); // 定义使用的语言
header('Content-Language: ' . Lang); // 输出响应头