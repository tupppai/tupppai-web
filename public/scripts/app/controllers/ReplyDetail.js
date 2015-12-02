define([
		'underscore',
		'app/models/AskReplies',
		'app/collections/Comments',
		'app/views/replydetail/ReplyDetailView',
		'app/views/replydetail/ReplyCommentView',
		],
    function (_, AskReplies, Comments, ReplyDetailView, ReplyCommentView) {
        "use strict";

        return function(ask_id, reply_id) {
            var model = new AskReplies;
            model.url = 'replies/ask/' + reply_id;
            model.fetch();

            var view = new ReplyDetailView({
            	model: model
            });

            window.app.content.show(view);
        };
    });
