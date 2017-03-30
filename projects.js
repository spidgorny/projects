function copy(a) {
    console.log(a);
    var input = a.querySelector('input');
    input.style.display = '';
    input.focus();
    input.select();
    document.execCommand('copy');
    input.style.display = 'none';
    return false;
}
