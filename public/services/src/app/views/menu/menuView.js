define(['tpl!app/views/menu/menu.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .menuPs": "menuMy",
            	"click .menuMy-list": "menuMyListhide",
            	// "click .menuPs": "menuPs",
            },
            onShow: function() {
                // $("#serveceMenu").removeClass("none");
            },
            onRender: function() {
                var src = $('body').attr("data-src");
                $(".personalCenter").find("img").attr("href", $("body").attr("data-src"))
            },
            //个人菜单
            menuMy: function(e) {
            	$(e.currentTarget).find(".menuMy-list").removeClass("hide");
            	$(".menuPs-list").addClass("hide");
            },  
            //求p按钮          
            menuPs: function(e) {
            	$(e.currentTarget).find(".menuPs-list").removeClass("hide");
            	$(".menuMy-list").addClass("hide");
            },
            menuMyListhide: function(e) {
                $(e.currentTarget).addClass("hide");
            }
        });
    });
