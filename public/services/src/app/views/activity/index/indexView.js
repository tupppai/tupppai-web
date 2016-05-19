define(['tpl!app/views/activity/index/index.html'],
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
