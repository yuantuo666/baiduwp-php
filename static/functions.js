/**
 * PanDownload 网页复刻版，JS函数文件
 *
 * 许多函数来源于github，详见项目里的Thanks
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://imwcr.cn/
 * @link https://space.bilibili.com/88197958
 *
 */
function validateForm() {
	var input = document.forms["form1"]["surl"];
	var link = input.value;
	if (link == null || link === "") { input.focus(); Swal.fire("提示", "请填写分享链接", "info"); return false; }
	var uk = link.match(/uk=(\d+)/), shareid = link.match(/shareid=(\d+)/);
	if (uk != null && shareid != null) {
		input.value = "";
		$("form").append(`<input type="hidden" name="uk" value="${uk[1]}"/><input type="hidden" name="shareid" value="${shareid[1]}"/>`);
		return true;
	}
	var surl = link.match(/surl=([A-Za-z0-9-_]+)/);
	if (surl == null) {
		surl = link.match(/1[A-Za-z0-9-_]+/);
		if (surl == null) {
			input.focus(); Swal.fire("提示", "分享链接填写有误，请检查", "info"); return false;
		} else surl = surl[0];
	} else surl = "1" + surl[1];
	input.value = surl;
	return true;
}
function dl(fs_id, timestamp, sign, randsk, share_id, uk) {
	var form = $('<form method="post" action="?download" target="_blank"></form>');
	form.append(`<input type="hidden" name="fs_id" value="${fs_id}"/><input type="hidden" name="time" value="${timestamp}"/><input type="hidden" name="sign" value="${sign}"/>
		<input type="hidden" name="randsk" value="${randsk}"/><input type="hidden" name="share_id" value="${share_id}"/><input type="hidden" name="uk" value="${uk}"/>`);
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
function Getpw() {
	var link = $("[name='surl']").val();
	var pw = link.match(/(提取码|pwd=|pwd:|密码)( |:|：)*([a-zA-Z0-9]{4})/i);
	if (pw != null && pw.length === 4) {
		$("[name='pwd']").val(pw[3]);
	}
}
function SubmitLink() {
	var link = $("[name='surl']").val();

	var uk = link.match(/uk=(\d+)/),
		shareid = link.match(/shareid=(\d+)/);
	if (uk != null && shareid != null) {
		Swal.fire("Tip", "暂不支持老版本分享链接，请保存到网盘后重新分享", "info");
		return false;
	}

	var surl = link.match(/surl=([A-Za-z0-9-_]+)/);
	if (surl == null) {
		surl = link.match(/1[A-Za-z0-9-_]+/);
		if (surl != null) {
			surl = surl[0];
		}
	} else {
		surl = "1" + surl[1];
	}

	if (surl == null || surl === "") {
		$("[name='surl']").focus();
		Swal.fire("Tip", "未检测到有效百度网盘分享链接，请检查输入的链接", "info");
		return false;
	}
	var pw = $("[name='pwd']").val();
	if (pw.length != 0 && pw.length != 4) {
		$("[name='pwd']").focus();
		Swal.fire("Tip", "提取码错误，请检查", "info");
		return false;
	}
	OpenRoot(surl, pw);
	$("#index").hide();
	$("#list").show();
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
function addUri() {
	//配置
	var wsurl = $('#wsurl').val();
	var uris = [$('#http')[0].href, $('#https')[0].href];
	var token = $('#token').val();
	var filename = $('#filename b').text();;


	var options = {
		"max-connection-per-server": "16",
		"user-agent": "LogStatistic"
	};
	if (filename != "") {
		options.out = filename;
	}

	json = {
		"id": "baiduwp-php",
		"jsonrpc": '2.0',
		"method": 'aria2.addUri',
		"params": [uris, options],
	};

	if (token != "") {
		json.params.unshift("token:" + token); // 坑死了，必须要加在第一个
	}

	patt = /^wss?\:\/\/(((([A-Za-z0-9]+[A-Za-z0-9\-]+[A-Za-z0-9]+)|([A-Za-z0-9]+))(\.(([A-Za-z0-9]+[A-Za-z0-9\-]+[A-Za-z0-9]+)|([A-Za-z0-9]+)))*(\.[A-Za-z0-9]{2,10}))|(localhost)|((([01]?\d?\d)|(2[0-4]\d)|(25[0-5]))(\.([01]?\d?\d)|(2[0-4]\d)|(25[0-5])){3})|((\[[A-Za-z0-9:]{2,39}\])|([A-Za-z0-9:]{2,39})))(\:\d{1,5})?(\/.*)?$/;
	if (!patt.test(wsurl)) {
		Swal.fire('地址错误', 'WebSocket 地址不符合验证规则，请检查是否填写正确！', 'error');
		return;
	}
	var ws = new WebSocket(wsurl);

	ws.onerror = event => {
		console.log(event);
		Swal.fire('连接错误', 'Aria2 连接错误，请打开控制台查看详情！', 'error');
	};
	ws.onopen = () => { ws.send(JSON.stringify(json)); }

	ws.onmessage = event => {
		console.log(event);
		received_msg = JSON.parse(event.data);
		if (received_msg.error !== undefined) {
			if (received_msg.error.code === 1) Swal.fire('通过RPC连接失败', '请打开控制台查看详细错误信息，返回信息：' + received_msg.error.message, 'error');
		}
		switch (received_msg.method) {
			case "aria2.onDownloadStart":
				Swal.fire('Aria2 发送成功', 'Aria2 已经开始下载！' + filename, 'success');

				localStorage.setItem('aria2wsurl', wsurl);// add aria2 config to SessionStorage
				if (token != "" && token != null) localStorage.setItem('aria2token', token);
				break;

			case "aria2.onDownloadError": ;
				Swal.fire('下载错误', 'Aria2 下载错误！', 'error');
				break;

			case "aria2.onDownloadComplete":
				Swal.fire('下载完成', 'Aria2 下载完成！', 'success');
				break;

			default:
				break;
		}

		// version = received_msg.result.version;
	};
}

function makeQRCode(element, text, size = 512, correctLevel = QRCode.CorrectLevel.M) { // 二维码
	return new QRCode(element, {
		text, correctLevel,
		height: size, width: size
	});
}

async function getAPI(method) { // 获取 API 数据
	try {
		const response = await fetch(`api.php?m=${method}`, { // fetch API
			credentials: 'same-origin' // 发送验证信息 (cookies)
		});
		if (response.ok) { // 判断是否出现 HTTP 异常
			return {
				success: true,
				data: await response.json() // 如果正常，则获取 JSON 数据
			}
		} else { // 若不正常，返回异常信息
			return {
				success: false,
				msg: `服务器返回异常 HTTP 状态码：HTTP ${response.status} ${response.statusText}.`
			};
		}
	} catch (reason) { // 若与服务器连接异常
		return {
			success: false,
			msg: '连接服务器过程中出现异常，消息：' + reason.message
		};
	}
}

function Backtoindex() {
	$("#list").hide();
	$("#index").show();
}
// https://stackoverflow.com/questions/15900485/correct-way-to-convert-size-in-bytes-to-kb-mb-gb-in-javascript
function formatBytes(a, b = 2) {
	if (0 === a) return "0 Bytes";
	const c = 0 > b ? 0 : b,
		d = Math.floor(Math.log(a) / Math.log(1024));
	return parseFloat((a / Math.pow(1024, d)).toFixed(c)) + " " + ["Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"][d]
}

function formatDate(time, format = 'YY-MM-DD hh:mm:ss') {
	if (time == undefined) return "--";
	time = Number(time + "000");
	var date = new Date(time);

	var year = date.getFullYear(),
		month = date.getMonth() + 1,
		day = date.getDate(),
		hour = date.getHours(),
		min = date.getMinutes(),
		sec = date.getSeconds();
	var preArr = Array.apply(null, Array(10)).map(function (elem, index) {
		return '0' + index;
	});

	var newTime = format.replace(/YY/g, year)
		.replace(/MM/g, preArr[month] || month)
		.replace(/DD/g, preArr[day] || day)
		.replace(/hh/g, preArr[hour] || hour)
		.replace(/mm/g, preArr[min] || min)
		.replace(/ss/g, preArr[sec] || sec);

	return newTime;
}

async function OpenRoot(surl, pwd) {
	Swal.fire({
		title: "正在获取文件列表",
		icon: "info",
		html: "请稍候...",
		allowOutsideClick: false,
		allowEscapeKey: false,
	});
	Swal.showLoading();
	try {
		data = `surl=${surl}&pwd=${pwd}`;
		await fetch(`api.php?m=GetList`, { // fetch API
			credentials: 'same-origin',
			method: 'POST',
			body: data,
			headers: new Headers({
				'Content-Type': 'application/x-www-form-urlencoded'
			})
		}).then(function (response) {
			return response.json();
		})
			.then(function (json) {
				console.log(json);
				if (json.error == 0) {
					// success
					LoadList(json);
					Swal.close();
				} else {
					// fail
					Swal.fire(json.title, json.msg, "error");
					Backtoindex();
				}

			});

	} catch (reason) {
		Swal.fire("获取文件列表失败", "连接服务器过程中出现异常，消息：" + reason.message, "error");
		Backtoindex();
	}
}

async function OpenDir(path) {
	Swal.fire({
		title: "正在获取文件列表",
		icon: "info",
		html: "请稍候...",
		allowOutsideClick: false,
		allowEscapeKey: false,
	});
	Swal.showLoading();
	try {
		randsk = encodeURIComponent(files.dirdata.randsk);
		dir = encodeURIComponent(path);
		data = `surl=${files.dirdata.surl}&pwd=${files.dirdata.pwd}&dir=${dir}&randsk=${randsk}&uk=${files.dirdata.uk}&sign=${files.dirdata.sign}&time=${files.dirdata.timestamp}&shareid=${files.dirdata.shareid}`;
		await fetch(`api.php?m=GetList`, { // fetch API
			credentials: 'same-origin',
			method: 'POST',
			body: data,
			headers: new Headers({
				'Content-Type': 'application/x-www-form-urlencoded'
			})
		}).then(function (response) {
			return response.json();
		})
			.then(function (json) {
				console.log(json);
				if (json.error == 0) {
					// success
					LoadList(json);
					Swal.close();
				} else {
					// fail
					Swal.fire(json.title, json.msg, "error");
				}

			});

	} catch (reason) {
		Swal.fire("获取文件列表失败", "连接服务器过程中出现异常，消息：" + reason.message, "error");
	}
}


function LoadList(json) {
	if (typeof (json) == "string") files = JSON.parse(json);
	else files = json;
	if (files.error != 0) {
		Swal.fire("无法加载列表", "请刷新页面重试，错误代码：" + files.error, "error");
		return;
	}
	var Src = `<li class="breadcrumb-item"><a href="javascript:OpenRoot('${files.dirdata.surl}','${files.dirdata.pwd}');">All files</a></li>`;
	for (var i = 0; i < files.dirdata.src.length; i++) {
		Dir = files.dirdata.src[i];
		Active = (Dir.isactive) ? "active" : "";
		fullsrc = Dir.fullsrc.replace(/\\/g, "\\\\").replace(/&/g, '&amp;').replace(/\'/g, "\\\'"); // use &amp; to replace & to avoid error
		Src = Src + `<li class="breadcrumb-item ${Active}"><a href="javascript:OpenDir('${fullsrc}');">${Dir.dirname}</a></li>`;
	}
	Src = Src + `<span class="mx-2">(${files.filenum} 个文件)<span>`;

	$("#dir-list").html(Src);

	var List = "";
	var filesnum = 0;

	for (var i = 0; i < files.filedata.length; i++) {
		Files = files.filedata[i];
		Time = formatDate(Files.uploadtime, 'YY/MM/DD hh:mm:ss');
		Num = (Array(3).join(0) + (i + 1)).slice(-3);
		if (files.filedata[i].isdir) {
			// dir
			path = Files.path.replace(/\\/g, "\\\\").replace(/&/g, '&amp;').replace(/\'/g, "\\\'"); // use &amp; to replace & to avoid error
			List = List + `<li class="list-group-item border-muted text-muted py-2" id="item${i}"><i class="far fa-folder mr-2"></i>
<a href="javascript:OpenDir('${path}');" class="filename">${Files.name}</a>
<br><span>${Num} | ${Time}</span>
</li>`
		} else {
			// file
			Size = formatBytes(Files.size);
			List = List + `<li class="list-group-item border-muted text-muted py-2" id="item${i}"><i class="far fa-file mr-2"></i>
<a href="javascript:Download('${i}');" class="filename">${Files.name}</a>
<br><span>${Num} | ${Time} | ${Size}</span>
</li>`;
			filesnum++;
		}
	}
	$("#files-list").html(List);

	// load file icon
	$(".fa-file").each(function () {
		var icon = getIconClass($(this).next().text());
		if (icon !== "") {
			if ($.inArray(icon, ['fa-windows', 'fa-android', 'fa-apple']) >= 0) $(this).removeClass("far").addClass("fab");
			$(this).removeClass("fa-file").addClass(icon);
		}
	});
}
