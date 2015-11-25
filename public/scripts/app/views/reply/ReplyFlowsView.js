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
                "click .pressed" : 'pressedBtn',
            },
            replyScroll : function(e) {
                var targetVal       = $(e.currentTarget).width() * 2;
                var navTargetVal    = ($(".nav").width() - $(".nav-bottom").width()) / targetVal;
                var time            = $(e.currentTarget).attr("time");
                var speed           = 3;

                if (e.type == "mouseenter") {
                    if (time) {
                        clearInterval(time);
                    };
                    var startVal = $(e.currentTarget).scrollLeft();

                    time = setInterval(function() {
                        startVal += speed;
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
            pressedBtn : function(e) {
                var index = parseInt($(e.currentTarget).attr('ask'));
                console.log(index)
                console.log(e.currentTarget)
                $(e.currentTarget).parents(".reply-footer").siblings(".reply-main").scrollLeft(index * 288);
                $(e.currentTarget).siblings(".nav-bottom").animate({
                    left: index * $(e.currentTarget).width() + "px"
                })

            },
            pressed: function(e) {

                $(e.currentTarget).addClass('nav-pressed').siblings().removeClass('nav-pressed');
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
