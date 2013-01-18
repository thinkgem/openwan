/*---------------------------------------------------------------------------*\
|  Subject:    Mz XmlDocument
|  NameSpace:  System.Xml.MzXmlDocument
|  Author:     meizz
|  Created:    2006-01-23
|  Version:    2006-04-26
|-------------------------------------------------------------
|  MSN: huangfr@msn.com   QQ: 112889082   http://www.meizz.com
|  Email: mz@126.com      CSDN ID:meizz   Copyright (c)  meizz
\*---------------------------------------------------------------------------*/

function MzXmlDocument()
{
  if(document.implementation&&document.implementation.createDocument)
  {
    var doc=document.implementation.createDocument("","",null);
    doc.addEventListener("load",function(e){this.readyState=4;},false);
    doc.readyState=4; return doc;
  }
  else
  {
    var msxmls=["MSXML2","Microsoft","MSXML","MSXML3"];
    for(var i=0;i<msxmls.length;i++)
      try{return new ActiveXObject(msxmls[i]+'.DomDocument')}catch(e){}
    throw new Error("Could not find an installed XML parser!");
  }
}
MzXmlDocument.Extends(System, "MzXmlDocument");

var IE7 = false;  //repair for IE7 2006-04-26
if(/MSIE (\d+(\.\d+)?)/.test(navigator.userAgent))
{
  IE7 = parseFloat(RegExp.$1)>=7;
}

