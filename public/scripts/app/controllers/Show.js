define([
        'underscore', 
        'app/models/Ask',
        'app/collections/Replies',
        'app/views/show/ShowView',
        'app/views/show/ShowReplyView',
        'app/views/show/ShowAskView',
        'app/views/PopupView',
       ],
    function (_, Ask, Replies, ShowView, ShowReplyView, ShowAskView, PopupView) {
        "use strict";

        return function(id) {

            var ask = new Ask;
            ask.url = '/replies/ask/'+id;
            ask.fetch();
            
            var reply = new Replies;
            reply.data.ask_id = id;
            reply.url = '/replies';
            //reply.fetch();

            var view = new ShowView({});
            window.app.content.show(view);

            var showReplyView = new Backbone.Marionette.Region({el:"#showReplyView"});
            var view = new ShowReplyView({
                collection: reply
            });
            showReplyView.show(view);

            var showAskView = new Backbone.Marionette.Region({el:"#showAskView"});
            var view = new ShowAskView({
                model: ask
            });
            showAskView.show(view);

            var view = new PopupView({
                model: ask
            });
            window.app.modal.show(view);
        };
    });
