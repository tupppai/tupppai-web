define(['imagesLoaded',
		'app/views/Base',
		'app/collections/Asks', 
		'tpl!app/templates/ask/AsksView.html'
	], function (imagesLoaded, View, Asks, template) {

        "use strict";
        
        return View.extend({
            collection: Asks,
            tagName: 'div',
            className: 'ask-container grid',
            template: template,
            events: {
                "click .download" : "download",
            },
            construct: function () {
                var self = this;
                self.listenTo(self.collection, 'change', self.render);
				//瀑布流
                self.scroll();

                self.collection.loading();

                $(document).on('mouseenter', '.ask-main', function() {
                    $(this).siblings('.person-message').addClass('hide');
                    $(this).addClass('hover');
                });
                $(document).on('mouseenter', '.ask-desc', function() {
                    $(this).parents('.grid-item').find('.ask-main').addClass('hover');
                    $(this).parents('.grid-item').find('.person-message').addClass('hide');
                });
                $(document).on('mouseleave', '.ask-mouseenter', function() {
                    $(this).find('.person-message').removeClass('hide');
                    $(this).find('.ask-main').removeClass('hover');
                });
            },
            render: function() {
				this.renderMasonry();                	
		
            }
        });
    });
