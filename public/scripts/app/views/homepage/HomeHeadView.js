define([
        'app/views/Base', 
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
       ],
    function (View, Users, Replies, Asks, Inprogresses, template, HomeReplyView, HomeAskView, HomeConductView, HomeFansView, HomeAttentionView) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .menu-bar-item" : 'homeNav',
                "click .menu-nav-reply" : 'homeReply',
                "click .menu-nav-ask" : 'homeAsk',
                "click .menu-nav-conduct" : 'homeConduct',
                "click .personage-fans" : 'FansList',
                "click #attention" : "attention",
                "click #cancel_attention" : "cancelAttention",
                "click .personage-attention" : "attentionList",
            },

            initialize: function() {
                var self = this;
                this.listenTo(this.model, 'change', this.render);
            },
            onRender: function() {

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
          
            },
            attention: function(event) {

                var el = $(event.currentTarget);
                var id = el.attr("data-id");
                $.post('user/follow', {
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
                $.post('user/follow', {
                    uid: id,
                    status: 0
                }, function(data) {
                    if(data.ret == 1) 
                        $(el).addClass('hide').siblings().removeClass('hide');
                });
            },

            attentionList: function() {
                $('.attention-nav').removeClass("hide");
                $('.fans-nav').addClass("hide");
                $("#homeCantainer").empty();
                $(".home-nav").children("li").removeClass("active");    

                var user = new Users;
                var fansCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var fans_view = new HomeAttentionView({
                    collection: user
                });
                fansCantainer.show(fans_view);

            },
            FansList: function(e) {
                $("#homeCantainer").empty();
                $(".home-nav").children("li").removeClass("active");    
                $(".fans-nav").removeClass("hide");
                $('.attention-nav').addClass("hide");

                var user = new Users;
                var fansCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var fans_view = new HomeFansView({
                    collection: user
                });
                fansCantainer.show(fans_view);
            },
            homeReply: function(e) {
                $('.fans-nav').addClass("hide");
                $('.attention-nav').addClass("hide");
                $("#homeCantainer").empty();
                
                var reply = new Replies;
                var homeReplyCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var reply_view = new HomeReplyView({
                    collection: reply
                });
                homeReplyCantainer.show(reply_view);
            },
            homeConduct: function(e) {
                $('.fans-nav').addClass("hide");
                $('.attention-nav').addClass("hide");
                $("#homeCantainer").empty();

                var inprogress = new Inprogresses;
                var conductCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var conduct_view = new HomeConductView({
                    collection: inprogress
                });
                conductCantainer.show(conduct_view);

            },
            homeAsk: function(e) {
                $('.fans-nav').addClass("hide");
                $('.attention-nav').addClass("hide");
                $("#homeCantainer").empty();
                console.log(e);
                var ask = new Asks;
                var askCantainer = new Backbone.Marionette.Region({el:"#homeCantainer"});
                var ask_view = new HomeAskView({
                    collection: ask
                });
                askCantainer.show(ask_view);   
                
       
            },
            homeNav : function(e) {
                $(e.currentTarget).addClass("active").siblings().removeClass("active");

                var type = $(e.currentTarget).attr('data-type');
                var id = $(e.currentTarget).attr('data-id');
            },
        });
    });
