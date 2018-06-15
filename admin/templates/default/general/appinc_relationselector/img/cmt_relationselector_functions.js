function windowOpen(url) {
    var myWindow=window.open(url,'windowRef','toolbar=no,width=718,height=400,resizable=no,scrollbars=no,location=no,status=no');
    if (!myWindow.opener) myWindow.opener = self;
}

function windowClose() {
    window.close();
}

/*
function updateOpener() {
    window.opener.document.forms[0].links.value = '';
    window.close();
}
 */