 define([ 
        'app/views/Base',
        'tpl!app/templates/channel/ChannelFoldView.html'
       ],
    function (View, template ) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: 'channel-fold',
            template: template,
            events: {
                "mouseenter .long-pic, .fold-comments, .channel-works-head, .like-actionbar" : "foldScroll",
                "mouseleave .long-pic, .fold-comments, .channel-works-head, .like-actionbar" : "foldScroll",
            },
            foldScroll: function(e) {
                var longPic = $(e.currentTarget).parents(".channel-works-right").find(".channel-works-contain");
                var length  = longPic.length;
                var width   = 0;
                var artworkScrollLeft = $(e.currentTarget).parents(".channel-works-right").scrollLeft();
                var foldTime = $(e.currentTarget).parents(".channel-works-right").attr("foldTime");
                var speed = parseInt($(e.currentTarget).parents(".channel-works-right").attr("speed"));

                for (var i = 0; i < length; i++) {
                    width += (longPic[i].offsetWidth + 20);
                };
                if (e.type == "mouseenter" && $(e.currentTarget).hasClass("long-pic")) {
                    speed = 1;
                };                
                if (e.type == "mouseleave" && $(e.currentTarget).hasClass("long-pic")) {
                    speed = -1;
                };
                $(e.currentTarget).parents(".channel-works-right").attr("speed", speed);

                if (width > 980) {
                    clearInterval(foldTime);
                    foldTime = setInterval(function() {
                        speed = parseInt(speed);
                        artworkScrollLeft += speed;
                        if(artworkScrollLeft + 980 > width) {
                            clearInterval(foldTime);
                            artworkScrollLeft = width - 980;
                        } else if(artworkScrollLeft < 0) {
                            clearInterval(foldTime);
                            artworkScrollLeft = 0;
                        };
                        $(e.currentTarget).parents(".channel-works-right").attr("foldTime", foldTime);
                        $(e.currentTarget).parents(".channel-works-right").scrollLeft(artworkScrollLeft);
                    }, 8);
                };
                if(($(e.currentTarget).hasClass("fold-comments") || $(e.currentTarget).hasClass("channel-works-head") || $(e.currentTarget).hasClass("like-actionbar")) && e.type == "mouseenter") {
                    clearInterval(foldTime);
                }
            },
            construct: function () {
                this.listenTo(this.collection, 'change', this.render);
            },
 
        });
    });
