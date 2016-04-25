/**
 * 图片轮播
 */
define(['lib/swiper/swiper'], function (Swiper) {
    "use strict";

    var imageSwiper = {};

    /**
     * swiper-container
     */
    imageSwiper.init = function () {
        var length = $(".swiper-container").find("img").length;

        if(length >= 2) {
            var mySwiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                centeredSlides: true,
                autoplay: 2500,
                autoplayDisableOnInteraction: false,
                lazyLoading:true,
            });
        };
    }

    return imageSwiper;
});