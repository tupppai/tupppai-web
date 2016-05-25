define(['tpl!app/views/channel/channelDetail/channelDetail.html'],
    function (template) {
        "use strict";

        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .works-tap": "clickNav",
            },
            clickNav: function(e) {
                $(e.currentTarget).addClass("activity").siblings(".works-tap").removeClass("activity");

                var type = $(e.currentTarget).attr("data-type");

                this.trigger('click:nav', type);
            },
        });
    });
