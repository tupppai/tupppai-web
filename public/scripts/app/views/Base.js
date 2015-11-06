define(['marionette', 'imagesLoaded'],
    function (Marionette, imagesLoaded) {
        "use strict";
        
        return Marionette.ItemView.extend({
            initialize: function(){ 
                //console.log('base view initialize'); 
                $(window).unbind('scroll'); 

                this.construct();
            },
            construct: function () {
            },
            onRender: function(){ 
                this.loadImage(); 
            },
            loadImage: function() {
                var imgLoad = imagesLoaded('.is-loading', function() { 
                    //console.log('all image loaded');
                });
                imgLoad.on('progress', function ( imgLoad, image ) {
                    if(image.isLoaded) {
                        setTimeout(function() {
                            if(image) {
                                image.img.parentNode.className =  '';
                                $(image.img).css('opacity', 0);
                                //$(image.img).fadeIn(300);
                                $(image.img).animate({
                                    opacity: 1
                                }, 300);
                            }
                        }, 400);
                    }
                });
            },
            scroll: function() {
                var self = this;
/*
                var emptyView = '<div id="emptyContentView" class="emptyContentView"> <span class="remind-content"> <i class="empty-icon bg-sprite-new"></i> <p class="empty-content">暂时还没有评论哦</p> </span> </div>';
                append($(self.el), emptyView);
*/

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
                        self.collection.loadMore(function(data){
                        });
                    }
                });
            }
        });
    });
