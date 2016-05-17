define(['tpl!app/views/hot/reply/reply.html', 'imageLazyLoad'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'loading',
            template: template,
            onShow: function() {
            },

        });
    });
