define(['tpl!app/views/common/header/header.html'],
    function (template) {
        "use strict";

        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .login-tupppai": 'fnLoginPopup',
                "click .resgister-tupppai" : 'fnResgister'
            },
            onShow: function() {
              $("a#login-popup").fancybox({
                    'padding': 0
                });
              $("a#resgister-popup").fancybox({
                    'padding': 0
                });
            },
            fnLoginPopup:function() {
                $("a#login-popup").click();
            },
            fnResgister: function() {
                $("a#resgister-popup").click();
            }

        });
    });
