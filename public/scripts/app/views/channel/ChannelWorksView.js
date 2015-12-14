 define([ 
        'app/views/Base',
        'masonry', 
        'imagesLoaded',
        'app/collections/Replies',
        'tpl!app/templates/channel/ChannelWorksView.html'
       ],
    function (View, masonry, imagesLoaded, Replies, template) {

        "use strict";
        return View.extend({
            collection: Replies,
            tagName: 'div',
            className: 'reply-container grid',
            template: template,
            events: {
                "mouseover .reply-main": "channelFadeIn",
                "mouseleave .reply-main": "channelFadeOut",
            },
            channelFadeIn: function(e) {
                $(e.currentTarget).find(".reply-artwork-pic").stop(true, true).fadeIn(1500);
                $(e.currentTarget).find(".reply-works-pic").stop(true, true).fadeOut(1500);
                $(e.currentTarget).animate({
                    height: $(e.currentTarget).find(".reply-works-pic").height() + "px"
                }, 1500);
                $(e.currentTarget).find(".reply-artwork-pic").css({
                    marginTop: -$(e.currentTarget).find(".reply-artwork-pic").height() / 2 + "px"
                })
            },
            channelFadeOut: function(e) {
                $(e.currentTarget).find(".reply-artwork-pic").stop(true, true).fadeOut(1500);
                $(e.currentTarget).find(".reply-works-pic").stop(true, true).fadeIn(1500);
            },
            construct: function () {
                var self = this;
                self.listenTo(self.collection, 'change', self.renderMasonry);
                self.scroll();
                self.collection.loading();
            },
            render: function() {
                this.renderMasonry();
            }
           
        });
    });
