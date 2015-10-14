define(['marionette',  
        'imagesLoaded', 
        'app/models/User',
        'tpl!app/templates/home/HomeView.html',
        'app/views/home/AskListView', 
        'app/views/home/ReplyListView', 
        'app/views/home/InprogressListView', 
        'app/views/UploadingView', 
    ], function (Marionette, imagesLoaded, User, template, askListView, replyListView, inprogressListView, UploadingView) {
        "use strict";

        var homeView = Marionette.ItemView.extend({
            model: User,
            tagName: 'div',
            className: '',
            template : template,
            initialize: function () {
                console.log('homemodule');
                this.listenTo(this.model, "change", this.render);
                $('.title-bar').addClass('hidder-animation');
            },
            events: {
                "click #load_ask" : "loadAsks",
                "click #load_reply" : "loadReplies",
                "click #load_inprogress" : "loadInprogress",
                "click #attention" : "attention",
                "click #cancel_attention" : "cancelAttention",
                "click #delete_card" : "deleteCard",
                "click .photo-item-reply" : "photoShift",
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
                $(e.currentTarget).parent().parent().addClass('hide');
            },
            onRender: function() {
                var imgLoad = imagesLoaded('.is-loading', function() { 
                    console.log('all image loaded');
                });
                imgLoad.on('progress', function ( imgLoad, image ) {
                    console.log(image);
                    if(image.isLoaded)  
                        image.img.parentNode.className =  '';
                });
                // 求P图片切换
                $('.photo-item-reply').click(function(e){
                     var AskSmallUrl = $(e.currentTarget).find('img').attr("src");
                     var AskLargerUrl = $(e.currentTarget).prev().find('img').attr("src");
                     $(e.currentTarget).prev().find('img').attr("src",AskSmallUrl);
                     $(e.currentTarget).find('img').attr("src",AskLargerUrl);
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
