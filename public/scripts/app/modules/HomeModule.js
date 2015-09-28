define(['marionette', 'app/models/User', 
        'tpl!app/templates/HomeView.html',
        'app/views/home/AskListView', 
        'app/views/home/ReplyListView', 
        'app/views/home/InprogressListView', 
    ], function (Marionette, User, template, askListView, replyListView, inprogressListView) {
        "use strict";

        var homeView = Marionette.ItemView.extend({
            model: User,
            tagName: 'div',
            className: '',
            template : template,
            initialize: function () {
                console.log('homemodule');
                this.listenTo(this.model, "change", this.render);
            },
            events: {
                "click #load_ask" : "loadAsks",
                "click #load_reply" : "loadReplies",
                "click #load_inprogress" : "loadInprogress",
                "click .cancel-attention" : "click",
            },
            loadAsks: function(e) {
                var view = new askListView();
                this.showNav(e); 
            },
            loadReplies: function (e){
                var view = new replyListView(); 
                this.showNav(e); 
            },
            loadInprogress: function(e){
                var view = new inprogressListView();
                this.showNav(e); 
            },
            showNav: function(event) {
                $('.border-nav').addClass('hide');
                $(event.currentTarget).find('.border-nav').removeClass('hide');
            },
            click: function() {
                alert('123');
            }
        });

        return homeView;
    });
