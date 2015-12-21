define([
        'masonry', 
        'imagesLoaded',
        'app/views/Base',
        'app/models/Base',
        'app/models/Like',  
        'app/collections/Replies', 
        'tpl!app/templates/hot/HotFlowsView.html'
       ],
    function (masonry, imagesLoaded, View, ModelBase, Like, Replies, template) {

        "use strict";
        return View.extend({
            collection: Replies,
            tagName: 'div',
            className: 'hot-container grid',
            template: template,
            events: {
                "click .like_toggle"            : 'likeToggle',
                "click .photo-item-reply-work"  : "photoShift",
                //鼠标hover之后切换作品和原图
                "mouseover .reply-image"        : "toggle_image"
            },
            construct: function () {
                var self = this;
                self.listenTo(self.collection, 'change', self.renderMasonry);

                self.scroll();
                self.collection.loading();
            },
            render: function() {
                this.renderMasonry();
            },
            toggle_image: function() {

            }
        });
    });
