define(['tpl!app/views/channel/channelWorks/channelWorks.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'loading',
            template: template,
            onShow: function() {
            },

        });
    });
