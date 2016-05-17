/**
 * 动态列表
 */
define(['zepto', 'common', 'lib/imagesloaded/imagesloaded', 'lib/masonry/masonry'], function($, common, imagesLoaded, Masonry) {
    "use strict";

    $.fn.asynclist = function(options) {
        
        var self = options.root;
        self.page = 1;
        self.size = options.size ? options.size : 15;

        self.loading = false;
        self.collection = options.collection ? options.collection : options.root.collection;
        self.finished = false;
        // 是否渲染瀑布流
        self.renderMasonry = options.renderMasonry ? options.renderMasonry : false;
        // item selector default 'loading'
        self.itemSelector = options.itemSelector ? options.itemSelector : 'loading';
        // loading call back
        self.callback = options.callback ? options.callback : function() {};
        
        if (self.renderMasonry) {
            render_masonry(self);    
        }
        
        $(window).scroll(function() {
            var scrollHeight = $(document).height() - $(window).height();
            var scrollTop = document.documentElement.scrollTop + document.body.scrollTop;
            if (scrollTop > scrollHeight - 30 && !self.loading && !self.finished) {
                $('.body-loading').removeClass('hide');
                
                self.page ++;
                self.loading = true;

                // new a collection
                var temp_collection = new window.app.collection;
                temp_collection.url = self.collection.url;

                temp_collection.fetch({
                    data: {
                        page: self.page,
                        size: self.size
                    },
                    success: function(data) {
                        var models = data.models;
                        
                        if (models.length == 0) {
                            self.finished = true;    
                            $('.body-loading').addClass('hide');
                        }

                        _.each(models, function(model) {
                            
                            if (self.renderMasonry) {
                                self.collection.add(model); 
                                render_masonry(self);
                            } else {
                                self.collection.add(model);

                                $('.body-loading').addClass('hide');
                                self.loading = false;
                            }                               
                        });
                    }
                });
            }     
        });
    };
    
    /**
     * 渲染瀑布流
     */
    var render_masonry = function(view) {
        var self = view;
        var counter = 0;
        
        if (!self.msnry) {
            self.msnry = new Masonry('.grid', {
                itemSelector: '.grid-item',
                columnWidth: 0
            });    
        }

        var items = self.$('.' + self.itemSelector);
        // 下次不会重复渲染
        items.removeClass(self.itemSelector).hide(); 
        
        _.each(items, function(item) {
            counter ++;

            imagesLoaded(item, function(stat) {
                counter --;
                
                if (counter == 0) { 
                    self.loading = false;
                    self.callback && self.callback();
                }

                if (stat.hasAnyBroken)
                    return false;

                $(item).addClass('grid-item').show();

                self.msnry && self.msnry.appended(item);
                self.msnry && self.msnry.layout();
                
                $('.body-loading').addClass('hide');
            });
        });
    }
});
