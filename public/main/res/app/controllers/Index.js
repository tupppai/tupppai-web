define(["app/models/Ask","app/collections/Asks","app/collections/Tags","app/collections/Banners","app/views/index/IndexView","app/views/index/IndexItemView","app/views/index/IndexBannerView","app/views/tag/TagView"],function(e,t,n,r,i,s,o,u){"use strict";return function(){$("title").html("图派-首页"),$(".header-back").removeClass("height-reduce");var e=new t;e.url="/populars",e.data.size=16,$(".title-bar").removeClass("hide"),$(".header-back").removeClass("height-reduce");var a=new n,f=new Backbone.Marionette.Region({el:"#tagGroup"}),l=new u({collection:a});f.show(l);var l=new i({});window.app.content.show(l);var c=new Backbone.Marionette.Region({el:"#indexItemView"}),l=new s({collection:e});c.show(l);var h=new r,f=new Backbone.Marionette.Region({el:"#indexBannerView"}),l=new o({collection:h});f.show(l)}});