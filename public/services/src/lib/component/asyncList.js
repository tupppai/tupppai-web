/**
 * 动态列表
 */
define(['zepto'], function ($) {
   "use strict";

    var Asynclist = function (that, callback) {
        this.obj = that;
        this.is_loading = false;
        this.callback = callback;
    }

    Asynclist.prototype = {
        init: function () {
            var _this = this;

            $(window).scroll(function () {
                var scrollHeight = $(document).height() - $(window).height();
                var scrollt = document.documentElement.scrollTop + document.body.scrollTop;

                if (scrollt == scrollHeight && !_this.is_loading) {
                    _this.is_loading = true;

                    $(_this.obj).append('<div class="asynclist">加载中...</div>');
                    _this.callback();
                };
            });
        },
        success: function () {
            var _this = this;

            $('.asynclist').remove();
            _this.is_loading = false;
            _this.init();
        },
        finish: function () {
            var _this = this;

            $('.asynclist').remove();
            _this.is_loading = true;
            $(_this.obj).append('<div class="asynclist-finish">没有更多了</div>')
        }
    };

    function Plugin(callback) {
        var asynclist = new Asynclist(this, callback);

        asynclist.init();
        return asynclist;
    }

    $.fn.asynclist = Plugin;
});