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

            
        };
    });
