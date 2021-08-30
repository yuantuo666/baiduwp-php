$(".fa-file").each(function() {
    var icon = getIconClass($(this).next().text());
    if (icon !== "") {
        if ($.inArray(icon, ['fa-windows', 'fa-android', 'fa-apple']) >= 0) $(this).removeClass("far").addClass("fab");
        $(this).removeClass("fa-file").addClass(icon);
    }
});

$('[data-qrcode-attr]').each(function(i) {
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
    makeQRCode(codeDiv, this.getAttribute(this.dataset.qrcodeAttr), size, level);
    codeDiv.removeAttribute('id');
});

$('[data-qrcode-text]').each(function(i) {
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

getAPI('CheckUpdate').then(function(response) {
    if (response.success) {
        console.log('检查更新信息：');
        console.log(response);
        const data = response.data;
        if (data.code === 0) {
            if (data.have_update) {
                const div = document.createElement('div');
                div.id = 'CheckUpdate';
                div.style.margin = '0.3rem 1rem';
                div.style.display = 'none';
                div.innerHTML = `Baiduwp-PHP 项目有新的版本：${data.version}（${data.isPreRelease ? '此版本为预发行版本，' : ''}当前版本为${data.now_version}）！请联系站长更新！
					&nbsp; <a href="${data.page_url}" target="_blank">发行版页面</a> &nbsp; <a href="${data.file_url}" target="_blank">下载程序文件</a><div style="float: right;"><a href="javascript:SetUpdateTip(false);">不再提示</a></div>`;
                document.body.insertAdjacentElement('beforeBegin', div);
                if (localStorage.getItem('UpdateTip') != "false")
                    $('#CheckUpdate').show(1500);
            }
        } else if (data.code === 2) {
            const div = document.createElement('div');
            div.id = 'CheckUpdate';
            div.style.margin = '0.3rem 1rem';
            div.style.display = 'none';
            div.innerHTML = `Baiduwp-PHP 项目版本异常！当前版本：${data.now_version}，项目最新版本为：${data.version}${data.isPreRelease ? '（预发行版本）' : ''}！
				&nbsp; <a href="${data.page_url}" target="_blank">发行版页面</a> &nbsp; <a href="${data.file_url}" target="_blank">下载程序文件</a><div style="float: right;"><a href="javascript:SetUpdateTip(false);">不再提示</a></div>`;
            document.body.insertAdjacentElement('beforeBegin', div);
            if (localStorage.getItem('UpdateTip') != "false")
                $('#CheckUpdate').show(1500);
        } else if (data.code === 1) {
            console.log('服务器获取更新失败！详细信息：');
            console.log(data);
        } else {
            console.log('服务器获取更新失败，且错误码不在支持列表中！详细信息：');
            console.log(data);
        }
    } else {
        console.log('检查更新失败！详细信息：');
        console.log(response);
    }
});

function SetUpdateTip(value) {
    localStorage.setItem('UpdateTip', `${value}`); // 不知为啥，只能用string类型
    if (value) $('#CheckUpdate').show(2000);
    else $('#CheckUpdate').hide(2000);
}