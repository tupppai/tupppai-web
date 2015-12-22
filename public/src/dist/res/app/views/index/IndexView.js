define(['app/views/Base', 'tpl!app/templates/index/IndexView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({

            template: template,
            events: {
                "mouseover .hot-picture": "indexFadeIn",
                "mouseleave .hot-picture": "indexFadeOut",
                "click .scrollTop-icon": "scrollTop",
            },
            initialize:function() {
                $(".ask-uploading-popup-hide").addClass('hide');
            },
            onRender: function() {
            	$(".tupai-index").addClass("active").siblings().removeClass("active");
                // setTimeout(function(){
                //     var id = $("body").attr("data-uid");
                //     if( id ) {
                //         $(".login-popup").addClass("hide");
                //         $(".ask-uploading-popup-hide").removeClass('hide');
                //     } else {
                //         $(".ask-uploading-popup-hide").addClass('hide');
                //         $(".login-popup").removeClass("hide");
                //     }
                // },500);
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
