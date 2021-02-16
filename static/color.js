const colorMode = localStorage.getItem('colorMode'); // 获取色彩模式配置
if (colorMode === null) { // 若没有配置（跟随浏览器）
	if (window.matchMedia('(prefers-color-scheme: dark)').matches) DarkMod(); // 深色模式
	else if (window.matchMedia('(prefers-color-scheme: light)').matches) LightMod();// 浅色模式
	else LightMod(); // 对于不支持选择的老版本浏览器，启用浅色模式
} else if (colorMode === 'dark') DarkMod(); // 深色模式
else if (colorMode === 'light') LightMod(); // 浅色模式
else { // 配置错误时的自动纠正程序
	localStorage.removeItem('colorMode');
	if (window.matchMedia('(prefers-color-scheme: dark)').matches) DarkMod();
	else if (window.matchMedia('(prefers-color-scheme: light)').matches) LightMod();
	else LightMod(); // 对于不支持选择的老版本浏览器，启用浅色模式

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

function LightMod() {
	document.querySelector('#ColorMode-Dark').disabled = true;
	document.querySelector('#Swal2-Light').disabled = false;
}
function DarkMod() {
	document.querySelector('#ColorMode-Dark').disabled = false;
	document.querySelector('#Swal2-Dark').disabled = false;
}