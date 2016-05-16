define(['tpl!app/views/ask/index/index.html'],
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
                var id = $(e.currentTarget).attr("id");
                $.get('/record?type=1&target='+ id, function(data) {
                    var title = '添加成功，在"进行中"等你下载喽';
                    fntoast(title);
                });

            },
        });
    });


