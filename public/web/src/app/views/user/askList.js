define([
		'app/views/user/ask/ask',
        'app/views/user/work/work',
        'app/views/user/progress/progress',
		], function (ask, work, progress) {
    "use strict";

    return window.app.list.extend({
        tagName: 'div',
        className: 'user-width',
        getChildView: function(category) {
            var type = category.collection.type;
            switch(type) {
                case 'ask':
                    return ask;
                    break;
                case 'work':
                    return work
                    break;
                case 'progress':
                    return progress
                    break;
                default:
                    return ask
                    break;
            }
        },
        childEvents: {
        },
        onShow:function() {
        }
    });
});
