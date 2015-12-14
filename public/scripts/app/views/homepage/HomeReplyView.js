define([
        'masonry',
        'imagesLoaded',
        'app/collections/Replies',
        'app/views/Base', 
        'tpl!app/templates/homepage/HomeReplyView.html'
       ],
    function (masonry, imagesLoaded, Replies, View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: 'grid clearfix ',
            template: template,
            collections: Replies,

            construct: function () {
                this.listenTo(this.collection, 'change', this.renderMasonry);
            },
            render: function() {
                this.renderMasonry();
            }
        });
    });
