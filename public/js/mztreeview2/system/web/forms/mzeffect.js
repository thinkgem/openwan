/*---------------------------------------------------------------------------*\
|  Subject:    Html Element effect base
|  NameSpace:  System.Web.Forms.MzEffect
|  Author:     meizz
|  Created:    2006-07-07
|  Version:    2006-08-05
|-----------------------------------
|  MSN:huangfr@msn.com  QQ:112889082
|  http://www.meizz.com  Copyright (c) meizz   MIT-style license
|  The above copyright notice and this permission notice shall be
|  included in all copies or substantial portions of the Software
\*---------------------------------------------------------------------------*/

//op{interval, duration, increased, continual}
function MzEffect()
{
  this.element=MzEffect.check(arguments[0]);
  if(!this.element) return;

  //prevent repeated effect
  this.attributeName = "att_"+ this._className.replace(/\W/g, "_");
  //if(this.element.getAttribute(this.attributeName)) return;
  //else this.element.setAttribute(this.attributeName, "runing", 0);

  this.terminative=false;  //true for terminate effect

  this.options=System.incorporate({
    interval: 10,   //milliseconds
    duration: 800,  //milliseconds
    increased: true,//false for decreasing
    continual: true
  },arguments[1]||{});

  if(this.initialize) this.initialize();
  this.beginTime = new Date().getTime();
  this.endTime = this.beginTime + this.options.duration;
  if(this.options.onbeforestart) this.options.onbeforestart(this);
  if(this.options.continual) this._process(); else if(!this.virgin){
  this.restore=function(){alert(this +".restore() is unconsummated!");};
  this.dispose();}
};
t=MzEffect.Extends(System, "MzEffect");

t._process=function()
{
  var now = new Date().getTime(), me = this, op=this.options;
  if (now>=this.endTime)
  {
    this.render((op.increased===true ? 1 : 0));
    if(this.finish) this.finish();
    if(op.onafterfinish) op.onafterfinish(this);
    this.dispose(); return;
  }
  var schedule = (now-this.beginTime) / op.duration;
  if(op.increased!==true) schedule = 1 - schedule;

  if(op.onbeforeupdate) op.onbeforeupdate(this);
  if(this.render)this.render(schedule);
  if(op.onafterupdate)op.onafterupdate(this);if(!this.terminative)

  this.timer=setTimeout(function(){me._process()},op.interval); else
  this.element.removeAttribute(this.attributeName);
};
t.cancel=function(){if(this.timer)clearTimeout(this.timer);};
t.dispose=function()
{
  //alert(this +" deleting");
  this.element.removeAttribute(this.attributeName);
  delete this.timer;
  delete this.element;
  delete this.options;
  delete this.endTime;
  delete this.beginTime;
  delete this.terminative;
  delete this.attributeName;
};




//static mehtods
MzEffect.check=function(e)
{
  if("object"==typeof e && !e.tagName) return null;
  if("string"==typeof e && !(e=document.getElementById(e)))
  return null; return e;
};
MzEffect.hide=function()
{
  for (var e=null, n=arguments.length, i=0; i<n; i++)
  {
    if(e=MzEffect.check(arguments[i]))
    {
      if(System.ie && !e.style.width && !e.style.height)
        e.style.display="none"; else
      new MzEffect.Opacity(e,
      {
        increased: false,
        duration: 200,
        onafterfinish: function(o){o.element.style.display="none";o.restore();}
      });
    }
  }
};
MzEffect.show=function()
{
  for (var e=null, n=arguments.length, i=0; i<n; i++)
  {
    if(e=MzEffect.check(arguments[i]))
    {
      e.style.display="";
      if(System.ie && !e.style.width && !e.style.height) return;
      new MzEffect.Opacity(e,{duration:360});
    }
  }
};


//op{interval, duration, increased}
MzEffect.Combo=function(effects, op)
{
  this.effects=effects||[];
  if(this.effects.length==0) return;
  MzEffect.apply(this, [this.effects[0].element, op]);
};
t=MzEffect.Combo.Extends(MzEffect, "MzEffect.Combo");
t.render=function(schedule)
{
  for(var i=0; i<this.effects.length; i++)
  {
    this.effects[i].cancel();
    this.effects[i].render(schedule);
  }
};
t.finish=function()
{
  for(var i=0; i<this.effects.length; i++)
  {
    if(this.effects[i].finish) this.effects[i].finish();
    this.effects[i].dispose();
  }
};
t.dispose=function()
{
  MzEffect.prototype.dispose.call(this);
  delete this.effects;
};


