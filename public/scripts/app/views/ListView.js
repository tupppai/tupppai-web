define(['app/views/Base', 'app/collections/Asks', 'tpl!app/templates/AskItemView.html', 'remodal'],
    function (View, Asks, template) {
        "use strict";
        
        return View.extend({
            collection: Asks,
            tagName: 'div',
            className: 'photo-container',
            template: template,
            events: {
                "click .like_toggle" : "likeToggle"
            },
            likeToggle : function() {

                $(this).toggleClass('icon-like-pressed');
                console.log(this);
            },
            construct: function () {
                var self = this;
                window.app.content.close();

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
                            //todo: 优化成单条添加
                            window.app.content.reset();
                            window.app.content.show(self);
                        });
                    }
                });

                self.collection.loadMore(function(){ 
                    //todo: 优化成单条添加
                    window.app.content.reset();
                    window.app.content.show(self);
                });
            },
            render: function() {
                this.onRender(); 

                var template = this.template;
                var el = $(this.el);
                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    el.append(html);
                });
            }
        });
    });
