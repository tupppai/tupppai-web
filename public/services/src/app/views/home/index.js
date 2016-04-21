define(['tpl!app/views/home/index/index.html'],
    function (template) {
        "use strict";
        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            initialize: function() {
            	//this.listenTo(this.model, 'change', this.render);
            }
        });
    });
