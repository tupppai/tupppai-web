define(['tpl!app/views/hot/reply/reply.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'loading grid-item',
            template: template,
            onRender: function(){ 
                // this.loadImage(); 
                 // this.$('.imageLoad').imageLoad({scrop: true});
            },
            loadImage: function() {

                // class=center-loading 图片居中显示 图片被容器center-loading-image-container包裹

                    var image = $("img.center-loading"); 
                    $.each(image, function() {

                        var imageWidth  = this.offsetWidth;
                        var imageHeight = image.height;
                        var imageRatio  = imageWidth/imageHeight;
                        var centerLoadContainer = $(image).parents('.center-loading-container');
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

                        $(image).css('left', offsetLeft);
                        $(image).css('top', offsetTop);
                        $(image).width(tempWidth);
                        $(image).height(tempHeight);       
                    });
            },
        });
    });
