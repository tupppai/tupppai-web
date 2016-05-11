define(['tpl!app/views/ask/index/index.html', 'waterfall', ''],
    function (template, waterfall) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'grid-item',
            template: template,
            events: {
            	"click .menuMy": "menuMy",
                "click .menuPs": "menuPs",
            	"click .help-btn": "download",
            },
            onShow: function() {
            	$("#indexMenu").remove();
                
                // 渲染瀑布流
                $('.grid').waterfall({
                  // options
                  root: '.grid',
                  itemSelector: '.grid-item',
                  columnWidth: $('.grid-item').width()/2
                });
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
                    var title = '已添加至进行中';
                    fntoast(title);
                });

            },
        });
    });


