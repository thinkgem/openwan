/*---------------------------------------------------------------------------*\
|  Subject:    Xml String Data
|  NameSpace:  System.Data.MzData
|  Author:     meizz
|  Created:    2005-12-28
|  Version:    2006-06-26
|-------------------------------------------------------------
|  MSN: huangfr@msn.com   QQ: 112889082   http://www.meizz.com
|  Email: mz@126.com      CSDN ID:meizz   Copyright (c)  meizz
\*---------------------------------------------------------------------------*/

function MzData(){System.call(this);}
t=MzData.Extends(System, "MzData");
MzData.addProperty("__", "\x0f", Function.READ);
t.rootId="-1"; t.dividerEncoding = t.divider = "_";
t.indexes = t.jsDataPath = t.xmlDataPath = "";

t.appendIndexes=function(s){this.indexes += this.get__()+ s +this.get__();}
t.getUniqueId=function(){return "MzData"+(MzData.nodeCounter++).toString(36);};
MzData.nodeCounter=0;

window.MzDataNode = function()
{
  this.index= (MzData.nodeCounter++).toString(36);
  this.childNodes=[];
};
t.nodePrototype=MzDataNode;
t=MzDataNode.Extends(System, "MzDataNode");
t.text = t.path = t.sourceIndex="";
t.isLoaded = t.hasChild= false;
t.parentNode = t.$$caller = null;  //instance of System.Data.MzData

//public
MzData.prototype.setDivider=function(d)
{
  this.divider=d; for(var a="", i=0; i<d.length; i++)
  a+=("\'^{[(\\-|+.*?)]}$\"".indexOf(d.charAt(i))>-1?"\\":"")+d.charAt(i);
  this.dividerEncoding = a;
};
MzData.prototype.setJsDataPath=function(path)
{
  if(path.length>0) this.jsDataPath = path.replace(/[\\\/]*$/, "/");
}
MzData.prototype.setXmlDataPath=function(path)
{
  if(path.length>0) this.xmlDataPath = path.replace(/[\\\/]*$/, "/");
}
//private: initialize data node
MzData.prototype.initialize = function()
{
  this.selectedNode=null; this.currentNode=null;
  if("object"!=typeof this.nodes) this.nodes={};
  if("object"!=typeof this.dataSource) this.dataSource={};

  var _=this.get__(), d=this.dividerEncoding, a=[], i;

  for(i in this.dataSource)a[a.length]=i;this.appendIndexes(a.join(_));
  this.dataSource.length=(this.dataSource.length||0)+ a.length;

  a=(MzData.nodeCounter++).toString(36);
  var node=this.nodes[a]=this.rootNode = new this.nodePrototype; //this.imaginaryNode
  node.$$caller=this;node.index=a;node.virgin=this.rootId=="-1";

  if(node.virgin)
  {
    node.id=node.path="-1";
    node.loadChildNodes();
    node.hasChildNodes();
  }
  else
  {
    a=new RegExp("([^"+_+d+"]+)"+ d +"("+ this.rootId +")("+_+"|$)");
    if(a.test(this.indexes))
    {
      a=RegExp.$1 + this.divider + this.rootId;
      node.childNodes[0]=node.DTO(this.nodePrototype, a);
      node.isLoaded=true; node.hasChild=true;
    }
  }
  this._initialized=true;
};
//public: append data onafterload
MzData.prototype.appendData = function(data)
{
  if("object"!=typeof this.dataSource) this.dataSource={}; var a=[],id;
  for(id in data) if(!this.dataSource[id])
  {this.dataSource[id]=data[id];a[a.length]=id;}
  if(this._initialized) this.appendIndexes(a.join(this.get__()));
  this.dataSource.length=(this.dataSource.length||0)+a.length;data=null;a=null;
};
//public: getNode (has Builded) by sourceId
MzData.prototype.getNodeById = function(id)
{
  if(id==this.rootId&&this.rootNode.virgin) return this.rootNode;
  var _=this.get__(), d = this.dividerEncoding;
  var reg=new RegExp("([^"+_+ d +"]+"+ d + id +")("+_+"|$)");
  if(reg.test(this.indexes)){var s=RegExp.$1;
  if(s=this.dataSource[s].getAttribute("index_"+ this.hashCode))
    return this.nodes[s];
  else{/*System._alert("The node isn't initialized!");*/ return null;}}
  /*alert("sourceId="+ id +" is nonexistent!");*/ return null;
};
//public: asynchronous get childNodes from JS File
MzData.prototype.loadJsData = function(JsFileUrl)
{
  var js; if(js = System.load("",JsFileUrl)){try{var d=eval(js);
  if("object"!=d && "object"==typeof(data) && null!=data)d=data;
  this.appendData(d); data=d=null;}catch(e){}}
};
//public: asynchronous get childNodes from XML File
MzData.prototype.loadXmlData = function(url, parentId)
{
  if(System.supportsXmlHttp())
  {
    Using("System.Xml.MzXmlDocument");
    function Sleep(n){var start=new Date().getTime();
    while(true) if(new Date().getTime()-start>n) break;}
    if("undefined"==typeof parentId) parentId=this.rootId;
    var x=new MzXmlDocument(); x.async=false; x.load(url);
    if(x.readyState==4)
    {
      if(!x.documentElement)
        alert("xmlDoc.documentElement = null, Please update your browser");
      this._loadXmlNodeData(x.documentElement, parentId);
    }
  }
};
//public: asynchronous get childNodes from XML String
MzData.prototype.loadXmlDataString = function(xmlString, parentId)
{
  if(System.supportsXmlHttp())
  {
    Using("System.Xml.MzXmlDocument");
    if("undefined"==typeof parentId) parentId=this.rootId;
    var x=new MzXmlDocument(); x.loadXML(xmlString);
    this._loadXmlNodeData(x.documentElement, parentId);
  }
};
MzData.prototype._loadXmlNodeData = function(xmlNode, parentId)
{
  if(!(xmlNode && xmlNode.hasChildNodes())) return;
  for(var k,id,i=0,data={},n=xmlNode.childNodes; i<n.length; i++)
  {
    if(n[i].nodeType==1){id=n[i].getAttribute("id")||this.getUniqueId();
    if(n[i].hasChildNodes()){for(k=0,nic=n[i].childNodes;k<nic.length;k++)
    {
      if(nic[k].nodeType==1){this._loadXmlNodeData(n[i], id);break;}}
    }
    for(var k=0,s="",a=n[i].attributes; k<a.length; k++)
      s=s.setAttribute(a[k].name, a[k].value);
    if(!s.getAttribute("text")) s="text:;"+ s;
    a=parentId + this.divider + id; data[a]=s;}
  }
  this.appendData(data);
};

