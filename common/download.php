<?php

/**
 * PanDownload 网页复刻版，PHP 语言版
 * 
 * 获取下载地址
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
require_once("./common/invalidCheck.php");
if (!CheckPassword(true)) dl_error(Language["PasswordError"], "密码错误或超时，请返回首页重新验证密码。"); // 密码错误
if ($_SERVER['REQUEST_METHOD'] !== 'POST') dl_error("方法错误", "请不要直接访问此页面或使用 GET 方式访问！"); // 方法错误
if ((isset($_POST["fs_id"]) &&
    isset($_POST["time"]) &&
    isset($_POST["sign"]) &&
    isset($_POST["randsk"]) &&
    isset($_POST["share_id"]) &&
    isset($_POST["uk"])) !== true) {
    dl_error("参数有误", "POST 传参出现问题！请不要自行构建表单提交！"); // 参数不齐
    exit;
}

$ip = sanitizeContent(getip());
$isipwhite = FALSE; //初始化 防止报错
if (USING_DB) {
    connectdb();

    // 查询数据库中是否存在已经保存的数据
    $sql = "SELECT * FROM `" . $dbtable . "_ip` WHERE `ip` LIKE '$ip';";
    $mysql_query = mysqli_query($conn, $sql);
    if ($result = mysqli_fetch_assoc($mysql_query)) {
        // 存在 判断类型
        if ($result["type"] == -1) {
            // 黑名单
            $isipwhite = FALSE;
            dl_error(Language["AccountError"], "当前ip已被加入黑名单，请联系站长解封");
            exit;
        } elseif ($result["type"] == 0) {
            // 白名单
            echo "<script>console.log('当前IP为白名单~');</script>";
            $isipwhite = TRUE;
        }
    }
}

// fix #191
$fs_id = sanitizeContent($_POST["fs_id"], 'number'); // only number
$timestamp = sanitizeContent($_POST["time"], 'number'); // only number
$sign = sanitizeContent($_POST["sign"]); // character + number
$randsk = sanitizeContent($_POST["randsk"]); // character + number + '%'
$share_id = sanitizeContent($_POST["share_id"], 'number'); // only number
$uk = sanitizeContent($_POST["uk"], 'number'); // only number

$json4 = getDlink($fs_id, $timestamp, $sign, $randsk, $share_id, $uk, APP_ID);
if ($json4["errno"] !== 0) {
    $error = [
        -9 => ["文件不存在(-9)", "请返回首页重新解析。"],
        112 => ["链接超时(112)", "获取链接超时，每次解析列表后只有5min有效时间，请返回首页重新解析。"],
        113 => ["传参错误(113)", "获取失败，请检查参数是否正确。"],
        118 => ["服务器错误(118)", "服务器错误，请求百度服务器时，未传入sekey参数或参数错误。"],
        110 => ["服务器错误(110)", "服务器错误，可能服务器IP被百度封禁，请切换 IP 或更换服务器重试。"],
    ];
    if (isset($error[$json4["errno"]])) dl_error($error[$json4["errno"]][0], $error[$json4["errno"]][1]);
    else dl_error("获取下载链接失败", "未知错误！<br />错误号：" . $json4["errno"], true); // 未知错误
}

$dlink = $json4["list"][0]["dlink"];
// 获取文件相关信息
$md5 = sanitizeContent($json4["list"][0]["md5"]);
$filename = $json4["list"][0]["server_filename"];
$size = sanitizeContent($json4["list"][0]["size"], "number");
$path = $json4["list"][0]["path"];
$server_ctime = (int)$json4["list"][0]["server_ctime"] + 28800; // 服务器创建时间 +8:00

if (USING_DB) {
    connectdb();

    $DownloadLinkAvailableTime = (is_int(DownloadLinkAvailableTime)) ? DownloadLinkAvailableTime : 8;
    // 查询数据库中是否存在已经保存的数据
    $sql = "SELECT * FROM `$dbtable` WHERE `md5`='$md5' AND `ptime` > DATE_SUB(NOW(),INTERVAL $DownloadLinkAvailableTime HOUR);";
    $mysql_query = mysqli_query($conn, $sql);
}
if (USING_DB and $result = mysqli_fetch_assoc($mysql_query)) {
    $realLink = $result["realLink"];
    $usingcache = true;
} else {

    // 判断今天内是否获取过文件
    if (USING_DB and !$isipwhite) { // 白名单和小文件跳过
        // 获取解析次数
        $sql = "SELECT count(*) as Num FROM `$dbtable` WHERE `userip`='$ip' AND `size`>=52428800 AND date(`ptime`)=date(now());";
        $mysql_query = mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($mysql_query);
        if ($result["Num"] >= DownloadTimes) {
            // 提示无权继续
            dl_error(Language["NoChance"], "<p class='card-text'>数据库中无此文件解析记录。</p><p class='card-text'>剩余解析次数为零，请明天再试。</p><hr />" . FileInfo($filename, $size, $md5, $server_ctime));
            exit;
        }
    }

    $DBSVIP = GetDBBDUSS();
    $SVIP_BDUSS = $DBSVIP[0];
    $id = $DBSVIP[1];

    // 开始获取真实链接
    $headerArray = array('User-Agent: LogStatistic', 'Cookie: BDUSS=' . $SVIP_BDUSS . ';'); // 仅此处用到SVIPBDUSS

    $getRealLink = head($dlink, $headerArray); // 禁止重定向
    $getRealLink = strstr($getRealLink, "Location");
    $getRealLink = substr($getRealLink, 10);
    $realLink = getSubstr($getRealLink, "http://", "\r\n"); // 删除 http://
    $usingcache = false;

    switch (SVIPSwitchMod) {
        case 1:
            //模式1：用到废为止
        case 2:
            //模式2：轮番上
            if ($id != "-1" and (strstr('https://' . $realLink, "//qdall") or $realLink == "")) {
                //限速进行标记 并刷新页面重新解析
                $sql = "UPDATE `" . $dbtable . "_svip` SET `state`= -1 WHERE `id`=$id";
                $mysql_query = mysqli_query($conn, $sql);
                if ($mysql_query != false) {
                    // SVIP账号自动切换成功，对用户界面进行刷新进行重新获取
                    $Language = Language;
                    echo "<div class=\"row justify-content-center\">
								<div class=\"col-md-7 col-sm-8 col-11\">
									<div class=\"alert alert-danger\" role=\"alert\">
										<h5 class=\"alert-heading\">{$Language["SwitchWait"]}</h5>
										<hr />
										<p class=\"card-text\">当前SVIP账号已经被限速</p>
										<p class=\"card-text\">正在自动切换新账号中</p>
										<p class=\"card-text\">预计需要2~3秒，请耐心等待</p>
										</p>
									</div>
								</div>
							</div>
							<script>
								setTimeout(() => location.reload(), 2000);
							</script>";
                    exit;
                } else {
                    // SVIP账号自动切换失败
                    dl_error("SVIP账号切换失败", "数据库出现问题，无法切换SVIP账号，请联系站长修复", true);
                    exit;
                }
            }
            break;
        case 3:
            //模式3：手动切换，不管限速
        case 4:
            //模式4：轮番上(无视限速)
        case 0:
            //模式0：使用本地解析
        default:
            break;
    }
}

// 1. 使用 dlink 下载文件   2. dlink 有效期为8小时   3. 必需要设置 User-Agent 字段   4. dlink 存在 HTTP 302 跳转
if ($realLink == "") echo '<div class="row justify-content-center"><div class="col-md-7 col-sm-8 col-11"><div class="alert alert-danger" role="alert">'
    . '<h5 class="alert-heading">' . Language["DownloadLinkError"] . '</h5><hr /><p class="card-text">已获取到文件，但未能获取到下载链接！</p>'
    . '<p class="card-text">请检查你是否在 <code>config.php</code> 中配置 <b>普通账号</b> 的 BDUSS 和 STOKEN！</p>'
    . '<p class="card-text">未配置 或 普通账号失效均会导致失败！（账号失效的原因包括但不限于 退出登录、修改密码）</p>' . FileInfo($filename, $size, $md5, $server_ctime) . '</div></div></div>'; // 未配置 SVIP 账号
else {

    // 记录下使用者ip，下次进入时提示
    if (USING_DB and !$usingcache) {
        $ptime = date("Y-m-d H:i:s");
        $Sqlfilename = htmlspecialchars($filename, ENT_QUOTES); // 防止出现一些刁钻的文件名无法处理
        $Sqlpath = htmlspecialchars($path, ENT_QUOTES);
        $sql = "INSERT INTO `$dbtable`(`userip`, `filename`, `size`, `md5`, `path`, `server_ctime`, `realLink` , `ptime`,`paccount`) VALUES ('$ip','$Sqlfilename','$size','$md5','$Sqlpath','$server_ctime','$realLink','$ptime','$id')";
        $mysql_query = mysqli_query($conn, $sql);
        if ($mysql_query == false) {
            // 保存错误
            dl_error(Language["DatabaseError"], "数据库错误，请联系站长修复。");
            exit;
        }
    }

?>
    <div class="row justify-content-center">
        <div class="col-md-7 col-sm-8 col-11">
            <div class="alert alert-primary" role="alert">
                <h5 class="alert-heading"><?php echo Language["DownloadLinkSuccess"]; ?></h5>
                <hr />
                <?php
                if (USING_DB) {
                    if ($usingcache) echo "<p class=\"card-text\">下载链接从数据库中提取，不消耗免费解析次数。</p>";
                    else echo "<p class=\"card-text\">服务器将保存下载地址" . DownloadLinkAvailableTime . "小时，时限内再次解析不消耗免费次数。</p>";
                }
                echo FileInfo($filename, $size, $md5, $server_ctime);

                echo '<hr><p class="card-text">' . Language["Preview"] . '</p>';
                if ($_SERVER['HTTP_USER_AGENT'] == "LogStatistic") {

                    $type = substr($filename, -4);
                    if ($type == ".jpg" || $type == ".png" || $type == "jpeg" || $type == ".bmp" || $type == ".gif") {
                        echo '<img src="https://' . $realLink . '" class="img-fluid rounded" style="width: 100%;">';
                    } elseif ($type == ".mp4") {
                        echo '<video src="https://' . $realLink . '" controls="controls" style="width: 100%;">您的浏览器不支持播放此视频！</video>';
                    } elseif ($type == ".mp3" || $type == ".wav") {
                        echo '<audio src="https://' . $realLink . '" controls="controls" style="width: 100%;">您的浏览器不支持播放此音频！</audio>';
                    } else {
                        echo '<p class="card-text">' . Language["NotSupportWithUA"] . '</p>';
                    }
                } else {
                    echo '<p class="card-text">' . Language["NotSupportWithoutUA"] . '</p>';
                }
                echo '<hr />';
                $DownloadLinkAvailableTime = (is_int(DownloadLinkAvailableTime)) ? DownloadLinkAvailableTime : 8;
                $Language_DownloadLink = Language["DownloadLink"];
                if (strstr('https://' . $realLink, "//qdall")) echo '<h5 class="text-danger">当前SVIP账号已被限速，请联系站长更换账号。</h5>';
                echo "<p class=\"card-text\"><a id=\"http\" href=\"http://$realLink\" style=\"display: none;\">下载链接</a>"
                    . "<a id=\"https\" data-qrcode-attr=\"href\" data-qrcode-level=\"L\" href=\"https://$realLink\" target=\"_blank\" rel=\"nofollow noopener noreferrer\">$Language_DownloadLink （"
                    . (((int)$size < 52428800) ? '无需' : '需要') . "设置 UA，$DownloadLinkAvailableTime 小时内有效）</a></p>";
                ?>
                <p class="card-text">
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#SendToAria2"><?php echo Language["SendToAria2"]; ?>(Motrix)</a>
                    <a href="" id="filecxx" style="display: none;"><?php echo Language["SendToFilecxx"]; ?></a>
                </p>
                <script>
                    try {
                        let filec_address = create_fileu_address({
                            uri: "https://<?php echo $realLink; ?>",
                            user_agent: "LogStatistic",
                            file_name: "<?php echo $filename; ?>"
                        });
                        $("#filecxx").attr("href", filec_address);
                        $("#filecxx").show();
                    } catch (e) {
                        $("#filecxx").hide();
                    }
                </script>
                <p class="card-text"><a href="?help" target="_blank"><?php echo Language["DownloadLink"] . Language["HelpButton"]; ?>（必读）</a></p>
                <p class="card-text"><?php echo Language["DownloadTip"]; ?></p>

                <div class="modal fade" id="SendToAria2" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?php echo Language["SendToAria2"]; ?> Json-RPC</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <p><label class="control-label">RPC地址</label>
                                        <input id="wsurl" class="form-control" value="ws://localhost:6800/jsonrpc">
                                    </p>
                                    <small>推送aria2默认配置:<b>ws://localhost:6800/jsonrpc</b><br />推送Motrix默认配置:<b>ws://localhost:16800/jsonrpc</b></small>
                                </div>
                                <div class="form-group">
                                    <p><label class="control-label">Token</label>
                                        <input id="token" class="form-control" placeholder="没有请留空">
                                    </p>
                                </div>
                                <small>填写的信息在推送成功后将会被自动保存。</small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" onclick="addUri()" data-dismiss="modal"><?php echo Language["Send"]; ?></button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo Language["Close"]; ?></button>
                            </div>
                        </div>
                    </div>
                    <script>
                        $(function() {
                            if (localStorage.getItem('aria2wsurl') !== null)
                                $('#wsurl').attr('value', localStorage.getItem('aria2wsurl'));
                            if (localStorage.getItem('aria2token') !== null)
                                $('#token').attr('value', localStorage.getItem('aria2token'));
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
<?php }
