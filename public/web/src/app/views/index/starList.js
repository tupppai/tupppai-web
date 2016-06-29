define([
		'app/views/index/starHeader/starHeader',
		'app/views/index/star/star',
		], function (starHeader,star) {
    "use strict";

    return window.app.list.extend({
        tagName: 'div',
        className: 'container',
        headerView: starHeader,
        childView: star,
        childEvents: {
        },
        onShow:function() {
        }
    });
});
