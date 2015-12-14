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
                this.listenTo(this.collection, 'change', this.renderMasonry);
                this.scroll();
                this.collection.reset();
                this.collection.data.uid = uid;
                this.collection.data.page = 0;
                this.collection.loading(this.showEmptyView);
            },
            showEmptyView: function(data) {
                if(data.data.page == 1 && data.length == 0) {
                    append($("#contentView"), ".emptyContentView");
                } 
            },
            render: function() {
                this.renderMasonry();
            }
        });
    });
