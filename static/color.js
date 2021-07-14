const colorMode = localStorage.getItem('colorMode'); // 获取色彩模式配置
{ // 限制变量作用域
	function DarkMod() { // 更改为深色模式
		document.querySelector('#ColorMode-Light').disabled = true;
		document.querySelector('#ColorMode-Dark').disabled = false;
		document.querySelector('#Swal2-Light').disabled = true;
		document.querySelector('#Swal2-Dark').disabled = false;
	}
	function LightMod() { // 更改为浅色模式
		document.querySelector('#ColorMode-Light').disabled = false;
		document.querySelector('#ColorMode-Dark').disabled = true;
		document.querySelector('#Swal2-Light').disabled = false;
		document.querySelector('#Swal2-Dark').disabled = true;
	}
	function followBrowser() {
		const dark = window.matchMedia('(prefers-color-scheme: dark)'),
			light = window.matchMedia('(prefers-color-scheme: light)');
		function change() { // 更改配色
			if (dark.matches) { // 深色模式
				DarkMod();
			} else if (light.matches) { // 浅色模式
				LightMod();
			} else { // 对于不支持选择的远古浏览器，启用浅色模式
				LightMod();
			}
		}
		dark.addEventListener('change', change); // 配色更改事件
		light.addEventListener('change', change);
		change(); // 加载页面时初始化
	}

	if (colorMode === null) { // 若没有配置
		followBrowser(); // 跟随浏览器
	} else if (colorMode === 'dark') { // 深色模式
		DarkMod();
	} else if (colorMode === 'light') { // 浅色模式
		LightMod();
	} else { // 配置错误时的自动纠正
		localStorage.removeItem('colorMode'); // 删除配置
		followBrowser(); // 跟随浏览器
		document.addEventListener('DOMContentLoaded', function () { // 弹出提示
			Swal.fire({
				title: '色彩配置有误',
				html: '色彩模式配置出现错误，已重置配置！<br/>按 OK 刷新页面。',
				icon: 'warning',
				footer: '<a href="?usersettings" target="_blank">点此去修改颜色模式的页面</a>'
			}).then(function () {
				location.reload(); // 重载页面
			});
		});
	}
}