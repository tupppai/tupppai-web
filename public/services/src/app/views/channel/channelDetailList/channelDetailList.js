define([
		'app/views/hot/hotWorks/hotWorks',
		'app/views/channel/channelSeek/channelSeek',
        'lib/component/asyncList'
		],
    function (hotWorks, channelSeek, asynclist) {
        "use strict";

        return window.app.list.extend({
            tagName: 'div',
            className: 'hot-pageSection clearfix grid',
            getChildView: function(item) {
                switch(item.collection.type) {
                    case 'works':
                        return hotWorks;
                    case 'seek':
                    default:
                        return channelSeek;
                }
            },
            onShow: function() {
                $(".menuPs").addClass("hide");
                this.$el.asynclist({
                    root: this,
                    collection: this.collection,
                    renderMasonry: true,
                    itemSelector: 'loading',
                    callback: function(item) {
                       $('.imageLoad2').imageLoad({scrop: true});
                    }
                });
            }
        });
    });
