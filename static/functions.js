/**
 * PanDownload 网页复刻版，JS函数文件
 *
 * 许多函数来源于github，详见项目里的Thanks
 *
 * @version 2.1.3
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
function validateForm() {
	var link = document.forms["form1"]["surl"].value;
	if (link == null || link === "") { document.forms["form1"]["surl"].focus(); Swal.fire("提示", "请填写分享链接", "info"); return false; }
	var uk = link.match(/uk=(\d+)/), shareid = link.match(/shareid=(\d+)/);
	if (uk != null && shareid != null) {
		document.forms["form1"]["surl"].value = "";
		$("form").append(`<input type="hidden" name="uk" value="${uk[1]}"/><input type="hidden" name="shareid" value="${shareid[1]}"/>`);
		return true;
	}
	var surl = link.match(/surl=([A-Za-z0-9-_]+)/);
	if (surl == null) {
		surl = link.match(/1[A-Za-z0-9-_]+/);
		if (surl == null) {
			document.forms["form1"]["surl"].focus(); Swal.fire("提示", "分享链接填写有误，请检查", "info"); return false;
		} else surl = surl[0];
	} else surl = "1" + surl[1];
	document.forms["form1"]["surl"].value = surl;
	return true;
}
function dl(fs_id, timestamp, sign, randsk, share_id, uk, bdstoken, filesize) {
	var form = $('<form method="post" action="?download" target="_blank"></form>');
	form.append(`<input type="hidden" name="fs_id" value="${fs_id}"/><input type="hidden" name="time" value="${timestamp}"/><input type="hidden" name="sign" value="${sign}"/>
		<input type="hidden" name="randsk" value="${randsk}"/><input type="hidden" name="share_id" value="${share_id}"/><input type="hidden" name="uk" value="${uk}"/><input type="hidden" name="bdstoken" value="${bdstoken}"/><input type="hidden" name="filesize" value="${filesize}"/>`);
	$(document.body).append(form); form.submit();
}
function OpenDir(path, pwd, share_id, uk, surl, randsk, sign, timestamp, bdstoken) {
	var form = $('<form method="post"></form>');
	form.append(`<input type="hidden" name="dir" value="${path}"/><input type="hidden" name="pwd" value="${pwd}"/><input type="hidden" name="surl" value="${surl}"/>
	<input type="hidden" name="share_id" value="${share_id}"/><input type="hidden" name="uk" value="${uk}"/><input type="hidden" name="randsk" value="${randsk}"/><input type="hidden" name="sign" value="${sign}"/><input type="hidden" name="timestamp" value="${timestamp}"/><input type="hidden" name="bdstoken" value="${bdstoken}"/>`);
	$(document.body).append(form); form.submit();
}
function getIconClass(filename) {
	var filetype = {
		file_video: ["wmv", "rmvb", "mpeg4", "mpeg2", "flv", "avi", "3gp", "mpga", "qt", "rm", "wmz", "wmd", "wvx", "wmx", "wm", "mpg", "mp4", "mkv", "mpeg", "mov", "asf", "m4v", "m3u8", "swf"],
		file_audio: ["wma", "wav", "mp3", "aac", "ra", "ram", "mp2", "ogg", "aif", "mpega", "amr", "mid", "midi", "m4a", "flac"],
		file_image: ["jpg", "jpeg", "gif", "bmp", "png", "jpe", "cur", "svg", "svgz", "ico", "webp", "tif", "tiff"],
		file_archive: ["rar", "zip", "7z", "iso"],
		windows: ["exe"],
		apple: ["ipa"],
		android: ["apk"],
		file_alt: ["txt", "rtf"],
		file_excel: ["xls", "xlsx", "xlsm", "xlsb", "csv", "xltx", "xlt", "xltm", "xlam"],
		file_word: ["doc", "docx", "docm", "dotx"],
		file_powerpoint: ["ppt", "pptx", "potx", "pot", "potm", "ppsx", "pps", "ppam", "ppa"],
		file_pdf: ["pdf"],
	};
	var point = filename.lastIndexOf(".");
	var t = filename.substr(point + 1);
	if (t === "") return "";
	t = t.toLowerCase();
	for (var icon in filetype) for (var type in filetype[icon]) if (t === filetype[icon][type]) return "fa-" + icon.replace('_', '-');
	return "";
}
function OpenRoot(surl, pwd) {
	var form = $('<form method="post"></form>');
	form.append(`<input type="hidden" name="surl" value="${surl}"/><input type="hidden" name="pwd" value="${pwd}"/>`);
	$(document.body).append(form); form.submit();
}
function Getpw() {
	var link = document.forms["form1"]["surl"].value;
	var pw = link.match(/提取码.? *(\w{4})/);
	if (pw != null) {
		document.forms["form1"]["pwd"].value = pw[1];
	}
}

// 以下推送到aria2代码来自TkzcM
function utoa(str) {
	return window.btoa(unescape(encodeURIComponent(str)));
}
// base64 encoded ascii to ucs-2 string
function atou(str) {
	return decodeURIComponent(escape(window.atob(str)));
}
function getCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	}
	return null;
}
async function checkVer() {
	let token = $('#token').val()
	let aria2url = $('#url').val()
	if (token != "") {
		postVer = JSON.stringify({
			jsonrpc: '2.0',
			method: 'aria2.getVersion',
			id: 'baiduwp',
			params: ['token:' + token]
		})
	} else {
		postVer = JSON.stringify({
			jsonrpc: '2.0',
			method: 'aria2.getVersion',
			id: 'baiduwp',
			params: []
		})
	}
	const getVer = await fetch(aria2url, {
		body: postVer,
		method: 'POST',
		headers: { 'content-type': 'text/json' }
	}).catch((error) => {
		Swal.fire('Sorry~', '连接aria2失败', 'error')
	});
	if (getVer != null)
		if (await getVer.status === 200) {
			Swal.fire('成功', '发现' + JSON.parse(await getVer.text()).result.version + '版aria2，请点击Send', 'success')
		}
		else {
			Swal.fire('Sorry~', '连接aria2失败', 'error')
		}
}
async function addUri() {
	let token = $('#token').val()
	let aria2url = $('#url').val()
	let filename = $('#filename b').text();
	// Thanks to acgotaku/BaiduExporter
	const httpurl = $('#http')[0].href
	const httpsurl = $('#https')[0].href
	const headerOption = ['User-Agent: LogStatistic']
	let post
	let postVer
	if (token != "") {// 构造post请求
		postVer = JSON.stringify({
			jsonrpc: '2.0',
			method: 'aria2.getVersion',
			id: 'baiduwp',
			params: ['token:' + token]
		})
		post = JSON.stringify({ jsonrpc: '2.0', id: 'baiduwp', method: 'aria2.addUri', params: ["token:" + token, [httpurl, httpsurl], { header: headerOption, out: filename }] })//修复aria2文件名问题
	}
	else {
		postVer = JSON.stringify({
			jsonrpc: '2.0',
			method: 'aria2.getVersion',
			id: 'baiduwp',
			params: []
		})
		post = JSON.stringify({ jsonrpc: '2.0', id: 'baiduwp', method: 'aria2.addUri', params: [[httpurl, httpsurl], { header: headerOption, out: filename }] })//修复aria2文件名问题
	}
	const getVer = await fetch(aria2url, {
		body: postVer,
		method: 'POST',
		headers: { 'content-type': 'text/json' }
	}).catch((error) => {
		Swal.fire('Sorry~', '连接aria2失败', 'error')
	});
	if (getVer != null)
		if (await getVer.status === 200) {
			Swal.fire('detected aria2 version ' + JSON.parse(await getVer.text()).result.version, 'sending request...', 'success')
			const sendLink = await fetch(aria2url, { body: post, method: 'POST', headers: { 'content-type': 'text/json' } }).catch((e) => { Swal.fire('Sorry~', e, 'error') })
			if (sendLink != null)
				if (await sendLink.status === 200) {
					Swal.fire('成功发送', 'Good Luck', 'success')
					document.cookie = 'aria2url=' + utoa(aria2url) // add aria2 config to cookie
					if (token != "" && token != null) {
						document.cookie = 'aria2token=' + utoa(token)
					}
				}
				else {
					Swal.fire('Sorry~', '连接aria2失败', 'error')
				}
		} else {
			Swal.fire('Sorry~', '连接aria2失败', 'error')
		}
}
