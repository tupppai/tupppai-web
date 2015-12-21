 define([ 
        'app/views/Base',
        'masonry', 
        'imagesLoaded',
        'app/collections/Replies',
        'tpl!app/templates/channel/ChannelWorksView.html'
       ],
    function (View, masonry, imagesLoaded, Replies, template) {

        "use strict";
        return View.extend({
            collection: Replies,
            tagName: 'div',
            className: 'channel-reply-container grid',
            template: template,
            construct: function () {
                this.listenTo(this.collection, 'change', this.renderMasonry);
            },
            render: function() {
                this.renderMasonry();
            }
        });
    });
