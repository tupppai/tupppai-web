define([
        'app/views/Base', 
        'app/collections/Threads', 
        'tpl!app/templates/search/ThreadItemView.html'
       ],
        function (View, Threads, template) {
            "use strict";
            return View.extend({
                tagName: 'div',
                className: '',
                template: template,
                collection: Threads,

                construct: function() {
                    var self = this;
                    this.listenTo(this.collection, 'change', this.render);
                    self.collection.loadMore();
                },
                render: function() {
                   var template = this.template;
                   var el = $(this.el);
                    this.collection.each(function(model,a,b){
                        append(el, template(model.toJSON()));
                    });
                    this.onRender();
                }
            });
        });
