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
                "click .fonbox": "fonbox",
                "click .money-cancel": "moneyCancel",
                "click #confirm": "submitPay" 
            },
            paymentMethod: function() {
                $(".fonbox").removeClass("blo");
            },
            fonbox: function(e) {
                if ($(e.target).hasClass("fonbox")) {
                    $(e.target).addClass("blo");
                    $(".payment-method").removeClass("blo");
                    $(".box").addClass("blo");
                };
            },
            moneyCancel: function() {
                $(".fonbox").addClass("blo");
            },
            submitPay:function(e) {
                var uid = $(".user-message").attr("data-uid");
                var amount = document.getElementById('amount').value * 1000;
                var channel = $(e.currentTarget).attr("data-pay");

                $.post('pay',{
                    uid: uid,
                    channel: channel,
                    amount: amount
                },function(data){
                });
                
            },
            recharge: function(e) {
                var data_pay = $(e.currentTarget).attr("data-pay");
                $("#confirm").attr("data-pay", data_pay);
                $(".payment-method").addClass("blo");
                $(".box").removeClass("blo");
            },
            moneyCancel: function() {
                $(".fonbox").addClass("blo");
            }
        });
    });
