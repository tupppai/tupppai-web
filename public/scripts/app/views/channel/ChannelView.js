 define([ 
        'app/views/Base',
        'app/collections/Channels',
        'app/views/channel/ChannelFoldView',
        'app/views/channel/ChannelWorksView',
        'tpl!app/templates/channel/ChannelView.html'
       ],
    function (View, Channels, ChannelFoldView, ChannelWorksView, template) {

        "use strict";
        return View.extend({
            template: template,
            events: {
                "click .like_toggle" : 'likeToggleLarge',
                "mouseover .reply-main": "channelFadeIn",
                "mouseleave .reply-main": "channelFadeOut",
                "click .fold-icon": "ChannelFold",
                "click .pic-icon": "ChannelPic",
                "click .download" : "download", 
                "mouseover .header-nav span" : "bgcChange", 
                "mouseleave .header-nav span" : "backChange", 
                "click .header-nav" : "colorChange", 
            },
            colorChange: function(e) {
                $(e.currentTarget).addClass("bgc-change").siblings(".header-nav").removeClass("bgc-change");
            },
            bgcChange: function(e) {
                $(e.currentTarget).addClass("present-nav");
            },
            backChange: function(e) {
                $(e.currentTarget).removeClass("present-nav");
            },
            ChannelPic:function(e) {
                setTimeout(function(){
                    $("body").scrollTop(401);
                },500);
                $("body").scrollTop(400);

                $("#channelWorksPic").empty();
                var channel = new Channels;
                var channel_id = 1002;

                var channelWorksPic = new Backbone.Marionette.Region({el:"#channelWorksPic"});
                var view = new ChannelWorksView({
                    collection: channel
                });

                view.scroll();
                view.collection.reset();
                view.collection.size = 6;
                view.collection.data.type = "replies";
                view.collection.data.channel_id = channel_id;
                view.collection.data.page = 0;
                view.collection.loading();
                channelWorksPic.show(view);

                $(e.currentTarget).css({
                    backgroundPosition: "-128px -501px"
                }).siblings(".fold-icon").css({
                    backgroundPosition: "-127px -528px"
                })
            },
            ChannelFold:function(e) {
                setTimeout(function(){
                    $("body").scrollTop(401);
                },500);
                $("body").scrollTop(400);
                
                $("#channelWorksPic").empty();
                var channel = new Channels;
                var channel_id = 1002;

                var channelWorksFold = new Backbone.Marionette.Region({el:"#channelWorksPic"});
                var view = new ChannelFoldView({
                    collection: channel
                });

                view.scroll();
                view.collection.reset();
                view.collection.size = 6;
                view.collection.data.type = "replies";
                view.collection.data.channel_id = channel_id;
                view.collection.data.page = 0;
                view.collection.loading();
                channelWorksFold.show(view);

                $(e.currentTarget).css({
                    backgroundPosition: "-155px -528px"
                }).siblings(".pic-icon").css({
                    backgroundPosition: "-155px -501px"
                })
            },
            channelFadeIn: function(e) {
                $(e.currentTarget).css({
                    height: $(e.currentTarget).height() + "px"
                });
                $(e.currentTarget).find(".reply-works-pic").fadeOut(1500);
                $(e.currentTarget).find(".reply-artwork-pic").fadeIn(1500);
                $(e.currentTarget).siblings(".reply-footer").find(".nav-bottom").animate({
                    marginLeft: "50px"
                }, 1500);
                $(e.currentTarget).siblings(".reply-footer").find(".ask-nav").addClass("nav-pressed");
                $(e.currentTarget).siblings(".reply-footer").find(".reply-nav").removeClass("nav-pressed");
            },
            channelFadeOut: function(e) {
                $(e.currentTarget).siblings(".reply-footer").find(".nav-bottom").stop(true, true).animate({
                    marginLeft: "0"
                }, 1500);
                $(e.currentTarget).find(".reply-artwork-pic").stop(true, true).fadeOut(1500);
                $(e.currentTarget).find(".reply-works-pic").stop(true, true).fadeIn(1500);
                $(e.currentTarget).siblings(".reply-footer").find(".ask-nav").removeClass("nav-pressed");
                $(e.currentTarget).siblings(".reply-footer").find(".reply-nav").addClass("nav-pressed");
            },
        
           
        });
    });
