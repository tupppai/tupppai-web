define(['marionette',  
        'imagesLoaded', 
        'app/models/User',
        'tpl!app/templates/HomeView.html',
        'app/views/home/AskListView', 
        'app/views/home/ReplyListView', 
        'app/views/home/InprogressListView', 
    ], function (Marionette, imagesLoaded, User, template, askListView, replyListView, inprogressListView) {
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
            },
            onRender: function(type) {
                var imgLoad = imagesLoaded('.is-loading', function() { 
                    console.log('all image loaded');
                });
                imgLoad.on('progress', function ( imgLoad, image ) {
                    console.log(image);
                    if(image.isLoaded)  
                        image.img.parentNode.className =  '';
                });
                alert(type);
                $('#load_ask').trigger('click');

            },
            loadAsks: function(e) {
                var view = new askListView();
                this.showNav(e); 
                location.href  = '#/home/ask/' + this.model.data.uid;
            },
            loadReplies: function (e){
                var view = new replyListView(); 
                this.showNav(e); 
                location.href  = '#/home/reply/' + this.model.data.uid;
            },
            loadInprogress: function(e){
                var view = new inprogressListView();
                location.href  = '#/home/inprogress/' + this.model.data.uid;
                this.showNav(e); 
            },
            showNav: function(event) {
                $('#' + event.currentTarget.id).addClass('designate-nav').siblings().removeClass('designate-nav');
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
