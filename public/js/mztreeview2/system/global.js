/*-=< base function >=-*/
//HTMLElement.getElementById extend
if(document && !document.getElementById)
{
  document.getElementById=function(id)
  {
    if(document.all) return document.all(id); return null;
  }
}
function searchByTagName(e, TAG)
{
  while(e!=null && e.tagName){if(e.tagName==TAG.toUpperCase())
  return(e); e=e.parentNode; } return null;
}
function searchById(e, id)
{
  while(e = e.parentNode){if(e.id==id) return(e);} return null;
}
function loadCssFile(url, uniqueId)
{
  if(document.getElementById(uniqueId)) return;
  if(/\w+\.\w+(\?|$)/.test(url))
  {
    var link  = document.createElement("LINK");
    link.href = url;
    link.id   = uniqueId;
    link.type = "text/css";
    link.rel  = "Stylesheet";
    document.getElementsByTagName("HEAD")[0].appendChild(link);
  }
};
function MzHideShow(bool, i)
{
  var F=MzHideShow;
  if(typeof(i)=="undefined")
  {
    i=0; var e=F.target.style;
    bool=F.target.style.display=="none";
    e.display="";   e.overflow="hidden";
    F._height=parseFloat(F.target.offsetHeight);
    F.height=e.height;
    F.overflow=e.overflow;
  } i=i+F.step;
  if(i>=100)
  {
    var e=F.target.style;
    e.display = bool ? "" : "none";
    F.trigger.src = bool ? F.hideSrc : F.showSrc;
    e.height=F.height;
    e.overflow=F.overflow;
  }
  else
  {
    var n = bool ? i/100: (100-i)/100;
    F.target.style.height=Math.ceil(F._height*n) +"px";
    setTimeout("MzHideShow("+ bool +","+ i +")", F.delay);
  }
}
MzHideShow.step=9;
MzHideShow.delay=1;
MzHideShow.showSrc="/images/show.gif";
MzHideShow.hideSrc="/images/hide.gif";
function hs(e, id)
{
  var o=document.getElementById(id);
  if(o)
  {
    MzHideShow.target = o;
    MzHideShow.trigger = e;
    MzHideShow();
  }
}
/*
function sleep(ms)
{
  var start = new Date().getTime();
  var xh = new XMLHttpRequest();
  while(new Date().getTime()-start<ms)
  {
    try{
      xh.open("GET", "file:///c:\\?mz="+ Math.random(), false);
      xh.send(null);
    }catch(ex){return;}
  }
}

function Sleep(n){var start=new Date().getTime(); //for opera only
while(true) if(new Date().getTime()-start>n) break;}
*/


/*-=< HTMLElement >=-*/
if("undefined"!=typeof HTMLDocument)
{
  if("undefined"==typeof HTMLDocument.prototype.createStyleSheet)
  {
    HTMLDocument.prototype.createStyleSheet=function(url)
    {
      var e;if(/[\w\-\u4e00-\u9fa5]+\.\w+(\?|$)/.test(url))
      {
        e=document.createElement("LINK"); e.type="text/css";
        e.rel="Stylesheet"; e.href=url;}else{
        e=document.createElement("STYLE"); e.type="text/css";
      }
      document.getElementsByTagName("HEAD")[0].appendChild(e);
      return e;
    };
  }
}
if(typeof(HTMLElement)!="undefined" && !window.opera)
{

  HTMLElement.prototype.contains = function(obj)
  {
    if(obj==this)return true;
    if(obj==null)return false;
    return this.contains(obj.parentNode);
  };
  HTMLElement.prototype.__defineGetter__("outerHTML",function()
  {
    var a=this.attributes, str="<"+this.tagName, i=0;for(;i<a.length;i++)
    if(a[i].specified) str+=" "+a[i].name+'="'+a[i].value+'"';
    if(!this.canHaveChildren) return str+" />";
    return str+">"+this.innerHTML+"</"+this.tagName+">";
  });
  HTMLElement.prototype.__defineSetter__("outerHTML",function(s)
  {
    var r = this.ownerDocument.createRange();
    r.setStartBefore(this);
    var df = r.createContextualFragment(s);
    this.parentNode.replaceChild(df, this);
    return s;
  });
  HTMLElement.prototype.__defineGetter__("canHaveChildren",function()
  {
    return !/^(area|base|basefont|col|frame|hr|img|br|input|isindex|link|meta|param)$/.test(this.tagName.toLowerCase());
  });
}
if(!window.attachEvent && window.addEventListener)
{
  Window.prototype.attachEvent = HTMLDocument.prototype.attachEvent=
  HTMLElement.prototype.attachEvent=function(en, func, cancelBubble)
  {
    var cb = cancelBubble ? true : false;
    this.addEventListener(en.toLowerCase().substr(2), func, cb);
  };
  Window.prototype.detachEvent = HTMLDocument.prototype.detachEvent=
  HTMLElement.prototype.detachEvent=function(en, func, cancelBubble)
  {
    var cb = cancelBubble ? true : false;
    this.removeEventListener(en.toLowerCase().substr(2), func, cb);
  };
}
if(typeof Event!="undefined" && !window.opera)
{
  Event.prototype.__defineSetter__("returnValue", function(b){
    if (!b) this.preventDefault();
    return b;
  });
  Event.prototype.__defineSetter__("cancelBubble", function(b){
    if (b) this.stopPropagation();
    return b;
  });
  Event.prototype.__defineGetter__("offsetX", function(){return this.layerX;});
  Event.prototype.__defineGetter__("offsetY", function(){return this.layerY;});
  Event.prototype.__defineGetter__("srcElement", function(){
    var node = this.target;
    while (node.nodeType != 1){node = node.parentNode; if(node==null) return null;}
    return node;
  });
}

