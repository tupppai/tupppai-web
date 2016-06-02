define([
		'app/views/personal/honor/fansView',
        'lib/component/asyncList',
        'app/views/personal/empty/emptyView'
		],
    function (fansView, asyncList, emptyView) {
        "use strict";

        return window.app.list.extend({
            tagName: 'div',
            className: '',
            childView: fansView,
            emptyView: emptyView,
            onShow: function() {
                var honorType = $("body").attr("honor-type");
                $(".empty-buttom").addClass("hide");
                $("body").attr("tapTapy", "")
                if (honorType == "fans") {
                    $(".follow").addClass("following")
                    title('我的粉丝');
                    $(".empty-p").text("暂时没有粉丝")
                } else {
                    title('我的关注');
                    $(".empty-p").text("暂时没有关注")
                }
                this.$el.asynclist({
                    root: this,
                    collection: this.collection,
                    renderMasonry: false
                });
            }
        });
    });
