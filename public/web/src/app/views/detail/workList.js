define([
		'app/views/detail/work/work',
		'app/views/detail/workHeader/workHeader',
		], function (work,workHeader) {
    "use strict";

    return window.app.list.extend({
        tagName: 'div',
        className: 'container detail-work',
        headerView: workHeader,
        childView: work,
        childEvents: {
        },
        onRender: function() {

        },

    });
});
