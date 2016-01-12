define([
        'underscore',
        'app/models/AskReplies',
        'app/collections/Comments',
        'app/views/replydetailplay/ReplyDetailPlayView'
        ],
    function (_, AskReplies, Comments, ReplyDetailPlayView) {
        "use strict";

        return function(ask_id, reply_id) {

            $("title").html("图派-作品详情");
            $('.header-back').addClass("height-reduce");

            var model = new AskReplies;
            model.url = 'replies/reply/' + reply_id;
            model.fetch();
            var view = new ReplyDetailPlayView({
                model: model
            });
            window.app.content.show(view);

            $(".header-container").attr("data-reply-id",reply_id);

        };
    });
