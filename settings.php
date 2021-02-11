<?php

/**
 * PanDownload 网页复刻版，PHP 语言版Settings文件
 *
 * 设置及后台功能
 *
 * @version 2.0.0
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
// 隐藏错误代码，保护信息安全
if (DEBUG) {
	error_reporting(E_ALL);
} else {
	error_reporting(0); // 关闭错误报告
}
$method = (!empty($_GET["m"])) ? $_GET["m"] : "";
$is_login = (empty($_SESSION["admin_login"])) ? false : $_SESSION["admin_login"];
if (!$is_login and !empty($_POST["setting_password"])) {
	// 开始验证密码
	if ($_POST["setting_password"] === ADMIN_PASSWORD) {
		// 密码正确
		$_SESSION["admin_login"] = true;
		$is_login = true;
	} else {
		// 密码错误
		$_SESSION["admin_login"] = false;
		echo "<script>Swal.fire('管理员密码错误，如果忘记密码请进入 config.php 查看');</script>";
	}
}
if ($is_login) connectdb();
if ($method == "API" and $is_login) {
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
		case "IPGetTable":
			$page = (!empty($_GET["page"])) ? $_GET["page"] : "";
			echo GetIPTablePage($page);
			break;
		default:
			echo "<h1>没有参数传入</h1>";
			break;
	}
	exit;
}

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
		<td>暂未开发</td>
		<td>" . $Result["userip"] . "</td>
		<td style=\"width:80px;\">" . $Result["filename"] . "</td>
		<td>" . formatSize((int)$Result["size"]) . "</td>
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
		<td><a href=\"javascript:SettingFirstAccount(" . $Result["id"] . ");\"  class=\"btn btn-outline-primary btn-sm\">设为当前解析账号</a></td>
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
		<td>" . $Result["ip"] . "</td>
		<td>" . $type . "</td>
		<td>" . $Result["remark"] . "</td>
		<td>" . $Result["add_time"] . "</td>
		</tr>";
		$AllRow .= $EachRow;
	}
	return $AllRow;
}

?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="referrer" content="same-origin" />
	<meta name="author" content="Yuan_Tuo" />
	<meta name="version" content="<?php echo programVersion; ?>" />
	<title><?php echo Sitename; ?> - Settings</title>
	<link rel="icon" href="favicon.ico" />
	<link rel="stylesheet" href="static/index.css" />
	<link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/4.1.2/css/bootstrap.min.css" />
	<link rel="stylesheet" disabled id="ColorMode-Dark" href="https://cdn.jsdelivr.net/gh/vinorodrigues/bootstrap-dark@0.0.9/dist/bootstrap-nightfall.css" />
	<link rel="stylesheet" href="https://cdn.staticfile.org/font-awesome/5.8.1/css/all.min.css" />
	<link rel="stylesheet" disabled id="Swal2-Dark" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4.0.2/dark.min.css" />
	<link rel="stylesheet" disabled id="Swal2-Light" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-default@4.0.2/default.min.css" />
	<script src="https://cdn.staticfile.org/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://cdn.staticfile.org/popper.js/1.12.5/umd/popper.min.js"></script>
	<script src="https://cdn.staticfile.org/twitter-bootstrap/4.1.2/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.14.0/dist/sweetalert2.min.js"></script>
	<script src="static/color.js"></script>
</head>

<body>
	<div class="container">
		<div class="row justify-content-center">
			<?php


			if (!$is_login) { ?>
				<!-- 登录 -->
				<div class="col-lg-6 col-md-9 mx-auto mb-5 input-card">
					<div class="card">
						<div class="card-header bg-dark text-light">Pandownload复刻版-后台登录</div>
						<div class="card-body">
							<form name="form1" method="post">
								<div class="form-group my-2">
									<input type="text" class="form-control" name="setting_password" placeholder="Password">
									<small class="form-text text-right">密码是中文，别想破解了~</small>
								</div>
								<button type="submit" class="mt-4 mb-3 form-control btn btn-success btn-block">登录</button>
						</div>
					</div>
				</div>
			<?php } else {
				// 登录后操作
			?>
				<div class="col-md-12 col-sm-12 col-12">
					<?php if ($method == "analyse") { ?>
						<nav>
							<ol class="breadcrumb my-4">
								<li class="breadcrumb-item"><a href="index.php">baiduwp-php</a></li>
								<li class="breadcrumb-item"><a href="settings.php">后台管理</a></li>
								<li class="breadcrumb-item">数据分析</li>
							</ol>
						</nav>
						<div class="card">
							<div class="card-header">
								数据分析
							</div>
							<div class="card-body">
								<h5 class="card-title">最近解析</h5>
								<div style="overflow:scroll;">
									<table id="AnalyseTable" class="table table-striped" style="min-width:1400px;" page=1>
										<thead>
											<tr>
												<th scope="col">#</th>
												<th scope="col">操作</th>
												<th scope="col">用户ip</th>
												<th scope="col">文件名</th>
												<th scope="col">文件大小</th>
												<!-- <th scope="col">文件效验码</th> -->
												<th scope="col">文件路径</th>
												<!-- <th scope="col">文件创建时间</th> -->
												<th scope="col">文件下载地址</th>
												<th scope="col">解析时间</th>
												<th scope="col">解析账号</th>
												<!-- 新增解析账号：ALTER TABLE `bdwp` ADD `paccount` INT NOT NULL COMMENT '解析账号id' AFTER `ptime`; -->
											</tr>
										</thead>
										<tbody>
											<?php
											echo GetAnalyseTablePage(1);
											?>

										</tbody>
									</table>
								</div>
								<br />
								<a href="javascript:AnalyseLoadmore();" class="btn btn-primary">加载更多</a>
								<script>
									function AnalyseLoadmore() {
										newpage = Number($("#AnalyseTable").attr("page")) + 1;
										$.get("settings.php?m=API&act=AnalyseGetTable&page=" + String(newpage), function(data, status) {
											if (status == "success") {
												$("#AnalyseTable").append(data);
												$("#AnalyseTable").attr("page", newpage);
											}
										});
									}
								</script>
							</div>
						</div>
					<?php } elseif ($method == "svip") {
						// 先处理是否有新增加数据
						if (isset($_POST["BDUSS"])) {
							$BDUSS = (!empty($_POST["BDUSS"])) ? trim($_POST["BDUSS"]) : "";
							$STOKEN = (!empty($_POST["STOKEN"])) ? $_POST["STOKEN"] : "";
							$name = (!empty($_POST["name"])) ? $_POST["name"] : "";
							if ($BDUSS != "" and strlen($BDUSS) == 192) {
								// 开始录入
								$add_time = date("Y-m-d H:i:s");
								$sql = "INSERT INTO `" . $dbtable . "_svip`( `name`, `svip_bduss`, `svip_stoken`, `add_time`, `state`, `is_using`) VALUES ('$name','$BDUSS','$STOKEN','$add_time',1,'')";
								$Result = mysqli_query($conn, $sql);
								if ($Result != false) echo "<script>Swal.fire('新增成功');</script>";
								else {
									$Error = addslashes(mysqli_error($conn));
									echo "<script>Swal.fire('添加失败','$Error');</script>";
								}
							} else {
								echo "<script>Swal.fire('请检查BDUSS是否填写正确')</script>";
							}
						}
						if (isset($_POST["MULTI_BDUSS"])) {
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
								if ($affect_row == -1) {
									$Msg = "错误在" . $sccess_result . "行";
								} else {
									$Msg = "成功导入" . $sccess_result . "条数据";
								}
								echo "<script>Swal.fire('$Msg');</script>";
							} else {
								echo "<script>Swal.fire('请检查BDUSS是否填写正确')</script>";
							}
						}

					?>
						<nav>
							<ol class="breadcrumb my-4">
								<li class="breadcrumb-item"><a href="index.php">baiduwp-php</a></li>
								<li class="breadcrumb-item"><a href="settings.php">后台管理</a></li>
								<li class="breadcrumb-item">会员账号</li>
							</ol>
						</nav>
						<!-- SVIP -->
						<div class="card">
							<div class="card-header">
								SVIP账号管理
							</div>
							<div class="card-body">
								<h5 class="card-title">默认账号</h5>
								<?php
								$sql = "SELECT * FROM `" . $dbtable . "_svip` WHERE `state`!=-1 ORDER BY `is_using` DESC LIMIT 0,1"; // 时间倒序输出第一项未被限速账号
								$Result = mysqli_query($conn, $sql);

								if ($Result =  mysqli_fetch_assoc($Result)) {
									$id = $Result["id"];
									$name = $Result["name"];
									$add_time = $Result["add_time"];
									$is_using = $Result["is_using"];
									$state = ($Result["state"] == -1) ? "限速" : "正常";

									$sql = "SELECT count(`id`) as AllCount,sum(`size`) as AllSize FROM `$dbtable` WHERE `paccount`=$id"; // 时间倒序输出第一项未被限速账号
									$Result = mysqli_query($conn, $sql);

									if ($Result = mysqli_fetch_assoc($Result)) {
										$AllCount = $Result["AllCount"];
										$AllSize = ($AllCount == "0") ? "无数据" : formatSize((int)$Result["AllSize"]); // 格式化获取到的文件大小
										$ParseCountMsg =  "累计解析次数：$AllCount 个<br />累计解析大小：$AllSize";
									}

									echo "<p>ID：$id<br />";
									echo "名称：$name<br />";
									echo "启用时间：$add_time<br />";
									echo "是否使用(最新时间将被用于解析)：$is_using<br />";
									echo "状态：$state<br />";
									echo "$ParseCountMsg</p>";
								} else echo "<p>Error!当前没有可用账户，正使用本地解析。</p>";
								?>
								<br>
								<h5 class="card-title">所有账号</h5>
								<div style="overflow:scroll;">
									<table id="SvipTable" class="table table-striped" style="min-width:1400px;" page=1>
										<thead>
											<tr>
												<th scope="col">#</th>
												<th scope="col">操作</th>
												<th scope="col">是否正在使用</th>
												<th scope="col">账号名称</th>
												<th scope="col">会员状态</th>
												<th scope="col">添加时间</th>
												<th scope="col">会员bduss</th>
												<th scope="col">会员stoken</th>
										</thead>
										<tbody>
											<?php
											echo GetSvipTablePage(1);
											?>

										</tbody>
									</table>
								</div>
								<br />
								<a href="javascript:SvipLoadmore();" class="btn btn-primary">加载更多</a>
								<script>
									function SvipLoadmore() {
										newpage = Number($("#SvipTable").attr("page")) + 1;
										$.get("settings.php?m=API&act=SvipGetTable&page=" + String(newpage), function(data, status) {
											if (status == "success") {
												$("#SvipTable").append(data);
												$("#SvipTable").attr("page", newpage);
											}
										});
									}

									function SettingFirstAccount(id) {
										$.get("settings.php?m=API&act=SvipSettingFirstAccount&id=" + String(id), function(data, status) {
											if (status == "success") {
												var json = JSON.parse(data);
												Swal.fire(json.msg);
												if (json.refresh == true) setTimeout("location.reload();", 3000);
											}
										});
									}
								</script>
								<br><br><br>
								<!-- 新增会员数据 -->
								<h5 class="card-title">新增会员数据</h5>
								<form action="settings.php?m=svip" method="post">
									<div class="form-group">
										<label>账号名称</label>
										<input type="text" class="form-control form-control-sm" name="name">
									</div>
									<div class="form-group">
										<label>BDUSS</label>
										<input type="text" class="form-control form-control-sm" name="BDUSS">
									</div>
									<div class="form-group">
										<label>STOKEN</label>
										<input type="text" class="form-control form-control-sm" name="STOKEN">
									</div>
									<button type="submit" class="btn btn-primary">提交</button>
								</form>
								<!-- 新增会员数据 -->
								<br><br>
								<h5 class="card-title">批量导入svip</h5>
								<form action="settings.php?m=svip" method="post">
									<div class="form-group">
										<label>账号名称</label>
										<input type="text" class="form-control form-control-sm" name="name">
									</div>
									<div class="form-group">
										<label>MULTI BDUSS（每行一个）</label>
										<textarea type="text" class="form-control form-control-sm" name="MULTI_BDUSS" style="height: 200px;"></textarea>
									</div>
									<button type="submit" class="btn btn-primary">提交</button>
								</form>
							</div>
						</div>
					<?php } elseif ($method == "iplist") {
						// 先处理是否有新增加数据
						if (isset($_POST["ip"])) {
							$ip = (!empty($_POST["ip"])) ? trim($_POST["ip"]) : "";
							$remark = (!empty($_POST["remark"])) ? $_POST["remark"] : "";
							$type = $_POST["type"];
							if ($ip != "") {
								// 开始录入
								$add_time = date("Y-m-d H:i:s");
								$sql = "INSERT INTO `" . $dbtable . "_ip`( `ip`, `remark`, `type`, `add_time`) VALUES ('$ip','$remark',$type,'$add_time')";
								$Result = mysqli_query($conn, $sql);
								if ($Result != false) echo "<script>Swal.fire('新增成功');</script>";
								else {
									$Error = addslashes(mysqli_error($conn));
									echo "<script>Swal.fire('添加失败','$Error');</script>";
								}
							} else {
								echo "<script>Swal.fire('请检查IP和账号种类是否填写正确')</script>";
							}
						}
					?>
						<nav>
							<ol class="breadcrumb my-4">
								<li class="breadcrumb-item"><a href="index.php">baiduwp-php</a></li>
								<li class="breadcrumb-item"><a href="settings.php">后台管理</a></li>
								<li class="breadcrumb-item">IP黑/白名单</li>
							</ol>
						</nav>
						<!-- IP地址 -->
						<div class="card">
							<div class="card-header">
								黑/白名单管理
							</div>
							<div class="card-body">
								<h5 class="card-title">所有IP</h5>
								<div style="overflow:scroll;">
									<table id="IPTable" class="table table-striped" style="min-width:600px;" page=1>
										<thead>
											<tr>
												<th scope="col">#</th>
												<th scope="col">IP</th>
												<th scope="col">账号状态</th>
												<th scope="col">备注</th>
												<th scope="col">添加时间</th>
										</thead>
										<tbody>
											<?php
											echo GetIPTablePage(1);
											?>

										</tbody>
									</table>
								</div>
								<br />
								<a href="javascript:IPLoadmore();" class="btn btn-primary">加载更多</a>
								<script>
									function IPLoadmore() {
										newpage = Number($("#IPTable").attr("page")) + 1;
										$.get("settings.php?m=API&act=IPGetTable&page=" + String(newpage), function(data, status) {
											if (status == "success") {
												$("#IPTable").append(data);
												$("#IPTable").attr("page", newpage);
											}
										});
									}
								</script>
								<br><br><br>
								<!-- 新增IP -->
								<h5 class="card-title">新增IP</h5>
								<form action="settings.php?m=iplist" method="post">
									<div class="form-group">
										<label>IP地址</label>
										<input type="text" class="form-control form-control-sm" name="ip">
									</div>
									<div class="form-group">
										<label>备注</label>
										<input type="text" class="form-control form-control-sm" name="remark">
									</div>
									<div class="form-group">
										<label>账号种类</label>
										<select class="form-control form-control-sm" name="type">
											<option value="0">白名单</option>
											<option value="-1">黑名单</option>
										</select>
									</div>
									<button type="submit" class="btn btn-primary">提交</button>
								</form>

							</div>
						</div>
					<?php } elseif ($method == "DownloadTimes") {
						// 先处理是否有新增加数据
						if (isset($_POST["DownloadTimes"])) {
							$origin_config = file_get_contents("config.php");
							$update_config = str_replace('const DownloadTimes = ' . DownloadTimes . ';', 'const DownloadTimes = ' . $_POST["DownloadTimes"] . ';', $origin_config);
							$len = file_put_contents('config.php', $update_config);

							if ($len != false) {
								echo "<script>Swal.fire('成功！成功写入 config.php 共 $len 个字符。刷新页面后可看到修改的内容。');</script>";
							} else {
								echo "<script>Swal.fire('写入失败，请检查 config.php 文件状态及当前用户权限。或者手动修改 config.php 中相关设置。');</script>";
							}
						}
					?>
						<nav>
							<ol class="breadcrumb my-4">
								<li class="breadcrumb-item"><a href="index.php">baiduwp-php</a></li>
								<li class="breadcrumb-item"><a href="settings.php">后台管理</a></li>
								<li class="breadcrumb-item">下载次数限制修改</li>
							</ol>
						</nav>
						<!-- 下载次数限制修改 -->
						<div class="card">
							<div class="card-header">
								下载次数限制修改
							</div>
							<div class="card-body">
								<h5 class="card-title">下载次数限制修改</h5>
								<?php
								echo "当前设置的下载次数：" . DownloadTimes;
								?>
								<br>
								注意！此功能需要修改config.php的信息，请小心使用。
								<br><br><br>
								<!-- 修改下载次数 -->
								<h5 class="card-title">修改下载次数</h5>
								<form action="settings.php?m=DownloadTimes" method="post">
									<div class="form-group">
										<label>下载次数</label>
										<input type="text" class="form-control form-control-sm" name="DownloadTimes">
									</div>
									<button type="submit" class="btn btn-primary">提交</button>
								</form>

							</div>
						</div>
					<?php } else { ?>
						<nav>
							<ol class="breadcrumb my-4">
								<li class="breadcrumb-item"><a href="index.php">baiduwp-php</a></li>
								<li class="breadcrumb-item"><a href="settings.php">后台管理</a></li>
								<li class="breadcrumb-item">概览</li>
							</ol>
						</nav>
						<!-- 概览 -->
						<div class="card">
							<div class="card-header">
								概览
							</div>
							<div class="card-body">
								<div class="row">

									<div class="col-md-6 col-sm-12">
										<h5 class="card-title">使用统计</h5>
										<p class="card-text">
											<?php
											$sql = "SELECT count(`id`) as AllCount,sum(`size`) as AllSize FROM `$dbtable`";
											$Result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
											// 存在数据
											$AllCount = $Result["AllCount"];
											$AllSize = formatSize((int)$Result["AllSize"]); // 格式化获取到的文件大小
											echo "累计解析 $AllCount 个，共 $AllSize";
											?>
											<br />
											<?php
											$sql = "SELECT count(`id`) as AllCount,sum(`size`) as AllSize FROM `$dbtable` WHERE date(`ptime`)=date(now());"; // 获取今天的解析量
											$Result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
											// 存在数据
											$AllCount = $Result["AllCount"];
											$AllSize = formatSize((int)$Result["AllSize"]); // 格式化获取到的文件大小
											echo "今日解析 $AllCount 个，共 $AllSize";
											?>
										</p>
										<a href="?m=analyse" class="btn btn-primary">查看详细情况</a>
										<br><br>
									</div>
									<div class="col-md-6 col-sm-12">
										<h5 class="card-title">SVIP账号</h5>
										<p class="card-text">
											<?php
											$sql = "SELECT count(`id`) as AllCount FROM `" . $dbtable . "_svip`";
											$Result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
											$AllCount = $Result["AllCount"];
											$SvipCountMsg =  "数据库中共 $AllCount 个账号";
											$sql = "SELECT count(`id`) as AllCount FROM `" . $dbtable . "_svip` WHERE `state`=-1";
											$Result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
											$AllCount = $Result["AllCount"];
											$SvipFailCountMsg = "有 $AllCount 个账号已被限速";
											echo $SvipCountMsg . "<br />" . $SvipFailCountMsg;
											?>
										</p>
										<a href="?m=svip" class="btn btn-primary">查看详细情况</a>
										<br><br>
									</div>
								</div>
								<div class="row">

									<div class="col-md-6 col-sm-12">
										<h5 class="card-title">黑/白名单</h5>
										<p class="card-text">
											<?php
											$sql = "SELECT count(`id`) as AllCount FROM `" . $dbtable . "_ip`";
											$Result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
											$AllCount = $Result["AllCount"];
											$SvipCountMsg =  "数据库中共 $AllCount 个ip";
											if ($AllCount != 0) {
												$sql = "SELECT count(`id`) as AllCount FROM `" . $dbtable . "_ip` WHERE `type`=-1";
												$Result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
												$AllCount = $Result["AllCount"];
												$SvipFailCountMsg = "有 $AllCount 个黑名单";
												$sql = "SELECT count(`id`) as AllCount FROM `" . $dbtable . "_ip` WHERE `type`=0";
												$Result = mysqli_fetch_assoc(mysqli_query($conn, $sql));
												$AllCount = $Result["AllCount"];
												$SvipSuccCountMsg = "有 $AllCount 个白名单";
											} else $SvipFailCountMsg = $SvipSuccCountMsg = "";
											echo $SvipCountMsg . "<br />" . $SvipFailCountMsg . "<br />" . $SvipSuccCountMsg;
											?>
										</p>
										<a href="?m=iplist" class="btn btn-primary">查看详细情况</a>
										<br><br>
									</div>
									<div class="col-md-6 col-sm-12">
										<h5 class="card-title">下载次数限制修改</h5>
										<p class="card-text">
											<?php
											echo "当前设置的下载次数：" . DownloadTimes;
											?>
										</p>
										<a href="?m=DownloadTimes" class="btn btn-primary">查看详细情况</a>
										<br><br>
									</div>
								</div>

							</div>



						</div>
				</div>
			<?php } ?>



		</div>
	<?php
			} ?>
	</div>
	</div>
</body>

</html>