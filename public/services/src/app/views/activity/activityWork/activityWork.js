define(['tpl!app/views/activity/activityWork/activityWork.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'loading clearfix myWork-pageSection',
            template: template,
            onShow: function() {
            },
        });
    });
