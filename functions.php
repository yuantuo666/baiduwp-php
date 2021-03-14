<?php

/**
 * PanDownload 网页复刻版，PHP 语言版函数文件
 *
 * 务必要保证此文件存在，否则整个服务将会不可使用！
 *
 * 请勿随意修改此文件！如需更改相关配置请到 config.php ！
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
$programVersion_Functions = '2.1.3';
if (!defined('init')) { // 直接访问处理程序
	header('Content-Type: text/plain; charset=utf-8');
	if (!file_exists('config.php')) {
		http_response_code(503);
		header('Content-Type: text/plain; charset=utf-8');
		header('Refresh: 5;url=install.php');
		die("HTTP 503 服务不可用！\r\n暂未安装此程序！\r\n将在五秒内跳转到安装程序！");
	} else {
		require('config.php');
		if ($programVersion_Functions !== programVersion) {
			http_response_code(503);
			header('Content-Type: text/plain; charset=utf-8');
			header('Refresh: 5;url=install.php');
			die("HTTP 503 服务不可用！\r\n配置文件版本异常！\r\n将在五秒内跳转到安装程序！\r\n若重新安装无法解决问题，请重新 Clone 项目并配置！");
		}
	}
	http_response_code(403);
	header('Refresh: 3;url=./');
	define('init', true);
	require('config.php');
	die("HTTP 403 禁止访问！\r\n此文件是 PanDownload 网页复刻版 PHP 语言版项目版本 " . programVersion . " 的有关文件！\r\n禁止直接访问！");
}

// main
function setCurl(&$ch, array $header)
{ // 批处理 curl
	$a = curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 忽略证书
	$b = curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 不检查证书与域名是否匹配（2为检查）
	$c = curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 以字符串返回结果而非输出
	$d = curl_setopt($ch, CURLOPT_HTTPHEADER, $header); // 请求头
	return ($a && $b && $c && $d);
}
function post(string $url, $data, array $header)
{ // POST 发送数据
	$ch = curl_init($url);
	setCurl($ch, $header);
	curl_setopt($ch, CURLOPT_POST, true); // POST 方法
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // POST 的数据
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
function get(string $url, array $header)
{ // GET 请求数据
	$ch = curl_init($url);
	setCurl($ch, $header);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
function head(string $url, array $header)
{ // 获取响应头
	$ch = curl_init($url);
	setCurl($ch, $header);
	curl_setopt($ch, CURLOPT_HEADER, true); // 返回响应头
	curl_setopt($ch, CURLOPT_NOBODY, true); // 只要响应头
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	$response = curl_exec($ch);
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE); // 获得响应头大小
	$result = substr($response, 0, $header_size); // 根据头大小获取头信息
	curl_close($ch);
	return $result;
}
function getSubstr(string $str, string $leftStr, string $rightStr)
{
	$left = strpos($str, $leftStr); // echo '左边:'.$left;
	$right = strpos($str, $rightStr, $left); // echo '<br>右边:'.$right;
	if ($left < 0 || $right < $left) return '';
	$left += strlen($leftStr);
	return substr($str, $left, $right - $left);
}
function formatSize(float $size, int $times = 0)
{ // 格式化size显示 PHP版本过老会报错
	if ($size > 1024) {
		$size /= 1024;
		return formatSize($size, $times + 1); // 递归处理
	} else {
		switch ($times) {
			case '0':
				$unit = ($size == 1) ? 'Byte' : 'Bytes';
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
		return sprintf('%.2f', $size) . $unit;
	}
}
function CheckPassword(bool $IsReturnBool = false)
{ // 校验密码
	if (IsCheckPassword) { // 若校验密码
		$return = false;
		if (!isset($_POST["Password"])) { // 若未传入 Password
			if (isset($_SESSION["Password"]) && $_SESSION["Password"] === Password) { // 若 SESSION 中密码正确
				$return = true;
			}
		} else if ($_POST["Password"] === Password) { // 若传入密码正确
			$_SESSION['Password'] = $_POST["Password"]; // 设置 SESSION
			$return = true;
		}
		if ($IsReturnBool) { // 若 $IsReturnBool 为 true 则只返回 true/false，不执行 dl_error
			return $return;
		}
		if (!$return) { // 若 $IsReturnBool 为 false 且验证失败，则执行 dl_error
			global $system_start_time;
			dl_error("密码错误", "请检查你输入的密码！");
			echo Footer;
			die('</div><script>console.log("后端计算时间：' . (microtime(true) - $system_start_time) . 's");</script></body></html>');
		}
	} else { // 若不校验密码则永远 true
		return true;
	}
}
// 解析分享链接
// 改用微信接口，不需要使用verifyPwd获取randsk
function getSign(string $surl, $randsk)
{
	if ($randsk === 1) return 1;
	$url = 'https://pan.baidu.com/s/1' . $surl;
	$header = array(
		"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36",
		"Cookie: BDUSS=" . BDUSS . ";STOKEN=" . STOKEN . ";BDCLND=" . $randsk . ";"
	);
	// 如果不修改这里,则要修改配置文件ini
	$result = get($url, $header);
	if (preg_match('/yunData.setData\((\{.*?\})\);/', $result, $matches)) {
		$result_decode = json_decode($matches[1], true, 512, JSON_BIGINT_AS_STRING);
		if (DEBUG) {
			echo '<pre>getSign():';
			var_dump($result_decode);
			echo '</pre>';
		}
		return $result_decode;
	} else {
		//有可能是账号被百度拉黑，导致获取到的页面不同 #83 #86
		if (DEBUG) {
			echo '<pre>getSign():no match</pre>';
			var_dump(htmlspecialchars($result));
		}

		if (strstr($result, "neglect:1") != false) {
			dl_error("根目录yunData获取失败", "当前账号已经被百度拉入黑名单<br />无法正常获取文件名及文件内容，请联系站长更换config.php中普通账号的BDUSS和STOKEN<br />此错误出现与会员账号及后台设置无关");
			exit;
		} else {
			dl_error("根目录yunData获取失败", "页面未正常加载，或者百度已经升级页面，无法正常获取根目录yunData数据。");
			// exit;
		}
		return 1;
	}
}
function FileList($sign)
{
	if ($sign === 1) return 1;
	return $sign['file_list'] === null ? 1 : $sign['file_list'];
}
function GetDir(string $dir, string $randsk, string $shareid, string $uk)
{
	$url = 'https://pan.baidu.com/share/list?shareid=' . $shareid . '&uk=' . $uk . '&dir=' . urlencode($dir);
	$header = array(
		"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36",
		"Cookie: BDUSS=" . BDUSS . ";STOKEN=" . STOKEN . ";BDCLND=" . $randsk . ";",
		"Referer: https://pan.baidu.com/disk/home"
	);
	$result = json_decode(get($url, $header), true);
	if (DEBUG) {
		echo '<pre>GetDir():';
		var_dump($result);
		echo '</pre>';
	}
	return $result;
}
function FileInfo(string $filename, float $size, string $md5, int $server_ctime)
{ // 输出 HTML 字符串
	return '<p class="card-text"  id="filename" >文件名：<b>' . $filename . '</b></p><p class="card-text">文件大小：<b>' . formatSize($size) . '</b></p><p class="card-text">文件MD5：<b>' . $md5
		. '</b></p><p class="card-text">上传时间：<b>' . date("Y年m月d日 H:i:s", $server_ctime) . '</b></p>';
}
function getDlink(string $fs_id, string $timestamp, string $sign, string $randsk, string $share_id, string $uk, string $bdstoken, bool $isnoualink, int $app_id = 250528)
{ // 获取下载链接

	if ($isnoualink) {
		$url = 'https://pan.baidu.com/api/sharedownload?app_id=' . $app_id . '&channel=chunlei&clienttype=0&sign=' . $sign . '&timestamp=' . $timestamp . '&web=1&bdstoken=' . $bdstoken; // 获取直链 50MB以内
	} else {
		$url = 'https://pan.baidu.com/api/sharedownload?app_id=' . $app_id . '&channel=chunlei&clienttype=12&sign=' . $sign . '&timestamp=' . $timestamp . '&web=1'; // 获取下载链接
	}

	$data = "encrypt=0" . "&extra=" . urlencode('{"sekey":"' . urldecode($randsk) . '"}') . "&fid_list=[$fs_id]" . "&primaryid=$share_id" . "&uk=$uk" . "&product=share&type=nolimit";
	$header = array(
		"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36",
		"Cookie: BDUSS=" . BDUSS . ";STOKEN=" . STOKEN . ";BDCLND=" . $randsk . ";",
		"Referer: https://pan.baidu.com/disk/home"
	);
	$result = json_decode(post($url, $data, $header), true);
	if (DEBUG) {
		echo '<pre>getDlink():';
		var_dump($result);
		echo '</pre>';
	}
	return $result;

	// 没有 referer 就 112 ，然后没有 sekey 参数就 118    -20出现验证码
	// 		参数				类型		描述
	// list					json array	文件信息列表
	// names				json		如果查询共享目录，该字段为共享目录文件上传者的uk和账户名称
	// list[0]["category"]	int			文件类型
	// list[0]["dlink"]		string		文件下载地址
	// list[0]["file_name"]	string		文件名
	// list[0]["isdir"]		int			是否是目录
	// list[0]["server_ctime"]	int		文件的服务器创建时间
	// list[0]["server_mtime"]	int		文件的服务修改时间
	// list[0]["size"]		int			文件大小
	// list[0]["thumbs"]				缩略图地址
	// list[0]["height"]	int			图片高度
	// list[0]["width"]		int			图片宽度
	// list[0]["date_taken"]	int		图片拍摄时间
}
function dl_error(string $title, string $content, bool $jumptip = false)
{
	if ($jumptip) {
		$content .= '<br>请打开调试模式，并将错误信息复制提交issue到<a href="https://github.com/yuantuo666/baiduwp-php">github项目</a>。';
	}
	if (Language["LanguageName"] != "Chinese") {
		$content = "To know more about it, you can translate the information following.<br />Raw Title:$title<br />Raw Message:$content<br /><br />If you still have question, please copy the information and sent to the email(yuantuo666@gmail.com) for help.";
		$title = "An error happened.";
	}
	echo '<div class="row justify-content-center"><div class="col-md-7 col-sm-8 col-11"><div class="alert alert-danger" role="alert">
	<h5 class="alert-heading">' . $title . '</h5><hr /><p class="card-text">' . $content;
	echo '</p></div></div></div>'; // 仅仅弹出提示框，并不结束进程
}
function get_BDCLND($surl, $Pwd)
{
	$header = array('User-Agent: netdisk');
	$url = 'https://pan.baidu.com/share/wxlist?clienttype=25&shorturl=' . $surl . '&pwd=' . $Pwd; // 使用新方法获取，减少花费的时间
	$result = head($url, $header);
	if (strstr($result, "BDCLND") == false) $bdclnd = false; // 修复：部分链接不存在bdclnd
	else $bdclnd = GetSubstr($result, 'BDCLND=', ';');

	if ($bdclnd) {
		if (DEBUG) {
			echo '<pre>get_BDCLND():';
			var_dump($bdclnd);
			echo '</pre>';
		}
		return $bdclnd;
	} else {
		if (DEBUG) {
			echo '<pre>【获取bdclnd失败，可能是不需要此参数】get_BDCLND():';
			var_dump($result);
			echo '</pre>';
		}
		echo '<script>Swal.fire("使用提示","检测到当前链接异常，保存到网盘重新分享后可获得更好的体验~","info");</script>';
		// 尝试使用老方法获取
		$header = head("https://pan.baidu.com/s/" . $surl, []);
		$bdclnd = preg_match('/BDCLND=(.+?);/', $header, $matches);
		if ($bdclnd) {
			if (DEBUG) {
				echo '<pre>【老版本方法】get_BDCLND():';
				var_dump($matches[1]);
				echo '</pre>';
			}
			return $matches[1];
		} else {
			if (DEBUG) {
				echo '<pre>【老版本方法】get_BDCLND():';
				var_dump($header);
				echo '</pre>';
			}
			return '';
		}
	}
}
function connectdb(bool $isAPI = false)
{
	$servername = DbConfig["servername"];
	$username = DbConfig["username"];
	$DBPassword = DbConfig["DBPassword"];
	$dbname = DbConfig["dbname"];
	$GLOBALS['dbtable'] = DbConfig["dbtable"];
	$conn = mysqli_connect($servername, $username, $DBPassword, $dbname);
	$GLOBALS['conn'] = $conn;
	// Check connection
	if (!$conn) {
		if ($isAPI) {
			// api特殊处理
			EchoInfo(-1, array("msg" => "数据库连接失败：" . mysqli_connect_error(), "sviptips" => "Error"));
			exit;
		} else {
			dl_error("服务器错误", "数据库连接失败：" . mysqli_connect_error());
			exit;
		}
	}
	mysqli_query($conn, "set sql_mode = ''");
	mysqli_query($conn, "set character set 'utf8'");
	mysqli_query($conn, "set names 'utf8'");
}
function GetList(string $Shorturl, string $Dir, bool $IsRoot, string $Password)
{
	$Url = 'https://pan.baidu.com/share/wxlist?channel=weixin&version=2.2.2&clienttype=25&web=1';

	$Root = ($IsRoot) ? "1" : "0";
	$Dir = urlencode($Dir);
	$Data = "shorturl=$Shorturl&dir=$Dir&root=$Root&pwd=$Password&page=1&num=1000&order=time";
	$header = array("User-Agent: netdisk", "Referer: https://pan.baidu.com/disk/home");
	$result = json_decode(post($Url, $Data, $header), true);
	if (DEBUG) {
		echo '<pre>GetList():';
		var_dump($result);
		echo '</pre>';
	}
	return $result;
}
$getConstant = function (string $name) {
	return constant($name);
};
/* 
 * 优化 JavaScript 代码体积
 * beta 版本
 */
