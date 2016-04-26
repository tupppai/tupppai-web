define([
		'app/views/personal/work/workView', 
		'app/views/personal/processing/processingView',
		'app/views/personal/reply/replyView',
		],
    function (workView, processingView, replyView) {
        "use strict";
        
        return window.app.list.extend({
            getChildView: function(item) {
                switch(item.collection.type) {
                    case 'reply':
                        return replyView;
                    case 'processing':
                        return processingView;
                    case 'work':
                    default:
                        return workView;
                }
            }
        });
    });
