//v1.7
// Flash Player Version Detection
// Detect Client Browser type
// Copyright 2005-2007 Adobe Systems Incorporated.  All rights reserved.
var isIE =(navigator.appVersion.indexOf("MSIE") != -1) ? true : false;
var isWin=(navigator.appVersion.toLowerCase().indexOf("win") != -1) ? true : false;
var isOpera=(navigator.userAgent.indexOf("Opera") != -1) ? true : false;
function ControlVersion()
{
	var version;
	var axo;
	var e;
	// NOTE : new ActiveXObject(strFoo) throws an exception if strFoo isn't in the registry
	try {
		// version will be set for 7.X or greater players
		axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");
		version=axo.GetVariable("$version");
	} catch (e) {
	}
	if (!version)
	{
		try {
			// version will be set for 6.X players only
			axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");

			// installed player is some revision of 6.0
			// GetVariable("$version") crashes for versions 6.0.22 through 6.0.29,
			// so we have to be careful.

			// default to the first public version
			version="WIN 6,0,21,0";
			// throws if AllowScripAccess does not exist (introduced in 6.0r47)
			axo.AllowScriptAccess="always";
			// safe to call for 6.0r47 or greater
			version=axo.GetVariable("$version");
		} catch (e) {
		}
	}
	if (!version)
	{
		try {
			// version will be set for 4.X or 5.X player
			axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");
			version=axo.GetVariable("$version");
		} catch (e) {
		}
	}
	if (!version)
	{
		try {
			// version will be set for 3.X player
			axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");
			version="WIN 3,0,18,0";
		} catch (e) {
		}
	}
	if (!version)
	{
		try {
			// version will be set for 2.X player
			axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
			version="WIN 2,0,0,11";
		} catch (e) {
			version=-1;
		}
	}
	return version;
}
// JavaScript helper required to detect Flash Player PlugIn version information
function GetSwfVer(){
	// NS/Opera version >= 3 check for Flash plugin in plugin array
	var flashVer=-1;
	if (navigator.plugins != null && navigator.plugins.length > 0) {
		if (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]) {
			var swVer2=navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
			var flashDescription=navigator.plugins["Shockwave Flash"+swVer2].description;
			var descArray=flashDescription.split(" ");
			var tempArrayMajor=descArray[2].split(".");
			var versionMajor=tempArrayMajor[0];
			var versionMinor=tempArrayMajor[1];
			var versionRevision=descArray[3];
			if (versionRevision == "") {
				versionRevision=descArray[4];
			}
			if (versionRevision[0] == "d") {
				versionRevision=versionRevision.substring(1);
			} else if (versionRevision[0] == "r") {
				versionRevision=versionRevision.substring(1);
				if (versionRevision.indexOf("d") > 0) {
					versionRevision=versionRevision.substring(0, versionRevision.indexOf("d"));
				}
			}
			var flashVer=versionMajor+"."+versionMinor+"."+versionRevision;
		}
	}
	// MSN/WebTV 2.6 supports Flash 4
	else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.6") != -1) flashVer=4;
	// WebTV 2.5 supports Flash 3
	else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.5") != -1) flashVer=3;
	// older WebTV supports Flash 2
	else if (navigator.userAgent.toLowerCase().indexOf("webtv") != -1) flashVer=2;
	else if ( isIE && isWin && !isOpera ) {
		flashVer=ControlVersion();
	}
	return flashVer;
}
// When called with reqMajorVer, reqMinorVer, reqRevision returns true if that version or greater is available
function DetectFlashVer(reqMajorVer, reqMinorVer, reqRevision)
{
	versionStr=GetSwfVer();
	if (versionStr == -1 ) {
		return false;
	} else if (versionStr != 0) {
		if(isIE && isWin && !isOpera) {
			// Given "WIN 2,0,0,11"
			tempArray        =versionStr.split(" "); 	// ["WIN", "2,0,0,11"]
			tempString       =tempArray[1];			// "2,0,0,11"
			versionArray     =tempString.split(",");	// ['2','0','0','11']
		} else {
			versionArray     =versionStr.split(".");
		}
		var versionMajor     =versionArray[0];
		var versionMinor     =versionArray[1];
		var versionRevision  =versionArray[2];
    // is the major.revision >= requested major.revision AND the minor version >= requested minor
		if (versionMajor > parseFloat(reqMajorVer)) {
			return true;
		} else if (versionMajor == parseFloat(reqMajorVer)) {
			if (versionMinor > parseFloat(reqMinorVer))
				return true;
			else if (versionMinor == parseFloat(reqMinorVer)) {
				if (versionRevision >= parseFloat(reqRevision))
					return true;
			}
		}
		return false;
	}
}
function AC_Generateobj(objAttrs, params, embedAttrs){
  var str='';
  if (isIE && isWin && !isOpera){
    str+='<object ';for(var i in objAttrs)str+=i+'="'+objAttrs[i]+'" ';str+='>';
    for(var i in params)str+='<param name="'+i+'" value="'+params[i]+'" /> ';str+='</object>';
  }else{
  	 str+='<embed ';for(var i in embedAttrs)str+=i+'="'+embedAttrs[i]+'" ';str+='> </embed>';
  }
  document.write(str);
}
function magicplayer(){
  var ret = new Object();ret.embedAttrs = new Object();ret.params = new Object();ret.objAttrs = new Object();
  var list=new Array('codebase','http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0','id','magicplayer','classid','clsid:d27cdb6e-ae6d-11cf-96b8-444553540000',
		'quality','high','align','middle','play','false','loop','true','scale','noscale','wmode',//'window','devicefont','src','magicplayer',
		'false','bgcolor','#000000','name','magicplayer','type','application/x-shockwave-flash','pluginspage','http://www.macromedia.com/go/getflashplayer',
		'menu','true','allowFullScreen','true','allowScriptAccess','always','movie','','salign','');
  for(var i=0;i<list.length;i+=2){
    switch(list[i].toLowerCase()){
      case "src":case "movie":
        for(var j=0;j<arguments.length;j++){
        	if(arguments[j].indexOf("width")>-1 || arguments[j].indexOf("height")>-1){
          ret.embedAttrs[arguments[j].split("=")[0]]=ret.objAttrs[arguments[j].split("=")[0]]=arguments[j].split("=")[1];}
        	else if(arguments[j].indexOf("movie")>-1){ret.movie=arguments[j].split("=")[1]+"?";}
      	  else{ list[i+1]+=arguments[j]+"&";}}
        ret.embedAttrs["src"]=ret.params["movie"]=ret.movie+list[i+1].substr(0,list[i+1].length-1);break;
      case "pluginspage":case "name":case "type":ret.embedAttrs[list[i]]=list[i+1];break;
      case "classid":case "codebase":case "id":ret.objAttrs[list[i]]=list[i+1];break;
      case "align":ret.embedAttrs[list[i]]=ret.objAttrs[list[i]]=list[i+1];break;//case "width":case "height":case "tabindex":
      default:ret.embedAttrs[list[i]]=ret.params[list[i]]=list[i+1];
  } }
  AC_Generateobj(ret.objAttrs, ret.params, ret.embedAttrs);
}