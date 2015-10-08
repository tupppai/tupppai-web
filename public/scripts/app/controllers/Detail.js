define([ 
        'underscore',  
        'app/models/Ask',
        'app/collections/Comments',
        'app/views/DetailView', 
        'app/views/PopupView',
        'app/views/DetailViewComment',
        'app/views/DetailViewAsk',
       ],
    function (_, Ask, Comments, DetailView, PopupView, DetailViewCommnet, DetailViewAsk) {
        "use strict";

        return function(id) {

            var ask = new Ask;
        	ask.url = '/asks/'+id;
            ask.fetch();

            var comment = new Comments;
            comments.url = '/comments';
            comments.fetch();

            
            var view = new DetailView();
			window.app.content.show(view);


            var askRegion = new Backbone.Marionette.Region({el:"#detailViewAsk"});
            var commentRegion = new Backbone.Marionette.Region({el:"#detailViewComment"});
        
            var view = new AskView({
                model: ask
            });
            askRegion.show(view);

            var view = new DetailViewCommnet({
                collection: comments
            });
            commentRegion.show(view);


            var view = new DetailViewAsk({
                model: ask
            });
            window.app.modal.show(view);
            // var region = new Backbone.Marionette.Region({el:"#content"});
            // var view = new CommentItemView({
            //     model: ask
            // });
            // region.show(view);
            // region.show(commentView);

        };
    });
