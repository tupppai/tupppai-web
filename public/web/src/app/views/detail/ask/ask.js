define(['tpl!app/views/detail/ask/ask.html'],
    function (template) {
        "use strict";

        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            events:{
                'click .ask-btn': "downloada"
            },
            onShow: function() {
            },
            downloada: function(e) {
                var type = $(e.currentTarget).attr("data-type");
                var id   = $(e.currentTarget).attr("data-id");
                var category_id = $(e.currentTarget).attr("category-id");
                if( category_id == 'undefined' ) {
                    var category_id = 0;
                }

                    $.get('/record?type='+ type +'&target='+ id +'&category_id='+ category_id, function(data) {
                    parse(data);
                    console.log(data)
                    if(data.ret != 0) {
                        var urls = data.url;

                        location.href = '/download?url='+urls;
                        console.log(location.href)
                        toast('已下载该图片，到进行中处理');
                    }
                });
            },

        });
    });
