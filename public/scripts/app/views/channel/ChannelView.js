 define([
        'masonry', 
        'imagesLoaded',
        'app/views/Base',
        'app/models/Base',
        'app/models/Like',  
        'app/collections/Replies', 
        'tpl!app/templates/channel/ChannelView.html'
       ],
    function (masonry, imagesLoaded, View, ModelBase, Like, Replies, template) {

        "use strict";
        return View.extend({
            collection: Replies,
            tagName: '',
            className: '',
            template: template,
            events: {
                // "click .like_toggle" : 'likeToggle',
            },
            // construct: function () {
            //     var self = this;
            //     self.listenTo(self.collection, 'change', self.renderMasonry);
            //     self.scroll();
            //     self.collection.loading();
            // },
            // render: function() {
            //     this.renderMasonry();
            // }
        });
    });
