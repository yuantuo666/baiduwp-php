<?php

/**
 * PanDownload 网页复刻版，PHP 语言版API文件
 *
 * 提供一些接口服务
 *
 * @version 1.4.5
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
session_start();
define('init', true);
if (version_compare(PHP_VERSION, '7.0.0', '<')) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://www.php.net/downloads.php');
	die("HTTP 503 服务不可用！\r\nPHP 版本过低！无法正常运行程序！\r\n请安装 7.0.0 或以上版本的 PHP！\r\n将在五秒内跳转到 PHP 官方下载页面！");
}
if (!(file_exists('config.php') && file_exists('functions.php'))) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://github.com/yuantuo666/baiduwp-php');
	die("HTTP 503 服务不可用！\r\n缺少相关配置和定义文件！无法正常运行程序！\r\n请重新 Clone 项目并配置！\r\n将在五秒内跳转到 GitHub 储存库！");
}
// 导入配置和函数
require('config.php');
require('functions.php');
// 通用响应头
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge,chrome=1');
//隐藏错误代码，保护信息安全
if (DEBUG) {
	error_reporting(E_ALL);
} else {
	error_reporting(0); //关闭错误报告
}

$method = (!empty($_GET["m"])) ? $_GET["m"] : "";
switch ($method) {
	case 'LastParse':
		//返回数据库中上一次解析的时间，及SVIP状态
		if (USING_DB) {
			//开启了数据库
			connectdb(true);

			$sql = "SELECT * FROM `$dbtable` WHERE `size`>=52428800 ORDER BY `ptime` DESC LIMIT 0,1"; //时间倒序输出第一项
			$mysql_query = mysqli_query($conn, $sql);
			if ($Result = mysqli_fetch_assoc($mysql_query)) {
				//存在数据
				$Time = $Result["ptime"];
				$realLink = $Result["realLink"];
				$SvipState = (strstr('https://' . $realLink, "//qdall")) ? 0 : 1; //1:正常 0:限速
				$SvipStateMsg = ($SvipState) ? "状态正常" : "已被限速";
				$SvipTips = ($SvipState) ? "正常" : "限速";
				EchoInfo(0, array(
					"msg" => "SVIP账号状态<br /><div align='left'>上次解析时间：" . $Time . "<br />上次解析状态：" . $SvipStateMsg . "</div>",
					"svipstate" => $SvipState,
					"sviptips" => $SvipTips
				));
			} else {
				EchoInfo(0, array("msg" => "数据库中没有数据"));
			}
		} else {
			//未开启数据库
			EchoInfo(-1, array("msg" => "未开启数据库功能", "sviptips" => "Unknown"));
		}
		break;

	case "ParseCount":
		//返回数据库中所有的解析总数和文件总大小
		if (USING_DB) {
			//开启了数据库
			connectdb(true);

			$sql = "SELECT count(`id`) as AllCount,sum(`size`) as AllSize FROM `$dbtable`";
			$mysql_query = mysqli_query($conn, $sql);
			if ($Result = mysqli_fetch_assoc($mysql_query)) {
				//存在数据
				$AllCount = $Result["AllCount"];
				$AllSize = formatSize((int)$Result["AllSize"]); //格式化获取到的文件大小
				$ParseCountMsg =  "累计解析 $AllCount 个，共 $AllSize";
			} else {
				EchoInfo(0, array("msg" => "当前数据库版本不支持此统计操作"));
				exit;
			}

			$sql = "SELECT count(`id`) as AllCount,sum(`size`) as AllSize FROM `$dbtable` WHERE date(`ptime`)=date(now());"; //获取今天的解析量
			$mysql_query = mysqli_query($conn, $sql);
			if ($Result = mysqli_fetch_assoc($mysql_query)) {
				//存在数据
				$AllCount = $Result["AllCount"];
				$AllSize = formatSize((int)$Result["AllSize"]); //格式化获取到的文件大小
				$TodayParseCountMsg =  "今日解析 $AllCount 个，共 $AllSize";
			} else {
				EchoInfo(0, array("msg" => "当前数据库版本不支持此统计操作"));
				exit;
			}
			EchoInfo(0, array("msg" => "系统使用统计<br /><div align='left'>$ParseCountMsg<br />$TodayParseCountMsg</div>"));
		} else {
			//未开启数据库
			EchoInfo(-1, array("msg" => "未开启数据库功能"));
		}
		break;
		case "CheckMySQLConnect":
			//检查数据库连接是否正常
			$servername = (!empty($_POST["servername"])) ? $_POST["servername"] : "";
			$username = (!empty($_POST["username"])) ? $_POST["username"] : "";
			$password = (!empty($_POST["password"])) ? $_POST["password"] : "";
			$dbname = (!empty($_POST["dbname"])) ? $_POST["dbname"] : "";
			$dbtable = (!empty($_POST["dbtable"])) ? $_POST["dbtable"] : "";
	
			$conn = mysqli_connect($servername, $username, $password);
			$GLOBALS['conn'] = $conn;
			// Check connection
			if (!$conn) {
				EchoInfo(-1, array("msg" => mysqli_connect_error()));
			} else {
				//连接成功，检查数据库是否存在
				$sql = "SELECT * FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '$dbname';"; //查询是否有此数据库
				$mysql_query = mysqli_query($conn, $sql);
				if (mysqli_fetch_assoc($mysql_query)) {
					//存在数据库
					EchoInfo(0, array("msg" => "数据库连接成功，存在 $dbname 数据库"));
				} else {
					//不存在数据库，需创建
					$sql = "CREATE DATABASE `$dbname` character set utf8;"; //查询是否有此数据库
					$mysql_query = mysqli_query($conn, $sql);
					if ($mysql_query) {
						//创建成功
						EchoInfo(0, array("msg" => "成功连接并创建数据库 $dbname 。"));
					} else {
						//创建失败
						EchoInfo(-1, array("msg" => "数据库连接成功，但创建数据库失败。<br />请手动创建 $dbname 数据库后再次检查连接。<br />"));
					}
				}
			}
			break;
	default:
		EchoInfo(-1, array("msg" => "无传入数据"));
		break;
}

function EchoInfo(int $error, array $Result)
{
	$ReturnArray = array("error" => $error);
	$ReturnArray += $Result;
	echo json_encode($ReturnArray);
}
