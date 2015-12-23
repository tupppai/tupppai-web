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
                        $('#sned_code').click(self.countdown);
                        $('#confirm_login').unbind('click').bind('click',self.changePassword);
                        $('.fg-main input').keyup(self.keyup);
                    }
                });
            },
            keyup:function() {
                var phone = $('input[name=forgetPwd_phone]').val();
                var code = $('input[name=forgetPwd_code]').val();
                var newPassword = $('input[name=forgetPwd_newPassword]').val();
                var anewPassword = $('input[name=forgetPwd_anewPassword]').val();
                if(phone != '' && code != '' && newPassword != '' && anewPassword != '' ) {
                    console.log( 123 );
                    $('.confirm-and-login').removeAttr('disabled').addClass('bg-btn');
                }
                if(phone == '' || code == '' || newPassword == '' || anewPassword == '' ) {
                    $('.confirm-and-login').attr("disabled", true).removeClass('bg-btn');
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
                            $('#sned_code').removeAttr("disabled").removeClass('sent').val('重新发送');
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
                var phone = $('input[name=forgetPwd_phone]').val();
                var url = "/user/code?phone="+phone;
                $.get(url, function( returnData ){
                    console.log(returnData);
                });
                util.hsTime('#sned_code');
            },
            changePassword:function() {
                var phone = $('input[name=forgetPwd_phone]').val();
                var code = $('input[name=forgetPwd_code]').val();
                var newPassword = $('input[name=forgetPwd_newPassword]').val();
                var anewPassword = $('input[name=forgetPwd_anewPassword]').val();
                if( phone == '') {
                    alert('手机号不能为空');
                    return false;
                }
                if( code == '') {
                    alert('验证码不能为空');
                    return false;
                }
                if( newPassword == '' || anewPassword == '') {
                    alert('密码不能为空');
                    return false;
                }
                if( newPassword != anewPassword) {
                    alert('两个密码不相同');
                    return false;
                }
                var url = "/user/forget";
                var postData = {
                    'phone': phone,
                    'code' : code,
                    'new_pwd': newPassword
                };

                $.post(url, postData, function( returnData ){
                    if(returnData.ret == 1) {
                        history.go(1);
                        location.reload();
                    } else {
                        console.log(returnData);
                    }
                });

            }
        });
    });
