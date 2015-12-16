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
                this.listenTo(this.collection, 'change', this.render);
                this.scroll();
                this.collection.loading(this.showEmptyView);
            },
            onRender: function() {
                $('.download').unbind('click').bind('click',this.download);
                this.loadImage(); 
            }

        });
    });
