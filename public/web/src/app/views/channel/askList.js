define([
		'app/views/channel/ask/ask',
		], function (ask) {
    "use strict";

    return window.app.list.extend({
        tagName: 'div',
        className: 'channel-ask',
        childView: ask,
        childEvents: {
        },
        onShow:function() {
        }
    });
});
