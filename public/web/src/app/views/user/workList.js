define([
		'app/views/user/work/work',
		], function (work) {
    "use strict";

    return window.app.list.extend({
        tagName: 'div',
        className: 'user-width',
        childView: work,
        childEvents: {
        },
        onShow:function() {
        }
    });
});
