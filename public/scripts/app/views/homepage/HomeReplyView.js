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
            className: 'grid clearfix',
            template: template,
            collections: Replies,

            construct: function () {
                var uid = $(".menu-nav-reply").attr("data-id");
                var self = this;
                self.listenTo(self.collection, 'change', self.renderMasonry);
                self.scroll();
                self.collection.reset();
                self.collection.data.uid = uid;
                self.collection.data.page = 0;
                self.collection.loading(account.showEmptyView);
            },
            render: function() {
                this.renderMasonry();
            }
        });
    });
