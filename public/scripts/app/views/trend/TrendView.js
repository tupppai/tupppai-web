define([
        'app/views/Base', 
        'tpl!app/templates/trend/TrendView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .like_toggle" : 'likeToggleLarge',
            },
            construct: function () {
                var self = this;
                self.listenTo(self.collection, 'change', self.render);
                self.scroll();
                self.collection.loading(account.showEmptyView);
            },
            onRender: function() {
                $('.download').unbind('click').bind('click',this.download);
                this.loadImage(); 
            }

        });
    });
