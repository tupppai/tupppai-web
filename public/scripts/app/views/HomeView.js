define([
        'app/views/Base', 
        'tpl!app/templates/HomeView.html',
        'app/views/home/AskListView', 
        'app/views/home/ReplyListView', 
        'app/views/home/InprogressListView', 
    ],
    function (View, homeTemplate, askListView,replyListView,inporgressListView) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: homeTemplate,
            events: {
                "click #load_ask" : "loadAsks",
                "click #load_reply" : "loadReplies",
                "click #load_inprogress" : "loadInprogress",
            },
            construct: function () {
                var self = this;
                window.app.content.close();
                window.app.content.show(self);


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
                var view = new inporgressListView();
                this.showNav(e); 
            },
            showNav: function(event) {
                $('.border-nav').addClass('hide');
                $(event.currentTarget).find('.border-nav').removeClass('hide');
            }
        });
    });
