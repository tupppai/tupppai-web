define(['tpl!app/views/activity/header/header.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .works-tap": "worksTap"
            },
            onShow: function() {
                $(".menuPs").addClass("hide");
                debugger;
            },
            worksTap: function(e) {
                var type = $(e.currentTarget).attr("data-type");
                $(e.currentTarget).addClass("activity").siblings(".works-tap").removeClass("activity");
                this.trigger('click:nav', type);
            },

        });
    });
