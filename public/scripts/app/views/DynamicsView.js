define([
        'app/views/Base', 
        'app/models/Like', 
        'app/models/Base',
        'tpl!app/templates/DynamicsView.html'
       ],
    function (View, Like,ModelBase, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click .like_toggle' : 'LikeToggle',
                'click .collection_toggle' : 'CollectionToggle',
            },
            onRender: function() {
                $('.download').unbind('click').bind('click',this.downloadClick);
            },
            CollectionToggle: function(e) {
                 var value = 1;
                if( $(e.currentTarget).hasClass('collection-pressed') ){
                    value = -1;
                }
                $(e.currentTarget).toggleClass('collection-pressed');
                var id = $(e.target).attr('data-id');
                var collection = new Collection({
                    id: id,
                    type: 1,
                    status: value 
                });

                collection.save(function(){

                    $(e.currentTarget).toggleClass('collection-pressed');
                    $(e.currentTarget).siblings('.collection-count').toggleClass('icon-like-color');

                    var collectionEle = $(e.currentTarget).siblings('.collection-count');
                    var collectionCount = collectionEle.text( Number(collectionEle.text())+value );
                });

            },
            LikeToggle: function(e) {
                var value = 1;
                if( $(e.currentTarget).hasClass('icon-like-pressed') ){
                    value = -1;
                }

                var id = $(e.target).attr('data-id');
                var like = new Like({
                    id: id,
                    type: 1,
                    status: value 
                });

                like.save(function(){

                    $(e.currentTarget).toggleClass('icon-like-pressed');
                    $(e.currentTarget).siblings('.like-count').toggleClass('icon-like-color');

                    var likeEle = $(e.currentTarget).siblings('.like-count');
                    var linkCount = likeEle.text( Number(likeEle.text())+value );
                });
            },
            construct: function () {
                var self = this;
                self.listenTo(self.collection, 'change', self.render);

                self.scroll();
                self.collection.loadMore();
            },
            render: function() {
                var el = $(this.el);
                var template = this.template;
                this.collection.each(function(model){
                    append(el, template(model.toJSON()));
                });

                this.onRender(); 
            },
            downloadClick: function(e) {
                var data = $(e.currentTarget).attr("data");
                var id   = $(e.currentTarget).attr("data-id");
                var model = new ModelBase;
                model.url = '/record?type='+data+'&target='+id;
                model.fetch({
                    success: function(data) {
                        var urls = data.get('url');
                        _.each(urls, function(url) {
                            location.href = '/download?url='+url;
                        });
                    }
                });
            },
        });
    });
