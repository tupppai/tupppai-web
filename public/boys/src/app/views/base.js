define(['marionette'], function (Marionette) {
    "use strict";
    
    return Marionette.View.extend({
        initialize:function() {
            share_friend();
            share_friend_circle();
        },
        onRender: function(){ 
            
        },
        render: function() {
            var self = this;
            self._ensureViewIsIntact();
            self.triggerMethod('before:render', this);
            //this._renderTemplate();
            self.isRendered = true;
            //this.bindUIElements();
         
            if(!this.collection && !this.model) {
                var el = $(this.el);
                var template = this.template;
                //append(el, template());
                var html = Marionette.Renderer.render(template, null, this);
                self.$el.append(html);
            }
            else if(this.collection) {
                var el = $(this.el);
                var template = this.template;
                this.collection.each(function(model){
                    append(el, template(model.toJSON()));
                });
            }
            else if(this.model) {
                var el = $(this.el);
                var template = this.template;
                self.$el.html( template(this.model.toJSON() ));
            }
             
            $(window).resize(infinite);
            function infinite() {
                var htmlWidth = $('html').width();
                if (htmlWidth >= 750) {
                    $("html").css({
                        "font-size" : "28px"
                    });
                } else {
                    $("html").css({
                        "font-size" :  28 / 750 * htmlWidth + "px"
                    });
                }
            }infinite();

        
            setTimeout(function() {
                self.triggerMethod('render', this);
            }, 100);
            return this;
        },
        scroll: function(collection) {
            var self = this;

            //页面滚动监听 进行翻页操作
            $(window).scroll(function() {
                //页面可视区域高度
                var windowHeight = $(window).height();
                //总高度
                var pageHeight   = $(document.body).height();
                //滚动条top
                var scrollTop    = document.body.scrollTop;
            
                if ((pageHeight-windowHeight-scrollTop)/windowHeight > 0.15) {
                    return false;
                }

                if(collection) {
                    self.collection = collection;
                }
                
                self.collection.loading(function(data){
                    if(data.length == 0)
                        $(window).unbind('scroll');
                });
            });
        },
    });
});
