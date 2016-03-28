define(['app/views/base', 'tpl!app/views/home/subview2.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            initialize: function() {
            	this.listenTo(this.collection, 'change', this.render);
            }
        });
    });
