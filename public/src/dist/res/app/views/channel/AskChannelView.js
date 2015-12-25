    define(['imagesLoaded',
    		'app/views/Base',
    		'app/collections/Asks', 
    		'tpl!app/templates/channel/AskChannelView.html'
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
                this.listenTo(this.collection, 'change', this.renderMasonry);
                this.scroll();
                this.collection.loading();
                
                $(document).on('mouseenter', '.person-message-1', function() {
                    $(this).animate({
                        'opacity' : 1
                    },11);
                    $(this).parents('.grid-item').find('.person-message').animate({
                        'opacity' : 0
                    },100);
                });
                $(document).on('mouseleave', '.person-message-1', function() {
                    $(this).animate({
                        'opacity' : 0
                    },200);
                    $(this).parents('.grid-item').find('.person-message').animate({
                        'opacity' : 1
                    },500);
                });
                $(document).on('mouseenter', '.ask-main', function() {
                    $(this).addClass('hover');
                    $(this).parents('.grid-item').find('.person-message').addClass('hide');
                });
                $(document).on('mouseenter', '.ask-desc', function() {
                    $(this).parents('.grid-item').find('.ask-main').addClass('hover');
                    $(this).parents('.grid-item').find('.person-message').addClass('hide');
                });
                $(document).on('mouseleave', '.ask-mouseenter', function() {
                    $(this).find('.person-message').removeClass('hide');
                    
                });
            },
            render: function() {
				this.renderMasonry();                	
		
            }
        });
    });
