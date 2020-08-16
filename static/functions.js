function validateForm() {
	var link = document.forms["form1"]["surl"].value;
	if (link == null || link === "") { document.forms["form1"]["surl"].focus(); return false; }
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
			document.forms["form1"]["surl"].focus(); return false;
		} else surl = surl[0];
	} else surl = "1" + surl[1];
	document.forms["form1"]["surl"].value = surl;
	return true;
}
function dl(fs_id, timestamp, sign, randsk, share_id, uk) {
	var form = $('<form method="post" action="?download" target="_blank"></form>');
	form.append(`<input type="hidden" name="fs_id" value="${fs_id}"/><input type="hidden" name="time" value="${timestamp}"/><input type="hidden" name="sign" value="${sign}"/>
		<input type="hidden" name="randsk" value="${randsk}"/><input type="hidden" name="share_id" value="${share_id}"/><input type="hidden" name="uk" value="${uk}"/>`);
	$(document.body).append(form); form.submit();
}
function OpenDir(path, pwd, share_id, uk, surl) {
	var form = $('<form method="post"></form>');
	form.append(`<input type="hidden" name="dir" value="${path}"/><input type="hidden" name="pwd" value="${pwd}"/><input type="hidden" name="surl" value="${surl}"/>
		<input type="hidden" name="share_id" value="${share_id}"/><input type="hidden" name="uk" value="${uk}"/>`);
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
		file_excel: ["xls", "xlsx"], // xlsm 等以及模板？
		file_word: ["doc", "docx"],
		file_powerpoint: ["ppt", "pptx"],
		file_pdf: ["pdf"],
	};
	var point = filename.lastIndexOf(".");
	var t = filename.substr(point + 1);
	if (t === "") return "";
	t = t.toLowerCase();
	for (var icon in filetype) for (var type in filetype[icon]) if (t === filetype[icon][type]) return "fa-" + icon.replace('_', '-');
	return "";
}
function OpenRoot(surl, pwd){
	var form = $('<form method="post"></form>');
	form.append(`<input type="hidden" name="surl" value="${surl}"/><input type="hidden" name="pwd" value="${pwd}"/>`);
	$(document.body).append(form); form.submit();
}