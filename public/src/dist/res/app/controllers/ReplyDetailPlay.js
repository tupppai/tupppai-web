define(["underscore","app/models/AskReplies","app/collections/Comments","app/views/replydetailplay/ReplyDetailPlayView"],function(e,t,n,r){"use strict";return function(e,n){$("title").html("图派-作品详情"),$(".header-back").addClass("height-reduce");var i=new t;i.url="replies/reply/"+n,i.fetch();var s=new r({model:i});window.app.content.show(s),$(".header-container").attr("data-reply-id",n)}});