define([
		'app/views/user/ask/ask',
		], function (ask) {
    "use strict";

    return window.app.list.extend({
        tagName: 'div',
        className: 'user-width',
        childView: ask,
        childEvents: {
        },
        onShow:function() {
        }
    });
});
