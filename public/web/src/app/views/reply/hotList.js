define([
		'app/views/reply/hotHeader/hotHeader',
		'app/views/reply/hot/hot',
		], function (hotHeader,hot) {
    "use strict";

    return window.app.list.extend({
        tagName: 'div',
        className: 'container',
        headerView: hotHeader,
        childView: hot,
        childEvents: {
        },
        onShow:function() {
        }
    });
});
