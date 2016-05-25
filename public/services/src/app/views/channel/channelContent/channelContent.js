define(['tpl!app/views/channel/channelContent/channelContent.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'aniFadeInUp',
            template: template,
        });
    });
