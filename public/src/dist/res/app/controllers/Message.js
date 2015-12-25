define([
        'app/models/Message',
        'app/collections/Messages',
        'app/views/message/MessageView', 
        'app/views/message/MessageItemView',
        'app/views/message/CommentItemView'
	   ],
    function ( Message, Messages, MessageView, messageItemView, CommentItemView) {
        "use strict";

        return function(type, uid) {
   
            var messages = new Messages;
            if(!type) type = 'comment';
            messages.data.type = type;

            var message = new Message({type: type});
            var view = new MessageView({model: message});
            window.app.content.show(view);


            if( type != 'comment') {
                var messageListRegion = new Backbone.Marionette.Region({el:"#message-item-list"});
                var view = new messageItemView({
                    collection: messages 
            });
                messageListRegion.show(view);
                
            } else {

            var commentListRegion = new Backbone.Marionette.Region({el:"#message-item-list"});
            var view = new CommentItemView({
                collection: messages 
            });
                commentListRegion.show(view);

            }

            $("title").html("图派-消息");
            $('.header-back').addClass("height-reduce");
            if( type == "follow") {
                $(".nav-title").html("关注通知");
            } else if( type == "reply" ) {
                $(".nav-title").html("帖子回复");
            } else if( type == "comment") {
                $(".nav-title").html("发出的评论");
            } else if( type === "system") {
                $(".nav-title").html("系统通知");
            }

            
        };
    });
