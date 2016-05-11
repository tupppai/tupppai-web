define([
		'app/views/personal/work/workView', 
		'app/views/personal/processing/processingView',
		'app/views/personal/reply/replyView',
		],
    function (workView, processingView, replyView) {
        "use strict";
        
        return window.app.list.extend({
            tagName: 'div',
            className: 'grid personal-grid',
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
            },
            onShow: function() {
                title('个人中心');
                $(".menuPs").removeClass("hide");
            }
        });
    });
