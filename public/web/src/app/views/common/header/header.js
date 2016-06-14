define(['tpl!app/views/common/header/header.html'],
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
