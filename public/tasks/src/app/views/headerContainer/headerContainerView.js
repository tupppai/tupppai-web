define(['app/views/base', 'tpl!app/views/headerContainer/headerContainer.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events : {
            	"mouseenter .header-portrait" : "headerPortrait",
            	"mouseleave .right-area" : "headerPortraitNone",
            },
            headerPortrait: function(e) {
            	$(".header-portrait").animate({
            		width: "60px",
            		height: "60px",
            	}, 300)
            	$(".function-list").fadeIn(300);
            },
            headerPortraitNone: function(e) {
            	$(".header-portrait").stop(true,true).animate({
            		width: "37px",
            		height: "37px",
            	}, 300)
            	$(".function-list").fadeOut(300)
                    
            }
        });
    });
