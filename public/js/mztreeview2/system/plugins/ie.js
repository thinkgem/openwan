/*---------------------------------------------------------------------------*\
|  Subject:    Plugins for Internet Explorer
|  NameSpace:  System.Plugins.IE
|  Author:     meizz
|  Created:    2006-02-24
|  Version:    2006-05-25
|-------------------------------------------------------------
|  MSN: huangfr@msn.com   QQ: 112889082   http://www.meizz.com
|  Email: mz@126.com      CSDN ID:meizz   Copyright (c)  meizz
\*---------------------------------------------------------------------------*/
System.scriptElement.addBehavior("#default#userdata");

System.encodeNameSpace=function(path)
{
  return path.replace(/\W/g, "_");
}
System.saveUserData=function(key, value)
{
  try
  {
    var t=System.scriptElement;
    var d=new Date(); d.setDate(d.getDate()+1);  //1 day
    t.load(System.encodeNameSpace(key));
    t.setAttribute("code", value);
    t.setAttribute("version", System.currentVersion);
    t.expires=d.toUTCString();
    t.save(System.encodeNameSpace(key));
    return  t.getAttribute("code");
  }
  catch (ex){}
}

System.loadUserData=function(key)
{
  try
  {
    var t=System.scriptElement;
    t.load(System.encodeNameSpace(key));
    if(System.currentVersion!=t.getAttribute("version")){
    if(t.getAttribute("code"))System.deleteUserData(key);
      return null;} return t.getAttribute("code");
  }
  catch (ex){return null;}
}

System.deleteUserData=function(key)
{
  try
  {
    var t=System.scriptElement;
  	t.load(System.encodeNameSpace(key));
    t.expires = new Date(315532799000).toUTCString();
    t.save(System.encodeNameSpace(key));
  }
  catch (ex){}
}

System.load = function(ns, path)
{
  path=System._mapPath(ns, path);
  if(/(\.js|\.html?)$/i.test(path))
  {
    var code=System.loadUserData(ns); if(code){
    return (System._codebase[ns]="//from userdata\r\n"+ code);}
  }
  function s(t){t=System._parseResponseText(t);System.saveUserData(ns,t);return t}
  try
  {
    if(System.supportsXmlHttp())
    {
      var x=System._xmlHttp; x.open("GET",path,false); x.send(null);
      if (x.readyState==4)
      {
        if(/^file\:/i.test(path)||x.status==0)return s(x.responseText);
        else if(x.status==200)   return s(x.responseText);
        else if(x.status==404)System._alert(ns+"\n"+System.FILE_NOT_FOUND);
        else throw new Error(x.status +": "+ x.statusText);
      }
    }
    else System._alert(System.NOT_SUPPORTED_XMLHTTP);
  }
  catch(ex){System._alert(ns+"\n"+ex.message);}return "";
};
