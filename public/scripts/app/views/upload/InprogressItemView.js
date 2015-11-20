define([
        'underscore',
        'app/views/Base',
        'tpl!app/templates/InprogressItemView.html'
       ],
    function (_, View, template) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function() { 
                var self = this;
                self.listenTo(self.collection, 'change', self.render);
                self.collection.loading();
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
