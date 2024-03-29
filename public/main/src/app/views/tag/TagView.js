define([
        'app/views/Base', 
        'tpl!app/templates/tag/TagView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function() { 
                var self = this;
                this.listenTo(this.collection, 'change', this.render);
                self.collection.loading();
            },
        });
    });
