define(['tpl!app/views/index/star/star.html'],
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
