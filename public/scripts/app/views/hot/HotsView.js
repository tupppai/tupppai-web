define([
        'masonry', 
        'imagesLoaded',
        'app/views/Base',
        'app/models/Base', 
        'app/collections/Replies', 
        'tpl!app/templates/hot/HotsView.html'
       ],
    function (masonry, imagesLoaded, View, ModelBase, Replies, template) {

        "use strict";
        return View.extend({
            collection: Replies,
            tagName: 'div',
            className: 'hot-container grid',
            template: template,
            events: {
                "click .photo-item-reply" : "photoShift"
            },
            // 求助图片切换
            photoShift: function(e) {
                 var AskSmallUrl = $(e.currentTarget).find('img').attr("src");
                 var AskLargerUrl = $(e.currentTarget).prev().find('img').attr("src");
                 $(e.currentTarget).prev().find('img').attr("src",AskSmallUrl);
                 $(e.currentTarget).find('img').attr("src",AskLargerUrl);
       
                 var replace = $(e.currentTarget).find('.bookmark');
                 var attr = replace.text();
                 if(attr == '原图') {
                    replace.text('作品');
                 } else {
                    replace.text('原图');
                 } 
                  
            },
            construct: function () {
                var self = this;
                self.listenTo(self.collection, 'change', self.render);

                self.scroll();
                self.collection.loadMore();
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
        });
    });
