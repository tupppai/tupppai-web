define([
        'masonry', 
        'app/views/Base',
        'app/collections/Asks', 
        'tpl!app/templates/ask/AsksItemView.html'
       ],
    function (masonry, View, Asks, template) {
        "use strict";

        
        return View.extend({
            collection: Asks,
            tagName: 'div',
            className: 'ask-container  grid',
            template: template,
            construct: function () {
                var self = this;
                self.listenTo(self.collection, 'change', self.render);

                self.scroll();
                self.collection.loadMore();
            },
            render: function() {
                var template = this.template;
                var el = this.el;
         
                this.collection.each(function(model){
                    append(el, template(model.toJSON()));
                });

                setTimeout(function(){
                var msnry = new masonry( '.grid', {});    
            }, 1000);
                
                this.onRender(); 
            },
            onRender: function() {
            }
        });
    });
