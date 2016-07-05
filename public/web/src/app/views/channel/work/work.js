define(['tpl!app/views/channel/work/work.html'],
    function (template) {
        "use strict";

        return window.app.view.extend({
            tagName: 'div',
            className: 'clearfix',
            template: template,

            onShow: function() {
            },

        });
    });
