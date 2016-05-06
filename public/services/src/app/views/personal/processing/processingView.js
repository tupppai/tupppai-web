define(['tpl!app/views/personal/processing/processing.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
            	"click .download": "download"
            },
            download: function(e) {
                // var type = $(e.currentTarget).attr("data-type");
                // var id   = $(e.currentTarget).attr("data-id");
                // var category_id = $(e.currentTarget).attr("category-id");
                // $.get('/record?type='+ type +'&target='+ id, function(data) {
                    var title = '长按图片即可下载图片';
                    fntoast(title);
                // });
            },
        });
    });


