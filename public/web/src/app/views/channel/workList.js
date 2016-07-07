define([
		'app/views/channel/work/work',
		], function (work) {
    "use strict";

    return window.app.list.extend({
        tagName: 'div',
        className: 'channel-work-width',
        childView: work,
        childEvents: {
        },
        onShow:function() {
        }
    });
});
