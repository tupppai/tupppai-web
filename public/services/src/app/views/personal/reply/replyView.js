define(['tpl!app/views/personal/reply/reply.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'myWork-pageSection grid-item',
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


