define(["underscore","app/collections/Categories","app/views/channel/ChannelView","app/views/channel/ChannelWorksView","app/views/channel/ChannelNavView","app/views/channel/ChannelFoldView"],function(e,t,n,r,i,s){"use strict";return function(e){var e=e,r=new n;window.app.content.show(r);var s=new t,o=new Backbone.Marionette.Region({el:"#channelNav"}),r=new i({collection:s});o.show(r),setTimeout(function(){e=="ask"?$(".header-nav[data-type=ask]").trigger("click"):e=="reply"?$(".header-nav[data-type=reply]").trigger("click"):e?$(".header-nav[data-id="+e+"]").trigger("click"):$(".nav-scroll div:first").trigger("click"),$(".header-back").addClass("height-reduce")},1e3)}});