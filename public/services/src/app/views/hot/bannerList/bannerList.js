define(['app/views/hot/hotBanner/hotBanner', 'swiper'], function (hotBanner) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'swiper-wrapper',
    	childView: hotBanner,
        onShow: function() {
            var swiper = new Swiper('#_banner', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                centeredSlides: true,
                autoplay: 2500,
                autoplayDisableOnInteraction: false,
                effect : 'flip',
                flip: {
                    slideShadows : true,
                    limitRotation : true,
                }
            });
        }
    });
});
