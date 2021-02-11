const colorMode = localStorage.getItem('colorMode'); // 获取色彩模式配置
if (colorMode === null) { // 若没有配置（跟随浏览器）
	if (window.matchMedia('(prefers-color-scheme: dark)').matches) { // 深色模式
		document.querySelector('#ColorMode-Dark').disabled = false;
		document.querySelector('#Swal2-Dark').disabled = false;
	} else if (window.matchMedia('(prefers-color-scheme: light)').matches) { // 浅色模式
		document.querySelector('#ColorMode-Dark').disabled = true;
		document.querySelector('#Swal2-Light').disabled = false;
	}
} else if (colorMode === 'dark') { // 深色模式
	document.querySelector('#ColorMode-Dark').disabled = false;
	document.querySelector('#Swal2-Dark').disabled = false;
} else if (colorMode === 'light') { // 浅色模式
	document.querySelector('#ColorMode-Dark').disabled = true;
	document.querySelector('#Swal2-Light').disabled = false;
} else { // 配置错误时的自动纠正程序
	localStorage.removeItem('colorMode');
	if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
		document.querySelector('#ColorMode-Dark').disabled = false;
		document.querySelector('#Swal2-Dark').disabled = false;
	} else if (window.matchMedia('(prefers-color-scheme: light)').matches) {
		document.querySelector('#ColorMode-Dark').disabled = true;
		document.querySelector('#Swal2-Light').disabled = false;
	}
	document.addEventListener('DOMContentLoaded', function () {
		Swal.fire({
			title: '错误',
			html: '色彩模式配置出现错误，已重置配置！<br/>按 OK 刷新页面……',
			icon: 'error',
			footer: '<a href="?usersettings" target="_blank">点此去修改颜色模式的页面</a>'
		}).then(function () {
			location.reload();
		});
	});
}