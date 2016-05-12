define(['tpl!app/views/hot/reply/reply.html','waterfall'],
    function (template, waterfall) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'grid-item',
            template: template,
            onShow: function() {
            },
        });
    });
