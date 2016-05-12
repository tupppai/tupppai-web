/**
 * 动态列表
 */
define(['zepto'], function ($) {
   "use strict";

    var Asynclist = function (that, options) {
        this.$el = that;
        this.options = {}
        this.options.page = 1;
        this.options.size = 10;

        this.options = _.clone(options);
        this.loading = false;

        if(this.options.view.collection) {
            this.collection = this.options.view.collection;
        }
    }

    Asynclist.prototype = {
        init: function () {
            var self = this;
            
            //this.$el.append('<div class="asynclist">加载中...</div>');
            $(window).scroll(function () {
                if(!self.collection) {
                    return false;
                }

                var scrollHeight = $(document).height() - $(window).height();
                var scrollTop = document.documentElement.scrollTop + document.body.scrollTop;

                if (scrollTop == scrollHeight && !self.loading) {
                    self.$el.append('<div class="asynclist">加载中...</div>');
                    self.loading = true;
                    self.collection.fetch({
                        success: function(data) {
                            self.loading = false;
                        }
                    });
                }
            });
        }
    };

    function Plugin(callback) {
        var asynclist = new Asynclist(this, callback);

        asynclist.init();
        return asynclist;
    }

    $.fn.asynclist = Plugin;
});
