define(['app/views/Base', 'app/collections/Asks', 'tpl!app/templates/index/IndexItemView.html'],
    function (View, Asks,  template) {
        "use strict";

        var indexItemView = '#indexItemView';
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            collection: Asks,
            
            construct: function() { 
                var self = this;
                this.listenTo(this.collection, 'change', this.render);
                self.collection.loadMore();
            },
            render: function() {
                var template = this.template;

                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    append(indexItemView, html);
                });
                this.onRender();
            }   
        });
    });
