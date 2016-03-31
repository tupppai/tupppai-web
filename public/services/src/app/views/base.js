define(['marionette'], function (Marionette) {
    "use strict";
    
    return Marionette.ItemView.extend({
        onRender: function(){ 
            var htmlWidth = $('html').width();
            if (htmlWidth >= 750) {
                $("html").css({
                    "font-size" : "28px"
                });
            } else {
                $("html").css({
                    "font-size" :  28 / 750 * htmlWidth + "px"
                });
            };
            $("#contentView").click(function(e) {
                if($(e.currentTarget).hasClass("inner-container")) {
                    $(".menuMy-list, .menuPs-list").addClass("hide")
                }
            })
        },
        render: function() {
            if(!this.collection && !this.model) {
                var el = $(this.el);
                var template = this.template;
                append(el, template());
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
                $(this.el).html( template(this.model.toJSON() ));
            }
            
            this.onRender(); 

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
        renderMasonry: function() {
            var self = this;

            var template = this.template;
            var el = this.el;

            if(this.collection.length != 0){ 
                var items = '';

                for(var i = 0; i < this.collection.models.length; i++) {
                    items += template((this.collection.models[i]).toJSON());
                }
                var $items = $(items);
                $items.hide();
                $(el).append($items);

                $items.imagesLoaded().progress( function( imgLoad, image ) {
                    var $item = $( image.img ).parents( '.grid-item' );


                    self.msnry = new masonry('.grid', {
                        itemSelector: '.grid-item',
                        isAnimated: true,
                        animationOptions: {
                            duration: 750,
                            easing: 'linear',
                            queue: false
                        }
                    });
                    $item.fadeIn(400);
                });
            }
        },
    });
});
