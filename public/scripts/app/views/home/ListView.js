define(['marionette', 'app/views/Base'],
    function (Marionette, View) {
        "use strict";

        var homeView = '#homeView';

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            construct: function () {
                var self = this;
                $(homeView).empty();

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

                self.collection.loadMore(function(){ 
                    //todo: 优化成单条添加
                    self.render();
                });
            },
            render: function() {
                var template = this.template;
                var el       = $(homeView);
                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    el.append(html);
                });
                $(homeView).append(self.el);
            }
        });
    });
