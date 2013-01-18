/*---------------------------------------------------------------------------*\
|  Subject:    document.cookie
|  NameSpace:  System.Net.MzCookie
|  Author:     meizz
|  Created:    2004-12-07
|  Version:    2006-04-03
|-------------------------------------------------------------
|  MSN: huangfr@msn.com   QQ: 112889082   http://www.meizz.com
|  Email: mz@126.com      CSDN ID:meizz   Copyright (c)  meizz
\*---------------------------------------------------------------------------*/

function MzCookie()
{
  var now = new Date();
  now.setTime(now.getTime() + 1000*60*60*24*3); //Save 3 days
  this.path = "/";
  this.expires = now;
  this.domain = "";
  this.secure = "";
}
MzCookie.Extends(System, "MzCookie");

//name: cookie name
//value: cookie value
MzCookie.prototype.add  = function(name, value)
{
  document.cookie =
    name + "="+ escape (value) 
    + ";expires=" + this.expires.toGMTString()
    + ";path="+ this.path
    + (this.domain == "" ? "" : ("; domain=" + this.domain))
    + (this.secure ? "; secure" : "");
};

//name: cookie name
MzCookie.prototype.get  = function(name)
{
  var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
  if(arr=document.cookie.match(reg)) return unescape(arr[2]);
  else return null;
};

//name: cookie name
MzCookie.prototype.remove  = function(name)
{
  var now = new Date();
  now.setTime(now.getTime() - 1);
  var V = this.get(name);
  if(V!=null) document.cookie= name + "="+ V 
    +";expires="+ now.toGMTString() + ";path="+ this.path;
};

MzCookie.prototype.setExpires = function(milliseconds)
{
  var now = new Date();
  now.setTime(now.getTime() + milliseconds);
  this.expires = now;
};