<?php
require_once('./common/list.php');

/**
 * PanDownload 网页复刻版，PHP 语言版API文件
 *
 * 提供一些接口服务
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
session_start();
define('init', true);
require('./common/functions.php');
$method = (!empty($_GET["m"])) ? $_GET["m"] : "";
if ($method === "CheckMySQLConnect") {
	if (file_exists('config.php')) {
		// 如果已经安装过一次，必须管理员登录
		$is_login = (empty($_SESSION["admin_login"])) ? false : $_SESSION["admin_login"];
		if (!$is_login) { // 未登录
			EchoInfo(-3, array("msg" => "请刷新页面后重新登录"));
			exit;
		}
	}
	error_reporting(0);
	// 检查数据库连接是否正常
	$servername = htmlspecialchars((!empty($_POST["servername"])) ? $_POST["servername"] : "", ENT_QUOTES);
	$username = htmlspecialchars((!empty($_POST["username"])) ? $_POST["username"] : "", ENT_QUOTES);
	$DBPassword = htmlspecialchars((!empty($_POST["DBPassword"])) ? $_POST["DBPassword"] : "", ENT_QUOTES);
	$dbname = htmlspecialchars((!empty($_POST["dbname"])) ? $_POST["dbname"] : "", ENT_QUOTES);
	$dbtable = htmlspecialchars((!empty($_POST["dbtable"])) ? $_POST["dbtable"] : "", ENT_QUOTES);
	if (!function_exists('mysqli_connect')) {
		EchoInfo(-2, array("msg" => "<br/>您未安装或未启用 mysqli 扩展，<br/>不能使用数据库功能。<br/>请自行关闭数据库功能。"));
		exit;
	}
	$conn = mysqli_connect($servername, $username, $DBPassword);
	$GLOBALS['conn'] = $conn;
	// Check connection
	if (!$conn) {
		EchoInfo(-1, array("msg" => mysqli_connect_error()));
		exit;
	}
	// 连接成功，检查数据库是否存在
	$sql = "SELECT * FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '$dbname';"; // 查询是否有此数据库
	$mysql_query = mysqli_query($conn, $sql);
	if (mysqli_fetch_assoc($mysql_query)) {
		// 存在数据库
		EchoInfo(0, array("msg" => "数据库连接成功，存在 $dbname 数据库"));
		exit;
	}
	// 不存在数据库，需创建
	$sql = "CREATE DATABASE `$dbname` character set utf8;"; // 查询是否有此数据库
	$mysql_query = mysqli_query($conn, $sql);
	if (!$mysql_query) {
		// 创建失败
		EchoInfo(-1, array("msg" => "数据库连接成功，但创建数据库失败。<br />请手动创建 $dbname 数据库后再次检查连接。<br />"));
		exit;
	}
	EchoInfo(0, array("msg" => "成功连接并创建数据库 $dbname 。"));
	die();
}

if (!file_exists('./common/invalidCheck.php')) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://github.com/yuantuo666/baiduwp-php');
	die("HTTP 503 服务不可用！\r\n缺少相关配置和定义文件！无法正常运行程序！\r\n请重新 Clone 项目并进入此页面安装！\r\n将在五秒内跳转到 GitHub 储存库！");
}
require('./common/invalidCheck.php');
// 导入配置和函数
require('./config.php');
// 通用响应头
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge,chrome=1');
// 隐藏错误代码，保护信息安全
if (DEBUG) {
	error_reporting(E_ALL);
} else {
	error_reporting(0); // 关闭错误报告
}
$is_login = (empty($_SESSION["admin_login"])) ? false : $_SESSION["admin_login"];
if ($method == "ADMINAPI") {
	if (!$is_login) {
		//没有登录管理员账号
		EchoInfo(-1, array("msg" => "未登录"));
		exit;
	}
	connectdb();

	$action = (!empty($_GET["act"])) ? $_GET["act"] : "";
	switch ($action) {
		case "AnalyseGetTable":
			$page = (!empty($_GET["page"])) ? $_GET["page"] : "";
			echo GetAnalyseTablePage($page);
			break;
		case "SvipGetTable":
			$page = (!empty($_GET["page"])) ? $_GET["page"] : "";
			echo GetSvipTablePage($page);
			break;
		case "singleBDUSS":
			// 先处理是否有新增加数据
			$BDUSS = htmlspecialchars((!empty($_POST["BDUSS"])) ? trim($_POST["BDUSS"]) : "", ENT_QUOTES);
			$STOKEN = htmlspecialchars((!empty($_POST["STOKEN"])) ? $_POST["STOKEN"] : "", ENT_QUOTES);
			$name = htmlspecialchars((!empty($_POST["name"])) ? $_POST["name"] : "", ENT_QUOTES);
			if (empty($BDUSS) || strlen($BDUSS) !== 192) {
				EchoInfo(-1, array("msg" => "添加失败", "detail" => "请检查BDUSS是否填写正确"));
				exit;
			}
			// 开始录入
			$add_time = date("Y-m-d H:i:s");
			$sql = "INSERT INTO `{$dbtable}_svip`( `name`, `svip_bduss`, `svip_stoken`, `add_time`, `state`, `is_using`) VALUES ('$name','$BDUSS','$STOKEN','$add_time',1,'')";
			$Result = mysqli_query($conn, $sql);
			if (!$Result) {
				$Error = addslashes(mysqli_error($conn));
				EchoInfo(-1, array("msg" => "添加失败", "detail" => $Error));
				exit;
			}
			EchoInfo(0, array("msg" => "新增成功", "detail" => "已经成功新增一条会员数据。3s后将刷新该页面。", "refresh" => true));
			break;
		case "multiBDUSS":
			$BDUSS = (!empty($_POST["MULTI_BDUSS"])) ? trim($_POST["MULTI_BDUSS"]) : "";
			$name = htmlspecialchars((!empty($_POST["name"])) ? $_POST["name"] : "", ENT_QUOTES);
			if (empty($BDUSS)) {
				EchoInfo(-1, array("msg" => "添加失败", "detail" => "请检查BDUSS是否填写正确"));
				exit;
			}
			// 开始录入
			$allsql = "";
			$add_time = date("Y-m-d H:i:s");

			$AllBduss = explode("\n", $BDUSS);
			for ($i = 0; $i < count($AllBduss); $i++) {
				$EachBDUSS = explode("----", htmlspecialchars($AllBduss[$i], ENT_QUOTES));
				$Num = count($EachBDUSS);
				$BDUSS = ($Num >= 1) ? $EachBDUSS[0] : "";
				$STOKEN = ($Num >= 2) ? $EachBDUSS[1] : "";
				$EachName = ($Num >= 3) ? $EachBDUSS[2] : "";
				$AccountName = ($EachName == "") ? $name . "-" . ($i + 1) : $EachName;
				$sql = "INSERT INTO `{$dbtable}_svip`( `name`, `svip_bduss`, `svip_stoken`, `add_time`, `state`, `is_using`) VALUES ('$AccountName','$BDUSS','$STOKEN','$add_time',1,'');";
				$allsql .= $sql;
			}

			$success_result = 0;
			if (mysqli_multi_query($conn, $allsql)) {
				do {
					$success_result = $success_result + 1;
				} while (mysqli_more_results($conn) && mysqli_next_result($conn));
			}

			$affect_row = mysqli_affected_rows($conn);
			if ($affect_row == -1) {
				EchoInfo(-1, array("msg" => "导入失败", "detail" => "错误在" . $success_result . "行"));
				exit;
			}
			EchoInfo(0, array("msg" => "导入成功", "detail" =>	"成功导入" . $success_result . "条数据。3s后将刷新该页面。", "refresh" => true));
			break;
		case "SvipSettingFirstAccount":
			$id = htmlspecialchars((!empty($_GET["id"])) ? $_GET["id"] : "", ENT_QUOTES);
			if ($id == "") {
				// 参数错误
				EchoInfo(-1, array("msg" => "传入参数错误"));
				exit;
			}
			// 开始处理
			// 这里最新的时间表示可用账号，按顺序排序
			$is_using = date("Y-m-d H:i:s");
			$sql = "UPDATE `{$dbtable}_svip` SET `is_using`= '$is_using' WHERE `id`=$id";
			$mysql_query = mysqli_query($conn, $sql);
			if (!$mysql_query) {
				// 失败
				EchoInfo(-1, array("msg" => "修改失败"));
				exit;
			}
			// 成功
			EchoInfo(0, array("msg" => "ID为 $id 的账号已被设置为首选账号。3s后将刷新该页面。", "refresh" => true));
			break;
		case "SvipSettingNormalAccount":
			$id = htmlspecialchars((!empty($_GET["id"])) ? $_GET["id"] : "", ENT_QUOTES);
			if ($id == "") {
				// 参数错误
				EchoInfo(-1, array("msg" => "传入参数错误"));
				exit;
			}
			// 开始处理
			$sql = "UPDATE `{$dbtable}_svip` SET `state`= 1 WHERE `id`=$id";
			$mysql_query = mysqli_query($conn, $sql);
			if (!$mysql_query) {
				EchoInfo(-1, array("msg" => "修改失败"));
				exit;
			}
			// 成功
			EchoInfo(0, array("msg" => "ID为 $id 的账号已被设置为正常账号。3s后将刷新该页面。", "refresh" => true));
			break;
		case "IPGetTable":
			$page = (!empty($_GET["page"])) ? $_GET["page"] : "";
			echo GetIPTablePage($page);
			break;
		case "NewIp":
			$ip = htmlspecialchars((!empty($_POST["ip"])) ? trim($_POST["ip"]) : "", ENT_QUOTES);
			$remark = htmlspecialchars((!empty($_POST["remark"])) ? $_POST["remark"] : "", ENT_QUOTES);
			$type = htmlspecialchars($_POST["type"], ENT_QUOTES);
			if (!$ip) {
				EchoInfo(-1, array("msg" => "添加失败", "detail" => "请检查IP和账号种类是否填写正确"));
				exit;
			}
			// 开始录入
			$add_time = date("Y-m-d H:i:s");
			$sql = "INSERT INTO `{$dbtable}_ip`( `ip`, `remark`, `type`, `add_time`) VALUES ('$ip','$remark',$type,'$add_time')";
			$Result = mysqli_query($conn, $sql);
			if (!$Result) {
				$Error = addslashes(mysqli_error($conn));
				EchoInfo(-1, array("msg" => "添加失败", "detail" => $Error));
			}
			EchoInfo(0, array("msg" => "新增成功", "detail" => "成功新增一条ip记录。3s后将刷新该页面。", "refresh" => true));
			break;
		case "setDownloadTimes":
			$origin_config = file_get_contents("config.php");
			$update_config = str_replace('const DownloadTimes = ' . DownloadTimes . ';', 'const DownloadTimes = ' . $_POST["DownloadTimes"] . ';', $origin_config);
			$len = file_put_contents('config.php', $update_config);

			if (!$len) {
				EchoInfo(-1, array("msg" => "添加失败", "detail" => "请检查 config.php 文件状态及当前用户权限。或者手动修改 config.php 中相关设置。"));
				exit;
			}
			EchoInfo(0, array("msg" => "设置成功", "detail" => "成功写入 config.php 共 $len 个字符。3s后将刷新该页面。", "refresh" => true));
			break;
		case "setSVIPSwitchMod":
			$origin_config = file_get_contents("config.php");
			$update_config = str_replace('const SVIPSwitchMod = ' . SVIPSwitchMod . ';', 'const SVIPSwitchMod = ' . $_POST["SVIPSwitchMod"] . ';', $origin_config);
			$len = file_put_contents('config.php', $update_config);

			if (!$len) {
				EchoInfo(-1, array("msg" => "添加失败", "detail" => "请检查 config.php 文件状态及当前用户权限。或者手动修改 config.php 中相关设置。"));
				exit;
			}
			EchoInfo(0, array("msg" => "设置成功", "detail" => "成功写入 config.php 共 $len 个字符。3s后将刷新该页面。", "refresh" => true));
			break;
		case "DeleteById":
			//通过指定表格与ip删除对应行
			$Type = (!empty($_GET["type"])) ? $_GET["type"] : "";
			$Id = htmlspecialchars((!empty($_GET["id"])) ? $_GET["id"] : "", ENT_QUOTES);
			if (!$Type || !$Id) {
				EchoInfo(-1, array("msg" => "未传入Type(删除种类)或Id(删除指定的id)"));
				exit;
			}
			// 开始执行
			// 生成SQL
			switch ($Type) {
				case 'AnalyseTable':
					// 使用统计 分析表格 $dbtable
					$Sql = "DELETE FROM `$dbtable` WHERE `id` = $Id";
					break;
				case 'SvipTable':
					// 会员账号表格
					$Sql = "DELETE FROM `{$dbtable}_svip` WHERE `id` = $Id";
					break;
				case 'IPTable':
					// ip黑白名单
					$Sql = "DELETE FROM `{$dbtable}_ip` WHERE `id` = $Id";
					break;
				default:
					// 无匹配
					EchoInfo(-1, array("msg" => "传入Type(删除种类)错误"));
					exit;
			}
			// 开始执行sql
			$Result = mysqli_query($conn, $Sql);
			if (!$Result) {
				$Error = addslashes(mysqli_error($conn));
				EchoInfo(-1, array("msg" => "删除失败，返回信息:$Error"));
			}
			EchoInfo(0, array("msg" => "成功删除id为 $Id 的数据。3s后将刷新该页面。", "refresh" => true)); //成功删除
			break;
		default:
			EchoInfo(-1, array("msg" => "没有参数传入"));
			break;
	}
	exit;
}

switch ($method) {
	case 'LastParse':
		// 返回数据库中上一次解析的时间，及SVIP状态
		if (!USING_DB) {
			// 未开启数据库
			EchoInfo(-1, array("msg" => "未开启数据库功能", "sviptips" => "Unknown"));
			exit;
		}
		// 开启了数据库
		connectdb(true);

		$sql = "SELECT * FROM `$dbtable` WHERE `size`>=52428800 ORDER BY `ptime` DESC LIMIT 0,1"; // 时间倒序输出第一项
		$mysql_query = mysqli_query($conn, $sql);
		$Result = mysqli_fetch_assoc($mysql_query);
		if (!$Result) {
			EchoInfo(-1, array("msg" => "数据库中没有状态数据，请解析一次大于50MB文件以刷新账号状态", "sviptips" => "Unknown")); //防止产生误解，把提示写完全
			exit;
		}
		// 存在数据
		$Time = $Result["ptime"];
		$realLink = $Result["realLink"];
		$SvipState = (strstr('https://' . $realLink, "//qdall")) ? 0 : 1; // 1:正常 0:限速
		$SvipStateMsg = ($SvipState) ? "状态正常" : "已被限速";
		$SvipTips = ($SvipState) ? "正常" : "限速";
		EchoInfo(0, array(
			"msg" => "SVIP账号状态<br /><div align='left'>上次解析时间：" . $Time . "<br />上次解析状态：" . $SvipStateMsg . "</div>",
			"svipstate" => $SvipState,
			"sviptips" => $SvipTips
		));
		break;

	case "ParseCount":
		// TODO: 增加缓存功能，防止频繁查询数据库 OR 数据库增加统计表格
		// 返回数据库中所有的解析总数和文件总大小
		if (!USING_DB) {
			// 未开启数据库
			EchoInfo(-1, array("msg" => "未开启数据库功能"));
			exit;
		}
		// 开启了数据库
		connectdb(true);

		$sql = "SELECT count(`id`) as AllCount,sum(`size`) as AllSize FROM `$dbtable`";
		$mysql_query = mysqli_query($conn, $sql);
		$Result = mysqli_fetch_assoc($mysql_query);
		if (!$Result) {
			EchoInfo(0, array("msg" => "当前数据库版本不支持此统计操作"));
			exit;
		}
		// 存在数据
		$AllCount = $Result["AllCount"];
		$AllSize = formatSize((float)$Result["AllSize"]); // 格式化获取到的文件大小
		$ParseCountMsg =  "累计解析 $AllCount 个，共 $AllSize";

		$sql = "SELECT count(`id`) as AllCount,sum(`size`) as AllSize FROM `$dbtable` WHERE date(`ptime`)=date(now());"; // 获取今天的解析量
		$mysql_query = mysqli_query($conn, $sql);
		$Result = mysqli_fetch_assoc($mysql_query);
		if (!$Result) {
			EchoInfo(0, array("msg" => "当前数据库版本不支持此统计操作"));
			exit;
		}
		// 存在数据
		$AllCount = $Result["AllCount"];
		$AllSize = formatSize((float)$Result["AllSize"]); // 格式化获取到的文件大小
		$TodayParseCountMsg =  "今日解析 $AllCount 个，共 $AllSize";
		EchoInfo(0, array("msg" => "系统使用统计<br /><div align='left'>$ParseCountMsg<br />$TodayParseCountMsg</div>"));
		break;
	case "CheckUpdate":
		$includePreRelease = false; // 定义和获取是否包含预发行，是否强制检查
		$enforce = false;
		if (isset($_GET['includePreRelease']) && $_GET['includePreRelease'] === 'true') {
			$includePreRelease = true;
		}
		if (isset($_GET['enforce']) && $_GET['enforce'] === 'true') {
			$enforce = true;
		}
		$result = CheckUpdate($includePreRelease, $enforce); // 获取结果
		header('Content-Type: application/json; charset=utf-8'); // 设置响应头
		echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); // 输出响应
		break;
	case "GetList":
		// 获取文件列表
		$surl = $_POST["surl"] ?? ""; // 获取surl
		$pwd = $_POST["pwd"] ?? ""; // 获取密码
		$dir = $_POST["dir"] ?? ""; // 获取目录
		$sign = $_POST["sign"] ?? "";
		$timestamp = $_POST["timestamp"] ?? "";
		$result = Parse::getList($surl, $pwd, $dir, $sign, $timestamp);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($result);
		break;
	default:
		EchoInfo(-1, array("msg" => "无传入数据"));
}
