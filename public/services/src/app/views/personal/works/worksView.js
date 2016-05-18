
define(['tpl!app/views/personal/works/works.html', 'lib/component/asyncList'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'myWork-pageSection loading clearfix',
            template: template,
            onRender: function() {
            },
            onShow: function() {
            }
        });
    });


