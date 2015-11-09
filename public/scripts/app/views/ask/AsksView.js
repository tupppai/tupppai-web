define([
        'masonry', 
        'imagesLoaded',
        'app/views/Base',
        'app/models/Base', 
        'app/collections/Asks', 
        'tpl!app/templates/ask/AsksView.html'
       ],
    function (masonry, imagesLoaded, View, ModelBase, Asks, template) {

        "use strict";12
        
        return View.extend({
            collection: Asks,
            tagName: 'div',
            className: 'ask-container grid',
            template: template,
            events: {
                "click .download" : "downloadClick",
            },
            construct: function () {
                $('.ask-main').hover(function(){
                    alert( 123 );
                })

                var self = this;
                self.listenTo(self.collection, 'change', self.render);

                self.scroll();
                self.collection.loadMore();

       
            },
            askLikeToggle: function(e) {
                var value = 1;
                if( $(e.currentTarget).hasClass('like-icon-pressed') ){
                    value = -1;
                }

                var id = $(e.currentTarget).attr('data-id');
                var like = new Like({
                    id: id,
                    type: 1,
                    status: value 
                });

                like.save(function(){

                    $(e.currentTarget).toggleClass('like-icon-pressed');
                    $(e.currentTarget).siblings('.actionbar-like-count').toggleClass('icon-like-color');

                    var likeEle = $(e.currentTarget).siblings('.actionbar-like-count');
                    var linkCount = likeEle.text( Number(likeEle.text())+value );
                });
            },
            onRender: function() {
                $('.ask-main').hover(function(){
                    alert( 123 );
                })
                alert( 234 );
            },
            render: function() {

                var template = this.template;
                var el = this.el;

                var msnry = null;
                if(this.collection.length != 0){ 
					var items = '';
					for(var i = 0; i < this.collection.models.length; i++) {
                        items += template((this.collection.models[i]).toJSON());
					}
					var $items = $(items);
					$items.hide();
                    $(el).append($items);

					$items.imagesLoaded().progress( function( imgLoad, image ) {
						var $item = $( image.img ).parents( '.grid-item' );
						msnry = new masonry('.grid', {
							itemSelector: '.grid-item'
						});
						$item.fadeIn(400);
					});
                }
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
            }
        });
    });
