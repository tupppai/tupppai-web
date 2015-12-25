define(['app/views/Base', 'tpl!app/templates/index/IndexView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({

            template: template,
            events: {
                "mouseover .hot-picture": "indexFadeIn",
                "mouseleave .hot-picture": "indexFadeOut",
                "click .scrollTop-icon": "scrollTop",
                "click .banner-left": "bannerScroll",
                "click .banner-right": "bannerScroll",
                "mouseover #indexBannerView": "bannerFadeIn",
                "mouseleave #indexBannerView": "bannerFadeIn",
            },
            bannerFadeIn: function(e) {
                var length = $("#indexBannerView").find(".recomment-section").length;
                if(e.type == "mouseover") {
                    if($("#indexBannerView").attr("bannerIndex") > 0) {
                        $(".banner-left").fadeIn(500);;
                    };
                    if($("#indexBannerView").attr("bannerIndex") <  (($(".swipe-wrap").find(".recomment-section")).length - 4)) {
                        $(".banner-right").fadeIn(500);
                    };
                }                
                if(e.type == "mouseleave") {
                    if($("#indexBannerView").attr("bannerIndex") > 0) {
                        $(".banner-left").fadeOut("blo");
                    };
                    if($("#indexBannerView").attr("bannerIndex") <  (($(".swipe-wrap").find(".recomment-section")).length - 4)) {
                        $(".banner-right").fadeOut(500);
                    };
                }
            },            
            bannerScroll: function(e) {
                var bannerIndex = $("#indexBannerView").attr("bannerIndex");
                if($(e.currentTarget).hasClass("banner-left")) {
                    bannerIndex--;
                    $(".swipe-wrap").animate({
                        marginLeft: - 320 * bannerIndex + "px"
                    }, 400);
                    $("#indexBannerView").attr("bannerIndex", bannerIndex);
                };
                if($(e.currentTarget).hasClass("banner-right")) {
                    bannerIndex++;
                    $(".swipe-wrap").animate({
                        marginLeft: - 320 * bannerIndex + "px"
                    }, 400);
                    $("#indexBannerView").attr("bannerIndex", bannerIndex);
                };  
            },
            initialize:function() {
                $(".ask-uploading-popup-hide").addClass('hide');
                $(".width-hide").removeClass('hide');
            },
            onRender: function() {
                
            	$(".tupai-index").addClass("active").siblings().removeClass("active");
   
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
