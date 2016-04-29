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
                    case 'replies':
                        return replyView;
                    case 'inprogresses':
                        return processingView;
                    case 'ask':
                    default:
                        return workView;
                }
            }
        });
    });
