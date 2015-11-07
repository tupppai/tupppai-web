define([
        'app/views/Base', 
        'app/collections/Replies', 
        'tpl!app/templates/search/ContentItemView.html'
       ],
        function (View, Replies, template) {
            "use strict";
           var replies = new Replies;
            return View.extend({
                tagName: 'div',
                className: '',
                template: template,
                collection: replies,

                construct: function() {
                    var self = this;
                    this.listenTo(this.collection, 'change', this.render);
                    self.collection.loadMore();
                },
                render: function() {
                   var template = this.template;
                   var el = $(this.el);
                    this.collection.each(function(model,a,b){
                        var html = template(model.toJSON());
                        el.append(html);
                    });
                    this.onRender();
                }
            });
        });
