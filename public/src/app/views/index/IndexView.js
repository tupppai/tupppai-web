define(['app/views/Base', 'tpl!app/templates/index/IndexView.html', 'superSlide'],
    function (View, template, superSlide) {
        "use strict";
        
        return View.extend({

            template: template,
            events: {
                "mouseover .hot-picture": "indexFadeIn",
                "mouseleave .hot-picture": "indexFadeOut",
                "click .scrollTop-icon": "scrollTop",
                // "click .banner-left": "bannerScroll",
                // "click .banner-right": "bannerScroll",
                // "mouseover #indexBannerView": "bannerFadeIn",
                // "mouseleave #indexBannerView": "bannerFadeIn",
            },
            // bannerFadeIn: function(e) {
            //     var length = $("#indexBannerView").find(".recomment-section").length;
               
            //     if ($("#indexBannerView").attr("bannerIndex") > 0) {
            //         $(".banner-left").fadeIn(500);
            //     }
            //     else if ($("#indexBannerView").attr("bannerIndex") <  (($(".swipe-wrap").find(".recomment-section")).length - 4)) {
            //         $(".banner-right").fadeIn(500);
            //     }
            //     else {
            //         $(".banner-left").fadeOut(500);
            //         $(".banner-right").fadeOut(500);
            //     }
            // },            
            // bannerScroll: function(e) {
            //     var bannerIndex = $("#indexBannerView").attr("bannerIndex");
            //     if($(e.currentTarget).hasClass("banner-left")) {
            //         bannerIndex--;
            //         $(".swipe-wrap").animate({
            //             marginLeft: - 320 * bannerIndex + "px"
            //         }, 400);
            //         $("#indexBannerView").attr("bannerIndex", bannerIndex);
            //     };
            //     if($(e.currentTarget).hasClass("banner-right")) {
            //         bannerIndex++;
            //         $(".swipe-wrap").animate({
            //             marginLeft: - 320 * bannerIndex + "px"
            //         }, 400);
            //         $("#indexBannerView").attr("bannerIndex", bannerIndex);
            //     };  
            // },
            initialize:function() {
                $(".ask-uploading-popup-hide").addClass('hide');
                $(".width-hide").removeClass('hide');
            },
            onRender: function() {
                $(".tupai-index").addClass("active").siblings().removeClass("active");
                setTimeout(function() {
                    var length= $(".swipe-wrap").find(".recomment-section").length;
                    $(".swipe-wrap").css({
                        width: length * 320 + "px"
                    });
                    if (length > 4) {
                        $(".banner-left, .banner-right").removeClass("blo"); 
                        $(".recommend-container").slide({
                            easing: "easeInOutCubic",
                            titCell: "",
                            mainCell: ".swipe-wrap",
                            autoPage: true,
                            effect: "leftLoop",
                            autoPlay: true,
                            vis: 4,
                            delayTime: 500,
                            pnLoop: true,
                            interTime: 2500
                        });
                    };
                }, 1000)
                
            },
            indexFadeIn: function(e) {
                $(e.currentTarget).find(".index-artwork").stop(true, true).fadeIn(1500);
                $(e.currentTarget).find(".index-work").stop(true, true).fadeOut(1500);
            },
            indexFadeOut: function(e) {
                $(e.currentTarget).find(".index-artwork").stop(true, true).fadeOut(1500);
                $(e.currentTarget).find(".index-work").stop(true, true).fadeIn(1500);
            },
        });
    });
