define([
		'underscore',
		'app/models/AskReplies',
		'app/collections/Comments',
        'app/views/replydetail/ReplyDetailView',
		],
    function (_, AskReplies, Comments, ReplyDetailView, ReplyPersonView ) {
        "use strict";

        return function(ask_id, reply_id) {

            setTimeout(function(){
                $("title").html("图派-作品详情");
                $('.header-back').removeClass("height-reduce");
            },100);

            var model = new AskReplies;
            model.url = 'replies/ask/' + reply_id;
            model.fetch();
            var view = new ReplyDetailView({
            	model: model
            });
            window.app.content.show(view);

            setTimeout(function(){
                $('.reply-trigger[data-id=' + reply_id + ']').trigger("click");
            },700);

        };
    });
