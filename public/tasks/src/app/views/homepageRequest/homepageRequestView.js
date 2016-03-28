define(['app/views/base', 'tpl!app/views/homepageRequest/homepageRequest.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events : {
            	"click .list-item" : "switchHomePage",
            },
            switchHomePage: function(e) {
            	$(e.currentTarget).removeClass("unactive")
            			.siblings().addClass("unactive");
                /*animation of arrow & switch homepage-content*/
                var targetClass = $(e.currentTarget).attr("class");
                if (targetClass.indexOf("myRequest")!==-1){
                    $(".icon-toggle-arrow").animate({
                        left:"265px",
                    },500);
                    $(".content-myRequest").removeClass("hide").siblings().addClass("hide");
                }
                if (targetClass.indexOf("myOffer")!==-1){
                    $(".icon-toggle-arrow").animate({
                        left:"475px",
                    },500);
                    $(".content-myOffer").removeClass("hide").siblings().addClass("hide");
                }
                if (targetClass.indexOf("myWork")!==-1){
                    $(".icon-toggle-arrow").animate({
                        left:"680px",
                    },500);
                    $(".content-myWork").removeClass("hide").siblings().addClass("hide");
                }
            },
        });
    });
