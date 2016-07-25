define(['tpl!app/views/detail/work/work.html'],
    function (template) {
        "use strict";

        return window.app.view.extend({
            tagName: 'div',
            className: 'clearfix detail-work',
            template: template,

            onShow: function() {
                this.$('.imageLoad').imageLoad({scrop: true});
            },

        });
    });
