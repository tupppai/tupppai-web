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
            	"click .menuTop": "goTop",
            },
            goTop : function() {
                var scroll = document.documentElement.scrollTop || document.body.scrollTop;  //火狐||谷歌的兼容
                var speed = scroll / 20;
                var timer = setInterval(function(){
                    scroll -= speed;
                    if(document.documentElement.scrollTop){
                        document.documentElement.scrollTop = scroll;
                    } else {
                        document.body.scrollTop = scroll;
                    }
                    if(scroll <= 0) {
                        scroll = 0;
                        clearInterval(timer);
                    }
                }, 20);
            },
            onShow: function() {
                var src = $('body').attr("data-src");
                $(".personalCenter").find("img").attr("href", $("body").attr("data-src"));
                setTimeout(function() {
                    var height = $(window).height()/2;
                    var documentHeight = $(document).height();
                    var htmlHeight = $("#header-section").height();
                    if(htmlHeight < height) {
                        $(".footer").css({
                            position: "absolute",
                            top: height+"px",
                        })
                    } else {
                        $(".footer").css({
                            position: "static",
                        })
                    }
                }, 1000)
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