//public
MzData.prototype.loadUlData=function(HtmlUL, parentId)
{
  if("undefined"==typeof parentId) parentId=this.rootId; var ul;
  if("string"==typeof HtmlUL&&(ul=document.getElementById(HtmlUL)));
  else if("object"==typeof HtmlUL&&(ul=HtmlUL.tagName)&&
    "UL OL".indexOf(ul.toUpperCase())>-1) ul=HtmlUL;
  if("object"==typeof ul)
  {
    var data={}; for(var i=0, n=ul.childNodes; i<n.length; i++)
    {
      if(n[i].nodeType==1 && n[i].tagName=="LI")
      {
        var id=n[i].getAttribute("sourceid")||this.getUniqueId(),txt="",link="";
        for(var k=0; k<n[i].childNodes.length; k++)
        {
          var node=n[i].childNodes[k];
          if(node.nodeType==3) txt += node.nodeValue;
          if(node.nodeType==1)
          {
            switch(node.tagName)
            {
              case "UL":
              case "OL": this.loadUlData(node, id); break;
              case "A" : if(!link) link=node; break;
            }
          }
        }
        var str="";
        if(link)
        {
          str=str.setAttribute("target", link.target);
          str=str.setAttribute("url", link.href);
          str=str.setAttribute("text", link.innerHTML);
        }
        else str = str.setAttribute("text", txt.trim());
        var a=n[i].attributes;
        for(var k=0; k<a.length; k++)
        {
          if(a[k].specified && a[k].name!="style")
            str = str.setAttribute(a[k].name, a[k].value);
        }
        a=parentId + this.divider + id;
        data[a]=str;
      }
    }
    this.appendData(data);
  }
}
//public: check node has child
MzDataNode.prototype.hasChildNodes = function()
{
  var $=this.$$caller;
  this.hasChild=$.indexes.indexOf($.get__()+ this.id + $.divider)>-1
  ||(this.sourceIndex&&(this.get("JSData")!=null||this.get("XMLData")!=null
  || this.get("ULData")!=null)); return this.hasChild;
};
//public: get node attribute
MzDataNode.prototype.get = function(attribName)
{
  if("undefined"!=typeof this[attribName]) return this[attribName];
  else return this.$$caller.dataSource[this.sourceIndex].getAttribute(attribName);
};
//public: set node attribute
MzDataNode.prototype.set = function(attribName, value)
{
  if(typeof(this[attribName])!="undefined") this[attribName]=value; else
  {
    var s=this.$$caller.dataSource[this.sourceIndex];
    this.$$caller.dataSource[this.sourceIndex] = s.setAttribute(attribName,value);
  }
};
//private: load all node's node and init
MzDataNode.prototype.loadChildNodes = function(DataNodeClass)
{
  var $=this.$$caller,r=$.dividerEncoding,_=$.get__(), i, cs;
  var tcn=this.childNodes;tcn.length=0;if(this.sourceIndex){
  if((i=this.get("JSData"))) $.loadJsData((/^\w+\.js(\s|\?|$)/i.test(i)?$.jsDataPath:"")+i);
  if((i=this.get("ULData"))) $.loadUlData(i, this.id);
  if((i=this.get("XMLData")))$.loadXmlData((/^\w+\.xml(\s|\?|$)/i.test(i)?$.xmlDataPath:"")+i,this.id);}
  var reg=new RegExp(_ + this.id + r +"[^"+ _ + r +"]+", "g"); 
  if((cs=$.indexes.match(reg))){for(i=0;i<cs.length;i++){
    tcn[tcn.length]=this.DTO(DataNodeClass, cs[i].substr(_.length));}}
  this.isLoaded = true;
};
MzDataNode.prototype.DTO=function(DataNodeClass, sourceIndex)
{
  var C=DataNodeClass||MzDataNode,$=this.$$caller,d=$.divider,n=new C,s;
  n.$$caller=this.$$caller; s=$.dataSource[n.sourceIndex=sourceIndex];
  n.id=sourceIndex.substr(sourceIndex.indexOf(d)+d.length);
  n.hasChildNodes();n.text=s.getAttribute("text");
  n.parentNode=this;$.nodes[n.index]=n;n.path=this.path+d+n.id;
  $.dataSource[sourceIndex]=s.setAttribute("index_"+ $.hashCode,n.index);
  return n;
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