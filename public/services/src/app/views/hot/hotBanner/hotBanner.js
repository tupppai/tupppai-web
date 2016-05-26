define(['tpl!app/views/hot/hotBanner/hotBanner.html', 'swiper'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'swiper-slide',
            template: template,
            onShow: function() {

            },

        });
    });
