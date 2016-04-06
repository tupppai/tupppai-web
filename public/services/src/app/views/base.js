define(['marionette'], function (Marionette) {
    "use strict";
    
    return Marionette.ItemView.extend({
        onShow: function(){ 

            // $("#contentView").click(function(e) {
            //     debugger;
            //     if($(e.target).hasClass("inner-container")) {
            //         $(".menuMy-list, .menuPs-list").addClass("hide")
            //     }
            // })
            //  点击跳回顶部
            $(".menuTop").click(function() {
                $(window).scrollTop(0);
            })
        },
        render: function() {
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
            
            this.onShow(); 

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

        //下载图片
        download: function(e) {
            var type = $(e.currentTarget).attr("data-type");
            var id   = $(e.currentTarget).attr("data-id");
            var category_id = $(e.currentTarget).attr("category-id");
            if( category_id == 'undefine' ) {
                var category_id = 0;
            }

            $.get('/record?type='+ type +'&target='+ id +'&category_id='+ category_id, function(data) {
                parse(data);
                console.log(data)
                if(data.ret == 1) {
                    var data = data.data;
                    var urls = data.url;
                    _.each(urls, function(url) {
                        location.href = '/download?url='+url;
                        console.log(location.href)
                    });
                    toast('已下载该图片，到进行中处理');
                }
            });
        },

        //瀑布流
        msnry: null,
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

        //点赞      
        pressedLike:function(e) {
            var item_id = $(e.currentTarget).attr("item-id");
            var target_id = $(e.currentTarget).attr("target-id");
            var like_count = $(e.currentTarget).find("span").text();
            if(!$(e.currentTarget).find(".like-icon").hasClass("liked-icon")) {
                $.post('/operations',{
                    target_type: 2,
                    item_id: item_id,
                    target_id: target_id,
                    number: 1,
                },function(){
                    like_count++;
                    $(e.currentTarget).find("span").text(like_count);
                    $(e.currentTarget).find(".like-icon").toggleClass('liked-icon')
                });
            }

        },
    });
});
