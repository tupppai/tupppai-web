define(['tpl!app/views/user/ask/ask.html'],
    function (template) {
        "use strict";

        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,

            onShow: function() {
                this.$('.imageLoad').imageLoad({scrop: true});
            },

        });
    });
