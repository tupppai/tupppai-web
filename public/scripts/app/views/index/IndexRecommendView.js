define(['app/views/Base', 'app/collections/Banners', 'tpl!app/templates/index/IndexRecommendView.html'],
    function (View, Banners,  template) {
        "use strict";

        var indexRecommendView = '#indexRecommendView';
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            collection: Banners,

            construct: function() { 
                var self = this;
                this.listenTo(this.collection, 'change', this.render);
                self.collection.loadMore();
            },
            render: function() {
                var template = this.template;

                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    append(indexRecommendView, html);
                });
                this.onRender();
            } 
        });
    });
