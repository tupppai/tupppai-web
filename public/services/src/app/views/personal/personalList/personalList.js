define([
		'app/views/personal/original/originalView',
		'app/views/personal/processing/processingView',
        'app/views/personal/works/worksView',
		'app/views/personal/empty/emptyView',
        'lib/component/asyncList'
		],
    function (originalView, processingView, worksView, emptyView, asynclist) {
        "use strict";

        return window.app.list.extend({
            tagName: 'div',
            className: 'grid personal-grid',
            emptyView: emptyView,
            getChildView: function(item) {
                switch(item.collection.type) {
                    case 'replies':
                        return worksView;
                    case 'inprogresses':
                        return processingView;
                    case 'ask':
                    default:
                        return originalView;
                }
            },
            onShow: function() {
                title('个人中心');
                // $(".menuPs").removeClass("hide");
                var type = $("body").attr("tapTapy");
                this.$el.asynclist({
                    root: this,
                    collection: this.collection,
                    type: type,
                    renderMasonry: true,
                    itemSelector: 'loading'
                });
            }
        });
    });
