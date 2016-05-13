define(['tpl!app/views/list/loading.html'], function (template) {
    "use strict";
    
    return window.app.view.extend({
        tagName: 'div',
        className: '',
        template: template,
    });
});
