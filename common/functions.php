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
if (!init) require_once("./common/invalidCheck.php");

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
	if ($response === false) {
		// get error msg
		$error = curl_error($ch);
		// close curl
		curl_close($ch);
		// return error msg
		return $error;
	}
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE); // 获得响应头大小
	$result = substr($response, 0, $header_size); // 根据头大小获取头信息
	curl_close($ch);
	return $result;
}
function getSubstr(string $str, string $leftStr, string $rightStr)
{
	$left = strpos($str, $leftStr);
	if ($left === false) return "";
	$left += strlen($leftStr);
	$right = strpos($str, $rightStr, $left);
	if ($right === false) return "";
	return substr($str, $left, $right - $left);
}
function formatSize(float $size, int $times = 0)
{
	$unit = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
	if ($size >= 1024) {
		$size /= 1024;
		$times++;
		return formatSize($size, $times);
	} else {
		return round($size, 2) . $unit[$times];
	}
}
function CheckPassword(bool $IsReturnBool = false)
{
	if (!IsCheckPassword) {
		return true;
	}
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
}
function GetSign(string $surl = "", string $share_id = "", string $uk = "")
{
	// construct url
	$parmas = "";
	if ($surl) $parmas .= "&surl=$surl";
	if ($share_id) $parmas .= "&shareid=$share_id";
	if ($uk) $parmas .= "&uk=$uk";
	$url = "https://pan.baidu.com/share/tplconfig?$parmas&fields=sign,timestamp&channel=chunlei&web=1&app_id=250528&clienttype=0";
	$header = [
		"User-Agent: netdisk;pan.baidu.com",
		"Cookie: " . Cookie,
	];
	$result = get($url, $header);
	$result = json_decode($result, true, 512, JSON_BIGINT_AS_STRING);
	if (($result["errno"] ?? 1) == 0) {
		$sign = $result["data"]["sign"];
		$timestamp = $result["data"]["timestamp"];
		return [0, $sign, $timestamp];
	} else {
		return [-1, $result["show_msg"] ?? "", ""];
	}
}
function FileInfo(string $filename, float $size, string $md5, int $server_ctime)
{ // 输出 HTML 字符串
	return '<p class="card-text" id="filename" >文件名：<b>' . $filename .
		'</b></p><p class="card-text">文件大小：<b>' . formatSize($size) .
		'</b></p><p class="card-text">文件MD5：<b>' . $md5 .
		'</b></p><p class="card-text">上传时间：<b>' . date("Y年m月d日 H:i:s", $server_ctime) . '</b></p>';
}
function getDlink(string $fs_id, string $timestamp, string $sign, string $randsk, string $share_id, string $uk, int $app_id = 250528)
{ // 获取下载链接
	$url = 'https://pan.baidu.com/api/sharedownload?app_id=' . $app_id . '&channel=chunlei&clienttype=12&sign=' . $sign . '&timestamp=' . $timestamp . '&web=1'; // 获取下载链接

	if (strstr($randsk, "%") != false) $randsk = urldecode($randsk);
	$data = "encrypt=0" . "&extra=" . urlencode('{"sekey":"' . $randsk . '"}') . "&fid_list=[$fs_id]" . "&primaryid=$share_id" . "&uk=$uk" . "&product=share&type=nolimit";
	$header = array(
		"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36 Edg/110.0.1587.69",
		"Cookie: " . Cookie,
		"Referer: https://pan.baidu.com/disk/home"
	);
	$result = json_decode(post($url, $data, $header), true);
	if (DEBUG) {
		echo '<script>console.log("getDlink():", ' . json_encode($result) . ');</script>';
	}
	return $result;
	// 没有 referer 就 112, 没有 sekey 参数就 118, -20出现验证码
}
function dl_error(string $title, string $content, bool $jumptip = false)
{
	if ($jumptip) {
		$content .= '<br>1. 请打开调试模式：将 config.php 文件中 DEBUG 的 false 修改为 true。<br>2. 刷新该页面并将错误信息以议题的形式提交到<a href="https://github.com/yuantuo666/baiduwp-php/issues">GitHub项目</a>。';
	}
	echo '<div class="row justify-content-center"><div class="col-md-7 col-sm-8 col-11"><div class="alert alert-danger" role="alert">
	<h5 class="alert-heading">' . $title . '</h5><hr /><p class="card-text">' . $content;
	echo '</p></div></div></div>';
	die();
}
function connectdb(bool $isAPI = false)
{
	$servername = DbConfig["servername"];
	$username = DbConfig["username"];
	$DBPassword = DbConfig["DBPassword"];
	$dbname = DbConfig["dbname"];
	$GLOBALS['dbtable'] = DbConfig["dbtable"];
	$conn = mysqli_init();
	mysqli_options($conn, MYSQLI_OPT_LOCAL_INFILE, false); // 感谢 unc1e 披露的漏洞
	$m = mysqli_real_connect($conn, $servername, $username, $DBPassword, $dbname, 3306);
	// $conn = mysqli_connect($servername, $username, $DBPassword, $dbname);

	// Check connection
	if (!$m) {
		if ($isAPI) {
			// api特殊处理
			EchoInfo(-1, array("msg" => "数据库连接失败：" . mysqli_connect_error(), "sviptips" => "Error"));
			exit;
		} else {
			dl_error("服务器错误", "数据库连接失败：" . mysqli_connect_error());
		}
	}
	$GLOBALS['conn'] = $conn;

	mysqli_query($conn, "set sql_mode = ''");
	mysqli_query($conn, "set character set 'utf8'");
	mysqli_query($conn, "set names 'utf8'");
}
function GetList(string $Shorturl, string $Dir, bool $IsRoot, string $Password, int $Page = 1)
{
	$Url = 'https://pan.baidu.com/share/wxlist?channel=weixin&version=2.2.2&clienttype=25&web=1';

	$Root = ($IsRoot) ? "1" : "0";
	$Dir = urlencode($Dir);
	$Data = "shorturl=$Shorturl&dir=$Dir&root=$Root&pwd=$Password&page=$Page&num=1000&order=time";
	$BDUSS = getSubstr(Cookie, 'BDUSS=', ';');
	$header = ["User-Agent: netdisk", "Cookie: BDUSS=$BDUSS", "Referer: https://pan.baidu.com/disk/home"];
	$result = json_decode(post($Url, $Data, $header), true);
	if (DEBUG) {
		echo '<script>console.log("GetList():");console.log(' . json_encode($result) . ');</script>';
	}
	return $result;
}
$getConstant = function (string $name) {
	return constant($name);
};
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
	$page = (int)$page;
	if ($page <= 0) exit;
	$EachPageNum = 10;
	$conn = $GLOBALS['conn'];
	$dbtable = $GLOBALS['dbtable'];
	$AllRow = "";
	$StartNum = ($page - 1) * $EachPageNum;
	$sql = "SELECT * FROM `$dbtable` ORDER BY `ptime` DESC LIMIT $StartNum,$EachPageNum";
	$mysql_query = mysqli_query($conn, $sql);
	while ($Result = mysqli_fetch_assoc($mysql_query)) {
		// 存在数据
		$EachRow = "<tr>" .
			"<th>{$Result["id"]}</th>" .
			"<td><div class=\"btn-group btn-group-sm\" role=\"group\">" .
			"<a class=\"btn btn-secondary\" href=\"javascript:DeleteById('AnalyseTable',{$Result["id"]});\">删除</a>" .
			"</div></td>" .
			"<td>{$Result["userip"]}</td>" .
			"<td style=\"width:80px;\">{$Result["filename"]}</td>" .
			"<td>" . formatSize((float)$Result["size"]) . "</td>" .
			"<td style=\"width:50px;\">{$Result["path"]}</td>" .
			"<td><a href=\"https://{$Result["realLink"]}\">" . substr($Result["realLink"], 0, 35) . "……</a></td>" .
			"<td>{$Result["ptime"]}</td><td>{$Result["paccount"]}</td>" .
			"</tr>";

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
	$sql = "SELECT * FROM `{$dbtable}_svip` ORDER BY `id` DESC LIMIT $StartNum,$EachPageNum";
	$mysql_query = mysqli_query($conn, $sql);
	while ($Result = mysqli_fetch_assoc($mysql_query)) {
		// 存在数据
		$is_using = ($Result["is_using"] != "0000-00-00 00:00:00") ? $Result["is_using"] : "";
		$state = ($Result["state"] == -1) ? "限速" : "正常";
		$EachRow = "<tr>" .
			"<th>{$Result["id"]}</th>" .
			"<td><div class=\"btn-group btn-group-sm\" role=\"group\">" .
			"<a class=\"btn btn-secondary\" href=\"javascript:SettingFirstAccount({$Result["id"]});\">使用此账号</a>" .
			"<a class=\"btn btn-secondary\" href=\"javascript:SettingNormalAccount({$Result["id"]});\">重置状态</a>" .
			"<a class=\"btn btn-secondary\" href=\"javascript:DeleteById('SvipTable',{$Result["id"]});\">删除</a>" .
			"</div></td>" .
			"<td>{$is_using}</td>" .
			"<td>{$Result["name"]}</td>" .
			"<td>{$state}</td>" .
			"<td>{$Result["add_time"]}</td>" .
			"<td><a href=\"javascript:Swal.fire('{$Result["svip_bduss"]}');\">" . substr($Result["svip_bduss"], 0, 20) . "……</a></td>" .
			"<td><a href=\"javascript:Swal.fire('{$Result["svip_stoken"]}');\">" . substr($Result["svip_stoken"], 0, 20) . "……</a></td>" .
			"</tr>";
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
	$sql = "SELECT * FROM `{$dbtable}_ip` ORDER BY `id` DESC LIMIT $StartNum,$EachPageNum";
	$mysql_query = mysqli_query($conn, $sql);
	while ($Result = mysqli_fetch_assoc($mysql_query)) {
		// 存在数据
		$type = ($Result["type"] == -1) ? "黑名单" : "白名单";
		$EachRow = "<tr>" .
			"<th>{$Result["id"]}</th>" .
			"<td><div class=\"btn-group btn-group-sm\" role=\"group\">" .
			"<a class=\"btn btn-secondary\" href=\"javascript:DeleteById('IPTable',{$Result["id"]});\">删除</a>" .
			"</div></td>" .
			"<td>{$Result["ip"]}</td>" .
			"<td>{$type}</td>" .
			"<td>{$Result["remark"]}</td>" .
			"<td>{$Result["add_time"]}</td>" .
			"</tr>";
		$AllRow .= $EachRow;
	}
	return $AllRow;
}
/**
 * 获取数据库中的BDUSS数据
 *
 * @return array 返回BDUSS和对应id id=-1表示本地解析
 */
function GetDBBDUSS()
{
	global $dbtable, $conn;
	// 获取SVIP BDUSS
	switch (SVIPSwitchMod) {
		case 1:
			//模式1：用到废为止
			// 时间倒序输出第一项未被限速账号
			$sql = "SELECT `id`,`svip_bduss`,`svip_stoken` FROM `{$dbtable}_svip` WHERE `state`!=-1 ORDER BY `is_using` DESC,`id` DESC LIMIT 0,1";
			$Result = mysqli_query($conn, $sql);
			if ($Result =  mysqli_fetch_assoc($Result)) {
				$SVIP_BDUSS = $Result["svip_bduss"];
				$SVIP_STOKEN = $Result["svip_stoken"];
				$id = $Result["id"];
			} else {
				// 数据库中所有SVIP账号已经用完，启用本地SVIP账号
				$SVIP_BDUSS = SVIP_BDUSS;
				$SVIP_STOKEN = SVIP_STOKEN;
				$id = "-1";
			}
			break;
		case 2:
			//模式2：轮番上
			// 时间顺序输出第一项未被限速账号
			$sql = "SELECT `id`,`svip_bduss`,`svip_stoken` FROM `{$dbtable}_svip` WHERE `state`!=-1 ORDER BY `is_using` ASC,`id` DESC LIMIT 0,1";

			$Result = mysqli_query($conn, $sql);
			if ($Result =  mysqli_fetch_assoc($Result)) {
				$SVIP_BDUSS = $Result["svip_bduss"];
				$SVIP_STOKEN = $Result["svip_stoken"];
				$id = $Result["id"];
				//不论解析成功与否，将当前账号更新时间，下一次使用另一账号
				// 开始处理
				// 这里最新的时间表示可用账号，按顺序排序
				$is_using = date("Y-m-d H:i:s");
				$sql = "UPDATE `{$dbtable}_svip` SET `is_using`= '$is_using' WHERE `id`=$id";
				$mysql_query = mysqli_query($conn, $sql);
				if ($mysql_query == false) {
					// 失败 但可继续解析
					dl_error("数据库错误", "请联系站长修复无法自动切换账号问题！");
				}
			} else {
				// 数据库中所有SVIP账号已经用完，启用本地SVIP账号
				$SVIP_BDUSS = SVIP_BDUSS;
				$SVIP_STOKEN = SVIP_STOKEN;
				$id = "-1";
			}
			break;
		case 3:
			//模式3：手动切换，不管限速
			// 时间倒序输出第一项账号，不管限速
			$sql = "SELECT `id`,`svip_bduss`,`svip_stoken` FROM `{$dbtable}_svip` ORDER BY `is_using` DESC,`id` DESC LIMIT 0,1";
			$Result = mysqli_query($conn, $sql);
			if ($Result =  mysqli_fetch_assoc($Result)) {
				$SVIP_BDUSS = $Result["svip_bduss"];
				$SVIP_STOKEN = $Result["svip_stoken"];
				$id = $Result["id"];
			} else {
				// 数据库中所有SVIP账号已经用完，启用本地SVIP账号
				$SVIP_BDUSS = SVIP_BDUSS;
				$SVIP_STOKEN = SVIP_STOKEN;
				$id = "-1";
			}
			break;
		case 4:
			//模式4：轮番上(无视限速)
			// 时间顺序输出第一项限速账号
			$sql = "SELECT `id`,`svip_bduss`,`svip_stoken` FROM `{$dbtable}_svip` ORDER BY `is_using` ASC,`id` DESC LIMIT 0,1";

			$Result = mysqli_query($conn, $sql);
			if ($Result =  mysqli_fetch_assoc($Result)) {
				$SVIP_BDUSS = $Result["svip_bduss"];
				$SVIP_STOKEN = $Result["svip_stoken"];
				$id = $Result["id"];
				//不论解析成功与否，将当前账号更新时间，下一次使用另一账号
				// 开始处理
				// 这里最新的时间表示可用账号，按顺序排序
				$is_using = date("Y-m-d H:i:s");
				$sql = "UPDATE `{$dbtable}_svip` SET `is_using`= '$is_using' WHERE `id`=$id";
				$mysql_query = mysqli_query($conn, $sql);
				if ($mysql_query == false) {
					// 失败 但可继续解析
					dl_error("数据库错误", "请联系站长修复无法自动切换账号问题！");
				}
			} else {
				// 数据库中所有SVIP账号已经用完，启用本地SVIP账号
				$SVIP_BDUSS = SVIP_BDUSS;
				$SVIP_STOKEN = SVIP_STOKEN;
				$id = "-1";
			}
			break;
		case 0:
			//模式0：使用本地解析
		default:
			$SVIP_BDUSS = SVIP_BDUSS;
			$SVIP_STOKEN = SVIP_STOKEN;
			$id = "-1";
			break;
	}
	return [$SVIP_BDUSS, $id, $SVIP_STOKEN];
}
/**
 * 用于获取账号状态
 *
 * @return array [errno,会员状态,用户名,登录状态,会员剩余时间]
 */
function AccountStatus(string $BDUSS, string $STOKEN)
{
	$Url = "https://pan.baidu.com/api/gettemplatevariable?channel=chunlei&web=1&app_id=250528&clienttype=0";
	$Header = [
		"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36",
		"Cookie: BDUSS=$BDUSS;STOKEN=$STOKEN;"
	];
	$Data = "fields=[%22username%22,%22loginstate%22,%22is_vip%22,%22is_svip%22,%22is_evip%22]";
	$Result = post($Url, $Data, $Header);
	$Result = json_decode($Result, true);
	if (DEBUG) {
		echo '<script>console.log("账号状态:");console.log(' . json_encode($Result) . ');</script>';
	}
	if ($Result["errno"] == 0) {
		// 正常
		$Username = $Result["result"]["username"];
		$LoginStatus = $Result["result"]["loginstate"];
		if ($Result["result"]["is_vip"] == 1) {
			$SVIP = 1; //会员账号
		} elseif ($Result["result"]["is_svip"] == 1 or $Result["result"]["is_evip"] == 1) {
			$SVIP = 2; //超级会员账号
		} else {
			$SVIP = 0; //普通账号
			return array(0, $SVIP, $Username, $LoginStatus, 0);
		}

		$Url = "https://pan.baidu.com/rest/2.0/membership/user?channel=chunlei&web=1&app_id=250528&clienttype=0";
		$Header = [
			"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36",
			"Cookie: BDUSS=$BDUSS;STOKEN=$STOKEN;"
		];
		$Data = "method=query";
		$Result = post($Url, $Data, $Header);
		$Result = json_decode($Result, true);
		if (DEBUG) {
			echo '<script>console.log("会员状态:");console.log(' . json_encode($Result) . ');</script>';
		}
		if (isset($Result["reminder"]["svip"])) {
			//存在会员信息
			$LeftSeconds = $Result["reminder"]["svip"]["leftseconds"];
			return array(0, $SVIP, $Username, $LoginStatus, $LeftSeconds);
		}
	} elseif ($Result["errno"] == -6) {
		// 账号失效
		return array(-6);
	} else {
		//未知错误
		return array($Result["errno"]);
	}
}
/**
 * 时间差计算
 *
 * @param Timestamp $time
 * @return String Time Elapsed
 * @author Shelley Shyan
 * @copyright http://phparch.cn (Professional PHP Architecture)
 */
function time2Units($time)
{
	$time = (int)$time;
	$year   = floor($time / 60 / 60 / 24 / 365);
	$time  -= $year * 60 * 60 * 24 * 365;
	$month  = floor($time / 60 / 60 / 24 / 30);
	$time  -= $month * 60 * 60 * 24 * 30;
	$day    = floor($time / 60 / 60 / 24);
	$time  -= $day * 60 * 60 * 24;
	$hour   = floor($time / 60 / 60);
	$time  -= $hour * 60 * 60;
	$minute = floor($time / 60);
	$time  -= $minute * 60;
	$second = $time;
	$elapse = '';

	$unitArr = array(
		'年'  => 'year', '个月' => 'month',  '天' => 'day',
		'小时' => 'hour', '分钟' => 'minute', '秒' => 'second'
	);

	foreach ($unitArr as $cn => $u) {
		if ($$u > 0) {
			$elapse = $$u . $cn;
			break;
		}
	}

	return $elapse;
}


function CheckUpdate(bool $includePreRelease = false, bool $enforce = false, array $info = []) // 检查更新程序
{

	$filePath = "update.json"; // 缓存文件路径
	if (!function_exists('download')) { // 似乎是 PHP 的 Bug ，无语了，下同
		function download(bool $includePreRelease, array &$info) // 下载
		{
			$header = array(
				"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 Edg/91.0.864.67"
			);
			if ($includePreRelease) { // 若包括 Pre-Release
				array_push($info, "Include-PreRelease");
				$result = getAPI('https://api.github.com/repos/yuantuo666/baiduwp-php/releases', $header);
				if ($result) {
					return json_decode($result, true)[0]; // 返回首个结果
				}
				array_push($info, "API-Download-Newest-Error");
				return false;
			} else { // 若不包括
				$result = getAPI('https://api.github.com/repos/yuantuo666/baiduwp-php/releases/latest', $header);
				if ($result) {
					return json_decode($result, true);
				}
				array_push($info, "API-Download-Latest-Error");
				return false;
			}
		}
	}
	if (!function_exists('downloadError')) {
		function downloadError(array &$info) // 下载失败
		{
			array_push($info, "Download-Error");
			return array("code" => 1, "info" => $info);
		}
	}
	if (!function_exists('getAPI')) {
		function getAPI(string $url, array $header) // 获取 API 数据
		{
			$ch = curl_init($url);
			setCurl($ch, $header);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 设置超时时间为 10s
			$result = curl_exec($ch);
			curl_close($ch);
			return $result;
		}
	}
	if ($enforce) { // 是否强制检查更新
		array_push($info, "Enforce-Check");
		$result = download($includePreRelease, $info); // 下载
		if (!$result) { // 若出错则直接 return
			return downloadError($info);
		}
	} else { // 不强制检查更新
		if (file_exists($filePath)) { // 检查更新缓存是否存在
			$lastm = filemtime($filePath); // 获取文件最后修改时间
			if ((!$lastm) || ($lastm + 3600 < time())) { // 获取失败或超时（一小时）则重新获取
				if (!$lastm) {
					array_push($info, "CacheFile-Get-LastM-Error"); // 获取失败
				} else {
					array_push($info, "CacheFile-Expired"); // 超时
				}
				$result = download($includePreRelease, $info); // 下载并检查是否出错
				if (!$result) {
					return downloadError($info);
				}
			} else {
				$file = fopen($filePath, "r"); // 打开文件
				if ($file) { // 若打开成功
					$result = fread($file, filesize($filePath)); // 读取文件
					if (!$result) { // 若读取失败
						array_push($info, "Read-CacheFile-Error");
						$result = download($includePreRelease, $info); // 下载并检查是否出错
						if (!$result) {
							return downloadError($info);
						}
					} else { // 若读取成功
						$result = json_decode($result, true); // 解码
						if (isset($result['prerelease'])) { // 测试是否包含 PreRelease（检查缓存文件是否存在问题）
							if ($result['prerelease'] && !$includePreRelease) { // 若不检查预发行版本但缓存为预发行
								array_push($info, "CacheFile-Is-PreRelease-But-Exclude-It");
								$result = download($includePreRelease, $info); // 下载并检查是否出错
								if (!$result) {
									return downloadError($info);
								}
							} else if (!$result['prerelease'] && $includePreRelease) { // 若检查预发行但缓存非预发行（这个只是用来防止万一，所以下载失败了不终止）
								array_push($info, "CacheFile-Isnot-PreRelease-But-Include-It--Try-To-Get");
								$download_result = download($includePreRelease, $info); // 下载并检查是否出错
								if (!$download_result) { // 下载失败的话还使用缓存
									array_push($info, "Download-Error");
									array_push($info, "Use-Cache");
								} else { // 若下载成功则用新的
									$result = $download_result;
								}
							} else { // 没有问题，使用缓存
								array_push($info, "Use-Cache");
							}
						} else { // 缓存文件存在问题
							array_push($info, "Invalid-CacheFile");
							$result = download($includePreRelease, $info); // 下载并检查是否出错
							if (!$result) {
								return downloadError($info);
							}
						}
					}
					fclose($file); // 关闭文件
				} else { // 打开失败
					array_push($info, "Open-CacheFile-Error");
					$result = download($includePreRelease, $info); // 下载并检查是否出错
					if (!$result) {
						return downloadError($info);
					}
				}
			}
		} else { // 文件不存在
			array_push($info, "No-CacheFile");
			$result = download($includePreRelease, $info); // 下载并检查是否出错
			if (!$result) {
				return downloadError($info);
			}
		}
	}

	if (!(isset($result['tag_name']) && isset($result['assets']) && isset($result['html_url']))) { // 若缓存文件存在问题
		array_push($info, "Invalid-CacheFile");
		$result = download($includePreRelease, $info); // 下载并检查是否出错
		if (!$result) {
			return downloadError($info);
		}
	}

	$version = substr($result['tag_name'], 1); // 解析数据
	$isPreRelease = $result['prerelease'];
	$url = "";
	$page_url = $result['html_url'];
	foreach ($result['assets'] as &$asset) {
		if ($asset['name'] === 'ProgramFiles.zip') {
			$url = $asset['browser_download_url'];
			break;
		}
	}

	$file = fopen($filePath, "w"); // 打开文件
	if ($file) { // 若打开成功
		fwrite($file, json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); // 写入文件
		if (!$result) { // 若写入失败
			array_push($info, "Write-NewFile-Error");
		}
		fclose($file); // 关闭文件
	} else { // 打开失败
		array_push($info, "Open-NewFile-Error");
	}


	$commonReturn = array(
		"code" => 0, "version" => $version, "PreRelease" => $isPreRelease,
		"file_url" => $url, "page_url" => $page_url, "info" => $info, "now_version" => programVersion
	);
	$compare = version_compare(programVersion, $version); // 比较版本
	if ($compare === -1 || $compare === 0) { // 更新或相同
		$commonReturn['have_update'] = ($compare === -1) ? true : false; // 更新则为 true
		return $commonReturn;
	} else { // 版本存在问题（比最新版还高？）
		if (in_array('Try-Get-Version-Include-PreRelease', $info)) { // 若已尝试获取预发行，则直接返回版本有误提示
			array_push($info, "Invalid-Version");
			$commonReturn['code'] = 2;
			$commonReturn['info'] = $info;
			return $commonReturn;
		} else { // 试图强制检查预发行版更新
			array_push($info, "Try-Get-Version-Include-PreRelease");
			array_splice($info, array_search('Use-Cache', $info), 1);
			return CheckUpdate(true, true, $info);
		}
	}
}
function getip()
{
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
		$ip = getenv("HTTP_CLIENT_IP");
	} else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	} else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
		$ip = $_SERVER['REMOTE_ADDR'];
	} else {
		$ip = "unknown";
	}
	return htmlspecialchars($ip, ENT_QUOTES); // 防注入 #193
}
function sanitizeContent($content, $type = "mixed")
{
	switch ($type) {
		case 'number':
			$pattern = "([0-9])";
			preg_match_all($pattern, $content, $matches);
			$content =  join("", $matches[0]);
			return $content;
		case 'mixed':
		default:
			$chars = [
				"\\",
				"'",
				"\"",
				",",
				" ",
				"<script>"
			];
			foreach ($chars as $char) {
				$content = str_replace($char, "", $content);
			}
			return $content;
	}
}
function decodeSceKey($seckey)
{
	$seckey = str_replace("-", "+", $seckey);
	$seckey = str_replace("~", "=", $seckey);
	$seckey = str_replace("_", "/", $seckey);
	return $seckey;
}
function decryptMd5($md5)
{
	if (preg_match('/^.{9}[a-f0-9]/', $md5) && ctype_xdigit(substr($md5, 9, 1))) {
		return $md5;
	}
	$key = dechex(ord(substr($md5, 9, 1)) - ord('g'));
	$key2 = substr($md5, 0, 9) . $key . substr($md5, 10, strlen($md5));
	$key3 = "";
	for ($a = 0; $a < strlen($key2); $a++) {
		$key3 .= dechex(hexdec($key2[$a]) ^ (15 & $a));
	}
	$md5 = substr($key3, 8, 8) . substr($key3, 0, 8) . substr($key3, 24, 8) . substr($key3, 16, 8);
	return $md5;
}
