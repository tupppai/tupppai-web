define([
		'underscore',  
		'app/views/replydetail/ReplyDetailView',
		'app/views/replydetail/ReplyCommentView',
		],
    function (_,  ReplyDetailView, ReplyCommentView) {
        "use strict";
        return function() {
            var view = new ReplyDetailView({});
            window.app.content.show(view);

            var replyCommentView = new Backbone.Marionette.Region({el:"#replyCommentView"});
            var view = new ReplyCommentView({});
            replyCommentView.show(view);
        };
    });
