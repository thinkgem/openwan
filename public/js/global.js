// OpenWan JavaScript <thinkgem@gmail.com> */
function tab(tname, cname, lenght, j) {
	for (i = 1; i <= lenght; i++) {
		$("#" + tname + i).removeClass("current");
	}
	$("#" + tname + j).addClass("current");
	for (i = 1; i <= lenght; i++) {
		$("#" + cname + i).hide();
		$("#" + cname + j).show();
	}
}

function addFavorite(sURL, sTitle){
    try{
        window.external.addFavorite(sURL, sTitle);
    }catch (e){
        try{
            window.sidebar.addPanel(sTitle, sURL, "");
        }catch (e){
            alert("加入收藏失败，请使用Ctrl+D进行添加");
        }
    }
}

function loading(e, text){
    var html = '<table width="100%" height="300"><tr><td align="center" valign="middle" style="font-size:14px;"><img src="'+baseDir+'img/loading.gif" alt="loading..."/>';
    if (text) html += '<br/><br/>' + text;
    html += '</td></tr></table>';
    $(e).html(html);
}