//base effect
//op{interval, duration, increased}
MzEffect.Opacity=function(element, op)
{
  MzEffect.apply(this, arguments);
};
MzEffect.Opacity.Extends(MzEffect, "MzEffect.Opacity").initialize=function()
{
  var op=this.options, obj=this.element;
  this.restore=function()
  {
    if(!System.ie)
    {
      obj.style.opacity = op.Effect_opacity;
      obj.style.MozOpacity = op.Effect_MozOpacity;
      obj.style.KHTMLOpacity = op.Effect_KHTMLOpacity;
    }
    else obj.style.filter = op.Effect_filter;
  };
  this.setOpacity=function(opacity){this.render(opacity);};
  this.render=function(opacity)
  {
    if(!System.ie)
    {
      obj.style.opacity = opacity;
      obj.style.MozOpacity = opacity;
      obj.style.KHTMLOpacity = opacity;
    }
    else obj.style.filter = "alpha(opacity:"+Math.round(opacity*100)+")";
  };
  if (System.ie)op.Effect_filter = obj.style.filter;
  else
  {
    op.Effect_opacity = obj.style.opacity;
    op.Effect_MozOpacity = obj.style.MozOpacity;
    op.Effect_KHTMLOpacity = obj.style.KHTMLOpacity;
  }
};


//op{interval, duration, increased}
MzEffect.MoveBy=function(element, x, y, op)
{
  this.endpointX=x;
  this.endpointY=y;
  MzEffect.apply(this, [element, op]);
};
MzEffect.MoveBy.Extends(MzEffect, "MzEffect.MoveBy").initialize=function()
{
  var obj=this.element, op=this.options;
  op.Effect_top  = obj.style.top;
  op.Effect_left = obj.style.left;
  op.Effect_position = obj.style.position;
  this.render=function(schedule)
  {
    var x = this.endpointY * schedule + this.originalY;
    var y = this.endpointX * schedule + this.originalX;
    this.setPosition(x, y);
  };
  this.setPosition=function(x, y)
  {
    obj.style.top  = y +"px";
    obj.style.left = x +"px";
  };
  this.restore=function()
  {
    obj.style.top  = op.Effect_top;
    obj.style.left = op.Effect_left;
    obj.style.position = op.Effect_position;
  };
  this.originalY = parseFloat(obj.style.top  || '0');
  this.originalX = parseFloat(obj.style.left || '0');
  if(obj.style.position == "") obj.style.position = "relative";
};


//op{interval, duration, increased, beginColor, endColor, finalColor}
function n2h(s){s=parseInt(s).toString(16);return ("00"+ s).substr(s.length);}
MzEffect.Glittery=function(element, op)
{
  MzEffect.apply(this, arguments);
};
MzEffect.Glittery.Extends(MzEffect, "MzEffect.Glittery").initialize=function()
{
  var op=this.options, obj=this.element, endColor="#FFFFFF";
  var backColor = (obj.currentStyle||obj.style).backgroundColor;
  if(backColor)
  {
    if(/^\#[\da-z]{6}$/i.test(backColor))endColor=backColor;
    if(backColor.indexOf("rgb(")==0)
    {
      var cols=backColor.substring(4, backColor.length-1).split(",");
      for(var i=0,endColor="#";i<cols.length;i++)endColor+=n2h(cols[i]);
    }
  }
  op.beginColor = op.beginColor || "#FFFF00";
  op.endColor   = op.endColor || endColor;
  op.finalColor = op.finalColor || obj.style.backgroundColor;

  this.colors_base=[
    parseInt(op.beginColor.substring(1,3),16),
    parseInt(op.beginColor.substring(3,5),16),
    parseInt(op.beginColor.substr(5),16)];
  this.colors_var=[
    parseInt(op.endColor.substring(1,3),16)-this.colors_base[0],
    parseInt(op.endColor.substring(3,5),16)-this.colors_base[1],
    parseInt(op.endColor.substr(5),16)-this.colors_base[2]];

  this.finish=function()
  {
    obj.style.backgroundColor = op.finalColor;
  };
  this.render=function(schedule)
  {
    var colors=[
      n2h(Math.round(this.colors_base[0]+(this.colors_var[0]*schedule))),
      n2h(Math.round(this.colors_base[1]+(this.colors_var[1]*schedule))),
      n2h(Math.round(this.colors_base[2]+(this.colors_var[2]*schedule)))];
    obj.style.backgroundColor = "#"+ colors.join("");
  };
  this.dispose=function()
  {
    MzEffect.prototype.dispose.call(this);
    delete this.colors_base;
    delete this.colors_var;
  };
};
