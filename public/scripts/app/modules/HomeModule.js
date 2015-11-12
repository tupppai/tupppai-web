define(['marionette',  
        'app/models/User',
        'tpl!app/templates/home/HomeView.html',
        'app/views/Base',
        'app/views/home/FriendshipListView', 
        'app/views/home/AskListView', 
        'app/views/home/ReplyListView', 
        'app/views/home/InprogressListView'
    ], function (Marionette, User, template, View, friendshipListView, askListView, replyListView, inprogressListView) {
        "use strict";

        var homeView = View.extend({
            model: User,
            tagName: 'div',
            className: '',
            template : template,
            askView: null,
            replyView: null,
            inprogressView: null,
            initialize: function () {
                this.listenTo(this.model, "change", this.render);
                $('.header-back').addClass('hidder-animation');
                $('.header').addClass('hidder-animation');

                this.friendshipView = new friendshipListView();
                this.askView = new askListView();
                this.replyView = new replyListView();
                this.inprogressView = new inprogressListView();
            },
            events: {
                "click #load_fan" : "loadFans",
                "click #load_follow" : "loadFollows",
                "click #load_ask" : "loadAsks",
                "click #load_reply" : "loadReplies",
                "click #load_inprogress" : "loadInprogress",
                "click #attention" : "attention",
                "click #cancel_attention" : "cancelAttention",
                "click .photo-item-reply" : "photoShift",
                "click .return-home-page" : "history",
                "click .delete-card" : "deleteCard",
                "click .personage-head-protrait img": "avatarPopup",
                "click .download" : "download",
            },
            loadFans:function(e) {
            
             

                $("#homeListView").empty();
                $(window).unbind('scroll'); 
                this.friendshipView.scroll();
                console.log(456);
                this.friendshipView.collection.reset();
                this.friendshipView.collection.url = '/fans';
                this.friendshipView.collection.data.uid = $(window.app.home.el).attr('data-uid');
                this.friendshipView.collection.data.page = 0;
                this.friendshipView.collection.loading(this.showEmptyView);
            },
            loadFollows:function() {
                $("#homeListView").empty();
                $(window).unbind('scroll'); 
                this.friendshipView.scroll();
                console.log(123);
                this.friendshipView.collection.url = '/follows';
                this.friendshipView.collection.reset();
                this.friendshipView.collection.data.uid = $(window.app.home.el).attr('data-uid');
                this.friendshipView.collection.data.page = 0;
                this.friendshipView.collection.loading(this.showEmptyView);
            },
            history:function() {
                history.go(-1);
                $('#headerView').removeClass('hidder-animation');
                $('.header').removeClass('hidder-animation');
                //window.location.reload();
            },
            // 进行中页面删除下载记录
            deleteCard: function(e) {
                var el = $(e.currentTarget);
                var id = el.attr("data-id");
                $.post('inprogresses/del', {
                    id: id
                }, function(data) {
                    $(e.currentTarget).parent().parent().remove();
                });
            },
            loadAsks: function(e) {
                $("#homeListView").empty();
                $(window).unbind('scroll'); 
                this.askView.scroll();
                this.askView.collection.reset();
                this.askView.collection.data.uid = $(window.app.home.el).attr('data-uid');
                this.askView.collection.data.page = 0;
                //this.askView.collection.loading();
                this.askView.collection.loading(this.showEmptyView);
            },
            loadReplies: function (e){
                $("#homeListView").empty();
                $(window).unbind('scroll'); 
                this.replyView.scroll();
                this.replyView.collection.reset();
                this.replyView.collection.data.uid = $(window.app.home.el).attr('data-uid');
                this.replyView.collection.data.page = 0;
                //this.replyView.collection.loading();
                this.replyView.collection.loading(this.showEmptyView);
            },
            loadInprogress: function(e){
                $("#homeListView").empty();
                $(window).unbind('scroll'); 
                this.inprogressView.scroll();
                this.inprogressView.collection.reset();
                this.inprogressView.collection.data.uid = $(window.app.home.el).attr('data-uid');
                this.inprogressView.collection.data.page = 0;
                this.inprogressView.collection.loading(this.showEmptyView);
            },
            showEmptyView: function(data) {
                if(data.data.page == 1 && data.length == 0) {
                    append($("#homeListView"), ".emptyContentView");
                }
            },
            attention: function(event) {
                var el = $(event.currentTarget);
                var id = el.attr("data-id");
                $.post('user/follow', {
                    uid: id
                }, function(data) {
                    if(data.ret == 1) 
                        $(event.currentTarget).addClass('hide').siblings().removeClass('hide');
                });
            },
            cancelAttention: function(event) {
                var el = $(event.currentTarget);
                var id = el.attr("data-id");
                $.post('user/follow', {
                    uid: id
                }, function(data) {
                    if(data.ret == 1) 
                        $(event.currentTarget).addClass('hide').siblings().removeClass('hide');
                });
            },
            avatarPopup: function(e) {
                var askSrc = $(e.currentTarget).attr('src');
                $('#ask_picture').attr('src',askSrc).css('height', '100%');
                $('.picture-product').addClass('hide');
                $('.picture-original').css('width','100%');
            }
        });

        return homeView;
    });
