/**
 * 动态列表
 */
define(['zepto', 'common', 'lib/imagesloaded/imagesloaded', 'lib/masonry/masonry'], function ($, common, imagesLoaded, Masonry) {
   "use strict";
    
    /**
     * 渲染瀑布流 
     * 瀑布流.grid .grid-item   
     */
    var render_masonry = function(view, callback) {
        var self = view;
        self.beginTime = new Date().getTime();
        var counter = 0; 
        
        var items = self.$('.' + self.itemSelector);
        items.removeClass(self.itemSelector).hide();

        // 这个初始化方法只跑一次，不然会乱序
        if(!self.msnry) {
            msnry = self.msnry = new Masonry('.grid', {
                itemSelector: '.grid-item',
                columnWidth: 0
            });
        }
        
        _.each(items, function(item) {
            counter ++;
            
            imagesLoaded(item, function(stat) {
                counter --;
                
                // 图片加载失败不显示
                if(stat.hasAnyBroken) 
                    return false;
                // 计算加载过程需要的时间
                self.time = self.threshold - new Date().getTime() + self.beginTime;

                // 显示图片并且添加到dom
                // 加载完全完成的回调
                if(counter == 0) setTimeout(function() {
                    items.addClass('grid-item').show();
                    self.msnry && self.msnry.layout();

                    self.callback();
                    self.loading = false;
                    // 移除loading动画
                    $("#__loading").remove();
                }, self.time);
            });
        });

    }

    $.fn.asynclist = function (options) {
        this.$el = this;
        
        var self = options.root;
        self.time = 0;
        self.page = 1;
        self.size = options.size ? options.size : 15;
        self.threshold = 2000;
        self.loading = false;
        self.collection = options.collection;
        // 是否最后一页
        self.finished = false;
    
        // 是否需要渲染瀑布流
        self.renderMasonry = options.renderMasonry ? options.renderMasonry : false;
        // item selector
        self.itemSelector = options.itemSelector ? options.itemSelector : '';
        
        self.callback = options.callback ? options.callback : function(){};

        // 默认第一次都要渲染
        if (self.renderMasonry) {
            render_masonry(self);
        } 

        $(window).scroll(function() {
            var scrollHeight = $(document).height() - $(window).height();
            var scrollTop = document.documentElement.scrollTop + document.body.scrollTop;

            if (scrollTop >  scrollHeight - 30 && !self.loading && !self.finished) {
                $('.body-loading').removeClass('hide');
                
                // 阈值计算开始时间
                self.loading = true;
                self.page ++;
                
                // new
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
                       
                        if (models.length == 0) {
                            self.finished = true;
                            $('.body-loading').addClass('hide');
                        }
                        
                        _.each(models, function(model) {
                            self.collection.add(model);
                            if (self.renderMasonry) {
                                render_masonry(self);
                            } else {
                                self.loading = false;    
                            }
                        });
                    }
                });
            }
        });
    }
});
