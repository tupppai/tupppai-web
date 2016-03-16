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
            className: 'channel-reply-container grid',
            template: template,
            events: {
                "mouseover .reply-main": "channelFadeIn",
                "mouseleave .reply-main": "channelFadeOut",
            },
            channelFadeIn: function(e) {
                var imgageHeight = $(e.currentTarget).height();
                $(e.currentTarget).css({
                    'height': imgageHeight + "px",
                });
                $(e.currentTarget).find(".reply-artwork-pic").css({
                    'height': imgageHeight + "px",
                    'lineHeight': imgageHeight + "px"
                })
                $(e.currentTarget).find(".reply-works-pic").fadeOut(700);
                $(e.currentTarget).find(".reply-artwork-pic").fadeIn(700);
                $(e.currentTarget).siblings(".reply-footer").find(".nav-bottom").animate({
                    marginLeft: "37px"
                }, 700);
                $(e.currentTarget).siblings(".reply-footer").find(".ask-nav").addClass("nav-pressed");
                $(e.currentTarget).siblings(".reply-footer").find(".reply-nav").removeClass("nav-pressed");
            },
            channelFadeOut: function(e) {
                $(e.currentTarget).siblings(".reply-footer").find(".nav-bottom").stop(true, true).animate({
                    marginLeft: "0"
                }, 700);
                $(e.currentTarget).find(".reply-artwork-pic").stop(true, true).fadeOut(700);
                $(e.currentTarget).find(".reply-works-pic").stop(true, true).fadeIn(700);
                $(e.currentTarget).siblings(".reply-footer").find(".ask-nav").removeClass("nav-pressed");
                $(e.currentTarget).siblings(".reply-footer").find(".reply-nav").addClass("nav-pressed");
            },
            construct: function () {
                this.listenTo(this.collection, 'change', this.renderMasonry);
            },
            render: function() {
                this.renderMasonry();
            }
        });
    });
