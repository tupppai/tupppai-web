define(['app/views/Base', 'app/models/User', 'tpl!app/templates/RegisterView.html'],
    function (View, User, template) {
        "use strict";
        
        return View.extend({
            template: template,
            construct: function () {
                var self = this;

                $(".register-popup").fancybox({
                    'register' :  600,
                    'padding' : 0,
                    afterShow: function(){
                        $('.send-verification-code').click(self.countdown);
                        $(".sex-pressed").click(self.optionSex);
                        $(".register-btn").click(account.register);
                        $('.register-panel input').keyup(account.register_keyup);
                    }
                });
            },
            countdown:function() {
                var util = {
                    wait: 60,
                    hsTime: function (that) {
                        var self = $(this);
                        var wait = $(that).val();
                        wait = wait.slice(0,-1);
                        self.addClass('sent');

                        if (wait == 0) {
                            $('.send-verification-code').removeAttr("disabled").removeClass('sent').val('重新发送');
                            self.wait = 60;
                        } else {
                            var self = this;
                            $(that).attr("disabled", true).addClass('sent').val( + self.wait + 'S');
                            self.wait--;
                            setTimeout(function () {
                                self.hsTime(that);
                            }, 1000)
                        }
                    }
                }
                util.hsTime('#sned_code');
            }
        });
    });
