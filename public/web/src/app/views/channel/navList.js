define([
		'app/views/channel/nav/nav',
		], function (nav) {
    "use strict";

    return window.app.list.extend({
        tagName: 'div',
        className: 'container  channel-list',
        childView: nav,
        childEvents: {
        },
        onShow:function() {
        }
    });
});
