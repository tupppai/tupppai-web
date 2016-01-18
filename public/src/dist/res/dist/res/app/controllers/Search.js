define(["underscore","app/models/Search","app/collections/Threads","app/collections/Users","app/collections/Topics","app/views/search/SearchView","app/views/search/UserItemView","app/views/search/ThreadItemView","app/views/search/TopicItemView"],function(e,t,n,r,i,s,o,u,a){"use strict";return function(e,f){setTimeout(function(){$("title").html("图派-搜索主页"),$(".header-back").removeClass("height-reduce")},100);var l=new t({type:e}),c=new s({model:l});window.app.content.show(c),$("#keyword").val(f);var h=new n;h.url="/search/threads",h.data.keyword=f;var p=new r;p.url="/search/users",p.data.keyword=f;var d=new i;d.url="/search/topics",d.data.keyword=f;var v=new Backbone.Marionette.Region({el:"#userItemView"}),m=new o({collection:p}),g=new Backbone.Marionette.Region({el:"#threadItemView"}),y=new u({collection:h}),b=new Backbone.Marionette.Region({el:"#topicItemView"}),w=new a({collection:d});switch(e){case"user":v.show(m);break;case"thread":g.show(y);break;case"topic":b.show(w);break;default:v.show(m),g.show(y),b.show(w)}}});