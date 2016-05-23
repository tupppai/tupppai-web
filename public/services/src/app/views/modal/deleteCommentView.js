define(['tpl!app/views/modal/deleteComment.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
        });
    });


