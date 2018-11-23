function serverupal() {
    var url = document.getElementById('url');
    if (!!url) document.location.href = '?' + url.value;
    return false;
}
