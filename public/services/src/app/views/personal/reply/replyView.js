define(['tpl!app/views/personal/reply/reply.html', 'lib/component/asyncList'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'myWork-pageSection grid-item loading',
            template: template,
            onRender: function() {
            },
            onShow: function() {
            }
        });
    });


