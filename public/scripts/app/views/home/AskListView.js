define([
        'app/views/home/HomeView', 
        'imagesLoaded',
        'app/models/Base', 
        'app/collections/Asks', 
        'tpl!app/templates/home/AskItemView.html'
       ],
    function (View, imagesLoaded, ModelBase, Asks, askItemTemplate) {
        "use strict";

        var asks = new Asks;

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            data: 0,
            collection: asks,
            template: askItemTemplate,
        
            onRender: function() {
                $('.download').unbind('click').bind('click',this.downloadClick);
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
