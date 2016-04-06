define(['app/views/base', 'tpl!app/views/mypage/help/help.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
        });
    });


