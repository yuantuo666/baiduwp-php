<?php

/**
 * PanDownload 网页复刻版，PHP 语言版
 *
 * 首页文件
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
require_once("./common/invalidCheck.php");
?>

<div class="col-lg-6 col-md-9 mx-auto mb-5 input-card">
	<div class="card">
		<div class="card-header bg-dark text-light">
			<?php if (USING_DB) { ?>
				<text id="parsingtooltip" data-placement="top" data-html="true" title="请稍等，正在连接服务器查询信息"><?php echo Language["IndexTitle"]; ?></text>
				<span style="float: right;" id="sviptooltip" data-placement="top" data-html="true" title="请稍等，正在连接服务器查询SVIP账号状态"><span class="point point-lg" id="svipstate-point"></span><span id="svipstate">Loading...</span></span>
			<?php } else echo Language["IndexTitle"]; ?>
		</div>
		<div class="card-body">
			<form name="form1" method="post" onsubmit="return validateForm()">
				<div class="form-group my-2"><input type="text" class="form-control" name="surl" placeholder="<?php echo Language["ShareLink"]; ?>" oninput="Getpw()"></div>
				<div class="form-group my-4"><input type="text" class="form-control" name="pwd" placeholder="<?php echo Language["SharePassword"]; ?>"></div>
				<?php
				if (IsCheckPassword) {
					$return = '<div class="form-group my-4"><input type="text" class="form-control" name="Password" placeholder="' . Language["PassWord"] . '"></div>';
					if (isset($_SESSION["Password"])) {
						if ($_SESSION["Password"] === Password) {
							$return = '<div>' . Language["PassWordVerified"] . '</div>';
						}
					}
					echo $return;
				} // 密码
				?>
				<button type="submit" class="mt-4 mb-3 btn btn-success btn-block"><?php echo Language["Submit"]; ?></button>
			</form>
			<?php if (file_exists("notice.html")) echo file_get_contents("notice.html"); ?>
		</div>
	</div>
	<?php if (USING_DB) { ?>
		<script>
			// 主页部分脚本
			$(document).ready(function() {

				$("#sviptooltip").tooltip(); // 初始化
				$("#parsingtooltip").tooltip(); // 初始化

				getAPI('LastParse').then(function(response) {
					if (response.success) {
						const data = response.data;
						if (data.error == 0) {
							// 请求成功
							if (data.svipstate == 1) {
								$("#svipstate-point").addClass("point-success");
							} else {
								$("#svipstate-point").addClass("point-danger");
							}
						}
						$("#svipstate").text(data.sviptips);
						$("#sviptooltip").attr("data-original-title", data.msg);
					}
				});

				getAPI('ParseCount').then(function(response) {
					if (response.success) {
						$("#parsingtooltip").attr("data-original-title", response.data.msg);
					}
				});
			});
		</script>
	<?php } ?>
	<script>
		// check if this site is in black list
		let blacklist = ["www.pojiewo.com", "bd.fkxz.cn"];
		if (blacklist.includes(document.domain)) {
			alert("当前网站在 baiduwp-php 项目的黑名单中，即将跳转到项目 Github 仓库");
			window.location.href = "https://github.com/yuantuo666/baiduwp-php";
		}
	</script>
</div>