define(['tpl!app/views/channel/nav/nav.html'],
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
