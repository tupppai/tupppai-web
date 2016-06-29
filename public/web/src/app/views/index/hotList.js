define([
		'app/views/index/hotHeader/hotHeader',
		'app/views/index/hot/hot',
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
