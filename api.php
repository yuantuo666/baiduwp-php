<?php

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
$programVersion_API = '2.1.3';
session_start();
define('init', true);
if (version_compare(PHP_VERSION, '7.0.0', '<')) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://www.php.net/downloads.php');
	die("HTTP 503 服务不可用！\r\nPHP 版本过低！无法正常运行程序！\r\n请安装 7.0.0 或以上版本的 PHP！\r\n将在五秒内跳转到 PHP 官方下载页面！");
}
$method = (!empty($_GET["m"])) ? $_GET["m"] : ""; // 下一步判断是否引用config.php需用到
if (!file_exists('functions.php')) {
	http_response_code(503);
	header('Content-Type: text/plain; charset=utf-8');
	header('Refresh: 5;url=https://github.com/yuantuo666/baiduwp-php');
	die("HTTP 503 服务不可用！\r\n缺少相关文件！无法正常运行程序！\r\n请重新 Clone 项目并配置！\r\n将在五秒内跳转到 GitHub 储存库！");
}
// 导入配置和函数
if ($method != "CheckMySQLConnect") { // 如果是使用检查连接，还没有配置好文件，不能引用
	if (!file_exists('config.php')) {
		http_response_code(503);
		header('Content-Type: text/plain; charset=utf-8');
		header('Refresh: 5;url=install.php');
		die("HTTP 503 服务不可用！\r\n暂未安装此程序！\r\n将在五秒内跳转到安装程序！");
	} else {
		require('config.php');
		if ($programVersion_API !== programVersion) {
			http_response_code(503);
			header('Content-Type: text/plain; charset=utf-8');
			header('Refresh: 5;url=install.php');
			die("HTTP 503 服务不可用！\r\n配置文件版本异常！\r\n将在五秒内跳转到安装程序！\r\n若重新安装无法解决问题，请重新 Clone 项目并配置！");
		}
	}
}
require('functions.php');
// 通用响应头
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge,chrome=1');
// 隐藏错误代码，保护信息安全
if ($method != "CheckMySQLConnect" and DEBUG) {
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
	} else {
		connectdb();
	}
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
			$BDUSS = (!empty($_POST["BDUSS"])) ? trim($_POST["BDUSS"]) : "";
			$STOKEN = (!empty($_POST["STOKEN"])) ? $_POST["STOKEN"] : "";
			$name = (!empty($_POST["name"])) ? $_POST["name"] : "";
			if ($BDUSS != "" and strlen($BDUSS) == 192) {
				// 开始录入
				$add_time = date("Y-m-d H:i:s");
				$sql = "INSERT INTO `" . $dbtable . "_svip`( `name`, `svip_bduss`, `svip_stoken`, `add_time`, `state`, `is_using`) VALUES ('$name','$BDUSS','$STOKEN','$add_time',1,'')";
				$Result = mysqli_query($conn, $sql);
				if ($Result != false) EchoInfo(0, array("msg" => "新增成功", "detail" => "已经成功新增一条会员数据。3s后将刷新该页面。", "refresh" => true));
				else {
					$Error = addslashes(mysqli_error($conn));
					EchoInfo(-1, array("msg" => "添加失败", "detail" => $Error));
				}
			} else {
				EchoInfo(-1, array("msg" => "添加失败", "detail" => "请检查BDUSS是否填写正确"));
			}
			break;
		case "multiBDUSS":
			$BDUSS = (!empty($_POST["MULTI_BDUSS"])) ? trim($_POST["MULTI_BDUSS"]) : "";
			$name = (!empty($_POST["name"])) ? $_POST["name"] : "";
			if ($BDUSS != "") {
				// 开始录入
				$allsql = "";
				$add_time = date("Y-m-d H:i:s");

				$AllBduss = explode("\n", $BDUSS);
				for ($i = 0; $i < count($AllBduss); $i++) {
					$sql = "INSERT INTO `" . $dbtable . "_svip`( `name`, `svip_bduss`, `add_time`, `state`, `is_using`) VALUES ('$name-" . ($i + 1) . "','" . $AllBduss[$i] . "','$add_time',1,'');";
					$allsql .= $sql;
				}

				$sccess_result = 0;
				if (mysqli_multi_query($conn, $allsql)) {
					do {
						$sccess_result = $sccess_result + 1;
					} while (mysqli_more_results($conn) && mysqli_next_result($conn));
				}

				$affect_row = mysqli_affected_rows($conn);
				if ($affect_row == -1)
					EchoInfo(-1, array("msg" => "导入失败", "detail" => "错误在" . $sccess_result . "行"));
				else EchoInfo(0, array("msg" => "导入成功", "detail" =>	"成功导入" . $sccess_result . "条数据。3s后将刷新该页面。", "refresh" => true));
			} else EchoInfo(-1, array("msg" => "添加失败", "detail" => "请检查BDUSS是否填写正确"));
			break;
		case "SvipSettingFirstAccount":
			$id = (!empty($_GET["id"])) ? $_GET["id"] : "";
			if ($id == "") {
				// 参数错误
				EchoInfo(-1, array("msg" => "传入参数错误"));
			} else {
				// 开始处理
				// 这里最新的时间表示可用账号，按顺序排序
				$is_using = date("Y-m-d H:i:s");
				$sql = "UPDATE `" . $dbtable . "_svip` SET `is_using`= '$is_using' WHERE `id`=$id";
				$mysql_query = mysqli_query($conn, $sql);
				if ($mysql_query != false) {
					// 成功
					EchoInfo(0, array("msg" => "ID为 $id 的账号已被设置为首选账号。3s后将刷新该页面。", "refresh" => true));
				} else {
					// 失败
					EchoInfo(-1, array("msg" => "修改失败"));
				}
			}
			break;
		case "SvipSettingNormalAccount":
			$id = (!empty($_GET["id"])) ? $_GET["id"] : "";
			if ($id == "") {
				// 参数错误
				EchoInfo(-1, array("msg" => "传入参数错误"));
			} else {
				// 开始处理
				$sql = "UPDATE `" . $dbtable . "_svip` SET `state`= 1 WHERE `id`=$id";
				$mysql_query = mysqli_query($conn, $sql);
				if ($mysql_query != false) {
					// 成功
					EchoInfo(0, array("msg" => "ID为 $id 的账号已被设置为正常账号。3s后将刷新该页面。", "refresh" => true));
				} else {
					// 失败
					EchoInfo(-1, array("msg" => "修改失败"));
				}
			}
			break;
		case "IPGetTable":
			$page = (!empty($_GET["page"])) ? $_GET["page"] : "";
			echo GetIPTablePage($page);
			break;
		case "NewIp":
			$ip = (!empty($_POST["ip"])) ? trim($_POST["ip"]) : "";
			$remark = (!empty($_POST["remark"])) ? $_POST["remark"] : "";
			$type = $_POST["type"];
			if ($ip != "") {
				// 开始录入
				$add_time = date("Y-m-d H:i:s");
				$sql = "INSERT INTO `" . $dbtable . "_ip`( `ip`, `remark`, `type`, `add_time`) VALUES ('$ip','$remark',$type,'$add_time')";
				$Result = mysqli_query($conn, $sql);
				if ($Result != false) EchoInfo(0, array("msg" => "新增成功", "detail" => "成功新增一条ip记录。3s后将刷新该页面。", "refresh" => true));
				else {
					$Error = addslashes(mysqli_error($conn));
					EchoInfo(-1, array("msg" => "添加失败", "detail" => $Error));
				}
			} else EchoInfo(-1, array("msg" => "添加失败", "detail" => "请检查IP和账号种类是否填写正确"));
			break;
		case "setDownloadTimes":
			$origin_config = file_get_contents("config.php");
			$update_config = str_replace('const DownloadTimes = ' . DownloadTimes . ';', 'const DownloadTimes = ' . $_POST["DownloadTimes"] . ';', $origin_config);
			$len = file_put_contents('config.php', $update_config);

			if ($len != false) EchoInfo(0, array("msg" => "设置成功", "detail" => "成功写入 config.php 共 $len 个字符。3s后将刷新该页面。", "refresh" => true));
			else EchoInfo(-1, array("msg" => "添加失败", "detail" => "请检查 config.php 文件状态及当前用户权限。或者手动修改 config.php 中相关设置。"));
			break;
		case "setSVIPSwitchMod":
			$origin_config = file_get_contents("config.php");
			$update_config = str_replace('const SVIPSwitchMod = ' . SVIPSwitchMod . ';', 'const SVIPSwitchMod = ' . $_POST["SVIPSwitchMod"] . ';', $origin_config);
			$len = file_put_contents('config.php', $update_config);

			if ($len != false) EchoInfo(0, array("msg" => "设置成功", "detail" => "成功写入 config.php 共 $len 个字符。3s后将刷新该页面。", "refresh" => true));
			else EchoInfo(-1, array("msg" => "添加失败", "detail" => "请检查 config.php 文件状态及当前用户权限。或者手动修改 config.php 中相关设置。"));
			break;
		case "DeleteById":
			//通过指定表格与ip删除对应行
			$Type = (!empty($_GET["type"])) ? $_GET["type"] : "";
			$Id = (!empty($_GET["id"])) ? $_GET["id"] : "";
			if ($Type != "" and $Id != "") {
				// 开始执行
				// 生成SQL
				switch ($Type) {
					case 'AnalyseTable':
						// 使用统计 分析表格 $dbtable
						$Sql = "DELETE FROM `$dbtable` WHERE `id` = $Id";
						break;
					case 'SvipTable':
						// 会员账号表格
						$Sql = "DELETE FROM `" . $dbtable . "_svip` WHERE `id` = $Id";
						break;
					case 'IPTable':
						// ip黑白名单
						$Sql = "DELETE FROM `" . $dbtable . "_ip` WHERE `id` = $Id";
						break;
					default:
						// 无匹配
						EchoInfo(-1, array("msg" => "传入Type(删除种类)错误"));
						exit;
						break;
				}
				// 开始执行sql
				$Result = mysqli_query($conn, $Sql);
				if ($Result != false) {
					EchoInfo(0, array("msg" => "成功删除id为 $Id 的数据。3s后将刷新该页面。", "refresh" => true)); //成功删除
				} else {
					$Error = addslashes(mysqli_error($conn));
					EchoInfo(-1, array("msg" => "删除失败，返回信息:$Error"));
				}
			} else EchoInfo(-1, array("msg" => "未传入Type(删除种类)或Id(删除指定的id)"));
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
		if (USING_DB) {
			// 开启了数据库
			connectdb(true);

			$sql = "SELECT * FROM `$dbtable` WHERE `size`>=52428800 ORDER BY `ptime` DESC LIMIT 0,1"; // 时间倒序输出第一项
			$mysql_query = mysqli_query($conn, $sql);
			if ($Result = mysqli_fetch_assoc($mysql_query)) {
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
			} else {
				EchoInfo(-1, array("msg" => "数据库中没有状态数据，请解析一次大于50MB文件以刷新账号状态", "sviptips" => "Unknown"));//防止产生误解，把提示写完全
			}
		} else {
			// 未开启数据库
			EchoInfo(-1, array("msg" => "未开启数据库功能", "sviptips" => "Unknown"));
		}
		break;

	case "ParseCount":
		// 返回数据库中所有的解析总数和文件总大小
		if (USING_DB) {
			// 开启了数据库
			connectdb(true);

			$sql = "SELECT count(`id`) as AllCount,sum(`size`) as AllSize FROM `$dbtable`";
			$mysql_query = mysqli_query($conn, $sql);
			if ($Result = mysqli_fetch_assoc($mysql_query)) {
				// 存在数据
				$AllCount = $Result["AllCount"];
				$AllSize = formatSize((float)$Result["AllSize"]); // 格式化获取到的文件大小
				$ParseCountMsg =  "累计解析 $AllCount 个，共 $AllSize";
			} else {
				EchoInfo(0, array("msg" => "当前数据库版本不支持此统计操作"));
				exit;
			}

			$sql = "SELECT count(`id`) as AllCount,sum(`size`) as AllSize FROM `$dbtable` WHERE date(`ptime`)=date(now());"; // 获取今天的解析量
			$mysql_query = mysqli_query($conn, $sql);
			if ($Result = mysqli_fetch_assoc($mysql_query)) {
				// 存在数据
				$AllCount = $Result["AllCount"];
				$AllSize = formatSize((float)$Result["AllSize"]); // 格式化获取到的文件大小
				$TodayParseCountMsg =  "今日解析 $AllCount 个，共 $AllSize";
			} else {
				EchoInfo(0, array("msg" => "当前数据库版本不支持此统计操作"));
				exit;
			}
			EchoInfo(0, array("msg" => "系统使用统计<br /><div align='left'>$ParseCountMsg<br />$TodayParseCountMsg</div>"));
		} else {
			// 未开启数据库
			EchoInfo(-1, array("msg" => "未开启数据库功能"));
		}
		break;
	case "CheckMySQLConnect":
		// 检查数据库连接是否正常
		$servername = (!empty($_POST["servername"])) ? $_POST["servername"] : "";
		$username = (!empty($_POST["username"])) ? $_POST["username"] : "";
		$DBPassword = (!empty($_POST["DBPassword"])) ? $_POST["DBPassword"] : "";
		$dbname = (!empty($_POST["dbname"])) ? $_POST["dbname"] : "";
		$dbtable = (!empty($_POST["dbtable"])) ? $_POST["dbtable"] : "";

		$conn = mysqli_connect($servername, $username, $DBPassword);
		$GLOBALS['conn'] = $conn;
		// Check connection
		if (!$conn) {
			EchoInfo(-1, array("msg" => mysqli_connect_error()));
		} else {
			// 连接成功，检查数据库是否存在
			$sql = "SELECT * FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '$dbname';"; // 查询是否有此数据库
			$mysql_query = mysqli_query($conn, $sql);
			if (mysqli_fetch_assoc($mysql_query)) {
				// 存在数据库
				EchoInfo(0, array("msg" => "数据库连接成功，存在 $dbname 数据库"));
			} else {
				// 不存在数据库，需创建
				$sql = "CREATE DATABASE `$dbname` character set utf8;"; // 查询是否有此数据库
				$mysql_query = mysqli_query($conn, $sql);
				if ($mysql_query) {
					// 创建成功
					EchoInfo(0, array("msg" => "成功连接并创建数据库 $dbname 。"));
				} else {
					// 创建失败
					EchoInfo(-1, array("msg" => "数据库连接成功，但创建数据库失败。<br />请手动创建 $dbname 数据库后再次检查连接。<br />"));
				}
			}
		}
		break;
	default:
		EchoInfo(-1, array("msg" => "无传入数据"));
		break;
}
