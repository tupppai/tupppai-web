define(['app/views/Base', 'app/collections/Asks', 'tpl!app/templates/HotItemView.html'],
    function (View, Asks, template) {
        "use strict";
        
        return View.extend({
            collection: Asks,
            tagName: 'div',
            className: 'photo-container',
            template: template,
            events: {
                "click .like_toggle" : "likeToggle",
            },
            likeToggle: function(e) {
                $(e.currentTarget).toggleClass('icon-like-pressed');
            },
            construct: function () {
                var self = this;
                self.listenTo(self.collection, 'change', self.render);

                self.scroll();
                self.collection.loadMore();
            },
            scroll: function() {
                var self = this;
                //页面滚动监听 进行翻页操作
                $(window).scroll(function() {
                    //页面可视区域高度
                    var windowHeight = $(window).height();
                    //总高度
                    var pageHeight   = $(document.body).height();
                    //滚动条top
                    var scrollTop    = $(window).scrollTop();
                
                    if ((pageHeight-windowHeight-scrollTop)/windowHeight < 0.15) {
                        //todo: 增加加载中...
                        self.collection.loadMore();
                    }
                });
            },
            render: function() {
                var template = this.template;
                var el = $(this.el);
                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    el.append(html);
                });

                // 求P图片切换
                $('.photo-item-reply').click(function(e){
                     var AskSmallUrl = $(e.currentTarget).find('img').attr("src");
                     var AskLargerUrl = $(e.currentTarget).prev().find('img').attr("src");
                     $(e.currentTarget).prev().find('img').attr("src",AskSmallUrl);
                     $(e.currentTarget).find('img').attr("src",AskLargerUrl);
                });

                this.onRender(); 
            }
        });
    });
