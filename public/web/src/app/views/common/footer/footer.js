define(['tpl!app/views/common/footer/footer.html'],
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
