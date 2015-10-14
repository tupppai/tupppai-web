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
                "click .photo-item-reply" : "photoShift",
            },
            // 求助图片切换
            photoShift: function(e) {
                     var AskSmallUrl = $(e.currentTarget).find('img').attr("src");
                     var AskLargerUrl = $(e.currentTarget).prev().find('img').attr("src");
                     $(e.currentTarget).prev().find('img').attr("src",AskSmallUrl);
                     $(e.currentTarget).find('img').attr("src",AskLargerUrl);              
            },
            likeToggle: function(e) {
                var value = 1;
                if( $(e.currentTarget).hasClass('icon-like-pressed') ){
                    value = -1;
                }

                $(e.currentTarget).toggleClass('icon-like-pressed');
                $(e.currentTarget).siblings('.actionbar-like-count').toggleClass('icon-like-color');

                var likeEle = $(e.currentTarget).siblings('.actionbar-like-count');
                var linkCount = likeEle.text( Number(likeEle.text())+value );
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
                this.onRender(); 
            }
        });
    });
