define([ 
        'underscore',  
        'app/models/Ask',
        'app/models/Reply',
        'app/collections/Comments',
        'app/views/comment/CommentView', 
        'app/views/comment/CommentItemView',
        'app/views/comment/HotCommentView',
        'app/views/comment/NewCommentView',
        'app/views/PopupView',
       ],function (_, Ask, Reply, Comments, CommentView, CommentItemView, HotCommentView, NewCommentView, PopupView) {
        "use strict";

        return function(type, id) {
            var model = null;

            if( type == 'ask') {
                var type = 1;
                model = new Ask;
                model.url = '/asks/'+id;
                model.fetch();
            }else {
                var type = 2;
                model = new Reply;
                model.url = '/replies/'+id;
                model.fetch();
            }
            window.app.home.close();

            /*
            var hot_comments = new Comments;
            hot_comments.url = '/comments?target_type=hot';
            hot_comments.data.type = type;
            hot_comments.data.target_id = id;
            */

            var new_comments = new Comments;
            new_comments.url = '/comments?target_type=new';
            new_comments.data.type = type;
            new_comments.data.target_id = id;

            $(window.app.content.el).attr('data-type', type);
            $(window.app.content.el).attr('data-id', id);

            /*
            hot_comments.fetch({
                data: {type: type, target_id: id},
                success: function(data) {
                    if( !data ) {
                        $('.comment-hot-title').removeClass('hide');
                    }
                }
            });         
            */

            new_comments.fetch({
                data: {type: type, target_id: id},
                success: function(data) {
                    if( data ) {
                        $('.comment-hot-content').removeClass('hide');
                    }
                }
            });

            var view = new CommentView();
            window.app.content.show(view);

            var modelRegion = new Backbone.Marionette.Region({el:"#commentItemView"});
            var view = new CommentItemView({
                model: model
            });
            modelRegion.show(view);

            /*
            var hotCommentRegion = new Backbone.Marionette.Region({el:"#hotCommentView"});
            var view = new HotCommentView({
                collection: hot_comments
            });
            hotCommentRegion.show(view);
            */

            var newCommentRegion = new Backbone.Marionette.Region({el:"#newCommentView"});
            var view = new NewCommentView({
                collection: new_comments
            });
            newCommentRegion.show(view);

            var view = new PopupView({
                model: model
            });
            window.app.modal.show(view);
        };
    });
