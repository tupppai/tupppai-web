 define([ 
        'app/views/Base',
        'app/models/Activity',
        'app/collections/Channels',
        'app/collections/Replies',
        'app/collections/Activities',
        'app/views/channel/ChannelFoldView',
        'app/views/channel/ChannelWorksView',
        'app/views/channel/ActivityView',
        'app/views/channel/ActivityIntroView',
        'tpl!app/templates/channel/ChannelView.html'
       ],
    function (View, Activity, Channels, Replies, Activities, ChannelFoldView, ChannelWorksView, ActivityView, ActivityIntroView, template) {

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
                "click .header-nav" : "colorChange", 
                "click .activitHide" : "channelOrActivity",
                "click .present-nav": "activityIntro",
                "mouseover .long-pic": "channelWidth",
                "mouseleave .long-pic": "channelWidth",
            },
            activityIntro:function() {
                var activity = new Activity;
                activity.url = '/activities/' + 1003;
                activity.fetch();

                var activityIntro = new Backbone.Marionette.Region({el:"#activityIntro"});
                var view = new ActivityIntroView({
                    model: activity
                });
                activityIntro.show(view);
            },
            channelWidth: function(e) {
                if(e.type == "mouseover") {
                    $(e.currentTarget).siblings(".view-details").animate({
                        width: "20px"
                    }, 500);
                }
                if(e.type == "mouseleave") {
                    $(e.currentTarget).siblings(".view-details").stop(true, true).animate({
                        width: "0px"
                    }, 500);
                }
            },
            channelOrActivity:function(e) {
                var type = $(e.currentTarget).attr("data-type");
                var id = $(e.currentTarget).attr("data-id");
                var category_id = $(e.currentTarget).attr("data-category-id");
                    $("#channelWorksPic").empty();
                
                    setTimeout(function(){
                        if( type == "channel") {
                            var reply = new Replies;
                            var channelWorksPic = new Backbone.Marionette.Region({el:"#channelWorksPic"});
                            var channel_view = new ChannelWorksView({
                                collection: reply
                            });
                            channel_view.collection.reset();
                            channel_view.collection.data.type = "replies";
                            channel_view.collection.data.category_id = id;
                            channel_view.collection.data.size = 6;
                            channel_view.collection.data.page = 0;
                            channel_view.collection.loading();
                            channelWorksPic.show(channel_view);
                        } else {
                            var activity = new Replies;
                            var activityWorksPic = new Backbone.Marionette.Region({el:"#channelWorksPic"});
                            var activity_view = new ActivityView({
                                collection: activity
                            });
                            activity_view.collection.reset();
                            activity_view.collection.data.category_id = id;
                            activity_view.collection.data.size = 6;
                            activity_view.collection.data.page = 0;
                            activity_view.collection.loading();
                            activityWorksPic.show(activity_view);
                        }
                    },100)
            },
         
            onRender:function() {
                setTimeout(function(){
                  $(".demand-p").removeClass('hide');
                  $(".channel-works-header").removeClass('hide');
                  $(".header-nav[data-id=1001]").addClass('bgc-change');
                  $("#channelWorksPic").empty();
                },100);
            },
            colorChange: function(e) {
                $("#channelWorksPic").empty();
                $(e.currentTarget).addClass("bgc-change").siblings(".header-nav").removeClass("bgc-change");
                var id = $(e.currentTarget).attr("data-id");
                var type = $(e.currentTarget).attr("data-type");
                if( type == "activity" ) {
                    $(".channel-activity-works").removeClass('hide');
                    $(".channel-big-pic").removeClass('hide');
                    $(".demand-p").addClass('hide');
                    $(".channel-works-header").addClass('hide');
                    $(".channel-fix").removeClass('hide');
                } else {
                    $(".channel-fix").addClass('hide');
                    $(".channel-big-pic").addClass('hide');
                    $(".channel-activity-works").addClass('hide');
                    $(".demand-p").removeClass('hide');
                    $(".channel-works-header").removeClass('hide');
                }
                    var imgageUrl = $(e.currentTarget).attr("data-src");
                    $('.channel-big-pic img').attr("src",imgageUrl );

            },
            ChannelPic:function(e) {
                var id = $(".bgc-change").attr("data-id");
                $("#channelWorksPic").empty();

                setTimeout(function(){
                        var reply = new Replies;
                        var channelWorksPic = new Backbone.Marionette.Region({el:"#channelWorksPic"});
                        var channel_view = new ChannelWorksView({
                            collection: reply
                        });
                        channel_view.collection.reset();
                        channel_view.collection.data.type = "replies";
                        channel_view.collection.data.channel_id = id;
                        channel_view.collection.data.size = 6;
                        channel_view.collection.data.page = 0;
                        channel_view.collection.loading();
                        channelWorksPic.show(channel_view);
                },100);
                $(e.currentTarget).css({
                    backgroundPosition: "-128px -501px"
                }).siblings(".fold-icon").css({
                    backgroundPosition: "-127px -528px"
                })                              
            },
            ChannelFold:function(e) {
                
                var channel_id = $(".bgc-change").attr("data-id");
                $("#channelWorksPic").empty();

                 setTimeout(function(){
                    var channel = new Channels;

                    var channelWorksFold = new Backbone.Marionette.Region({el:"#channelWorksPic"});
                    var view = new ChannelFoldView({
                        collection: channel
                    });

                    view.collection.reset();
                    view.collection.size = 10;
                    view.collection.data.type = "replies";
                    view.collection.data.channel_id = channel_id;
                    view.collection.data.page = 0;
                    view.collection.loading();
                    channelWorksFold.show(view);
                },100);
                $(e.currentTarget).css({
                    backgroundPosition: "-155px -528px"
                }).siblings(".pic-icon").css({
                    backgroundPosition: "-155px -501px"
                })
            },
            channelFadeIn: function(e) {
                $(e.currentTarget).css({
                    'height': $(e.currentTarget).height() + "px",
                    'line-height': $(e.currentTarget).height() + "px"
                });
                $(e.currentTarget).find(".reply-works-pic").fadeOut(1000);
                $(e.currentTarget).find(".reply-artwork-pic").fadeIn(1500);
                $(e.currentTarget).siblings(".reply-footer").find(".nav-bottom").animate({
                    marginLeft: "37px"
                }, 1000);
                $(e.currentTarget).siblings(".reply-footer").find(".ask-nav").addClass("nav-pressed");
                $(e.currentTarget).siblings(".reply-footer").find(".reply-nav").removeClass("nav-pressed");
            },
            channelFadeOut: function(e) {
                $(e.currentTarget).siblings(".reply-footer").find(".nav-bottom").stop(true, true).animate({
                    marginLeft: "0"
                }, 1000);
                $(e.currentTarget).find(".reply-artwork-pic").stop(true, true).fadeOut(1500);
                $(e.currentTarget).find(".reply-works-pic").stop(true, true).fadeIn(1500);
                $(e.currentTarget).siblings(".reply-footer").find(".ask-nav").removeClass("nav-pressed");
                $(e.currentTarget).siblings(".reply-footer").find(".reply-nav").addClass("nav-pressed");
            },
        
           
        });
    });
