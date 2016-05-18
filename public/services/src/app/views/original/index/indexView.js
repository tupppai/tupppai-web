define(['tpl!app/views/original/index/index.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'loading',
            template: template,
            events: {
            	"click .menuMy": "menuMy",
                "click .menuPs": "menuPs",
            	"click .help-btn": "download",
            },
            onShow: function() {
                $(".header").css({
                    position: "fixed"
                });
                $(".container > div").css({
                    borderTop: "4.3rem solid #f7f7f7"
                })
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
            download: function(e) {
                var scrollLeft = document.documentElement.scrollLeft || document.body.scrollLeft || 0;
                var scrollTop = document.documentElement.scrollTop || document.body.scrollTop || 0;
                var img = $(e.currentTarget).parents(".workSection").find(".old").clone().addClass("picBox");
                $(e.currentTarget).after(img);
                $(".picBox").css({
                    left: event.clientX + "px",
                    top: event.clientY  + "px",
                });
                setTimeout(function() {
                    $(".picBox").addClass("picBoxAni")
                }, 1)                
                setTimeout(function() {
                    $(".picBox").remove();
                }, 1500)
                var id = $(e.currentTarget).attr("id");
                $.get('/record?type=1&target='+ id, function(data) {
                    var title = '添加成功，在"进行中"等你下载喽';
                    fntoast(title);
                });
            },
        });
    });


