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
                "click .header-nav" : "allHandle", 
                "click .fold-icon": "ChannelFold",
                "click .pic-icon": "ChannelPic",
                "click .activitHide" : "channelOrActivity",
                "click #check_more" : "checkMore",
                "click .super-like" : "superLike",
                "click .download" : "download",
                // "click .arrow-right" : "foldScroll"
            },
            // foldScroll: function(e) {
            //     var longPic = $(e.currentTarget).siblings(".long-pic").find(".channel-works-contain");
            //     var length  = longPic.length;
            //     var width   = 0;
            //     var index   = null;
            //     var boor    = true;

            //     for (var i = 0; i < length; i++) {
            //         width += (longPic[i].offsetWidth + 20);
            //         if (width > 980 && boor) {
            //             index = i;
            //             boor = false;
            //         }
            //     };

            //     console.log(index);

            // },
            initialize:function() {
                $('.header-back').addClass("height-reduce");
            },
            checkMore:function() {
                var category_id = $(".bgc-change").attr("data-id");
                $("#multiclassConainerView").addClass('hide');
                $("#allAskConainerView").removeClass('hide');

                var ask = new Asks;
                ask.data.width = 300;
                ask.data.category_id = category_id;

                var askView = new Backbone.Marionette.Region({el:"#allAskContentShowView"});
                var ask_view = new AskFlowsView({
                    collection: ask
                });
                ask_view.collection.reset();
                ask_view.collection.loading();

                this.scroll(ask_view);
                askView.show(ask_view);

            },
<<<<<<< HEAD
            initialize:function() {
                $(".header-nav:first").trigger('click');
            },
            activityIntro:function(e) {
                var id = $(e.currentTarget).attr("data-id");
                var type = $(e.currentTarget).attr("data-type");
=======
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
>>>>>>> d9ca036a7cd122480fbddc057175ff4f8356cc0d

                var type    = $(e.currentTarget).attr("data-type");
                var id      = $(e.currentTarget).attr("data-id");

                if( type == "channel") {
                    $("#multiclassConainerView").removeClass('hide');

                    setTimeout(function(){
                        $(".pic-icon").trigger("click");
                    },500)
        
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
                }

                if(type == "activity") {
                    $("#activityConainerView").removeClass('hide');

                    var reply = new Replies;
                    reply.reset();
                    reply.data.category_id = id;
                    reply.data.size = 6;
                    reply.data.page = 0;
                    var activityWorksPic = new Backbone.Marionette.Region({el:"#activityContentShowView"});
                    var activity_view = new ActivityView({
                        collection: reply
                    });
                    activity_view.collection.loading();
                    self.scroll(activity_view);
                    activityWorksPic.show(activity_view);

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

                }

                if(type == "ask") {
                    $("#askContainerView").removeClass('hide');
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
                }

                if(type == "reply") {
                    $("#replyContainerView").removeClass('hide');

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
                    var channelWorksFold = new Backbone.Marionette.Region({el:"#multiclassContentShowView"});
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
