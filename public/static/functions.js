/**
 * PanDownload 网页复刻版，JS函数文件
 *
 * 许多函数来源于github，详见项目里的Thanks
 *
 * @author Yuan_Tuo <yuantuo666@gmail.com>
 * @link https://github.com/yuantuo666/baiduwp-php
 *
 */
const ROOT_PATH = window.location.pathname.slice(0,-1); // 获取网站根目录
addEventListener('DOMContentLoaded', function () {
	window.downloadpage = new bootstrap.Modal('#downloadpage', {
		keyboard: false,
		backdrop: 'static'
	});

});
function http_build_query(params, numeric_prefix, arg_separator) {
	let value, key, tmp = [];
	const _http_build_query_helper = (key, val, arg_separator) => {
		let k, tmp = [];
		if (val === true) {
			val = "1";
		} else if (val === false) {
			val = "0";
		}
		if (val !== null) {
			if (typeof val === "object") {
				for (k in val) {
					if (val[k] !== null) {
						tmp.push(_http_build_query_helper(key + "[" + k + "]", val[k], arg_separator));
					}
				}
				return tmp.join(arg_separator);
			} else if (typeof val !== "function") {
				return encodeURIComponent(key) + "=" + encodeURIComponent(val);
			} else {
				throw new Error('There was an error processing for http_build_query().');
			}
		} else {
			return '';
		}
	};

	if (!arg_separator) {
		arg_separator = "&";
	}
	for (key in params) {
		value = params[key];
		if (numeric_prefix && !isNaN(key)) {
			key = String(numeric_prefix) + key;
		}
		let query = _http_build_query_helper(key, value, arg_separator);
		if (query !== '') {
			tmp.push(query);
		}
	}

	return tmp.join(arg_separator);
}
// https://stackoverflow.com/questions/15900485/correct-way-to-convert-size-in-bytes-to-kb-mb-gb-in-javascript
function formatBytes(a, b = 2) {
	if (0 === a) return "0 Bytes";
	const c = 0 > b ? 0 : b,
		d = Math.floor(Math.log(a) / Math.log(1024));
	return parseFloat((a / Math.pow(1024, d)).toFixed(c)) + " " + ["Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"][d]
}
function formatDate(time, format = 'YY-MM-DD hh:mm:ss') {
	if (time === undefined) return "--";
	time = Number(time + "000");
	let date = new Date(time);

	let year = date.getFullYear(),
		month = date.getMonth() + 1,
		day = date.getDate(),
		hour = date.getHours(),
		min = date.getMinutes(),
		sec = date.getSeconds();
	let preArr = Array.apply(null, Array(10)).map(function (elem, index) {
		return '0' + index;
	});

	return format.replace(/YY/g, year)
		.replace(/MM/g, preArr[month] || month)
		.replace(/DD/g, preArr[day] || day)
		.replace(/hh/g, preArr[hour] || hour)
		.replace(/mm/g, preArr[min] || min)
		.replace(/ss/g, preArr[sec] || sec);
}
function getIconClass(filename) {
	const filetype = {
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
	let point = filename.lastIndexOf(".");
	let t = filename.substring(point + 1);
	if (t === "") return "";
	t = t.toLowerCase();
	for (let icon in filetype) for (let type in filetype[icon]) if (t === filetype[icon][type]) return "fa-" + icon.replace('_', '-');
	return "";
}
function Getpw() {
	let link = $("[name='surl']").val();
	let pw = link.match(/(提取码|pwd=|pwd:|密码|%E6%8F%90%E5%8F%96%E7%A0%81|%E5%AF%86%E7%A0%81)( |:|：|%EF%BC%9A|%20)*([a-zA-Z0-9]{4})/i);
	if (pw != null && pw.length === 4) {
		$("[name='pwd']").val(pw[3]);
	}
}
function SubmitLink() {
	let link = $("[name='surl']").val();

	let surl = null;

	let uk = link.match(/uk=(\d+)/),
		shareid = link.match(/shareid=(\d+)/);
	if (uk != null && shareid != null) {
		let tmp = uk[1] + "&" + shareid[1];
		surl = '2' + window.btoa(tmp) // base64 encode
	} else {
		surl = link.match(/surl=([A-Za-z0-9-_]+)/);
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
	}

	let pw = $("[name='pwd']").val();
	if (pw.length !== 0 && pw.length !== 4) {
		$("[name='pwd']").focus();
		Swal.fire("Tip", "提取码错误，请检查", "info");
		return false;
	}

	let password = $("[name='password']").val();

	OpenRoot(surl, pw, password);
	navigate('list')
}
function addUri() {
	Swal.fire({
		title: '正在添加下载任务',
		html: '请稍后...',
		allowOutsideClick: false,
		allowEscapeKey: false,
		allowEnterKey: false,
		showConfirmButton: false,
	});
	Swal.showLoading();
	//配置
	let wsurl = $('#wsurl').val();
	let uris = [$('input#downloadlink').val()];
	let token = $('#token').val();
	let filename = $('b#filename').text();
	let ua = $('b#ua').text();

	let options = {
		"max-connection-per-server": "16",
		"user-agent": ua
	};
	if (filename !== "") {
		options.out = filename;
	}

	let json = {
		"id": "baiduwp-php",
		"jsonrpc": '2.0',
		"method": 'aria2.addUri',
		"params": [uris, options],
	};

	if (token !== "") {
		json.params.unshift("token:" + token); // 坑死了，必须要加在第一个
	}

	patt = /^wss?\:\/\/(((([A-Za-z0-9]+[A-Za-z0-9\-]+[A-Za-z0-9]+)|([A-Za-z0-9]+))(\.(([A-Za-z0-9]+[A-Za-z0-9\-]+[A-Za-z0-9]+)|([A-Za-z0-9]+)))*(\.[A-Za-z0-9]{2,10}))|(localhost)|((([01]?\d?\d)|(2[0-4]\d)|(25[0-5]))(\.([01]?\d?\d)|(2[0-4]\d)|(25[0-5])){3})|((\[[A-Za-z0-9:]{2,39}\])|([A-Za-z0-9:]{2,39})))(\:\d{1,5})?(\/.*)?$/;
	if (!patt.test(wsurl)) {
		Swal.fire('地址错误', 'WebSocket 地址不符合验证规则，请检查是否填写正确！', 'error');
		return;
	}
	let ws = new WebSocket(wsurl);

	ws.onerror = event => {
		console.log(event);
		Swal.fire('连接错误', 'Aria2 连接错误，请打开控制台查看详情！', 'error');
	};
	ws.onopen = () => { ws.send(JSON.stringify(json)); }

	ws.onmessage = event => {
		console.log(event);
		let received_msg = JSON.parse(event.data);
		if (received_msg.error !== undefined) {
			if (received_msg.error.code === 1) Swal.fire('通过RPC连接失败', '请打开控制台查看详细错误信息，返回信息：' + received_msg.error.message, 'error');
		}
		switch (received_msg.method) {
			case "aria2.onDownloadStart":
				Swal.fire('Aria2 发送成功', 'Aria2 已经开始下载！' + filename, 'success');

				localStorage.setItem('aria2wsurl', wsurl);// add aria2 config to SessionStorage
				if (token !== "" && token != null) localStorage.setItem('aria2token', token);
				break;

			case "aria2.onDownloadError":
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
	method = ROOT_PATH + method;
	try {
		const response = await fetch(method, { // fetch API
			headers: new Headers({
				'Accept': 'application/json'
			}),
			credentials: 'same-origin' // 发送验证信息 (cookies)
		});
		if (response.ok) { // 判断是否出现 HTTP 异常
			return {
				success: true,
				data: await response.json() // 如果正常，则获取 JSON 数据
			}
		} else { // 若不正常，返回异常信息
			const json = await response.json();
			const message = json.message;
			return {
				success: false,
				msg: `服务器返回异常 HTTP 状态码：HTTP ${response.status} ${response.statusText}. ${message}`
			};
		}
	} catch (reason) { // 若与服务器连接异常
		return {
			success: false,
			msg: '连接服务器过程中出现异常，消息：' + reason.message
		};
	}
}

function navigate(path) {
	if (path && path.substring(0, 1) === "/") path = path.substring(1);
	$("#index").hide();
	$("#list").hide();
	$("#help").hide();
	$("#usersettings").hide();

	try {
		if (path === "" || $(`div.page#${path}`).length === 0) path = "index";
	} catch {
		path = "index";
	}

	if (path === "index") {
		checkPassword();
	}

	window.location.hash = "/" + path;
	$(`#${path}`).show();
}
async function OpenRoot(surl, pwd, password = "") {
	Swal.fire({
		title: "正在获取文件列表",
		icon: "info",
		html: "请稍候...",
		allowOutsideClick: false,
		allowEscapeKey: false,
	});
	Swal.showLoading();
	let data;
	try {
		data = {
			surl,
			pwd,
			password
		}
		await fetch(`${ROOT_PATH}/parse/list`, { // fetch API
			credentials: 'same-origin',
			method: 'POST',
			body: http_build_query(data),
			headers: new Headers({
				'Accept': 'application/json',
				'Content-Type': 'application/x-www-form-urlencoded'
			})
		}).then(function (response) {
			return response.json();
		})
			.then(function (json) {
				console.log(json);
				if (json.error === 0) {
					// success
					LoadList(json);
					Swal.close();
				} else {
					// fail
					Swal.fire(json.title || "获取文件列表失败", json.msg, "error");
					navigate('index');
				}

			});

	} catch (reason) {
		Swal.fire("获取文件列表失败", "连接服务器过程中出现异常，消息：" + reason.message, "error");
		navigate('index');
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
	let data;
	try {
		data = {
			dir: path,
			...files.dirdata
		}
		await fetch(`${ROOT_PATH}/parse/list`, { // fetch API
			credentials: 'same-origin',
			method: 'POST',
			body: http_build_query(data),
			headers: new Headers({
				'Accept': 'application/json',
				'Content-Type': 'application/x-www-form-urlencoded'
			})
		}).then(function (response) {
			return response.json();
		})
			.then(function (json) {
				console.log(json);
				if (json.error === 0) {
					// success
					LoadList(json);
					Swal.close();
				} else {
					// fail
					Swal.fire(json.title || "获取文件列表失败", json.msg, "error");
				}

			});

	} catch (reason) {
		Swal.fire("获取文件列表失败", "连接服务器过程中出现异常，消息：" + reason.message, "error");
	}
}


function LoadList(json) {
	let i, files;
	if (typeof (json) == "string") files = JSON.parse(json);
	else files = json;
	if (files.error !== 0) {
		Swal.fire("无法加载列表", "请刷新页面重试，错误代码：" + files.error, "error");
		return;
	}
	window.files = files;
	let Src = `<li class="breadcrumb-item"><a class="filename" href="javascript:OpenRoot('${files.dirdata.surl}','${files.dirdata.pwd}');">全部文件</a></li>`;
	let Dir;
	let Active;
	let fullsrc;
	for (i = 0; i < files.dirdata.src.length; i++) {
		Dir = files.dirdata.src[i];
		Active = (Dir.isactive) ? "active" : "";
		fullsrc = Dir.fullsrc.replace(/\\/g, "\\\\").replace(/&/g, '&amp;').replace(/'/g, "\\\'"); // use &amp; to replace & to avoid error
		Src = Src + `<li class="breadcrumb-item ${Active}"><a class="filename" href="javascript:OpenDir('${fullsrc}');">${Dir.dirname}</a></li>`;
	}
	Src = Src + `<span class="mx-2">(${files.filenum} 个文件)<span>`;

	$("#dir-list").html(Src);

	let List = "";
	let Files;
	let Time;
	let Num;
	let path;
	let Size;
	for (i = 0; i < files.filedata.length; i++) {
		Files = files.filedata[i];
		Time = formatDate(Files.uploadtime, 'YY/MM/DD hh:mm:ss');
		Num = (Array(3).join(0) + (i + 1)).slice(-3);
		if (files.filedata[i].isdir) {
			// dir
			path = Files.path.replace(/\\/g, "\\\\").replace(/&/g, '&amp;').replace(/'/g, "\\\'"); // use &amp; to replace & to avoid error
			List = List + `<li class="list-group-item border-muted text-muted py-2" id="item${i}"><i class="far fa-folder mr-2"></i>
<a onclick="OpenDir('${path}');" class="filename">${Files.name}</a>
<br><span>${Num} | ${Time}</span>
</li>`
		} else {
			// file
			Size = formatBytes(Files.size);
			List = List + `<li class="list-group-item border-muted text-muted py-2" id="item${i}"><i class="far fa-file mr-2"></i>
<a onclick="Download(${i});" class="filename">${Files.name}</a>
<br><span>${Num} | ${Time} | ${Size}</span>
</li>`;
		}
	}
	$("#files-list").html(List);

	// load file icon
	$(".fa-file").each(function () {
		let icon = getIconClass($(this).next().text());
		if (icon !== "") {
			if ($.inArray(icon, ['fa-windows', 'fa-android', 'fa-apple']) >= 0) $(this).removeClass("far").addClass("fab");
			$(this).removeClass("fa-file").addClass(icon);
		}
	});
}
async function Download(index = 0) {
	Swal.fire({
		title: "正在获取下载链接",
		icon: "info",
		html: "请稍候...",
		allowOutsideClick: false,
		allowEscapeKey: false,
	});
	Swal.showLoading();
	let files = window.files;
	let downloadfile = files.filedata[index];

	let data = {
		fs_id: downloadfile.fs_id,
		...files.dirdata
	}

	try {
		await fetch(`${ROOT_PATH}/parse/link`, { // fetch API
			credentials: 'same-origin',
			method: 'POST',
			body: http_build_query(data),
			headers: new Headers({
				'Accept': 'application/json',
				'Content-Type': 'application/x-www-form-urlencoded'
			})
		}).then(function (response) {
			return response.json();
		}).then(function (json) {
			console.log(json);
			let Size;
			let Time;
			let html;
			if (json.error === 0) {
				Swal.close();
				// success
				Size = formatBytes(json.filedata.size);
				Time = formatDate(json.filedata.uploadtime, 'YY/MM/DD hh:mm:ss');

				html = `<div class="list-group">
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">文件名称</label>
                <div class="col-sm-9">
                    <b id="filename">${json.filedata.filename}</b>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">文件大小</label>
                <div class="col-sm-9">
                    <b>${Size}</b>
                </div>
            </div>
			<div class="mb-3 row">
                <label class="col-sm-3 col-form-label">MD5</label>
                <div class="col-sm-9">
                    <b>${json.filedata.md5}</b>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">上传时间</label>
                <div class="col-sm-9">
                    <b>${Time}</b>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">User-Agent</label>
                <div class="col-sm-9">
                    <b id="ua">${json.user_agent}</b>
                </div>
            </div>`
				if (json.filedata.size <= 52428800) {
					html = html + `
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">下载地址</label>
                <div class="col-sm-9 input-group">
                    <input class="form-control" id="downloadlink" aria-describedby="copy" value="${json.directlink}"/>
                    <a type="button" class="btn btn-outline-secondary" id="copy" href="${json.directlink}" target="_blank"><i class="fas fa-download"></i></a>
                </div>
            </div>
        </div>`;
				} else {
					html = html + `
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">下载地址</label>
                <div class="col-sm-9 input-group">
                    <input class="form-control" id="downloadlink" aria-describedby="copy" value="${json.directlink}"/>
                    <button type="button" class="btn btn-outline-secondary" id="copy" onclick="CopyDownloadLink()"><i class="fas fa-copy"></i></button>
                </div>
            </div>
        </div>`;
				}
				$("#downloadlinkdiv").html(html);
				if (json.directlink.indexOf("//qdall01") !== -1) {
					$("#limit-tip").show();
				} else {
					$("#limit-tip").hide();
				}
				$('#downloadpage').modal('show');
				try {
					let filec_address = create_fileu_address({
						uri: json.directlink,
						user_agent: json.user_agent,
						file_name: json.filedata.filename
					});
					$("#filecxx").attr("href", filec_address);
					$("#filecxx").show();
				} catch (e) {
					$("#filecxx").hide();
				}

			} else {
				Swal.fire(json.title || "获取下载链接失败", json.msg, "error");
			}

		});

	} catch (reason) {
		Swal.fire("获取下载链接失败", reason.message, "error");
	}

}
function CopyDownloadLink() {
	const Success = () => {
		Swal.fire({
			title: "成功复制下载链接",
			html: "请设置下载器的 User-Agent 为 <b id='ua'>" + $("#ua").text() + "</b> 后下载，参考使用帮助",
			timer: 3000,
			timerProgressBar: true,
			icon: "success"
		});
	}

	// In unsecure site will not work, add check
	if (navigator.clipboard && window.isSecureContext) {
		navigator.clipboard.writeText($("input#downloadlink").val()).then(function () {
			console.log('Copying to clipboard was successful!');
		}, function (err) {
			console.error('Could not copy text: ', err);
			$("input#downloadlink").select();
			document.execCommand("copy");
			Success();
		}).then(function () {
			Success();
		});
	} else {
		$("input#downloadlink").select();
		document.execCommand("copy");
		Success();
	}
}