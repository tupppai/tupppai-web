define([
        'app/views/Base',
        'app/models/Base',
        'app/models/Ask', 
        'app/models/Reply',
        'app/collections/Comments',
        'tpl!app/templates/replydetailplay/ReplyDetailPlayView.html',
        'app/views/replydetailplay/ReplyDetailPersonView',
        'app/views/replydetailplay/ReplyDetailCommentView',
       ],
    function (View, ModelBase, Ask, Reply, Comments, template, ReplyDetailPersonView, ReplyDetailCommentView) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
           
            events: {
                "click .other-click" : "otherPerson",
                "click .old-click" : "oldPerson",
            },
            otherPerson: function(e) {   

                var reply_id = $(e.currentTarget).attr('data-id');
                var type = 2;

                // $("#comment_btn").attr("data-id",reply_id);
                // $("#comment_btn").attr("data-type", type);

                var model = new Reply;
                model.url = '/replies/' + reply_id;
                model.fetch();

                var comments = new Comments;
                comments.url = '/comments?target_type=new';
                comments.data.type = type;
                comments.data.target_id = reply_id;

                var replyDetailPersonView = new Backbone.Marionette.Region({el:"#replyDetailPersonView"});
                var view = new ReplyDetailPersonView({
                    model: model
                });
                replyDetailPersonView.show(view); 

                var userIfo = new Backbone.Marionette.Region({el:"#userIfo"});
                var view = new ReplyDetailCommentView({
                    collection: comments
                });
                userIfo.show(view); 
            },
            oldPerson: function(e) {   

                var reply_id = $(e.currentTarget).attr('data-id');
                var type = 2;

                // $("#comment_btn").attr("data-id",reply_id);
                // $("#comment_btn").attr("data-type", type);

                var model = new Reply;
                model.url = '/replies/' + reply_id;
                model.fetch();

                var comments = new Comments;
                comments.url = '/comments?target_type=new';
                comments.data.type = type;
                comments.data.target_id = reply_id;

                var replyDetailPersonView = new Backbone.Marionette.Region({el:"#replyDetailPersonView"});
                var view = new ReplyDetailPersonView({
                    model: model
                });
                replyDetailPersonView.show(view); 

                var userIfo = new Backbone.Marionette.Region({el:"#userIfo"});
                var view = new ReplyDetailCommentView({
                    collection: comments
                });
                userIfo.show(view); 
            },
            construct: function() {
                var self = this;
                this.listenTo(this.model, 'change', this.render);
            },
    });
