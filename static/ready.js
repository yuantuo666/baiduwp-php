$(".fa-file").each(function () {
	var icon = getIconClass($(this).next().text());
	if (icon !== "") {
		if ($.inArray(icon, ['fa-windows', 'fa-android', 'fa-apple']) >= 0) $(this).removeClass("far").addClass("fab");
		$(this).removeClass("fa-file").addClass(icon);
	}
});

$('[data-qrcode-attr]').each(function (i) {
	this.outerHTML = this.outerHTML + `<span class="qrcode" id="TEMP_QRCODE_ATTR_ID${i}"></span>`;
	const codeDiv = document.querySelector(`#TEMP_QRCODE_ATTR_ID${i}`),
		size = (this.getAttribute('data-qrcode-size') !== null) ? this.dataset.qrcodeSize : 512;
	let level = QRCode.CorrectLevel.M;
	if (this.getAttribute('data-qrcode-level') !== null) {
		switch (this.dataset.qrcodeLevel) {
			case 'L':
				level = QRCode.CorrectLevel.L;
				break;
			case 'M':
				break;
			case 'H':
				level = QRCode.CorrectLevel.H;
				break;
			case 'Q':
				level = QRCode.CorrectLevel.Q;
				break;
			default:
		}
	}
	makeQRCode(codeDiv, this.dataset.qrcodeAttr, size, level);
	codeDiv.removeAttribute('id');
});

$('[data-qrcode-text]').each(function (i) {
	this.outerHTML = this.outerHTML + `<span class="qrcode" id="TEMP_QRCODE_TEXT_ID${i}"></span>`;
	const codeDiv = document.querySelector(`#TEMP_QRCODE_TEXT_ID${i}`),
		size = (this.getAttribute('data-qrcode-size') !== null) ? this.dataset.qrcodeSize : 512;
	let level = QRCode.CorrectLevel.M;
	if (this.getAttribute('data-qrcode-level') !== null) {
		switch (this.dataset.qrcodeLevel) {
			case 'L':
				level = QRCode.CorrectLevel.L;
				break;
			case 'M':
				break;
			case 'H':
				level = QRCode.CorrectLevel.H;
				break;
			case 'Q':
				level = QRCode.CorrectLevel.Q;
				break;
			default:
		}
	}
	makeQRCode(codeDiv, this.dataset.qrcodeText, size, level);
	codeDiv.removeAttribute('id');
});