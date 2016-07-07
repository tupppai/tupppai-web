define(['tpl!app/views/channel/nav/nav.html'],
    function (template) {
        "use strict";

        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            events:{
                "click .channel-nav": "clickNav"
            },
            onShow: function() {
                this.$('.imageLoad').imageLoad({scrop: true});

                $('.channel-nav').first().addClass('selected');
            },
            clickNav: function(e) {
                $('.channel-nav').removeClass('selected');
                $(e.currentTarget).addClass('selected');
            }

        });
    });
