define(['app/views/base', 'tpl!app/views/home/subview1.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            initialize: function() {
            	this.listenTo(this.model, 'change', this.render);
            }
        });
    });