if(System.supportsXmlHttp() && "undefined"!=typeof XMLDocument && !IE7)
{
    (function()
    {
        var _xmlDocPrototype=XMLDocument.prototype;
        _xmlDocPrototype.__proto__={__proto__:_xmlDocPrototype.__proto__};
        var _p=_xmlDocPrototype.__proto__;
        _p.createNode=function(aType,aName,aNamespace)
        {
            switch(aType)
            {
                case 1:
                    if(aNamespace&&aNamespace!="")
                        return this.createElementNS(aNamespace,aName);
                    else return this.createElement(aName);
                case 2:
                    if(aNamespace&&aNamespace!="")
                        return this.createAttributeNS(aNamespace,aName);
                    else return this.createAttribute(aName);
                case 3:
                    default:return this.createTextNode("");
            }
        };
        _p.__realLoad=_xmlDocPrototype.load;
        _p.load=function(sUri)
        {
            this.readyState=0;
            this.__realLoad(sUri);
        };
        _p.loadXML=function(s)
        {
            var doc2=(new DOMParser).parseFromString(s,"text/xml");
            while(this.hasChildNodes())
                this.removeChild(this.lastChild);
            var cs=doc2.childNodes;
            var l=cs.length;
            for(var i=0;i<l;i++)
                this.appendChild(this.importNode(cs[i],true));
        };
        _p.setProperty=function(sName,sValue)
        {
            if(sName=="SelectionNamespaces")
            {
                this._selectionNamespaces={};
                var parts=sValue.split(/\s+/);
                var re= /^xmlns\:([^=]+)\=((\"([^\"]*)\")|(\'([^\']*)\'))$/;
                for(var i=0;i<parts.length;i++){
                    re.test(parts[i]);
                    this._selectionNamespaces[RegExp.$1]=RegExp.$4||RegExp.$6;
                }
            }
        };
        _p.__defineSetter__("onreadystatechange",function(f){
            if(this._onreadystatechange)
                this.removeEventListener("load",this._onreadystatechange,false);
            this._onreadystatechange=f;
            if(f)
                this.addEventListener("load",f,false);return f;
        });
        _p.__defineGetter__("onreadystatechange",function(){
            return this._onreadystatechange;
        });
        MzXmlDocument._mozHasParseError=function(oDoc){
            return!oDoc.documentElement||oDoc.documentElement.localName=="parsererror"&&oDoc.documentElement.getAttribute("xmlns")=="http://www.mozilla.org/newlayout/xml/parsererror.xml";
        };
        _p.__defineGetter__("parseError",function(){
            var hasError=MzXmlDocument._mozHasParseError(this);
            var res={errorCode:0,filepos:0,line:0,linepos:0,reason:"",srcText:"",url:""};
            if(hasError){
                res.errorCode= -1;
                try{
                    res.srcText=this.getElementsByTagName("sourcetext")[0].firstChild.data;
                    res.srcText=res.srcText.replace(/\n\-\^$/,"");
                }
                catch(ex){
                    res.srcText="";
                }
                try{
                    var s=this.documentElement.firstChild.data;
                    var re= /XML Parsing Error\:(.+)\nLocation\:(.+)\nLine Number(\d+)\,Column(\d+)/;
                    var a=re.exec(s);res.reason=a[1];res.url=a[2];res.line=a[3];res.linepos=a[4];
                }
                catch(ex){
                    res.reason="Unknown";
                }
            }
            return res;
        });
        var _nodePrototype=Node.prototype;
        _nodePrototype.__proto__={__proto__:_nodePrototype.__proto__};
        _p=_nodePrototype.__proto__;
        _p.__defineGetter__("xml",function(){
            return(new XMLSerializer).serializeToString(this);
        });
        _p.__defineGetter__("baseName",function(){
            var lParts=this.nodeName.split(":");
            return lParts[lParts.length-1];
        });
        _p.__defineGetter__("text",function(){
            var cs=this.childNodes;
            var l=cs.length;
            var sb=new Array(l);
            for(var i=0;i<l;i++)
                sb[i]=cs[i].text;
            return sb.join("");
        });
        _p.selectNodes=function(sExpr){
            var doc=this.nodeType==9?this:this.ownerDocument;
            var nsRes=doc.createNSResolver(this.nodeType==9?this.documentElement:this);
            var nsRes2;
            if(doc._selectionNamespaces){
                nsRes2=function(s){
                    if(doc._selectionNamespaces[s])
                        return doc._selectionNamespaces[s];
                    return nsRes.lookupNamespaceURI(s);
                };
            }
            else nsRes2=nsRes;
            var xpRes=doc.evaluate(sExpr,this,nsRes2,5,null);
            var res=[];
            var item;
            while((item=xpRes.iterateNext()))
                res.push(item);
            return res;
        };
        _p.selectSingleNode=function(sExpr){
            var doc=this.nodeType==9?this:this.ownerDocument;
            var nsRes=doc.createNSResolver(this.nodeType==9?this.documentElement:this);
            var nsRes2;
            if(doc._selectionNamespaces){
                nsRes2=function(s){
                    if(doc._selectionNamespaces[s])
                        return doc._selectionNamespaces[s];
                    return nsRes.lookupNamespaceURI(s);
                };
            }
            else nsRes2=nsRes;
            var xpRes=doc.evaluate(sExpr,this,nsRes2,9,null);
            return xpRes.singleNodeValue;
        };
        _p.transformNode=function(oXsltNode){
            var doc=this.nodeType==9?this:this.ownerDocument;
            var processor=new XSLTProcessor();
            processor.importStylesheet(oXsltNode);
            var df=processor.transformToFragment(this,doc);
            return df.xml;
        };
        _p.transformNodeToObject=function(oXsltNode,oOutputDocument){
            var doc=this.nodeType==9?this:this.ownerDocument;
            var outDoc=oOutputDocument.nodeType==9?oOutputDocument:oOutputDocument.ownerDocument;
            var processor=new XSLTProcessor();processor.importStylesheet(oXsltNode);
            var df=processor.transformToFragment(this,doc);
            while(oOutputDocument.hasChildNodes())
                oOutputDocument.removeChild(oOutputDocument.lastChild);
            var cs=df.childNodes;
            var l=cs.length;
            for(var i=0;i<l;i++)
                oOutputDocument.appendChild(outDoc.importNode(cs[i],true));
        };
        var _attrPrototype=Attr.prototype;
        _attrPrototype.__proto__={__proto__:_attrPrototype.__proto__};
        _p=_attrPrototype.__proto__;
        _p.__defineGetter__("xml",function(){
            var nv=(new XMLSerializer).serializeToString(this);
            return this.nodeName+"=\""+nv.replace(/\"/g,"&quot;")+"\"";
        });
        var _textPrototype=Text.prototype;
        _textPrototype.__proto__={__proto__:_textPrototype.__proto__};
        _p=_textPrototype.__proto__;
        _p.__defineGetter__("text",function(){
            return this.nodeValue;
        });
    })();
}