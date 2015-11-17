define([
        'masonry', 
        'imagesLoaded',
        'app/views/Base',
        'app/models/Base',
        'app/models/Like',  
        'app/collections/Replies', 
        'tpl!app/templates/reply/ReplyFlowsView.html'
       ],
    function (masonry, imagesLoaded, View, ModelBase, Like, Replies, template) {

        "use strict";
        return View.extend({
            collection: Replies,
            tagName: 'div',
            className: 'reply-container grid',
            template: template,
            events: {
                "click .like_toggle" : 'likeToggle',
                "click .pressed" : 'pressed',
            },
            pressed: function(e) {

                $(e.currentTarget).addClass('nav-pressed').siblings().removeClass('nav-pressed');
            },
            construct: function () {
                var self = this;
                self.listenTo(self.collection, 'change', self.renderMasonry);

                self.scroll();
                self.collection.loading();
            },
            render: function() {
                this.renderMasonry();
            }
        });
    });
