define([
        'app/models/Message',
        'app/collections/Messages',
        'app/views/message/MessageView', 
        'app/views/message/MessageListView',
        'app/views/message/CommentListView'
	   ],
    function ( Message, Messages, MessageView, messageListView, CommentListView) {
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
                var view = new messageListView({
                    collection: messages 
            });
                messageListRegion.show(view);
                
            } else {

            var commentListRegion = new Backbone.Marionette.Region({el:"#message-item-list"});
            var view = new CommentListView({
                collection: messages 
            });
                commentListRegion.show(view);

            }

            
        };
    });
