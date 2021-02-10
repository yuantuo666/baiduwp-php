<?php

/**
 * PanDownload 网页复刻版，PHP 语言版函数文件
 *
 * 务必要保证此文件存在，否则整个服务将会不可使用！
 *
 * 请勿随意修改此文件！如需更改相关配置请到 config.php ！
 *
 * @version 1.4.5
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
		header('Refresh: 5;url=https://github.com/yuantuo666/baiduwp-php');
		die("HTTP 503 服务不可用！\r\n缺少相关配置和定义文件！无法正常运行程序！\r\n请重新 Clone 项目并配置！\r\n将在五秒内跳转到 GitHub 储存库！");
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
			dl_error("密码错误", "请检查你输入的密码！");
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
	//如果不修改这里,则要修改配置文件ini
	$result = get($url, $header);
	if (preg_match('/yunData.setData\((\{.*?\})\);/', $result, $matches)) {
		$result = json_decode($matches[1], true, 512, JSON_BIGINT_AS_STRING);
		if (DEBUG) {
			echo '<pre>getSign():';
			var_dump($result);
			echo '</pre>';
		}
		return $result;
	} else {
		if (DEBUG) {
			echo '<pre>getSign():no match</pre>';
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
	return '<p class="card-text">文件名：<b>' . $filename . '</b></p><p class="card-text">文件大小：<b>' . formatSize($size) . '</b></p><p class="card-text">文件MD5：<b>' . $md5
		. '</b></p><p class="card-text">上传时间：<b>' . date("Y年m月d日 H:i:s", $server_ctime) . '</b></p>';
}
function getDlink(string $fs_id, string $timestamp, string $sign, string $randsk, string $share_id, string $uk, string $bdstoken, bool $isnoualink, int $app_id = 250528)
{ // 获取下载链接

	if ($isnoualink) {
		$url = 'https://pan.baidu.com/api/sharedownload?app_id=' . $app_id . '&channel=chunlei&clienttype=0&sign=' . $sign . '&timestamp=' . $timestamp . '&web=1&bdstoken=' . $bdstoken; //获取直链 50MB以内
	} else {
		$url = 'https://pan.baidu.com/api/sharedownload?app_id=' . $app_id . '&channel=chunlei&clienttype=12&sign=' . $sign . '&timestamp=' . $timestamp . '&web=1'; //获取下载链接
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

	//没有 referer 就 112 ，然后没有 sekey 参数就 118    -20出现验证码
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
	echo '</p></div></div></div>';
	return 0;
}
function get_BDCLND($surl, $Pwd)
{
	$header = array('User-Agent: netdisk');
	$url = 'https://pan.baidu.com/share/wxlist?clienttype=25&shorturl=' . $surl . '&pwd=' . $Pwd; //使用新方法获取，减少花费的时间
	$result = head($url, $header);
	$bdclnd = GetSubstr($result, 'BDCLND=', ';');

	if ($bdclnd) {
		if (DEBUG) {
			echo '<pre>get_BDCLND():';
			var_dump($bdclnd);
			echo '</pre>';
		}
		return $bdclnd;
	} else {
		if (DEBUG) {
			echo '<pre>get_BDCLND():';
			var_dump($header);
			echo '</pre>';
		}
		return '';
	}
}
function connectdb(bool $isAPI = false)
{
	$servername = DbConfig["servername"];
	$username = DbConfig["username"];
	$password = DbConfig["password"];
	$dbname = DbConfig["dbname"];
	$GLOBALS['dbtable'] = DbConfig["dbtable"];
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	$GLOBALS['conn'] = $conn;
	// Check connection
	if (!$conn) {
		if ($isAPI) {
			//api特殊处理
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
	$Data = "shorturl=$Shorturl&dir=$Dir&root=$Root&pwd=$Password&page=1&num=2000&order=time";
	$header = array("User-Agent: netdisk", "Referer: https://pan.baidu.com/disk/home");
	$result = json_decode(post($Url, $Data, $header), true);
	if (DEBUG) {
		echo '<pre>GetList():';
		var_dump($result);
		echo '</pre>';
	}
	return $result;
}
