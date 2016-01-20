 define([
        'app/views/Base',
        'tpl!app/templates/money/MoneyView.html'
       ],
    function (View, template) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .recharge": "recharge"
            },
            recharge: function() {
                $(".fonbox").removeClass("blo");
            }
        });
    });
