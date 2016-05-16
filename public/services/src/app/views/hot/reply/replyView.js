define(['tpl!app/views/hot/reply/reply.html', 'imageLazyLoad'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'loading grid-item',
            template: template,
            onShow: function() {
                //this.$('.imageLoad2').imageLoad({scrop: true});
            },

        });
    });
