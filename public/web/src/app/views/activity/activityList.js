define([
		'app/views/activity/work/work',
		], function (work) {
    "use strict";

    return window.app.list.extend({
        tagName: 'div',
        className: 'container grid clearfix',
        childView: work,
        childEvents: {
        },
        onShow:function() {
        }
    });
});
