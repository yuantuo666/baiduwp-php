<?php
require_once('./common/Parse.php');

/**
 * PanDownload 网页复刻版，PHP 语言版API文件
 *
 * 提供一些接口服务
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://github.com/yuantuo666/baiduwp-php
 *
 */
session_start();
define('init', true);
require('./common/functions.php');
$method = (!empty($_GET["m"])) ? $_GET["m"] : "";

if ($method === "CheckMySQLConnect") {
    // 验证管理员是否登录
    if (file_exists('config.php') && empty($_SESSION["admin_login"])) {
        EchoInfo(-3, array("msg" => "请刷新页面后重新登录"));
        exit;
    }
    error_reporting(0);
    // 获取数据库连接信息
    $dbtype = htmlspecialchars((!empty($_POST["dbtype"])) ? $_POST["dbtype"] : "", ENT_QUOTES);
    $servername = htmlspecialchars((!empty($_POST["servername"])) ? $_POST["servername"] : "", ENT_QUOTES);
    $username = htmlspecialchars((!empty($_POST["username"])) ? $_POST["username"] : "", ENT_QUOTES);
    $DBPassword = htmlspecialchars((!empty($_POST["DBPassword"])) ? $_POST["DBPassword"] : "", ENT_QUOTES);
    $dbname = htmlspecialchars((!empty($_POST["dbname"])) ? $_POST["dbname"] : "", ENT_QUOTES);
    $dbtable = htmlspecialchars((!empty($_POST["dbtable"])) ? $_POST["dbtable"] : "", ENT_QUOTES);

    try {
        if ($dbtype === "mysql") {
            $conn = connect_mysql($servername, $username, $DBPassword, $dbname, false, true);
            EchoInfo(0, array("msg" => "数据库连接成功，创建 {$dbname} 数据库"));
        } elseif ($dbtype === "sqlite") {
            $conn = connect_sqlite($servername, false);
            EchoInfo(0, array("msg" => "成功连接 SQLite 数据库。"));
        } else {
            throw new Exception("不支持的数据库类型: {$dbtype}");
        }
    } catch (Exception $e) {
        EchoInfo(-1, array("msg" => $e->getMessage()));
        exit;
    }
    exit;
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
error_reporting(0); // 关闭错误报告

$is_login = (empty($_SESSION["admin_login"])) ? false : $_SESSION["admin_login"];
if ($method == "ADMINAPI") {
	if (!$is_login) {
		//没有登录管理员账号
		EchoInfo(-1, array("msg" => "未登录"));
		exit;
	}
	if (!USING_DB) {
		EchoInfo(-1, array("msg" => "未启用数据库功能"));
		exit;
	}
	connectdb();

	$action = (!empty($_GET["act"])) ? $_GET["act"] : "";
	switch ($action) {
		case "AccountStatus":
			// 普通账号
			$return = "";
			$BDUSS = getSubstr(Cookie, 'BDUSS=', ';');
			$STOKEN = getSubstr(Cookie, 'STOKEN=', ';');
			$cache_key = md5($BDUSS);
			if (isset($_SESSION['cache'][$cache_key]) && $_SESSION['cache'][$cache_key]['time'] > time() - 3600) {
				$Status = $_SESSION['cache'][$cache_key]['data'];
			} else {
				$Status = AccountStatus($BDUSS, $STOKEN);
				$_SESSION['cache'][$cache_key] = [
					'time' => time(),
					'data' => $Status
				];
			}
			if ($Status[0] == 0) {
				//正常
				$AccountName = $Status[2];
				$return .= "账号名称：$AccountName<br />";
				if ($Status[3] == 1)
					$return .= "登录状态：<span class=\"text-success\">正常</span><br />";
				else
					$return .= "登录状态：<span class=\"text-danger\">异常</span><br />";

				$AccountVIP = ["普通账号", "普通会员", "超级会员"][$Status[1]];
				$return .= "会员状态：$AccountVIP<br />";
				if ($Status[4] != 0) {
					$AccountTime = time2Units($Status[4]);
					if ($Status[4] <= 60480)
						$return .= "剩余时间：<span class=\"text-danger\">$AccountTime</span><br />";
					else
						$return .= "剩余时间：$AccountTime<br />";
				}
			} elseif ($Status[0] == -6) {
				$return .= "id为 $id 的SVIP账号已经失效<br />";
			} else {
				$return .= "出现位置错误代码：" . $Status[0] . "<br />";
			}
			$normal_account_msg = $return;
			$return = "";

			// SVIP账号
			// 获取对应BDUSS
			$DBSVIP = GetDBBDUSS();
			$SVIP_BDUSS = $DBSVIP[0];
			$id = $DBSVIP[1];
			$SVIP_STOKEN = $DBSVIP[2];
			if ($SVIP_STOKEN == "") {
				$return .= "id为 $id 的SVIP账号没有设置对应STOKEN，无法检测<br />";
			} else {
				$cache_key = md5($SVIP_BDUSS);
				if (isset($_SESSION['cache'][$cache_key]) && $_SESSION['cache'][$cache_key]['time'] > time() - 3600) {
					$Status = $_SESSION['cache'][$cache_key]['data'];
				} else {
					$Status = AccountStatus($SVIP_BDUSS, $SVIP_STOKEN);
					$_SESSION['cache'][$cache_key] = [
						'time' => time(),
						'data' => $Status
					];
				}
				if ($Status[0] == 0) {
					$AccountName = $Status[2];
					$return .= "账号名称：$AccountName<br />";
					if ($Status[3] == 1)
						$return .= "登录状态：<span class=\"text-success\">正常</span><br />";
					else
						$return .= "登录状态：<span class=\"text-danger\">异常</span><br />";

					$AccountVIP = ["普通账号", "普通会员", "超级会员"][$Status[1]];
					$return .= "会员状态：$AccountVIP<br />";
					if ($Status[4] != 0) {
						$AccountTime = time2Units($Status[4]);
						if ($Status[4] <= 60480)
							$return .= "剩余时间：<span class=\"text-danger\">$AccountTime</span><br />";
						else
							$return .= "剩余时间：$AccountTime<br />";
					}
				} elseif ($Status[0] == -6) {
					$return .= "id为 $id 的SVIP账号已经失效<br />";
				} else {
					$return .= "出现位置错误代码：" . $Status[0] . "<br />";
				}
			}

			$svip_account_msg = $return;
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode(array("error" => 0, "normal_msg" => $normal_account_msg, "svip_msg" => $svip_account_msg));
			break;

		case "AnalyseGetTable":
			$page = (!empty($_GET["page"])) ? $_GET["page"] : "";
			header('Content-Type: text/html; charset=utf-8');
			echo GetAnalyseTablePage($page);
			break;
		case "SvipGetTable":
			$page = (!empty($_GET["page"])) ? $_GET["page"] : "";
			header('Content-Type: text/html; charset=utf-8');
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
			$Result = execute_exec($sql);
		    if (!$Result) {
		        EchoInfo(-1, array("msg" => "添加失败", "detail" => fetch_error()));
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
			$dbtype = $GLOBALS['dbtype'];
		    if ($dbtype === "mysql") {
		        if (mysqli_multi_query($allsql)) {
		            do {
		                $success_result += 1;
		            } while (mysqli_more_results($conn) && mysqli_next_result($conn));
		        }
		    } elseif ($dbtype === "sqlite") {
		        $success_result = execute_exec($allsql);
		    }

		    $affect_row = get_affected_rows();
		    if ($affect_row == -1) {
		        EchoInfo(-1, array("msg" => "导入失败", "detail" => "错误在" . $success_result . "行"));
		        exit;
		    }
		    EchoInfo(0, array("msg" => "导入成功", "detail" => "成功导入" . $success_result . "条数据。3s后将刷新该页面。", "refresh" => true));
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
			$mysql_query = execute_exec($sql);
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
			$mysql_query = execute_exec($sql);
			if (!$mysql_query) {
				EchoInfo(-1, array("msg" => "修改失败"));
				exit;
			}
			// 成功
			EchoInfo(0, array("msg" => "ID为 $id 的账号已被设置为正常账号。3s后将刷新该页面。", "refresh" => true));
			break;
		case "IPGetTable":
			$page = (!empty($_GET["page"])) ? $_GET["page"] : "";
			header('Content-Type: text/html; charset=utf-8');
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
			$Result = execute_exec($sql);
			if (!$Result) {
				EchoInfo(-1, array("msg" => "添加失败", "detail" => fetch_error()));
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
			$Result = execute_exec($Sql);
			if (!$Result) {
				$Error = fetch_error();
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
		$dbtype = $GLOBALS['dbtype'];
		if ($dbtype === "mysql") {
			$sql = "SELECT * FROM `$dbtable` WHERE `size`>=52428800 ORDER BY `ptime` DESC LIMIT 0,1"; // 时间倒序输出第一项
		}elseif ($dbtype === "sqlite") {
			$sql = "SELECT * FROM \"$dbtable\" WHERE CAST(\"size\" AS INTEGER) >= 52428800 ORDER BY \"ptime\" DESC LIMIT 1;";
		}

		$Result = fetch_assoc($sql);

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
    		"msg" => "SVIP账号状态<br />上次解析: " . $Time . "<br />账号状态: " . $SvipStateMsg,
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
		$Result = fetch_assoc($sql);

		if (!$Result) {
		    EchoInfo(0, array("msg" => "当前数据库版本不支持此统计操作"));
		    exit;
		}

		// 存在数据
		$AllCount = $Result["AllCount"];
		$AllSize = formatSize((float)$Result["AllSize"]); // 格式化获取到的文件大小
		$ParseCountMsg =  "累计解析: $AllCount ($AllSize)";

		$sql = "SELECT count(`id`) as AllCount,sum(`size`) as AllSize FROM `$dbtable` WHERE date(`ptime`)=date(now());"; // 获取今天的解析量
		$Result = fetch_assoc($sql);

		if (!$Result) {
		    EchoInfo(0, array("msg" => "当前数据库版本不支持此统计操作"));
		    exit;
		}

		// 存在数据
		$AllCount = $Result["AllCount"];
		$AllSize = formatSize((float)$Result["AllSize"]); // 格式化获取到的文件大小
		$TodayParseCountMsg =  "今日解析: $AllCount ($AllSize)";
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
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); // 输出响应
		break;
	case "GetList":
		// 获取文件列表
		if (!CheckPassword()) {
			// 密码错误
			EchoInfo(-1, array("msg" => "网站访问密码错误"));
			exit;
		}
		$surl = $_POST["surl"] ?? ""; // 获取surl
		$pwd = $_POST["pwd"] ?? ""; // 获取密码
		$dir = $_POST["dir"] ?? ""; // 获取目录
		$sign = $_POST["sign"] ?? "";
		$timestamp = $_POST["timestamp"] ?? "";
		$result = Parse::getList($surl, $pwd, $dir, $sign, $timestamp);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($result);
		break;
	case "Download":
		// 下载文件
		if (!CheckPassword()) {
			// 密码错误
			EchoInfo(-1, array("msg" => "网站访问密码错误"));
			exit;
		}
		$fs_id = $_POST["fs_id"] ?? "";
		$timestamp = $_POST["timestamp"] ?? "";
		$sign = $_POST["sign"] ?? "";
		$randsk = $_POST["randsk"] ?? "";
		$shareid = $_POST["shareid"] ?? "";
		$uk = $_POST["uk"] ?? "";

		$result = Parse::download($fs_id, $timestamp, $sign, $randsk, $shareid, $uk);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($result);
		break;
	case "Password":
		// 密码验证
		header('Content-Type: application/json; charset=utf-8');
		if (!IsCheckPassword) {
			echo json_encode(array("status" => 0, "msg" => "Never Gonna Let You Down"));
			exit;
		}
		if ($_SESSION["Password"] ?? "1234" === Password) {
			echo json_encode(array("status" => 2, "msg" => "意大利面拌42号混凝土"));
			exit;
		}
		echo json_encode(array("status" => 1, "msg" => "Never Gonna Give You Up"));
		exit;
	default:
		EchoInfo(-1, array("msg" => "无传入数据"));
		exit;
}
