TinyCore.AMD.define("tabs",[],function(){return{mediator:TinyCore.Toolbox.request("mediator"),onStart:function(){var a=document.querySelectorAll('[data-tc-modules="tabs"]');oTools.trackModule("JS_Libraries","call","tabs"),this.autobind(a)},autobind:function(a){var b=this;$(a).each(function(){var a,c=this,d=null,e=b.getTabsInfo(c);$(c).addClass("tab-container"),a=b.createDesktopTabs(e);for(var f=0;f<e.length;f++)$(document.getElementById(e[f].id)).before(b.createMobileTabs(e[f].id,e[f].name));$(this).prepend(a),$("a.update-tabs",c).bind("click",function(a){a.preventDefault(),b.mediator.publish("close:wysiwyg");var d=(a.srcElement||a.target).href;-1!==d.indexOf("#")&&b.updateTabs(c,d.split("#")[1])}),null===d&&(d=$("nav li:first a",c)[0]),b.updateTabs(c,d.href.split("#")[1])})},toggleTabs:function(a){$(a).each(function(){$(this).bind("click",function(a){a.preventDefault();var b=this.href;-1!==b.indexOf("#")&&$(document.getElementById(b.split("#")[1])).slideToggle()})})},getTabsInfo:function(a){var b={};return $("> section",a).each(function(a){var c=this;b[a]={},b[a].id=this.id,b[a].name=null!==c.getAttribute("data-tc-name")?c.getAttribute("data-tc-name"):c.id.replace("-"," "),b.length=a+1}),b},createDesktopTabs:function(a){var b=document.createElement("nav"),c=document.createElement("ul"),d="";b.className="tabs";for(var e=0;e<a.length;e++)d+='<li id="'+a[e].id+'-li"><a href="#'+a[e].id+'" class="update-tabs">'+a[e].name+"</a></li>";return c.innerHTML=d,b.innerHTML=c.outerHTML,b},createMobileTabs:function(a,b){var c=document.createElement("header"),d=document.createElement("a"),e=b;return c.className="tab",c.id=a+"-header",d.innerHTML=e,d.href="#"+a,d.className="update-tabs",c.innerHTML=d.outerHTML,c},updateTabs:function(a,b){$("> nav a.update-tabs, > header.tab a.update-tabs",a).each(function(){var a=$("#"+this.href.split("#")[1]),c=this.href.split("#")[1],d=$(document.getElementById(c+"-li")),e=$(document.getElementById(c+"-header"));-1!==this.href.indexOf(b)?(d.addClass("active"),e.addClass("active"),a.fadeIn()):(d.removeClass("active"),e.removeClass("active"),a.hide())})}}});