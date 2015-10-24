define(['marionette',  
        'app/models/User',
        'tpl!app/templates/home/HomeView.html',
        'app/views/Base',
        'app/views/home/AskListView', 
        'app/views/home/ReplyListView', 
        'app/views/home/InprogressListView', 
        'app/views/UploadingView', 
    ], function (Marionette, User, template, View, askListView, replyListView, inprogressListView, UploadingView) {
        "use strict";

        var homeView = View.extend({
            model: User,
            tagName: 'div',
            className: '',
            template : template,
            initialize: function () {
                console.log('homemodule');
                this.listenTo(this.model, "change", this.render);
                $('.header-back').addClass('hidder-animation');
                $('.header').addClass('hidder-animation');
            },
            events: {
                "click #load_ask" : "loadAsks",
                "click #load_reply" : "loadReplies",
                "click #load_inprogress" : "loadInprogress",
                "click #attention" : "attention",
                "click #cancel_attention" : "cancelAttention",
                "click .photo-item-reply" : "photoShift",
                "click .return-home-page" : "history",
                "click .delete-card" : "deleteCard"
               
            },
            history:function() {
                window.history.go(-1);
                $('#headerView').removeClass('hidder-animation');
                $('.header').removeClass('hidder-animation');
                window.location.reload();
            },
            // 求助图片切换
            photoShift: function(e) {
                     var AskSmallUrl = $(e.currentTarget).find('img').attr("src");
                     var AskLargerUrl = $(e.currentTarget).prev().find('img').attr("src");
                     $(e.currentTarget).prev().find('img').attr("src",AskSmallUrl);
                     $(e.currentTarget).find('img').attr("src",AskLargerUrl);              
            },
            // 进行中页面删除下载记录
            deleteCard:function(e) {
                var el = $(e.currentTarget);
                var id = el.attr("data-id");
                $.post('inprogresses/del', {
                    id: id
                }, function(data) {
                    $(e.currentTarget).parent().parent().remove();
                });
            },
            loadAsks: function(e) {
                var view = new askListView();
                $(document).on('click','.download',view.downloadClick);

            },
            loadReplies: function (e){
                var view = new replyListView();
            },
            loadInprogress: function(e){
                var view = new inprogressListView();
                var view = new UploadingView();
                window.app.modal.show(view);
            },
            attention: function(event) {
                $(event.currentTarget).addClass('hide').next().removeClass('hide');
            },
            cancelAttention: function(event) {
                $(event.currentTarget).addClass('hide').prev().removeClass('hide');
            },
        });

        return homeView;
    });
