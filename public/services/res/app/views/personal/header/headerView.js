define(["tpl!app/views/personal/header/header.html","app/views/personal/work/workView","app/views/personal/processing/processingView","app/views/personal/reply/replyView"],function(e,t,n,r){"use strict";return window.app.view.extend({tagName:"div",className:"",template:e,events:{"click .nav-item":"personalTap"},personalTap:function(e){var t=$(".header-portrait").attr("data-id"),n=this;$(e.currentTarget).addClass("active").siblings(".nav-item").removeClass("active");var r=$(e.currentTarget).attr("data-type");n.options.listenList.url="/v2/"+r+"?uid="+t,r=="ask"&&(n.options.listenList.url="/v2/asks?uid="+t+"&type=ask"),n.options.listenList.type=r,n.options.listenList.reset(),n.options.listenList.fetch()},onShow:function(){var e=this.options.listenList.type;this.$el.find("li.nav-item").removeClass("active"),this.$el.find("li.nav-item[data-type='"+e+"']").addClass("active");var t=$(".header-portrait").attr("data-id"),n=$("body").attr("data-uid");t==n?$(".own").removeClass("hide"):$(".ta").removeClass("hide")}})});