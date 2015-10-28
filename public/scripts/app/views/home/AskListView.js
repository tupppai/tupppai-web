define([
        'app/views/home/HomeView', 
        'imagesLoaded',
        'app/models/Base', 
        'app/collections/Replies', 
        'tpl!app/templates/home/AskItemView.html'
       ],
    function (View, imagesLoaded, ModelBase, Replies, askItemTemplate) {
        "use strict";

        var Replies = new Replies;

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            data: 0,
            collection: Replies,
            template: askItemTemplate,
            onRender: function() {
                $('#load_ask').addClass('designate-nav').siblings().removeClass('designate-nav');

                this.loadImage();
            },
            downloadClick: function(e) {
                var data = $(e.currentTarget).attr("data");
                var id   = $(e.currentTarget).attr("data-id");
                var model = new ModelBase;
                model.url = '/record?type='+data+'&target='+id;
                model.fetch({
                    success: function(data) {
                        var urls = data.get('url');
                        _.each(urls, function(url) {
                            location.href = '/download?url='+url;
                        });
                    }
                });
            },
        });
    });
