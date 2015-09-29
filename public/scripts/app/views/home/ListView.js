define(['marionette', 'app/views/Base'],
    function (Marionette, View) {
        "use strict";

        var homeListView = '#homeListView';

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            data: 0,
            construct: function () {
                var self = this;
                window.app.home.$el.show();
                $(homeListView).empty();

                //页面滚动监听 进行翻页操作
                $(window).scroll(function() {
                    //页面可视区域高度
                    var windowHeight = $(window).height();
                    //总高度
                    var pageHeight  = $(document.body).height();
                    //滚动条top
                    var scrollTop   = $(window).scrollTop();
                
                    if ((pageHeight - windowHeight - scrollTop)/windowHeight < 0.15) {
                        //todo: 增加加载中...
                        self.collection.loadMore(function(){ 
                            self.render();
                        });
                    }
                });

                self.collection.data.page = 0;
                self.collection.loadMore(function(){ 
                    //todo: 优化成单条添加
                    self.render();
                });
            },
            render: function() {
                var template = this.template;
                var el       = $(homeListView);
                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    el.append(html);
                });
                $(homeListView).append(self.el);
            }
        });
    });
