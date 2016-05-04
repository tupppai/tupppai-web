/**
 * 瀑布流
 */
define(['zepto', 'lib/masonry/masonry'], function ($, masonry) {
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

            $(_this.obj).masonry({
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