define([
        'app/views/Base', 
        'app/collections/Messages', 
        'app/collections/Users', 
        'app/collections/Replies',
        'app/collections/Asks',
        'app/collections/Inprogresses', 
        'tpl!app/templates/homepage/HomeHeadView.html',
        'app/views/homepage/HomeReplyView',
        'app/views/homepage/HomeAskView',
        'app/views/homepage/HomeConductView',
        'app/views/homepage/HomeFansView',
        'app/views/homepage/HomeAttentionView',
        'app/views/homepage/HomeLikedView',
        'app/views/homepage/HomeCollectionView',
        'app/views/homepage/HomeObtainLikeView',
        'app/views/homepage/HomeHerObtainLikeView',
       ],
    function (View, Messages, Users, Replies, Asks, Inprogresses, template, HomeReplyView, HomeAskView, HomeConductView, HomeFansView, HomeAttentionView,HomeLikedView,HomeCollectionView, HomeObtainLikeView, HomeHerObtainLikeView) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .menu-bar-item" : 'homeNav',
                "click .menu-nav-reply" : 'homeReply',
                "click .menu-nav-ask" : 'homeAsk',
                "click .menu-nav-liked" : 'homeLiked',
                "click .menu-nav-conduct" : 'homeConduct',
                "click .menu-nav-collection" : 'homeCollection',
                "click .personage-fans" : 'FansList',
                "click .personage-attention" : "attentionList",
                "click .personage-link" : "personageLink",
                "click #attention" : "attention",
                "click #cancel_attention" : "cancelAttention",
                "click #home-scrollTop" : 'scrollTop',
                "mouseenter .ask-work-pic": "homeScroll",
                "mouseleave .ask-work-pic": "homeScroll",
                "mouseleave .clickimgaa": "clickimgaaaa",
                "mouseenter .clickimgaa": "clickimg"
            },
            clickimgaaaa:function() {
                $('.clickimg').addClass('hide');
            },
            clickimg:function() {
                $('.clickimg').removeClass('hide');
            },
            initialize: function() {
                this.listenTo(this.model, 'change', this.render);
            },
            onRender: function() {
                var own_id = $(".homehead-cantainer").attr("data-id");
                var uid = window.app.user.get('uid');
                setTimeout(function(){
                    $(".width-hide").removeClass('hide');
                },3000);
                
                if( own_id == uid ) {
                    $("#attention").addClass("hide");
                    $("#cancel_attention").addClass("hide");
                    $('.home-self').removeClass("hide");
                } else {
                    $(".menu-nav-collection").addClass('hide');
                    $('.home-others').removeClass("hide");
                    $(".menu-nav-conduct").addClass("hide");
                }
            },
            homeScroll: function(e) {
                var longPic = $(e.currentTarget).find(".ask-box");
                var length  = longPic.length;
                var artworkScrollLeft = $(e.currentTarget). scrollLeft();
                var foldTime = $(e.currentTarget).attr("foldTime");
                var speed = parseInt($(e.currentTarget).attr("speed"));
                var width = length * 137;

                $(e.currentTarget).find(".ask-main-pic").css({
                    width: width + "px"
                });

                if (e.type == "mouseenter") {
                    speed = 1;
                };                
                if (e.type == "mouseleave") {
                    speed = -1;
                };
                $(e.currentTarget).attr("speed", speed);

                if (length > 5) {
                    clearInterval(foldTime);
                    foldTime = setInterval(function() {
                        speed = parseInt(speed);
                        artworkScrollLeft += speed;
                        if(artworkScrollLeft + 685 > width) {
                            clearInterval(foldTime);
                            artworkScrollLeft = width - 685;
                        } else if(artworkScrollLeft < 0) {
                            clearInterval(foldTime);
                            artworkScrollLeft = 0;
                        };
                        $(e.currentTarget).attr("foldTime", foldTime);
                        $(e.currentTarget).scrollLeft(artworkScrollLeft);
                    }, 8);
                };         
            },
            homeLiked:function() {
                $('.fans-nav').addClass("hide");
                $('.attention-nav').addClass("hide");
                $('.home-like-nav').addClass("hide");
               
                var uid = $(".menu-nav-liked").attr("data-id");
                var ask = new Asks;
                var likedCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var liked_view = new HomeLikedView({
                    collection: ask
                });
                liked_view.scroll();
                liked_view.collection.url = '/user/uped';
                liked_view.collection.reset();
                liked_view.collection.data.uid = uid;
                liked_view.collection.data.page = 0;
                liked_view.collection.loading(this.showEmptyView);
                likedCantainer.show(liked_view);
                
            },
            homeAsk: function(e) {
                $('.fans-nav').addClass("hide");
                $('.attention-nav').addClass("hide");
                $('.home-like-nav').addClass("hide");
                
                var uid = $(".menu-nav-reply").attr("data-id");
                var ask = new Asks;
                var askCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var ask_view = new HomeAskView({
                    collection: ask
                });

                ask_view.scroll();
                ask_view.collection.reset();
                ask_view.collection.data.uid = uid;
                ask_view.collection.data.page = 0;
                ask_view.collection.data.type = 'ask';
                ask_view.collection.loading(this.showEmptyView);
                askCantainer.show(ask_view);   
            },
            homeReply: function(e) {
                $('.fans-nav').addClass("hide");
                $('.attention-nav').addClass("hide");
                $('.home-like-nav').addClass("hide");
                
                var uid = $(".menu-nav-reply").attr("data-id");
                var homeReplyCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var reply = new Replies;
                var reply_view = new HomeReplyView({
                    collection: reply
                });
                reply_view.scroll();
                reply_view.collection.reset();
                reply_view.collection.data.uid = uid;
                reply_view.collection.data.page = 0;
                reply_view.collection.loading(this.showEmptyView);
                homeReplyCantainer.show(reply_view);
            },
            attention: function(event) {
                var el = $(event.currentTarget);
                var id = el.attr("data-id");
                $.post('/user/follow', {
                    uid: id,
                    status: 1
                }, function(data) {
                    if(data.ret == 1) 
                        $(el).addClass('hide').siblings().removeClass('hide');
                });
            },
            cancelAttention: function(event) {
                var el = $(event.currentTarget);
                var id = el.attr("data-id");
                $.post('/user/follow', {
                    uid: id,
                    status: 0
                }, function(data) {
                    if(data.ret == 1) 
                        $(el).addClass('hide').siblings().removeClass('hide');
                });
            },
            attentionList: function() {
                var own_id = $(".homehead-cantainer").attr("data-id");
                var uid = window.app.user.get('uid');
                
                if( own_id == uid ) {
                    $("#attention").addClass("hide")    ;
                    $("#cancel_attention").addClass("hide");
                    $('.home-self').removeClass("hide");
                } else {
                    $('.home-others').removeClass("hide");
                    $(".menu-nav-conduct").addClass("hide");
                }
                
                $('.fans-nav').addClass("hide");
                $('.home-like-nav').addClass("hide");
                $("#homeCantainer").empty();
                $(".home-nav").children("li").removeClass("active");    

                var uid = $(".menu-nav-reply").attr("data-id");
                var user = new Users;

                var fansCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var fans_view = new HomeAttentionView({
                    collection: user
                });

                fans_view.scroll();
                fans_view.collection.url = '/follows';
                fans_view.collection.reset();
                fans_view.collection.data.uid = uid;
                fans_view.collection.data.page = 0;
                fans_view.collection.loading(this.showEmptyView);
                fansCantainer.show(fans_view);

            },            
            personageLink: function() {
                var own_id = $(".homehead-cantainer").attr("data-id");
                var uid = window.app.user.get('uid');
                
                if( own_id == uid ) {
                    $("#attention").addClass("hide");
                    $("#cancel_attention").addClass("hide");
                    $('.home-self').removeClass("hide");

                    $('.fans-nav').addClass("hide");
                    $('.attention-nav').addClass("hide");

                    $("#homeCantainer").empty();
                    $(".home-nav").children("li").removeClass("active");    

                    var uid = $(".menu-nav-reply").attr("data-id");
                    var messages = new Messages;
                    messages.data.type = 'like';
                    var fansCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                    var fans_view = new HomeObtainLikeView({
                        collection: messages
                    });
                    fans_view.scroll();
                    fans_view.collection.reset(); 
                    fans_view.collection.data.uid = uid;
                    fans_view.collection.data.page = 0;
                    fans_view.collection.loading(this.showEmptyView);
                    fansCantainer.show(fans_view);
                } else {
                    $('.home-others').removeClass("hide");
                    $(".menu-nav-conduct").addClass("hide");

                    $('.fans-nav').addClass("hide");
                    $('.attention-nav').addClass("hide");

                    $("#homeCantainer").empty();
                    $(".home-nav").children("li").removeClass("active");    

                    var uid = $(".menu-nav-reply").attr("data-id");
                    var reply = new Replies;
                        reply.data.uid = uid;
                        reply.url = '/user/uped';
                        reply.data.page = 0;
                        reply.data.size = 10;
                    var fansCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                    var view = new HomeHerObtainLikeView({
                        collection: reply 
                    });
                    view.scroll();
                    view.collection.reset();
                    view.collection.data.uid = uid;
                    view.collection.data.page = 0;
                    view.collection.loading(this.showEmptyView);
                    fansCantainer.show(view);
                }
                

            },
            FansList: function(e) {
                var own_id = $(".homehead-cantainer").attr("data-id");
                var uid = window.app.user.get('uid');
                
                if( own_id == uid ) {
                    $("#attention").addClass("hide");
                    $("#cancel_attention").addClass("hide");
                    $('.home-self').removeClass("hide");
                } else {
                    $('.home-others').removeClass("hide");
                    $(".menu-nav-conduct").addClass("hide");
                }
                $("#homeCantainer").empty();
                $(".home-nav").children("li").removeClass("active");    
                $('.attention-nav').addClass("hide");
                $('.home-like-nav').addClass("hide");


                var uid = $(".menu-nav-reply").attr("data-id");
                var user = new Users;
                var fansCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var fans_view = new HomeFansView({
                    collection: user
                });
                fans_view.scroll();
                fans_view.collection.url = '/fans';
                fans_view.collection.reset();
                fans_view.collection.data.uid = uid;
                fans_view.collection.data.page = 0;
                fansCantainer.show(fans_view);
                fans_view.collection.loading(this.showEmptyView);
            },
            homeConduct: function(e) {
                $('.fans-nav').addClass("hide");
                $('.attention-nav').addClass("hide");
                $('.home-like-nav').addClass("hide");
             

                var uid = $(".menu-nav-reply").attr("data-id");
                var inprogress = new Inprogresses;
                var conductCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var conduct_view = new HomeConductView({
                    collection: inprogress
                });
                conduct_view.scroll();
                conduct_view.collection.reset();
                conduct_view.collection.data.uid = uid;
                conduct_view.collection.data.page = 0;
                conduct_view.collection.loading(this.showEmptyView);
                conductCantainer.show(conduct_view);
            },            
            homeCollection: function(e) {
                $('.fans-nav').addClass("hide");
                $('.attention-nav').addClass("hide");
                $('.home-like-nav').addClass("hide");

                var uid = $(".homehead-cantainer").attr("data-id");
                // var reply = new Inprogresses;
                var collectionCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var collection_view = new HomeCollectionView({
                    // collection: reply 
                });

                // collection_view.scroll();
                // collection_view.collection.url = '/user/collections';
                // collection_view.collection.reset();
                // collection_view.collection.data.uid = uid;
                // collection_view.collection.data.page = 0;
                // todo qiang
                // collection_view.collection.loading(this.showEmptyView);
                collectionCantainer.show(collection_view);

            },
            homeNav : function(e) {
                $(e.currentTarget).addClass("active").siblings().removeClass("active");
                var type = $(e.currentTarget).attr('data-type');
                var id = $(e.currentTarget).attr('data-id');
                    $(".ask-uploading-popup-hide").addClass("hide");
                    $("#homeCantainer").empty();
                
          
            },
            showEmptyView: function(data) {
                $(".inner-container .emptyContentView").empty();
                $(".inner-container .emptyContentView").addClass('hide');
                $(".addReplyMinHeight").addClass('ReplyMinHeight');
                if(data.data.page == 1 && data.length == 0) {
                    append($("#contentView"), ".emptyContentView");
                    $(".addReplyMinHeight").removeClass('ReplyMinHeight');
                }
            },
 
      
        });
    });
