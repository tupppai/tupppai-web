define([
        'app/views/Base', 
        'tpl!app/templates/HomeView.html',
        'app/views/home/AskListView', 
    ],
    function (View, homeTemplate, askListView) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: homeTemplate,
            events: {

            },
            construct: function () {
                var self = this;
                window.app.content.close();
                window.app.content.show(self);

                this.loadAsk();

            },
            loadAsks: function() {
                var view = new askListView();
            },
            loadReplies: function (){
                // var view = new askListView();

            },
            loadInprogress: function(){
                // var view = new askListView();

            }
        });
    });
