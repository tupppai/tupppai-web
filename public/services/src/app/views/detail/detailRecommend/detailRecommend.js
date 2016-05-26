define(['tpl!app/views/detail/detailRecommend/detailRecommend.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'recommend',
            template: template,
        });
    });
