define(['marionette', 'imagesLoaded', 'masonry', 'app/models/Base'],
    function (Marionette, imagesLoaded, masonry, ModelBase) {
        "use strict";
        
        return Marionette.ItemView.extend({
            initialize: function(){ 
                $(window).unbind('scroll'); 

                this.construct();

                $(window).scroll(function() {
                    var scrollTop = $(window).scrollTop();
                    if(scrollTop > 700) {
                        $(".scrollTop-icon").fadeIn(1000);
                    } else {
                        $(".scrollTop-icon").fadeOut(1000);
                    }
                });
                $(".ask-uploading-popup-hide").addClass("blo");
            },
            scrollTop: function() {
                $("html, body").animate({
                    scrollTop: "0" 
                }, 1000);
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

                // class=center-loading 图片居中显示 图片被容器center-loading-image-container包裹
                var centerImgLoad = imagesLoaded('.center-loading', function() {
                    // console.log('image load to set center');
                });
                centerImgLoad.on('progress', function(centerImgLoad, image) {
                    if (image.isLoaded) {
                        var imageWidth  = image.img.width;
                        var imageHeight = image.img.height;
                        var imageRatio  = imageWidth/imageHeight;
                        var centerLoadContainer = $(image.img).parents('.center-loading-image-container');
                        var containerWidth      = $(centerLoadContainer)[0].offsetWidth;
                        var containerHeight     = $(centerLoadContainer)[0].offsetHeight;
                        var tempWidth  = 0;
                        var tempHeight = 0;
                        var offsetLeft = 0;
                        var offsetTop  = 0;
                        if (imageHeight >= containerHeight && imageWidth >= containerWidth) {
                            // 图片宽高都大于容器宽高

                            // 图片长比较长，按照高度缩放，截取中间部分
                            if (imageWidth / imageHeight >= containerWidth / containerHeight) {
                              

                                tempHeight = containerHeight;
                                tempWidth  = imageWidth * containerHeight / imageHeight;

                                offsetLeft = (containerWidth - tempWidth) / 2;
                                offsetTop  = 0;
                            } else {
                                //图片比较高，安装宽度缩放，截取中间部分
                                tempWidth  = containerWidth;
                                tempHeight = imageHeight * containerWidth / imageWidth;

                                offsetLeft = 0;
                                offsetTop  = (containerHeight - tempHeight) / 2;
                            };    
                        } else if (imageWidth <= containerWidth && imageHeight <= containerHeight) {
                            // 图片宽高都小于容器宽高
                            if (imageRatio > containerWidth / containerHeight) {
                                tempHeight   = containerHeight;
                                tempWidth    = imageWidth * containerHeight / imageHeight;

                                offsetTop    = 0;
                                offsetLeft   = (imageWidth - tempWidth) / 2;
                            } else {
                                tempWidth    = containerWidth;
                                tempHeight   = imageHeight * containerWidth / imageWidth;

                                offsetLeft   = 0;
                                offsetTop    = (imageHeight - tempHeight) / 2;
                            }
                        } else if (imageWidth <= containerWidth && imageHeight > containerHeight) {
                            // 图片宽度小于容器 高度大于容器  
                            tempWidth  = containerWidth;
                            tempHeight = imageHeight * containerWidth / imageWidth;

                            offsetTop  = (imageHeight - tempHeight) / 2;
                            offsetLeft = 0;
                        } else if (imageWidth > containerWidth && imageHeight <= containerHeight) {
                            // 图片宽度大于容器 图片高度小于容器
                            tempHeight = containerHeight;
                            tempWidth  = imageRatio * containerHeight;

                            offsetLeft = (imageWidth - tempWidth) / 2;
                            offsetTop  = 0;
                        };          

                        $(image.img).css('left', offsetLeft);
                        $(image.img).css('top', offsetTop);
                        $(image.img).width(tempWidth);
                        $(image.img).height(tempHeight);       
                    };
                });
            },
			page: function() {
			},
			download: function(e) {
				var type = $(e.currentTarget).attr("data-type");
                var id   = $(e.currentTarget).attr("data-id");
                var category_id = $(e.currentTarget).attr("category-id");
                if( category_id == 'undefine' ) {
                    var category_id = 0;
                }

                $.get('/record?type='+ type +'&target='+ id +'&category_id='+ category_id, function(data) {
                    parse(data);
                    if(data.ret == 1) {
                        var data = data.data;
                        var urls = data.url;
                        _.each(urls, function(url) {
                            location.href = '/download?url='+url;
                        });

                        toast('已下载该图片，到进行中处理');
                    }
                });
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
                    var scrollTop    = $(window).scrollTop();
                
                    if ((pageHeight-windowHeight-scrollTop)/windowHeight < 0.15) {
                        if(collection) {
                            self = collection;
                        }

                        self.collection.loading(function(data){ });
                    }
                });
            },
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
            superLike: function(e) {
                var value = $(e.currentTarget).attr('data-love');
                var id   = $(e.currentTarget).attr('data-id');
                var likeEle = $(e.currentTarget).find('.like-count');
                var type   = 2;

                $.get('/love', {
                    id: id,
                    num: value,
                    type: 2,
                }, function(data) {
                    if( data.ret != 1) {
                        var data = parse(data);
                    
                    } else {
                        value++;
                        if(value > 3) {
                            value = 0;
                            $(e.currentTarget).attr("data-love", value);
                            $(e.currentTarget).find(".bg-sprite-rebirth").removeClass("like-icon-three").addClass("like-icon");

                            $(e.currentTarget).removeClass('liked');
                            $(e.currentTarget).find('.like-count').toggleClass('like-color');

                        likeEle.text( Number(likeEle.text()) - 3);
                    }
                    if(value == 1) {
                        $(e.currentTarget).attr("data-love", value);
                        $(e.currentTarget).find(".bg-sprite-rebirth").removeClass("like-icon").addClass("like-icon-one");

                        $(e.currentTarget).addClass('liked');
                        $(e.currentTarget).find('.like-count').toggleClass('like-color');

                        likeEle.text( Number(likeEle.text())+ 1 );
                    }                
                    if(value == 2) {
                        $(e.currentTarget).attr("data-love", value);
                        $(e.currentTarget).find(".bg-sprite-rebirth").removeClass("like-icon-one").addClass("like-icon-two");
                            $(e.currentTarget).find('.like-count').toggleClass('like-color');

                        likeEle.text( Number(likeEle.text())+ 1 );
                    }                
                        if(value == 3) {
                            $(e.currentTarget).attr("data-love", value);
                            $(e.currentTarget).find(".bg-sprite-rebirth").removeClass("like-icon-two").addClass("like-icon-three");

                            $(e.currentTarget).addClass('liked');
                            $(e.currentTarget).find('.like-count').toggleClass('like-color');

                        likeEle.text( Number(likeEle.text())+ 1 );
                        }
                    }
                });
     
            },
			collectToggle: function(e) {
				var value = $(e.currentTarget).hasClass('collected') ? -1: 1;

                var id 	= $(e.target).attr('data-id');
                var type= $(e.target).attr('data-type');
                var collection = new ModelBase({
                    id: id,
                    type: 1,
                    status: value 
                });
				collection.url =  '/collect';

                collection.save(null, function(){
                    $(e.currentTarget).toggleClass('collected');
                    $(e.currentTarget).siblings('.collection-count').toggleClass('collection-color');

                    var collectionEle = $(e.currentTarget).siblings('.collection-count');
                    collectionEle.text( Number(collectionEle.text())+value );
                });
            },
        });
    });
