define(["underscore","app/collections/Categories","app/views/channel/ChannelView","app/views/channel/ChannelWorksView","app/views/channel/ChannelNavView","app/views/channel/ChannelFoldView"],function(e,t,n,r,i,s){"use strict";return function(e){var e=e,r=new n;window.app.content.show(r);var s=new t,o=new Backbone.Marionette.Region({el:"#channelNav"}),r=new i({collection:s});o.show(r),$(".header-container").attr("data-type",e),$(".header-back").addClass("height-reduce")}});