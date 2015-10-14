define(['app/views/Base', 'tpl!app/templates/show/ShowReplyView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function() { 
                var self = this;
                this.listenTo(this.collection, 'change', this.render);

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
                console.log(this.collection);
                var template = this.template;
                var el       = $(this.el);
                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    el.append(html);
                });
                this.onRender();
            }
    
        });
    });
