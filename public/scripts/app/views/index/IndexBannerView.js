define([
        'app/views/Base', 
        'app/collections/Banners', 
        'tpl!app/templates/index/IndexBannerView.html'
        ],
    function (View, Banners, template) {
        "use strict";

        var indexRecommendView = '#indexRecommendView '+"div";
        
        return View.extend({
            tagName: 'div',
            className: 'swipe-wrap',
            template: template,
            collection: Banners,
            construct: function() { 
                var self = this;
                this.listenTo(this.collection, 'change', this.render);
                self.collection.loading();
            },
            render: function() {

                var template = this.template;

                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    append(indexRecommendView, html);
                });
                var widthScreen = $(window).width();
                if( widthScreen < 700 ) {
                    //append 里面有settimeout
                    setTimeout(function() {
                       Swipe(document.getElementById('indexBannerView'));    
                    }, 1200);
                }

                this.onRender();
            } 
        });
    });
