define(['tpl!app/views/detail/ask/ask.html'],
    function (template) {
        "use strict";

        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,

            onShow: function() {
            },

        });
    });
