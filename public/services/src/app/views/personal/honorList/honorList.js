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
                var targetUid = $("body").attr("targetUid");;
                var uid = window.app.user.get('uid'); //当前登入id
                var honorType = $("body").attr("honor-type");

                $(".empty-buttom").addClass("hide");
                $("body").attr("tapTapy", "");
                if(targetUid == uid) {
                    if (honorType == "fans") {
                        $(".follow").addClass("following")
                        title('我的粉丝');
                        $(".empty-p").text("暂时没有粉丝")
                    } else {
                        title('我的关注');
                        $(".empty-p").text("暂时没有关注")
                    }
                } else {
                    if (honorType == "fans") {
                        $(".follow").addClass("following")
                        title('ta的粉丝');
                        $(".empty-p").text("暂时没有粉丝")
                    } else {
                        title('ta的关注');
                        $(".empty-p").text("暂时没有关注")
                    }
                    $(".follow").addClass("hide");
                }
                this.$el.asynclist({
                    root: this,
                    collection: this.collection,
                    renderMasonry: false
                });
            }
        });
    });
