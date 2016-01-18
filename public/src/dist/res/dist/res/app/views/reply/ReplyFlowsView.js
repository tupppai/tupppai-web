 define([
        'masonry', 
        'imagesLoaded',
        'app/views/Base',
        'app/models/Base',
        'app/models/Like',  
        'app/collections/Replies', 
        'tpl!app/templates/reply/ReplyFlowsView.html'
       ],
    function (masonry, imagesLoaded, View, ModelBase, Like, Replies, template) {

        "use strict";
        return View.extend({
            collection: Replies,
            tagName: 'div',
            className: 'reply-container grid',
            template: template,
            events: {
                "click .like_toggle" : 'likeToggle',
                "click .pressed" : 'pressed',
                "mouseenter .reply-main" : 'replyScroll',
                "mouseleave .reply-main" : 'replyScroll',
            },
            replyScroll : function(e) {
                var length = $(e.currentTarget).children().children("img").length;
                var targetVal       = $(e.currentTarget).width() * (length - 1);
                var navTargetVal    = Math.abs(($(e.currentTarget).siblings(".reply-footer").find(".nav").width() - $(".nav-bottom").width()) / targetVal);
                var time            = $(e.currentTarget).attr("time");
                var speed           = 2;

                if (e.type == "mouseenter") {             
                    if (time) {
                        clearInterval(time);
                    };
                    var startVal = $(e.currentTarget).scrollLeft();

                    time = setInterval(function() {
                        startVal += speed;
                        var scroll = Math.round(startVal / $(e.currentTarget).width());
                        $(e.currentTarget).siblings(".reply-footer").find(".nav").children("span").removeClass("nav-pressed");
                        $(e.currentTarget).siblings(".reply-footer").find(".nav").children("span").eq(scroll).addClass("nav-pressed");
                        
                        if (startVal >= targetVal) {
                            clearInterval(time);
                            startVal = targetVal;
                        };
                        $(e.currentTarget).scrollLeft(startVal);
                        $(e.currentTarget).siblings(".reply-footer").children().children(".nav-bottom").css({
                            left: startVal * navTargetVal + "px"
                        });
                        $(e.currentTarget).attr("time", time);
                    }, 1);
                };
                if (e.type == "mouseleave") {
                    if (time) {
                        clearInterval(time);
                    };
                    var startVal = $(e.currentTarget).scrollLeft();

                    time = setInterval(function() {
                        startVal -= speed;
                        var scroll = Math.round(startVal / $(e.currentTarget).width());
                        $(e.currentTarget).siblings(".reply-footer").find(".nav").children("span").removeClass("nav-pressed");
                        $(e.currentTarget).siblings(".reply-footer").find(".nav").children("span").eq(scroll).addClass("nav-pressed");
                        if (startVal <= 0) {
                            clearInterval(time);
                            startVal = 0;
                        };
                        $(e.currentTarget).scrollLeft(startVal);
                        $(e.currentTarget).siblings(".reply-footer").children().children(".nav-bottom").css({
                            left: startVal * navTargetVal + "px"
                        });
                        $(e.currentTarget).attr("time", time);
                    }, 1);
                };        
            },            
   
            pressed: function(e) {
                $(e.currentTarget).addClass("nav-pressed").siblings().removeClass("nav-pressed");
                var index = $(e.currentTarget).index();
                $(e.currentTarget).parents(".reply-footer").siblings(".reply-main").scrollLeft(index * 280);
                $(e.currentTarget).siblings(".nav-bottom").animate({
                    left: index * $(e.currentTarget).width() + "px"
                })
                $(e.currentTarget).addClass('nav-pressed').siblings().removeClass('nav-pressed');
            },
            construct: function () {
                this.listenTo(this.collection, 'change', this.renderMasonry);
                this.scroll();
                this.collection.loading();
            },
            render: function() {
                this.renderMasonry();
            }
        });
    });
