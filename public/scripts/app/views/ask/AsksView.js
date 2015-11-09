define([
        'masonry', 
        'imagesLoaded',
        'app/views/Base',
        'app/models/Base', 
        'app/collections/Asks', 
        'tpl!app/templates/ask/AsksView.html'
       ],
    function (masonry, imagesLoaded, View, ModelBase, Asks, template) {

        "use strict";
        
        return View.extend({
            collection: Asks,
            tagName: 'div',
            className: 'ask-container grid',
            template: template,
            events: {
                "click .download" : "downloadClick",
            },
            construct: function () {
                var self = this;
                self.listenTo(self.collection, 'change', self.render);
                self.scroll();

                self.collection.loadMore(function() {
                    $('.ask-main').unbind('hover').bind('mouseenter', function(){
                        $(this).siblings('.person-message').addClass('hide');
                    });
                    $('.ask-mouseenter').unbind('hover').bind('mouseleave', function(){
                        $(this).find('.person-message').removeClass('hide');
                    });
                 
                });
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
