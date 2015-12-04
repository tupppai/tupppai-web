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
                    self.collection.loading();
                },
            });
        });
