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
                var _this = $(e.currentTarget);
                $('.nav').removeClass('select');
                _this.addClass('select');
                var type = _this.attr('data-type');

                this.trigger('click:nav',type);

            }

        });
    });
