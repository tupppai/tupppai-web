/**
 * 动态列表
 */
define(['zepto', 'common', 'lib/imagesloaded/imagesloaded', 'lib/masonry/masonry', 'lib/infinite/jquery-ias'], function ($, common, imagesLoaded, Masonry, ias) {
   "use strict";

    var render = function(view, callback) {
        var self = view;
        var counter = 0; 

        var items = self.$('.loading');
        _.each(items, function(item) {
            counter ++;
            $(item).removeClass('loading').hide();
            imagesLoaded(item, function(stat) {
                counter --;
                // 加载完全完成的回调
                if(counter == 0) 
                    self.loading = false;
                // 图片加载失败不显示
                if(stat.hasAnyBroken) 
                    return false;
                // 计算加载过程需要的时间
                self.time = self.threshold - new Date().getTime() + self.beginTime;
                // 显示图片并且添加到dom
                setTimeout(function() {
                    $(item).addClass('grid-item').show();
                    self.msnry && self.msnry.appended(item);
                    self.msnry && self.msnry.layout();
                    // 移除loading动画
                    $("#__loading").remove();
                }, self.time);
            });
        });

        // 这个初始化方法只跑一次，不然会乱序
        if(!self.msnry) {
            self.msnry = new Masonry('.grid', {
                itemSelector: '.grid-item',
                columnWidth: 0
            });
        }
    }

    $.fn.asynclist = function (options) {
        this.$el = this;

        var self = options;
        self.time = 0;
        self.page = 1;
        self.size = 15;
        self.threshold = 2000;
        self.loading = false;

        // 默认第一次都要渲染
        render(self);

        $(window).scroll(function() {
            var scrollHeight = $(document).height() - $(window).height();
            var scrollTop = document.documentElement.scrollTop + document.body.scrollTop;

            if (scrollTop == scrollHeight && !self.loading) {
                self.$el.append(loadingDiv)
                // 阈值计算开始时间
                self.beginTime = new Date().getTime();
                self.loading = true;
                self.page ++;

                var collection = new window.app.collection;
                collection.url = self.collection.url;
                collection.fetch({
                    data: {
                        page: self.page,
                        size: self.size
                    },
                    success: function(data) {
                        // 阈值计算
                        var models = data.models;
                        _.each(models, function(model) {
                            self.collection.add(model);
                            render(self);
                        });
                    }
                });
            }
        });
        
    }

});