/*-=< Function >=-*/
//apply and call
if(typeof(Function.prototype.apply)!="function")
{
  Function.prototype.apply = function(obj, argu)
  {
    if(obj) obj.constructor.prototype.___caller = this;
    for(var a=[], i=0; i<argu.length; i++) a[i] = "argu["+ i +"]";
    var t = eval((obj ? "obj.___caller" : "this") +"("+ a.join(",") +");");
    if(obj) delete obj.constructor.prototype.___caller; return t;};
    Function.prototype.call = function(obj){
    for(var a=[], i=1; i<arguments.length; i++) a[i-1]=arguments[i];
    return this.apply(obj, a);
  };
}

/*-=< Array >=-*/
//[extended method] push  insert new item
if(typeof(Array.prototype.push)!="function")
{
  Array.prototype.push = function()
  {
    for (var i=0; i<arguments.length; i++)
      this[this.length] = arguments[i];
    return this.length;
  };
}
//[extended method] shift  delete the first item
if(typeof(Array.prototype.shift)!="function")
{
  Array.prototype.shift = function()
  {
    var mm = null;
    if(this.length>0)
    {
      mm = this[0]; for(var i=1; i<this.length; i++)
      this[i-1]=this[i]; this.length=this.length -1;
    }
    return mm;
  };
}
//[extended method] unique  Delete repeated item
Array.prototype.Unique = function()
{
  var a = {}; for(var i=0; i<this.length; i++)
  {
    if(typeof a[this[i]] == "undefined")
      a[this[i]] = 1;
  }
  this.length = 0;
  for(var i in a)
    this[this.length] = i;
  return this;
};
//[extended method] indexOf
if(typeof(Array.prototype.indexOf)!="function")
{
  Array.prototype.indexOf=function(item, start)
  {
    start=start||0; if(start<0)start=Math.max(0,this.length+start);
    for(var i=start;i<this.length;i++){if(this[i]===item)return i;}
    return -1;
  };
}

/*-=< Date >=-*/
//datetime format
Date.prototype.format = function(format)
{
  var o = {
    "M+" : this.getMonth()+1, //month
    "d+" : this.getDate(),    //day
    "h+" : this.getHours(),   //hour
    "m+" : this.getMinutes(), //minute
    "s+" : this.getSeconds(), //second
    "q+" : Math.floor((this.getMonth()+3)/3),  //quarter
    "S" : this.getMilliseconds() //millisecond
  }
  if(/(y+)/.test(format)) format=format.replace(RegExp.$1,
    (this.getFullYear()+"").substr(4 - RegExp.$1.length));
  for(var k in o)if(new RegExp("("+ k +")").test(format))
    format = format.replace(RegExp.$1,
      RegExp.$1.length==1 ? o[k] :
        ("00"+ o[k]).substr((""+ o[k]).length));
  return format;
};

if(typeof(Number.prototype.toFixed)!="function")
{
    Number.prototype.toFixed = function(d)
    {
        var s=this+"";if(!d)d=0;
        if(s.indexOf(".")==-1)s+=".";s+=new Array(d+1).join("0");
        if (new RegExp("^(-|\\+)?(\\d+(\\.\\d{0,"+ (d+1) +"})?)\\d*$").test(s))
        {
            var s="0"+ RegExp.$2, pm=RegExp.$1, a=RegExp.$3.length, b=true;
            if (a==d+2){a=s.match(/\d/g); if (parseInt(a[a.length-1])>4)
            {
                for(var i=a.length-2; i>=0; i--) {a[i] = parseInt(a[i])+1;
                if(a[i]==10){a[i]=0; b=i!=1;} else break;}
            }
            s=a.join("").replace(new RegExp("(\\d+)(\\d{"+d+"})\\d$"),"$1.$2");
        }if(b)s=s.substr(1);return (pm+s).replace(/\.$/, "");} return this+"";
    };
}

/*-=< String >=-*/
String.prototype.trim = function()
{
  return this.replace(/(^[\s\t　]+)|([　\s\t]+$)/g, "");
};
String.prototype.capitalize = function()
{
  return this.charAt(0).toUpperCase() + this.substr(1);
};
String.prototype.getAttribute = function(attribute)
{
  if(new RegExp("(^|;)\\s*"+attribute+"\\s*:\\s*([^;]*)\\s*(;|$)","i").test(this))
  return RegExp.$2.replace(/%3B/gi,";").replace(/%25/g,"%"); return null;
};
String.prototype.setAttribute = function(attribute, value)
{
  value=(""+value).replace(/%/g,"%25").replace(/;/g,"%3B").replace(/\r|\n/g,"");
  return (attribute +":"+ value +";" + this);
};
String.prototype.deleteAttribute = function(attribute)
{
  return this.replace(new RegExp("\\b\\s*"+attribute+"\\s*:\\s*([^;]*)\\s*(;|$)","gi"),"");
};
String.prototype.getQueryString = function(name)
{
  var reg = new RegExp("(^|&|\\?)"+ name +"=([^&]*)(&|$)"), r;
  if (r=this.match(reg)) return unescape(r[2]); return null;
};
String.prototype.sub = function(n)
{
  var r = /[^\x00-\xff]/g;
  if(this.replace(r, "mm").length <= n) return this;
  n = n - 3;
  var m = Math.floor(n/2);
  for(var i=m; i<this.length; i++)
  {
    if(this.substr(0, i).replace(r, "mm").length>=n)
    {
      return this.substr(0, i) +"...";
    }
  }
  return this;
};