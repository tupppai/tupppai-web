define(['grid', 'tpl!app/views/activity/work/work.html'],
    function (grid, template) {
        "use strict";

        return window.app.view.extend({
            tagName: 'div',
            className: 'grid-item',
            template: template,

            onShow: function() {

            },

        });
    });
