$(".fa-file").each(function () {
	var icon = getIconClass($(this).next().text());
	if (icon !== "") {
		if ($.inArray(icon, ['fa-windows', 'fa-android', 'fa-apple']) >= 0) $(this).removeClass("far").addClass("fab");
		$(this).removeClass("fa-file").addClass(icon);
	}
});

$('[data-qrcode-attr]').each(function (i) {
	this.outerHTML = this.outerHTML + `<span class="qrcode" id=TEMP_QRCODE_ATTR_ID${i}></span>`;
	const codeDiv = document.querySelector(`#TEMP_QRCODE_ATTR_ID${i}`);
	makeQRCode(codeDiv, this.getAttribute(this.dataset.qrcodeAttr));
	codeDiv.removeAttribute('id');
});

$('[data-qrcode-text]').each(function (i) {
	this.outerHTML = this.outerHTML + `<span class="qrcode" id=TEMP_QRCODE_TEXT_ID${i}></span>`;
	const codeDiv = document.querySelector(`#TEMP_QRCODE_TEXT_ID${i}`);
	makeQRCode(codeDiv, this.dataset.qrcodeText);
	codeDiv.removeAttribute('id');
});