define(['app/views/Base', 'app/models/User', 'tpl!app/templates/register/ForgetPasswordView.html'],
    function (View, User, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function () {
                var self = this;
                $(".forget-popup").fancybox({
                    afterShow: function() {
                        $('.send-verification-code').click(self.Countdown);
                    }
                });

            },
            Countdown:function() {
                var value=Number($('.send-verification-code').val()); 
                if (value>1) {
                    document.all['time'].value=value-1; 
                } else { 
                    document.all['time'].value="同意"; 
                    return false; 
                } 
                var self = this;
                setTimeout(function() {
                    $('.send-verification-code').click();
                },1000);
            }
        });
    });
