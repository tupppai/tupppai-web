define([
        'masonry',
        'imagesLoaded',
        'app/views/Base', 
        'tpl!app/templates/homepage/HomeReplyView.html'
       ],
    function (masonry, imagesLoaded,  View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: 'grid clearfix ',
            template: template,
            construct: function () {
                this.listenTo(this.collection, 'change', this.renderMasonry);
            },
            render: function() {
                this.renderMasonry();
            }
        });
    });
