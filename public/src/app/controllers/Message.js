define([
        'app/models/Message',
        'app/collections/Messages',
        'app/views/message/MessageView', 
        'app/views/message/MessageItemView',
        'app/views/message/MessagePraiseView',
        'app/views/message/CommentItemView'
	   ],
    function ( Message, Messages, MessageView, MessageItemView, MessagePraiseView, CommentItemView) {
        "use strict";

        return function(type, uid) {
   
            var messages = new Messages;
            if(!type) type = 'comment';
            messages.data.type = type;

            var message = new Message({type: type});
            var view = new MessageView({model: message});
            window.app.content.show(view);


            if( type == 'comment') {

                var commentListRegion = new Backbone.Marionette.Region({el:"#message-item-list"});
                var view = new CommentItemView({
                    collection: messages 
                });
                commentListRegion.show(view);

            }  else if (type == "like") {
                var commentListRegion = new Backbone.Marionette.Region({el:"#message-item-list"});
                var view = new MessagePraiseView({
                    collection: messages 
                });
                commentListRegion.show(view);
            } else {
                var messageListRegion = new Backbone.Marionette.Region({el:"#message-item-list"});
                var view = new MessageItemView({
                    collection: messages 
            });
                messageListRegion.show(view);
                
            } 

            $("title").html("图派-消息");
            
            $('.header-back').addClass("height-reduce");

            if( type == "follow") {
                $(".title-follow").removeClass("blo").siblings("span").addClass("blo");
            } else if( type == "reply" ) {
                $(".title-reply").removeClass("blo").siblings("span").addClass("blo");
            } else if( type == "comment") {
                $(".title-comment").removeClass("blo").siblings("span").addClass("blo");
            } else if( type === "system") {
                $(".title-system").removeClass("blo").siblings("span").addClass("blo");
            } else if( type == "like") {
                $(".title-praise").removeClass("blo").siblings("span").addClass("blo");
            };

            
        };
    });
