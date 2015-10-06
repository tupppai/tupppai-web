define(['app/views/Base', 'app/collections/Asks', 'tpl!app/templates/AskItemView.html','tpl!app/templates/AskCardView.html'],
    function (View, Asks, template, AskCardView) {
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
            flag: true,
            render: function() {
                var template = this.template;
                var el = $(this.el);
                if(this.flag) {
                    el.prepend(AskCardView());   
                    this.flag = false;
                }
                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    el.append(html);
                });
                
                $(".appDownload").click(function(){
                    $("a.menu-bar-item").removeClass('active');
                    $("a.menu-bar-item[href='#download']").addClass('active');
                });

                this.onRender(); 
            }
        });
    });
