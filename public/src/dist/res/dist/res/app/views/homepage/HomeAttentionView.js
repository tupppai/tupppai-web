define(["app/views/Base","app/collections/Users","tpl!app/templates/homepage/HomeFansView.html"],function(e,t,n){"use strict";return e.extend({tagName:"div",className:"",data:0,collections:t,template:n,construct:function(){this.listenTo(this.collection,"change",this.render)},onRender:function(){$(".home-nav li").removeClass("active")}})});