define(["app/views/Base","tpl!app/templates/channel/ChannelNavView.html","superSlide"],function(e,t,n){"use strict";return e.extend({tagName:"div",className:"nav-scroll clearfix",template:t,construct:function(){this.listenTo(this.collection,"change",this.render),this.collection.loading()},onRender:function(){var e=$(".header-container").attr("data-type");e=="ask"?$(".header-nav[data-id=1008]").trigger("click"):e=="reply"?$(".header-nav[data-id=1007]").trigger("click"):e?$(".header-nav[data-id="+e+"]").trigger("click"):$(".nav-scroll div:first").trigger("click"),$(".menu-bar-item[href='/#channel']").addClass("active"),setTimeout(function(){var e=$(".channel-header-nav").find(".present-nav").length;e>6&&($(".channel-nav-left, .channel-nav-right").removeClass("blo"),$(".channel-header").slide({easing:"easeInOutCubic",titCell:"",mainCell:".nav-scroll ",autoPage:!0,effect:"leftLoop",autoPlay:!0,vis:6,delayTime:500,pnLoop:!0,interTime:2500,triggerTime:550}))},1500),$(window).width()<640&&pageResponse({selectors:".channel-contain",mode:"auto",width:"1180",height:"80"})}})});