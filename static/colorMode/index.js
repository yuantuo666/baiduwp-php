let colorMode = localStorage.getItem('colorMode');
if (colorMode === null) {
	document.querySelector('#ColorMode-Auto').disabled = false;
	if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
		document.querySelector('#Swal2-Dark').disabled = false;
	} else if (window.matchMedia('(prefers-color-scheme: light)').matches) {
		document.querySelector('#Swal2-Light').disabled = false;
	}
} else if (colorMode === 'dark') {
	document.querySelector('#ColorMode-Dark').disabled = false;
	document.querySelector('#Swal2-Dark').disabled = false;
} else if (colorMode === 'light') {
	document.querySelector('#ColorMode-Light').disabled = false;
	document.querySelector('#Swal2-Light').disabled = false;
} else {
	Swal.fire('错误', '色彩模式配置出现错误，已重置配置！<br/>即将刷新页面……', 'error').then(function () {
		localStorage.removeItem('colorMode');
		location.reload();
	});
}
