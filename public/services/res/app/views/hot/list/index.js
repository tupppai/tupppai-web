define(["app/views/hot/reply/replyView","lib/component/asyncList"],function(e){"use strict";return window.app.list.extend({tagName:"div",className:"hot-pageSection clearfix grid",childView:e,onShow:function(){title("热门作品"),$(".menuPs").removeClass("hide");var e={};share_friend(e,function(){},function(){}),share_friend_circle(e,function(){},function(){}),this.$el.asynclist(this)}})});