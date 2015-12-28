 define([ 
        'app/views/Base',
        'app/models/Activity',
        'app/collections/Asks', 
        'app/collections/Channels',
        'app/collections/Replies',
        'app/collections/Activities',
        'app/views/channel/ChannelFoldView',
        'app/views/channel/ChannelWorksView',
        'app/views/channel/ActivityView',
        'app/views/channel/ActivityIntroView',
        'app/views/channel/ChannelDemandView',
        'app/views/channel/AskChannelView',
        'app/views/ask/AskFlowsView',
        'tpl!app/templates/channel/ChannelView.html'
       ],
    function (View, Activity, Asks,  Channels, Replies, Activities, ChannelFoldView, ChannelWorksView, ActivityView, ActivityIntroView, ChannelDemandView,AskChannelView, AskFlowsView, template) {

        "use strict";
        return View.extend({
            template: template,
            events: {
                "click .header-nav" : "colorChange", 
                "click .present-nav": "activityIntro",  
                "mouseover .reply-main": "channelFadeIn",
                "mouseleave .reply-main": "channelFadeOut",
                "click .fold-icon": "ChannelFold",
                "click .pic-icon": "ChannelPic",
                "click .download" : "download",
                "click .activitHide" : "channelOrActivity",
                "mouseover .long-pic": "channelWidth",
                "mouseleave .long-pic": "channelWidth",
                "click .super-like" : "superLike",
                "click #check_more" : "checkMore"
            },
            onRender: function() {
                $(window).resize(function(){
                    var width = ($(window).width());
                    if(width > 1351) {
                        $(".channel-big-pic").addClass("channel-big-pic-one").removeClass("channel-big-pic-two");
                    } else {
                        $(".channel-big-pic").addClass("channel-big-pic-two").removeClass("channel-big-pic-one");
                    }
                });
            },
            checkMore:function() {
                var category_id = $(".bgc-change").attr("data-id");
                $(".demand-p").addClass('hide');
                $(".channel-works").addClass('hide');

                var ask = new Asks;
                ask.data.width = 300;
                ask.data.category_id = category_id;

                var askView = new Backbone.Marionette.Region({el:"#askflowsShow"});
                var ask_view = new AskFlowsView({
                    collection: ask
                });
                ask_view.collection.reset();
                ask_view.collection.loading();

                this.scroll(ask_view);
                askView.show(ask_view);

            },
                initialize:function() {
                $(".ask-uploading-popup-hide").removeClass('hide');
                $('.header-back').addClass("height-reduce");
                $(".header-nav:first").trigger('click');
            },
            activityIntro:function(e) {
                var id = $(e.currentTarget).attr("data-id");
                var type = $(e.currentTarget).attr("data-type");

                if(type == "activity") {
                    var activity = new Activity;
                    activity.url = '/activities/' + id;
                    activity.fetch();

                    var activityIntro = new Backbone.Marionette.Region({el:"#activityIntro"});
                    var view = new ActivityIntroView({
                        model: activity
                    });
                    activityIntro.show(view);

                    var activityHref = $(e.currentTarget).attr("activity-href");
                    $(".attr-href").attr("href",activityHref);
                }
               if( type == "channel" ) {
                    var ask = new Asks;
                    ask.data.size = 6;
                    ask.data.category_id = id;
                    ask.data.page = 0;

                    var channelDemand = new Backbone.Marionette.Region({el:"#channelDemand"});
                    var view = new ChannelDemandView({
                        collection: ask
                    });
                    channelDemand.show(view);
                } 
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
            ChannelFold:function(e) {
                var category_id = $(".bgc-change").attr("data-id");
                var type = $(".bgc-change").attr("data-type");
                $("#channelWorksPic").empty();
                     setTimeout(function(){
                        var channel = new Channels;
                        var channelWorksFold = new Backbone.Marionette.Region({el:"#channelWorksPic"});
                        var view = new ChannelFoldView({
                            collection: channel
                        });

                        view.collection.reset();
                        view.collection.size = 10;
                        view.collection.data.category_id = category_id;
                        view.collection.data.type = "replies";
                        view.collection.data.page = 0;
                        view.collection.loading();
                        view.scroll(view);
                        channelWorksFold.show(view);

                        $(e.currentTarget).css({
                            backgroundPosition: "-155px -528px"
                        }).siblings(".pic-icon").css({
                            backgroundPosition: "-155px -501px"
                        })
                    },100);
            },
            channelOrActivity:function(e) {
                var self = this;
                $("#channelWorksPic").empty();

                var width = ($(window).width());
                if(width > 1351) {
                    $(".channel-big-pic").addClass("channel-big-pic-one").removeClass("channel-big-pic-two");
                } else {
                    $(".channel-big-pic").addClass("channel-big-pic-two").removeClass("channel-big-pic-one");
                };
            
                    var type    = $(e.currentTarget).attr("data-type");
                    var id      = $(e.currentTarget).attr("data-id");
                    if( type == "channel") {
                        var channel = new Channels;
                        var channelWorksFold = new Backbone.Marionette.Region({el:"#channelWorksPic"});
                        var view = new ChannelFoldView({
                            collection: channel
                        });
                        view.collection.reset();
                        view.collection.data.category_id = id;
                        view.collection.data.type = "replies";
                        view.collection.size = 10;
                        view.collection.data.page = 0;
                        view.collection.loading();
                        self.scroll(view);
                        channelWorksFold.show(view);

                        $(".fold-icon").css({
                            backgroundPosition: "-155px -528px"
                        }).siblings(".pic-icon").css({
                            backgroundPosition: "-155px -501px"
                        })

                    }
                    if(type == "activity") {
                        var reply = new Replies;
                        reply.reset();
                        reply.data.category_id = id;
                        reply.data.size = 6;
                        reply.data.page = 0;
                        var activityWorksPic = new Backbone.Marionette.Region({el:"#channelWorksPic"});
                        var activity_view = new ActivityView({
                            collection: reply
                        });
                        activity_view.collection.loading();
                        self.scroll(activity_view);
                        activityWorksPic.show(activity_view);
                    }

                    if(type == "ask") {
                        var ask = new Asks;
                        ask.data.size = 15;
                        ask.data.page = 0;
                        var askView = new Backbone.Marionette.Region({el:"#channelWorksPic"});
                        var ask_view = new AskChannelView({
                            collection: ask
                        });
                        ask_view.collection.reset();
                        ask_view.collection.loading();

                        self.scroll(ask_view);
                        askView.show(ask_view);
                    }

                    if(type == "reply") {
                        var reply = new Replies;
                        var replyView = new Backbone.Marionette.Region({el:"#channelWorksPic"});
                        var reply_view = new ChannelWorksView({
                            collection: reply
                        });
                        reply_view.collection.reset();
                        reply_view.collection.data.size = 6;
                        reply_view.collection.data.page = 0;
                        reply_view.collection.loading();

                        self.scroll(reply_view);
                        replyView.show(reply_view);
                    }
            },
            colorChange: function(e) {
                $("#channelWorksPic").empty();
                $("#askflowsShow").empty();
                $(".demand-p").removeClass('hide');
                $(".channel-works").removeClass('hide');
                $('.header-back').addClass("height-reduce");
                $(".channel-header").find(".header-nav").removeClass('bgc-change');
                $(e.currentTarget).addClass("bgc-change");

                var id      =   $(e.currentTarget).attr("data-id");
                var type    =   $(e.currentTarget).attr("data-type");
                                $("#attrChannelId").attr("data-id",id);
                                $(".login-upload").attr("data-id",id);

                if( type == "activity" ) {
                    $(".channel-activity-works").removeClass('hide');
                    $(".channel-big-pic").removeClass('hide');
                    $(".demand-p").addClass('hide');
                    $(".channel-works-header").addClass('hide');
                    $(".channel-fix").removeClass('hide');
                    $(".askForP-icon").addClass("hide");
                    $(".channel-reply").addClass("hide");
                    $(".channel-ask").addClass("hide");
                    $(".channel-activity-works").addClass('hide');
                    $(".channel-activity-works").removeClass('hide');

                    var imgageUrl = $(e.currentTarget).attr("data-src");
                    $('.channel-big-pic img').attr("src",imgageUrl );
                } 
                if( type == "channel" )  {
                    $(".askForP-icon").removeClass("hide");
                    $(".channel-fix").addClass('hide');
                    $(".channel-big-pic").addClass('hide');
                    $(".channel-ask").addClass('hide');
                    $(".channel-activity-works").addClass('hide');
                    $(".demand-p").removeClass('hide');
                    $(".channel-works-header").removeClass('hide');
                    $(".reply-area").removeClass("hide");
                    $(".channel-reply").addClass("hide");

                }

                if( type == "ask") {
                    $(".demand-p").addClass("hide");
                    $(".channel-big-pic").addClass("hide");
                    $(".channel-activity-works").addClass('hide');
                    $(".channel-works-header").addClass("hide");
                    $(".channel-fix").addClass("hide");
                    $(".channel-reply").addClass("hide");
                    $(".channel-ask").removeClass("hide");
                    $(".askForP-icon").removeClass("hide");
                }

                if( type == "reply") {
                    $(".demand-p").addClass("hide");
                    $(".channel-big-pic").addClass("hide");
                    $(".channel-works-header").addClass("hide");
                    $(".channel-activity-works").addClass("hide");
                    $(".channel-fix").addClass("hide");
                    $(".channel-ask").addClass("hide");
                    $(".channel-reply").removeClass("hide");
                }

                $(".pic-icon").css({
                    backgroundPosition: "-128px -501px"
                }).siblings(".fold-icon").css({
                    backgroundPosition: "-127px -528px"
                }) 


            },
            ChannelPic:function(e) {
                $("#channelWorksPic").empty();
                var id = $(".bgc-change").attr("data-id");
                var type = $(".bgc-change").attr("data-type");

                if(type == "channel") {
                        var reply = new Replies;
                        var channelWorksPic = new Backbone.Marionette.Region({el:"#channelWorksPic"});
                        var channel_view = new ChannelWorksView({
                            collection: reply
                        });
                        channel_view.collection.reset();
                        channel_view.collection.data.category_id = id;
                        channel_view.collection.data.size = 6;
                        channel_view.collection.data.page = 0;
                        channel_view.collection.loading();

                        channel_view.scroll(channel_view);
                        channelWorksPic.show(channel_view);
                        $(e.currentTarget).css({
                            backgroundPosition: "-128px -501px"
                        }).siblings(".fold-icon").css({
                            backgroundPosition: "-127px -528px"
                        })                              
                }
            },
            channelFadeIn: function(e) {
                var imgageHeight = $(e.currentTarget).height();
                $(e.currentTarget).css({
                    'height': imgageHeight + "px",
                    'line-height': imgageHeight + "px"
                });
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
            }
           
        });
    });
