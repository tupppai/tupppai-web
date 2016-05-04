/**
 * 瀑布流
 */
define(['zepto', 'lib/masonry/masonry'], function ($, Masonry) {
    "use strict";

    var defaults = {
        itemSelector: '.grid-item',
        columnWidth: 200
    };

    var waterFall = function (that, options) {
        this.obj = that;
        this.option = $.extend({}, defaults, options);
        this._init();
    }

    waterFall.prototype = {

        _init: function () {
            var _this = this;

            var msnry = new Masonry('.grid', {
               itemSelector: _this.option.itemSelector,
               columnWidth: _this.option.columnWidth
            });
        }
    };

    function Plugin(that, options) {
        var waterfall = new waterFall(that, options);

        return waterfall;
    }
    $.fn.waterfall = Plugin;
});