$JSCode = array("get" => function (string $value) {
	$value = preg_replace('# *//.*#', '', $value);
	$value = preg_replace('#/\*.*?\*/#s', '', $value);
	$value = preg_replace('#(\r?\n|\t| ){2,}#', '$1', $value);
	$value = preg_replace('#([,;{])[ \t]*?\r?\n[ \t]*([^ \t])#', '$1 $2', $value);
	$value = preg_replace('#(\r?\n|\t| ){2,}#', '$1', $value);
	$value = preg_replace('#([^ \t])[ \t]*?\r?\n[ \t]*?\}#', '$1 }', $value);
	$value = preg_replace('#(\r?\n|\t| ){2,}#', '$1', $value);
	$value = preg_replace('#([,;{])\t+#', '$1 ', $value);
	$value = preg_replace('#\t+\}#', ' }', $value);
	$value = preg_replace('#(\r?\n|\t| ){2,}#', '$1', $value);
	return $value;
}, "echo" => function (string $value) {
	global $JSCode;
	echo $JSCode['get']($value);
});
/* 
 * 将settings.php里面的代码移到functions.php里面来
 * 方便api调用
 */
function EchoInfo(int $error, array $Result)
{
	$ReturnArray = array("error" => $error);
	$ReturnArray += $Result;
	echo json_encode($ReturnArray);
}
function GetAnalyseTablePage(string $page)
{
	if ($page <= 0) exit;
	$EachPageNum = 10;
	$conn = $GLOBALS['conn'];
	$dbtable = $GLOBALS['dbtable'];
	$AllRow = "";
	$StartNum = ((int)$page - 1) * $EachPageNum;
	$sql = "SELECT * FROM `$dbtable` ORDER BY `ptime` DESC LIMIT $StartNum,$EachPageNum";
	$mysql_query = mysqli_query($conn, $sql);
	while ($Result = mysqli_fetch_assoc($mysql_query)) {
		// 存在数据
		$EachRow = "<tr>
		<th>" . $Result["id"] . "</th>
		<td><div class=\"btn-group btn-group-sm\" role=\"group\">
			<a class=\"btn btn-secondary\" href=\"javascript:DeleteById('AnalyseTable'," . $Result["id"] . ");\">删除</a>
		</div></td>
		<td>" . $Result["userip"] . "</td>
		<td style=\"width:80px;\">" . $Result["filename"] . "</td>
		<td>" . formatSize((float)$Result["size"]) . "</td>
		<td style=\"width:50px;\">" . $Result["path"] . "</td>
		<td><a href=\"https://" . $Result["realLink"] . "\">" . substr($Result["realLink"], 0, 35) . "……</a></td>
		<td>" . $Result["ptime"] . "</td><td>" . $Result["paccount"] . "</td>
		</tr>";
		$AllRow .= $EachRow;
	}
	return $AllRow;
}
function GetSvipTablePage(string $page)
{
	if ($page <= 0) exit;
	$EachPageNum = 10;
	$conn = $GLOBALS['conn'];
	$dbtable = $GLOBALS['dbtable'];
	$AllRow = "";
	$StartNum = ((int)$page - 1) * $EachPageNum;
	$sql = "SELECT * FROM `" . $dbtable . "_svip` ORDER BY `id` DESC LIMIT $StartNum,$EachPageNum";
	$mysql_query = mysqli_query($conn, $sql);
	while ($Result = mysqli_fetch_assoc($mysql_query)) {
		// 存在数据
		$is_using = ($Result["is_using"] != "0000-00-00 00:00:00") ? $Result["is_using"] : "";
		$state = ($Result["state"] == -1) ? "限速" : "正常";
		$EachRow = "<tr>
		<th>" . $Result["id"] . "</th>
		<td><div class=\"btn-group btn-group-sm\" role=\"group\">
			<a class=\"btn btn-secondary\" href=\"javascript:SettingFirstAccount(" . $Result["id"] . ");\">使用此账号</a>
			<a class=\"btn btn-secondary\" href=\"javascript:SettingNormalAccount(" . $Result["id"] . ");\">重置状态</a>
			<a class=\"btn btn-secondary\" href=\"javascript:DeleteById('SvipTable'," . $Result["id"] . ");\">删除</a>
		</div></td>
		<td>" .  $is_using . "</td>
		<td>" . $Result["name"] . "</td>
		<td>" . $state . "</td>
		<td>" . $Result["add_time"] . "</td>
		<td><a href=\"javascript:Swal.fire('" . $Result["svip_bduss"] . "')\">" . substr($Result["svip_bduss"], 0, 20) . "……</a></td>
		<td><a href=\"javascript:Swal.fire('" . $Result["svip_stoken"] . "')\">" . substr($Result["svip_stoken"], 0, 20) . "……</a></td>
		</tr>";
		$AllRow .= $EachRow;
	}
	return $AllRow;
} // name 账号名称	svip_bduss 会员bduss	svip_stoken 会员stoken	add_time 会员账号加入时间	state 会员状态(0:正常,-1:限速)	is_using 是否正在使用(非零表示真)
function GetIPTablePage(string $page)
{
	if ($page <= 0) exit;
	$EachPageNum = 10;
	$conn = $GLOBALS['conn'];
	$dbtable = $GLOBALS['dbtable'];
	$AllRow = "";
	$StartNum = ((int)$page - 1) * $EachPageNum;
	$sql = "SELECT * FROM `" . $dbtable . "_ip` ORDER BY `id` DESC LIMIT $StartNum,$EachPageNum";
	$mysql_query = mysqli_query($conn, $sql);
	while ($Result = mysqli_fetch_assoc($mysql_query)) {
		// 存在数据
		$type = ($Result["type"] == -1) ? "黑名单" : "白名单";
		$EachRow = "<tr>
		<th>" . $Result["id"] . "</th>
		<td><div class=\"btn-group btn-group-sm\" role=\"group\">
			<a class=\"btn btn-secondary\" href=\"javascript:DeleteById('IPTable'," . $Result["id"] . ");\">删除</a>
		</div></td>
		<td>" . $Result["ip"] . "</td>
		<td>" . $type . "</td>
		<td>" . $Result["remark"] . "</td>
		<td>" . $Result["add_time"] . "</td>
		</tr>";
		$AllRow .= $EachRow;
	}
	return $AllRow;
}
