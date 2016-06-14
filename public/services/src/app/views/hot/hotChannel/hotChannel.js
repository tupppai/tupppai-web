define(['tpl!app/views/hot/hotChannel/hotChannel.html', 'swiper'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'swiper-hide',
            template: template,
            onShow: function() {
                var swiper = new Swiper('.hot-channel', {
                    slidesPerView: 4,
                    paginationClickable: true,
                });
            },

        });
    });
