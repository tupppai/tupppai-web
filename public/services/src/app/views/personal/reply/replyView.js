define(['tpl!app/views/personal/reply/reply.html', 'lib/component/asyncList'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'myWork-pageSection grid-item loading',
            template: template,
            onRender: function() {
                
                // // 渲染瀑布流
                // $('.grid').waterfall({
                //   // options
                //   root: '.grid',
                //   itemSelector: '.grid-item',
                //   columnWidth: $('.grid-item').width()/2
                // });
                // this.$el.asynclist(this);
            },
            onShow: function() {
            }
        });
    });


