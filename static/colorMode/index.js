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
}