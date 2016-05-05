/**
 * 瀑布流
 */
define(['zepto', 'lib/masonry/masonry', 'lib/imagesloaded/imagesloaded'], function ($, Masonry, imagesLoaded) {
    "use strict";

    var defaults = {
        root: '.grid',
        itemSelector: '.grid-item',
        columnWidth: 0
    };

    var waterFall = function (that, options) {
        this.obj = that;
        this.option = $.extend({}, defaults, options);
        this._init();
    }

    waterFall.prototype = {

        _init: function () {
            var _this = this;
            
            // 图片加载完再进行瀑布流渲染
            imagesLoaded(_this.option.root, function() {
                var msnry = new Masonry(_this.option.root, {
                    itemSelector: _this.option.itemSelector,
                    columnWidth: 0
                });
            });
        }
    };

    function Plugin(that, options) {
        var waterfall = new waterFall(that, options);

        return waterfall;
    }
    $.fn.waterfall = Plugin;
});
