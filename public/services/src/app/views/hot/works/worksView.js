define(['tpl!app/views/hot/works/works.html', 'imageLazyLoad'],
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