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
                "click .header-nav span" : "colorChange", 
            },
            colorChange: function(e) {
                $(e.currentTarget).addClass("bgc-change").parent(".header-nav").siblings(".header-nav").find("span").removeClass("bgc-change");
            },
            bgcChange: function(e) {
                $(e.currentTarget).css ({
                    backgroundColor: "rgba(0, 0 ,0 ,0.4)"
                })
            },
            backChange: function(e) {
                $(e.currentTarget).css ({
                    backgroundColor: "rgba(0, 0 ,0 ,0)"
                })
            },
            ChannelPic:function() {
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
            },
            ChannelFold:function() {
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
