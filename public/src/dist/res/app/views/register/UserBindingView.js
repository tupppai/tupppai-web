define(['app/views/Base', 'app/models/User', 'tpl!app/templates/register/UserBindingView.html'],
    function (View, User, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function () {
            	var self = this;
                $(".binding-popup").fancybox({
                    afterShow: function() {
                        $('#sned_code_Binding').unbind('click').click(self.countdown);
                        $('#confirm_binding').unbind('click').bind('click', account.bind);
                        $('.binding-main input').unbind('keyup').keyup(self.keyup);
                        $('.sent').click(self.sent);
                    }
                });
            },
            keyup:function() {
                var phone = $('input[name=binding-phone]').val();
                var code = $('input[name=binding-code]').val();
                var password = $('input[name=binding-password]').val();
      
                if(phone != '' && code != '' && password != ''  ) {
                    $('.confirm-binding').removeAttr('disabled').addClass('bg-btn');
                }
                if(phone == '' || code == '' || password == '' ) {
                    $('.confirm-binding').attr("disabled", true).removeClass('bg-btn');
                }
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
                            $('#sned_code_Binding').removeAttr("disabled").removeClass('sent').val('重新发送');
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

                var phone = $('input[name=binding-phone]').val();
                var phone_lenght = phone.length;
                if( phone_lenght != 11 ) {
                    alert( "你的手机号码位数不对" );
                }else {

                var url = "/user/code?phone="+phone;
                $.get(url, function( returnData ){
                    console.log(returnData);
                });
                util.hsTime('#sned_code_Binding');

                }
            }
        });
    });
