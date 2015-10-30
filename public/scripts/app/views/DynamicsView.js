define(['app/views/Base', 'tpl!app/templates/DynamicsView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function () {
                var self = this;
                self.listenTo(self.collection, 'change', self.render);

                //self.scroll();
                self.collection.loadMore();
            },
            render: function() {
                var el = $(this.el);
                var template = this.template;
                this.collection.each(function(model){
                    append(el, template(model.toJSON()));
                });

                this.onRender(); 
            }
        });
    });
