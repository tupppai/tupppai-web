define(['app/views/base', 'tpl!app/views/menu/menu.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
            	"click .menuMy": "menuMy",
            	"click .menuPs": "menuPs",
            },
            onShow: function() {
                // $("#serveceMenu").removeClass("none");
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
        });
    });
