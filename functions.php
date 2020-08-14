<?php
/**
* Pandownload PHP 复刻版函数文件
*
* 务必要保证此文件存在，否则整个服务将会不可使用！
*
* 请勿随意修改此文件！如需更改相关配置请到 config.php ！
*
* @version 1.2.0
*
* @author Yuan_Tuo <yuantuo666@gmail.com>
* @link https://imwcr.cn/
* @link https://space.bilibili.com/88197958
*
* @author LC <lc@lcwebsite.cn>
* @link https://lcwebsite.cn/
* @link https://space.bilibili.com/52618445
*/
// main
function post($url, $data, array $headerArray) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); //忽略ssl
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}
function get($url, array $headerArray) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //忽略ssl
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}
function head($url, array $headerArray) {
	// curl 获取响应头
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //忽略ssl
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出
	curl_setopt($ch, CURLOPT_HEADER, true); // 返回 response header 默认 false 只会获得响应的正文
	curl_setopt($ch, CURLOPT_NOBODY, true); // 有时候为了节省带宽及时间，只需要响应头
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	$response = curl_exec($ch);
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE); // 获得响应头大小
	$header = substr($response, 0, $header_size); // 根据头大小获取头信息
	curl_close($ch);
	return $header;
}
function getSubstr($str, $leftStr, $rightStr) {
	$left = strpos($str, $leftStr);
	//echo '左边:'.$left;
	$right = strpos($str, $rightStr, $left);
	//echo '<br>右边:'.$right;
	if ($left < 0 or $right < $left) return '';
	return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
}
//格式化size显示
function formatSize($b, $times = 0) {
	if ($b > 1024) {
		$temp = $b / 1024;
		return formatSize($temp, $times + 1);
	} else {
		switch ($times) {
			case '0':
				$unit = 'B'; break;
			case '1':
				$unit = 'KB'; break;
			case '2':
				$unit = 'MB'; break;
			case '3':
				$unit = 'GB'; break;
			case '4':
				$unit = 'TB'; break;
			case '5':
				$unit = 'PB'; break;
			case '6':
				$unit = 'EB'; break;
			case '7':
				$unit = 'ZB'; break;
			default:
				$unit = '单位未知';
		}
		return sprintf('%.2f', $b) . $unit;
	}
}
// 检查密码
function CheckPassword() {
	if (IsCheckPassword) {
		if ((!isset($_POST["password"])) || $_POST["password"] != Password) die('<div class="row justify-content-center"><div class="col-md-7 col-sm-8 col-11">
		<div class="alert alert-danger" role="alert"><h5 class="alert-heading">错误</h5><hr><p class="card-text">密码错误！</p></div></div>
		</div></div><script>sweetAlert("错误","密码错误！","error");</script></body></html>');
		else echo '<script>sweetAlert("重要提示","请勿将密码告诉他人！此项目仅供测试使用！\r\n——Yuan_Tuo","info");</script>';
	}
}

// 解析分享链接
function verifyPwd($surl_1, $pwd) { // 验证提取码
	$url = 'https://pan.baidu.com/share/verify?channel=chunlei&clienttype=0&web=1&app_id=250528&surl=' . $surl_1;
	$data = "pwd=$pwd";
	$headerArray = array("User-Agent: netdisk", "Referer: https://pan.baidu.com/disk/home");
	$json1 = post($url, $data, $headerArray);
	$json1 = json_decode($json1, true);
	// -12 提取码错误
	if ($json1["errno"] == 0) return $json1["randsk"];
	else return 1;
}
function getSign($surl, $randsk) {
	if ($randsk == 1) return 1;
	$url = 'https://pan.baidu.com/s/1' . $surl;
	$headerArray = array(
		"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36",
		"Cookie: BDUSS=" . constant("BDUSS") . ";STOKEN=" . constant("STOKEN") . ";BDCLND=" . $randsk . ";"
	);
	$json2 = get($url, $headerArray);
	$re = '/yunData.setData\(({.+)\);/';
	$re = '/yunData.setData\(\{(.*)?\}\);/';
	if (preg_match($re, $json2, $matches)) {
		$json2 = $matches[0];
		$json2 = substr($json2, 16, -2);
		$json2 = json_decode($json2, true);
		return $json2;
	} else return 1;
}
function getFileList($shareid, $uk, $randsk) {
	$url = 'https://pan.baidu.com/share/list?app_id=250528&channel=chunlei&clienttype=0&desc=0&num=100&order=name&page=1&root=1&shareid=' . $shareid . '&showempty=0&uk=' . $uk . '&web=1';
	$headerArray = array(
		"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36",
		"Cookie: BDUSS=" . constant("BDUSS") . ";STOKEN=" . constant("STOKEN") . ";BDCLND=" . $randsk . ";",
		"Referer: https://pan.baidu.com/disk/home"
	);
	$json3 = get($url, $headerArray);
	$json3 = json_decode($json3, true);
	return $json3;
}

// 获取下载链接
function getDlink($fs_id, $timestamp, $sign, $randsk, $share_id, $uk) {
	$postdata = "encrypt=0";
	$postdata .= "&extra=" . urlencode("{\"sekey\":\"" . urldecode($randsk) . "\"}"); //被这个转义坑惨了QAQ
	$postdata .= "&fid_list=[$fs_id]";
	$postdata .= "&primaryid=$share_id";
	$postdata .= "&uk=$uk";
	$postdata .= "&product=share&type=nolimit";
	$url = 'https://pan.baidu.com/api/sharedownload?app_id=250528&channel=chunlei&clienttype=12&sign=' . $sign . '&timestamp=' . $timestamp . '&web=1';
	$headerArray = array(
		"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.514.1919.810 Safari/537.36",
		"Cookie: BDUSS=" . constant("BDUSS") . ";STOKEN=" . constant("STOKEN") . ";BDCLND=" . $randsk . ";",
		"Referer: https://pan.baidu.com/disk/home"
	);
	$res3 = post($url, $postdata, $headerArray);
	$res3 = json_decode($res3, true);
	//var_dump($postdata, $res3);
	//没有 referer 就 112 ，然后没有 sekey 参数就 118    -20？？？
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
	return $res3;
}

if (!defined('init')){ http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); die('想啥呢？访问这个文件？'); } // 直接访问处理程序