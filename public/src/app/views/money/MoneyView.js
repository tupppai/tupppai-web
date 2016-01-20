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
                "click .recharge": "paymentMethod",
                "click .ali, .weix": "recharge",
                "click .ali, .weix": "recharge",
                "click .money-cancel": "moneyCancel",
            },
            paymentMethod: function() {
                $(".fonbox").removeClass("blo");
            },
            recharge: function() {
                $(".payment-method").addClass("blo");
                $(".box").removeClass("blo");
            },
            moneyCancel: function() {
                $(".fonbox").addClass("blo");
            }
        });
    });
