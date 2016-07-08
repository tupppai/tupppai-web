define(['tpl!app/views/user/header/header.html'],
    function (template) {
        "use strict";

        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            events:{
                'click .nav': 'fnHeaderNav'
            },
            onShow: function() {
                this.$('.imageLoad').imageLoad({scrop: true});
            },
            fnHeaderNav: function(e) {
                $('.nav').removeClass('select');
                $(e.currentTarget).addClass('select');
            }

        });
    });
