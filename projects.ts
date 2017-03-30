function copy(a: HTMLAnchorElement) {
	console.log(a);
	let input: HTMLInputElement = a.querySelector('input');
	input.style.display = '';
	input.focus();
	input.select();
	document.execCommand('copy');
	input.style.display = 'none';
	return false;
}
