define(['app/views/Base', 'tpl!app/templates/index/IndexView.html', 'superSlide'],
    function (View, template, superSlide) {
        "use strict";
        
        return View.extend({

            template: template,
            events: {
                "mouseover .hot-picture": "indexFadeIn",
                "mouseleave .hot-picture": "indexFadeOut",
                "click .scrollTop-icon": "scrollTop",
            },
            initialize: function() {
            },
            onRender: function() {
                $(".tupai-index").addClass("active").siblings().removeClass("active");
                setTimeout(function() {
                    var length= $(".swipe-wrap").find(".recomment-section").length;
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
                }, 1500)
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
