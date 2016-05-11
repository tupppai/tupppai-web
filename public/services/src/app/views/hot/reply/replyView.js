define(['tpl!app/views/hot/reply/reply.html','waterfall'],
    function (template, waterfall) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'grid-item',
            template: template,
            onShow: function() {
                // 渲染瀑布流
                $('.grid').waterfall({
                  // options
                  root: '.grid',
                  itemSelector: '.grid-item',
                  columnWidth: $('.grid-item').width()/2
                });
            },
        });
    });
