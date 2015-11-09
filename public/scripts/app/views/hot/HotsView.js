define([
        'masonry', 
        'imagesLoaded',
        'app/views/Base',
        'app/models/Base',
        'app/models/Like',  
        'app/collections/Replies', 
        'tpl!app/templates/hot/HotsView.html'
       ],
    function (masonry, imagesLoaded, View, ModelBase, Like, Replies, template) {

        "use strict";
        return View.extend({
            collection: Replies,
            tagName: 'div',
            className: 'hot-container grid',
            template: template,
            events: {
                "click .like_toggle" : 'likeToggle',
                "click .photo-item-reply" : "photoShift"
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
