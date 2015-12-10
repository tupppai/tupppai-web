define([
        'underscore',
        'app/models/AskReplies',
        'app/collections/Comments',
        'app/views/replydetailplay/ReplyDetailPlayView'
        ],
    function (_, AskReplies, Comments, ReplyDetailPlayView) {
        "use strict";

        return function(ask_id, reply_id) {
            var model = new AskReplies;
            model.url = 'replies/ask/' + reply_id;
            model.fetch();
            var view = new ReplyDetailPlayView({
                model: model
            });
            window.app.content.show(view);

            setTimeout(function(){
                $('.pic-click[data-id=' + reply_id + ']').trigger("click");
            },700);

        };
    });
