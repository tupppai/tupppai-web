 define([ 
        'app/views/Base',
        'superSlide',
        'app/models/Activity',
        'app/collections/Asks', 
        'app/collections/Tags', 
        'app/collections/Channels',
        'app/collections/Replies',
        'app/collections/Activities',
        'app/views/channel/ChannelFoldView',
        'app/views/channel/ChannelWorksView',
        'app/views/channel/ActivityView',
        'app/views/channel/ActivityIntroView',
        'app/views/channel/ChannelDemandView',
        'app/views/channel/AskChannelView',
        'app/views/channel/AskChannelFlowsView',
        'app/views/tag/TagView',
        'tpl!app/templates/channel/ChannelView.html'
       ],
    function (View, superSlide, Activity, Asks, Tags, Channels, Replies, Activities, ChannelFoldView, ChannelWorksView, ActivityView, ActivityIntroView, ChannelDemandView,AskChannelView, AskChannelFlowsView,TagView, template) {

        "use strict";
        return View.extend({
            template: template,
            events: {
                "click .header-nav" : "allHandle", 
                "click .fold-icon": "ChannelFold",
                "click .pic-icon": "ChannelPic",
                "click .activitHide" : "channelOrActivity",
                "click #check_more" : "checkMore",
                "click .super-like" : "superLike",
                "click .download" : "download"
            },
            initialize:function() {
                $('.header-back').addClass("height-reduce");
                var tag = new Tags;
                var indexBanner = new Backbone.Marionette.Region({el:"#tagGroup"});
                var view = new TagView({
                    collection: tag
                });
                indexBanner.show(view);
            },
            checkMore:function() {
                $("#multiclassContentShowView").empty();
                var category_id = $(".bgc-change").attr("data-id");
                $("#multiclassConainerView").addClass('hide');
                $("#allAskConainerView").removeClass('hide');

                var ask = new Asks;
                ask.data.width = 300;
                ask.data.category_id = category_id;

                var askView = new Backbone.Marionette.Region({el:"#allAskContentShowView"});
                var ask_view = new AskChannelFlowsView({
                    collection: ask
                });
                ask_view.collection.reset();
                ask_view.collection.loading();

                this.scroll(ask_view);
                askView.show(ask_view);

            },
            channelOrActivity:function(e) {
                // 求P频道清空和隐藏
                $("#askContainerView").addClass('hide');
                $("#askContentShowView").empty();
                
                // 作品频道清空和隐藏
                $("#replyContainerView").addClass('hide');
                $("#replyContentShowView").empty();

                // 活动频道清空和隐藏
                $("#activityConainerView").addClass('hide');
                $("#activityContentShowView").empty();
                
                //普通频道清空和隐藏 
                $("#multiclassConainerView").addClass('hide');
                $("#multiclassContentShowView").empty(); 
                $("#askflowsShow").empty();              

                //普通频道清空和隐藏 
                $("#allAskConainerView").addClass('hide');
                $("#allAskContentShowView").empty();
                var self = this;

                var type    = $(e.currentTarget).attr("data-type");
                var id      = $(e.currentTarget).attr("data-id");

                if( type == "channel") {
                    $("#multiclassConainerView").removeClass('hide');
                    $(".ask-uploading-popup-hide").removeClass('hide');
                    
                    $(".fold-icon").click();

                    // 头部求P图显示正方形6张
                    var ask = new Asks;
                    ask.data.size = 6;
                    ask.data.category_id = id;
                    ask.data.page = 0;

                    var channelDemand = new Backbone.Marionette.Region({el:"#channelDemand"});
                    var view = new ChannelDemandView({
                        collection: ask
                    });
                    channelDemand.show(view);
                    setTimeout(function() {
                        $(".ask-uploading-popup-hide, .ask-explain").removeClass("blo");
                    }, 500);
                    setTimeout(function() {
                        $(".ask-explain").addClass("blo");
                    }, 5000);

                    $(".fold-icon").css({
                        backgroundPosition: "-155px -528px"
                    }).siblings(".pic-icon").css({
                        backgroundPosition: "-155px -501px"
                    })
                }

                if(type == "activity") {
                    $("#activityConainerView").removeClass('hide');
                    // 活动左侧内容
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

                    var imgageUrl = $(e.currentTarget).attr("data-src");
                    $('.channel-big-pic img').attr("src",imgageUrl );
                    
                    var reply = new Replies;
                    reply.data.category_id = id;
                    reply.data.size = 15;
                    reply.data.page = 0;
                    var activityWorksPic = new Backbone.Marionette.Region({el:"#activityContentShowView"});
                    var activity_view = new ActivityView({
                        collection: reply
                    });
                    activity_view.collection.reset();
                    activity_view.collection.loading();
                    self.scroll(activity_view);
                    activityWorksPic.show(activity_view);

                }

                if(type == "ask") {
                    $("#askContainerView").removeClass('hide');
                    var data_id = 0;
                    $("#attrChannelId").attr("data-id",data_id);
                    $(".login-upload").attr("data-id", data_id);
                    var ask = new Asks;
                        ask.data.size = 15;
                        ask.data.page = 0;
                    var askView = new Backbone.Marionette.Region({el:"#askContentShowView"});
                    var ask_view = new AskChannelView({
                        collection: ask
                    });
                    ask_view.collection.reset();
                    ask_view.collection.loading();

                    self.scroll(ask_view);
                    askView.show(ask_view);
                    setTimeout(function() {
                        $(".ask-uploading-popup-hide, .ask-explain").removeClass("blo");
                    }, 500);
                    setTimeout(function() {
                        $(".ask-explain").addClass("blo");
                    }, 5000)
                }

                if(type == "reply") {
                    $("#replyContainerView").removeClass('hide');
                    var data_id = 0;
                    $("#attrChannelId").attr("data-id",data_id);
                    $(".login-upload").attr("data-id", data_id);
                    
                    var reply = new Replies;
                    var replyView = new Backbone.Marionette.Region({el:"#replyContentShowView"});
                    var reply_view = new ChannelWorksView({
                        collection: reply
                    });
                    reply_view.collection.reset();
                    reply_view.collection.data.size = 6;
                    reply_view.collection.data.page = 0;
                    reply_view.collection.loading();

                    self.scroll(reply_view);
                    replyView.show(reply_view);
                    setTimeout(function() {
                        $(".ask-uploading-popup-hide, .ask-explain").removeClass("blo");
                    }, 500);
                    setTimeout(function() {
                        $(".ask-explain").addClass("blo");
                    }, 5000)
                }
            },
            allHandle: function(e) {


                $('.header-back').addClass("height-reduce");
                $(".channel-header").find(".header-nav").removeClass('bgc-change');
                $(e.currentTarget).addClass("bgc-change");

                var id      =   $(e.currentTarget).attr("data-id");
                var type    =   $(e.currentTarget).attr("data-type");
                                $("#attrChannelId").attr("data-id",id);
                                $(".login-upload").attr("data-id",id);

                $(".pic-icon").css({
                    backgroundPosition: "-128px -501px"
                }).siblings(".fold-icon").css({
                    backgroundPosition: "-127px -528px"
                }) 
            },
            ChannelFold:function(e) {
                var category_id = $(".bgc-change").attr("data-id");
                var type = $(".bgc-change").attr("data-type");
                $("#multiclassContentShowView").empty();

                setTimeout(function(){
                    var channel = new Channels;
                        channel.data.size = 6;
                        channel.data.type = "replies";
                        channel.data.page = 0;
                        channel.data.category_id = category_id;
                    var channelWorksFold = new Backbone.Marionette.Region({el:"#multiclassContentShowView"});
                    var view = new ChannelFoldView({
                        collection: channel
                    });

                    view.scroll(view);
                    view.collection.reset();
                    view.collection.loading();
                    channelWorksFold.show(view);
                    $(e.currentTarget).css({
                        backgroundPosition: "-155px -528px"
                    }).siblings(".pic-icon").css({
                        backgroundPosition: "-155px -501px"
                    })
                    var data_id = $(".user-message").attr("data-id");
                    if(!data_id) {
                        $('.ask-uploading-popup-hide').addClass('hide');
                        $(".login-popup-hide").removeClass('hide')
                    } else {
                        $(".login-popup-hide").addClass('hide')
                        $('.ask-uploading-popup-hide').removeClass('hide');
                    }
                },100);
            },
            ChannelPic:function(e) {
                $("#multiclassContentShowView").empty();
                var id = $(".bgc-change").attr("data-id");
                var type = $(".bgc-change").attr("data-type");

                if(type == "channel") {
                    
                    $("#multiclassConainerView").removeClass('hide');
                    var reply = new Replies;
                    
                    var channelWorksPic = new Backbone.Marionette.Region({el:"#multiclassContentShowView"});
                    var channel_view = new ChannelWorksView({
                        collection: reply
                    });
                    channel_view.collection.reset();
                    channel_view.collection.data.category_id = id;
                    channel_view.collection.data.size = 15;
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
            }
        });
    });
