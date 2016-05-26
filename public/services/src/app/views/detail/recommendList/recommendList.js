define(['app/views/detail/detailRecommend/detailRecommend'], 
	function (recommendView) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'clearfix recommend-box',
    	childView: recommendView,        
        onShow: function() {
            this.imageLazyLoad();
        },
        imageLazyLoad: function() {
            $("img.center-loading").lazyload({
                effect: "fadeIn",
                threshold : 50,
                load: function(image, count) {
                    //获取原始长宽
                    var image = image[0];
                    var imageWidth = image.naturalWidth;
                    var imageHeight = image.naturalHeight;
                    var imageRatio = imageWidth/imageHeight;
                    
                    var container = $(image).parent('.center-loading-container')[0];
                    var containerWidth = $(container).width();
                    var containerHeight = $(container).height();
                    var tempWidth = 0;
                    var tempHeight = 0;
                    var offsetLeft = 0;
                    var offsetTop  = 0;
                    
                    if (imageHeight >= containerHeight && imageWidth >= containerWidth) {
                        if (imageWidth / imageHeight >= containerWidth / containerHeight) {
                            tempHeight = containerHeight;
                            tempWidth  = imageWidth * containerHeight / imageHeight;
                            offsetLeft = (containerWidth - tempWidth) / 2;
                            offsetTop  = 0;
        
                        } else {
                            tempWidth  = containerWidth;
                            tempHeight = imageHeight * containerWidth / imageWidth;
                            offsetLeft = 0;
                            offsetTop  = (containerHeight - tempHeight) / 2;
                        } 
                    } else if (imageWidth <= containerWidth && imageHeight <= containerHeight) {
                        if (imageRatio > containerWidth / containerHeight) {
                            tempHeight   = containerHeight;
                            tempWidth    = imageWidth * containerHeight / imageHeight;
                            offsetTop    = 0;
                            offsetLeft   = (imageWidth - tempWidth) / 2;
                        } else {
                            tempWidth    = containerWidth;
                            tempHeight   = imageHeight * containerWidth / imageWidth
                            offsetLeft   = 0;
                            offsetTop    = (imageHeight - tempHeight) / 2;
                        }
                    } else if (imageWidth <= containerWidth && imageHeight > containerHeight) { 
                        tempWidth  = containerWidth;
                        tempHeight = imageHeight * containerWidth / imageWidth;
                        offsetTop  = (imageHeight - tempHeight) / 2;
                        offsetLeft = 0;
                    } else if (imageWidth > containerWidth && imageHeight <= containerHeight) {                                               
                        tempHeight = containerHeight;
                        tempWidth  = imageRatio * containerHeight;
                        offsetLeft = (imageWidth - tempWidth) / 2;
                        offsetTop  = 0;
                    } 
                    if(imageWidth/imageHeight == containerWidth/containerHeight) {
                        tempHeight = containerHeight;
                        tempWidth = containerWidth;
                        offsetLeft = 0;
                        offsetTop  = 0;
                    }
                    
                    $(image).css('left', offsetLeft);
                    $(image).css('top', offsetTop);
                    $(image).width(tempWidth);
                    $(image).height(tempHeight); 
                }
            });
        },
    });
});
