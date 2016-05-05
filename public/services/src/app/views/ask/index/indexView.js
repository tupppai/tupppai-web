define(['tpl!app/views/ask/index/index.html', 'waterfall'],
    function (template, waterfall) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'grid-item',
            template: template,
            onShow: function() {
            	$("#indexMenu").remove();
            },
            events: {
            	"click .menuMy": "menuMy",
                "click .menuPs": "menuPs",
            	"click .help-btn": "download",
            },
            onShow: function() {
                $('.grid').waterfall({
                  // options
                  itemSelector: '.grid-item',
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
                var type = $(e.currentTarget).attr("type");
                var id   = $(e.currentTarget).attr("id");
                var category_id = $(e.currentTarget).attr("category-id");
                if( category_id == 'undefine' ) {
                    var category_id = 0;
                }

                $.get('/record?type='+ type +'&target='+ id +'&category_id='+ category_id, function(data) {
                    parse(data);
                    console.log(data)
                    if(data.ret == 1) {
                        var data = data.data;
                        var urls = data.url;
                        _.each(urls, function(url) {
                            location.href = '/download?url='+url;
                            console.log(location.href)
                        });
                        toast('已下载该图片，到进行中处理');
                    }
                });
            },
        });
    });


