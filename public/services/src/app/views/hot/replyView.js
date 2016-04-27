define(['tpl!app/views/hot/reply.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'hot-pageSection clearfix',
            template: template,
            initialize: function() {
                this.listenTo(this.collection, 'change', this.render);
            },
        });
    });


