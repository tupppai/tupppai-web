define(['app/views/base', 'tpl!app/views/ask/post/deleteComment/deleteComment.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
        });
    });


